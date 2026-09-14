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
                'dateChartData'
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
        $company = $request->company;

        $records = [];

        /* ===== INCOME ===== */
        $income = DB::table('payment_histories as ph')
            ->join('entries as e', 'e.id', '=', 'ph.entry_id')
            ->select(
                'e.company',
                'ph.product_name',
                DB::raw('COALESCE(ph.payment_bank, e.payment) as payment'),
                'e.date',
                'ph.payment_date',
                'ph.amount'
            )
            ->whereNotNull('e.payment');

        if ($company)
            $income->where('e.company', 'like', '%' . $company . '%');
        if ($payment)
            $income->where('e.payment', $payment);
        if ($from && $to) {
            $income->whereBetween('ph.payment_date', [$from, $to]);
        } elseif ($from) {
            $income->whereDate('ph.payment_date', '>=', $from);
        } elseif ($to) {
            $income->whereDate('ph.payment_date', '<=', $to);
        }

        foreach ($income->orderBy('ph.payment_date', 'desc')->get() as $row) {
            $records[] = [
                'type' => 'Income',
                'company' => $row->company,
                'service' => $row->product_name,
                'payment' => $row->payment,
                'date' => $row->date,
                'payment_date' => $row->payment_date,
                'amount' => $row->amount,
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
                'type' => 'Expense',
                'company' => '—',
                'service' => '—',
                'payment' => $row->payment,
                'date' => $row->date,
                'payment_date' => '—',
                'amount' => $row->amount,
            ];
        }

        return $records;
    }

    private function getDashboardChartAndPaymentData($request)
    {
        $payment = $request->payment_mode;
        $from = $request->from_date;
        $to = $request->to_date;
        $company = $request->company;

        /*
        |--------------------------------------------------------------------------
        | Income Table Data
        |--------------------------------------------------------------------------
        */
        $incomeQuery = DB::table('payment_histories as ph')
            ->join('entries as e', 'e.id', '=', 'ph.entry_id')
            ->select(
                'e.company',
                'e.payment',
                'e.date',
                'ph.product_name',
                'ph.payment_date',
                'ph.payment_bank',
                'ph.amount',
                DB::raw("'Income' as type")
            )
            ->whereNotNull('e.payment');

        if (!empty($company)) {
            $incomeQuery->where('e.company', 'like', '%' . $company . '%');
        }

        if (!empty($payment)) {
            $incomeQuery->where('e.payment', $payment);
        }

        if (!empty($from) && !empty($to)) {
            $incomeQuery->whereBetween('ph.payment_date', [$from, $to]);
        } elseif (!empty($from)) {
            $incomeQuery->whereDate('ph.payment_date', '>=', $from);
        } elseif (!empty($to)) {
            $incomeQuery->whereDate('ph.payment_date', '<=', $to);
        }

        $income = $incomeQuery->orderBy('ph.payment_date', 'desc')->get();

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

        if (!empty($company)) {
            $bdmCountQuery->where('e.company', 'like', '%' . $company . '%');
        }
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

        if (!empty($company)) {
            $serviceCountQuery->where('e.company', 'like', '%' . $company . '%');
        }
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

        if (!empty($company)) {
            $bdmPriceQuery->where('e.company', 'like', '%' . $company . '%');
        }
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

        if (!empty($company)) {
            $servicePriceQuery->where('e.company', 'like', '%' . $company . '%');
        }
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

        if (!empty($company)) {
            $paymentChartQuery->where('e.company', 'like', '%' . $company . '%');
        }
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

        if (!empty($company)) {
            $dateChartQuery->where('e.company', 'like', '%' . $company . '%');
        }
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
