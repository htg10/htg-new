<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Telecaller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Entry;
use App\Models\Products;
use Illuminate\Support\Facades\DB;
use App\Exports\FinanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ContractPayment;

class BackendIndexController extends Controller
{
    public function index()
    {
        $entry = Entry::all();
        $services = Products::all();
        $banks = Bank::all();

        $entries = Entry::with(['product', 'user'])->get();

        $totalAmount = $entries->reduce(function ($carry, $entry) {
            return $carry + ($entry->product ? $entry->product->sum('total_amount') : 0);
        }, 0);

        $totalBalance = $entries->reduce(function ($carry, $entry) {
            return $carry + ($entry->product ? $entry->product->sum('total_amount') - $entry->product->sum('paid_amount') : 0);
        }, 0);

        $result = DB::select("SELECT COUNT(*) as total_entry, type FROM entries GROUP BY type");

        $charData = "";
        foreach ($result as $list) {
            $charData .= "['" . $list->type . "', " . $list->total_entry . "],";
        }

        $chartData = rtrim($charData, ',');

        $dashboardData = $this->getDashboardChartAndPaymentData(request());

        $income = $dashboardData['income'];
        $expense = $dashboardData['expense'];
        $totalIncome = $dashboardData['totalIncome'];
        $totalExpense = $dashboardData['totalExpense'];
        $netBalance = $dashboardData['netBalance'];

        $bdmCountChartData = $dashboardData['bdmCountChartData'];
        $serviceCountChartData = $dashboardData['serviceCountChartData'];
        $bdmPriceChartData = $dashboardData['bdmPriceChartData'];
        $servicePriceChartData = $dashboardData['servicePriceChartData'];
        $paymentChartData = $dashboardData['paymentChartData'];
        $dateChartData = $dashboardData['dateChartData'];

        // Contracts that still carry a balance (for the dashboard card)
        $contractsWithBalance = Entry::with(['product', 'user', 'contractPayments'])
            ->get()
            ->map(function ($e) {
                $e->contract_total   = $e->product->sum(fn ($p) => (float) $p->total_amount);
                $e->contract_paid    = $e->product->sum(fn ($p) => (float) $p->paid_amount);
                $e->contract_balance = round($e->contract_total - $e->contract_paid, 2);
                return $e;
            })
            ->filter(fn ($e) => $e->contract_balance > 0)
            ->sortByDesc('contract_balance')
            ->values();

        $totalPendingBalance = $contractsWithBalance->sum('contract_balance');

        if (Auth::user()->role_id == 1) {
            return view('admin.dashboard', compact(
                'entry',
                'services',
                'banks',
                'totalAmount',
                'totalBalance',
                'chartData',
                'income',
                'expense',
                'totalIncome',
                'totalExpense',
                'netBalance',
                'bdmCountChartData',
                'serviceCountChartData',
                'bdmPriceChartData',
                'servicePriceChartData',
                'paymentChartData',
                'dateChartData',
                'contractsWithBalance',
                'totalPendingBalance'
            ));
        } elseif (Auth::user()->role_id == 2) {
            return view('user.index');
        } elseif (Auth::user()->role_id == 3) {
            $telecallers = Telecaller::with(['bdm', 'telecallerUser'])
                ->where('created_by', Auth::id());
            return view('telecaller.dashboard', compact('telecallers'));
        }

        return redirect()->route('login');
    }

    public function paymentFilter(Request $request)
    {
        $dashboardData = $this->getDashboardChartAndPaymentData($request);

        $html = view('admin.dashboard-data', [
            'income' => $dashboardData['income'],
            'expense' => $dashboardData['expense'],
            'totalIncome' => $dashboardData['totalIncome'],
            'totalExpense' => $dashboardData['totalExpense'],
            'netBalance' => $dashboardData['netBalance'],
        ])->render();

        return response()->json([
            'html' => $html,
            'bdmCountChartData' => $dashboardData['bdmCountChartData'],
            'serviceCountChartData' => $dashboardData['serviceCountChartData'],
            'bdmPriceChartData' => $dashboardData['bdmPriceChartData'],
            'servicePriceChartData' => $dashboardData['servicePriceChartData'],
            'paymentChartData' => $dashboardData['paymentChartData'],
            'dateChartData' => $dashboardData['dateChartData'],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getFinanceData($request);

        return Excel::download(
            new FinanceExport($data),
            'finance-report.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFinanceData($request);

        $pdf = Pdf::loadView('admin.export.finance-pdf', [
            'records' => $data
        ]);

        return $pdf->download('finance-report.pdf');
    }


    private function getFinanceData($request)
    {
        $payment = $request->payment_mode;
        $from = $request->from_date;
        $to = $request->to_date;

        $records = [];

        /* ===== INCOME ===== */
        $income = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->select(
                'e.payment as payment',
                'e.date as date',
                DB::raw('SUM(p.paid_amount) as amount')
            )
            ->whereNotNull('e.payment')
            ->groupBy('e.payment', 'e.date');

        if ($payment)
            $income->where('e.payment', $payment);
        if ($from && $to) {
            $income->whereBetween('e.date', [$from, $to]);
        } elseif ($from) {
            $income->whereDate('e.date', '>=', $from);
        } elseif ($to) {
            $income->whereDate('e.date', '<=', $to);
        }

        foreach ($income->get() as $row) {
            $records[] = [
                'Income',
                $row->payment,
                $row->date,
                $row->amount
            ];
        }

        /* ===== EXPENSE ===== */
        $expense = DB::table('expenses')
            ->select(
                'payment_mode as payment',
                'date',
                'amount'
            );

        if ($payment)
            $expense->where('payment_mode', $payment);
        if ($from && $to) {
            $expense->whereBetween('date', [$from, $to]);
        } elseif ($from) {
            $expense->whereDate('date', '>=', $from);
        } elseif ($to) {
            $expense->whereDate('date', '<=', $to);
        }

        foreach ($expense->get() as $row) {
            $records[] = [
                'Expense',
                $row->payment,
                $row->date,
                $row->amount
            ];
        }

        return $records;
    }

    private function getDashboardChartAndPaymentData($request)
    {
        $payment = $request->payment_mode;
        $from = $request->from_date;
        $to = $request->to_date;

        /*
        |--------------------------------------------------------------------------
        | Income Table Data
        |--------------------------------------------------------------------------
        */
        $incomeQuery = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->select(
                'e.payment',
                'e.date',
                DB::raw('SUM(p.paid_amount) as amount'),
                DB::raw("'Income' as type")
            )
            ->whereNotNull('e.payment')
            ->groupBy('e.payment', 'e.date');

        if (!empty($payment)) {
            $incomeQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $incomeQuery->whereBetween('e.date', [$from, $to]);
        } elseif (!empty($from)) {
            $incomeQuery->whereDate('e.date', '>=', $from);
        } elseif (!empty($to)) {
            $incomeQuery->whereDate('e.date', '<=', $to);
        }

        $income = $incomeQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Expense Table Data
        |--------------------------------------------------------------------------
        */
        $expenseQuery = DB::table('expenses')
            ->select(
                'payment_mode as payment',
                'date',
                'amount',
                DB::raw("'Expense' as type")
            );

        if (!empty($payment)) {
            $expenseQuery->where('payment_mode', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $expenseQuery->whereBetween('date', [$from, $to]);
        } elseif (!empty($from)) {
            $expenseQuery->whereDate('date', '>=', $from);
        } elseif (!empty($to)) {
            $expenseQuery->whereDate('date', '<=', $to);
        }

        $expense = $expenseQuery->get();

        $totalIncome = $income->sum('amount');
        $totalExpense = $expense->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | BDM Wise Count
        |--------------------------------------------------------------------------
        */
        $bdmCountQuery = DB::table('entries as e')
            ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
            ->select(
                DB::raw("COALESCE(u.name, 'Unknown') as name"),
                DB::raw('COUNT(e.id) as count')
            )
            ->groupBy('u.name');

        if (!empty($payment)) {
            $bdmCountQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $bdmCountQuery->whereBetween('e.date', [$from, $to]);
        } elseif (!empty($from)) {
            $bdmCountQuery->whereDate('e.date', '>=', $from);
        } elseif (!empty($to)) {
            $bdmCountQuery->whereDate('e.date', '<=', $to);
        }

        $bdmCountChartData = $bdmCountQuery->get()->map(function ($row) {
            return [
                'name' => $row->name,
                'count' => (int) $row->count,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Service Wise Count
        |--------------------------------------------------------------------------
        */
        $serviceCountQuery = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->select(
                DB::raw("COALESCE(p.product_name, 'Unknown') as name"),
                DB::raw('COUNT(p.id) as count')
            )
            ->groupBy('p.product_name');

        if (!empty($payment)) {
            $serviceCountQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $serviceCountQuery->whereBetween('e.date', [$from, $to]);
        } elseif (!empty($from)) {
            $serviceCountQuery->whereDate('e.date', '>=', $from);
        } elseif (!empty($to)) {
            $serviceCountQuery->whereDate('e.date', '<=', $to);
        }

        $serviceCountChartData = $serviceCountQuery->get()->map(function ($row) {
            return [
                'name' => $row->name,
                'count' => (int) $row->count,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | BDM Wise Collection
        |--------------------------------------------------------------------------
        */
        $bdmPriceQuery = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
            ->select(
                DB::raw("COALESCE(u.name, 'Unknown') as name"),
                DB::raw('SUM(p.paid_amount) as amount')
            )
            ->whereNotNull('e.payment')
            ->groupBy('u.name');

        if (!empty($payment)) {
            $bdmPriceQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $bdmPriceQuery->whereBetween('e.date', [$from, $to]);
        } elseif (!empty($from)) {
            $bdmPriceQuery->whereDate('e.date', '>=', $from);
        } elseif (!empty($to)) {
            $bdmPriceQuery->whereDate('e.date', '<=', $to);
        }

        $bdmPriceChartData = $bdmPriceQuery->get()->map(function ($row) {
            return [
                'name' => $row->name,
                'amount' => (float) $row->amount,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Service Wise Collection
        |--------------------------------------------------------------------------
        */
        $servicePriceQuery = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->select(
                DB::raw("COALESCE(p.product_name, 'Unknown') as name"),
                DB::raw('SUM(p.paid_amount) as amount')
            )
            ->whereNotNull('e.payment')
            ->groupBy('p.product_name');

        if (!empty($payment)) {
            $servicePriceQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $servicePriceQuery->whereBetween('e.date', [$from, $to]);
        } elseif (!empty($from)) {
            $servicePriceQuery->whereDate('e.date', '>=', $from);
        } elseif (!empty($to)) {
            $servicePriceQuery->whereDate('e.date', '<=', $to);
        }

        $servicePriceChartData = $servicePriceQuery->get()->map(function ($row) {
            return [
                'name' => $row->name,
                'amount' => (float) $row->amount,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Payment Mode Wise Collection
        |--------------------------------------------------------------------------
        */
        $paymentChartQuery = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->select(
                DB::raw("COALESCE(e.payment, 'Unknown') as name"),
                DB::raw('SUM(p.paid_amount) as amount')
            )
            ->whereNotNull('e.payment')
            ->groupBy('e.payment');

        if (!empty($payment)) {
            $paymentChartQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $paymentChartQuery->whereBetween('e.date', [$from, $to]);
        } elseif (!empty($from)) {
            $paymentChartQuery->whereDate('e.date', '>=', $from);
        } elseif (!empty($to)) {
            $paymentChartQuery->whereDate('e.date', '<=', $to);
        }

        $paymentChartData = $paymentChartQuery->get()->map(function ($row) {
            return [
                'name' => $row->name,
                'amount' => (float) $row->amount,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | Date Wise Collection
        |--------------------------------------------------------------------------
        */
        $dateChartQuery = DB::table('entries as e')
            ->join('products as p', 'p.entry_id', '=', 'e.id')
            ->select(
                'e.date',
                DB::raw('SUM(p.paid_amount) as amount')
            )
            ->whereNotNull('e.payment')
            ->groupBy('e.date')
            ->orderBy('e.date', 'ASC');

        if (!empty($payment)) {
            $dateChartQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $dateChartQuery->whereBetween('e.date', [$from, $to]);
        } elseif (!empty($from)) {
            $dateChartQuery->whereDate('e.date', '>=', $from);
        } elseif (!empty($to)) {
            $dateChartQuery->whereDate('e.date', '<=', $to);
        }

        $dateChartData = $dateChartQuery->get()->map(function ($row) {
            return [
                'date' => $row->date,
                'amount' => (float) $row->amount,
            ];
        })->values();

        return [
            'income' => $income,
            'expense' => $expense,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance' => $netBalance,
            'bdmCountChartData' => $bdmCountChartData,
            'serviceCountChartData' => $serviceCountChartData,
            'bdmPriceChartData' => $bdmPriceChartData,
            'servicePriceChartData' => $servicePriceChartData,
            'paymentChartData' => $paymentChartData,
            'dateChartData' => $dateChartData,
        ];
    }


}
