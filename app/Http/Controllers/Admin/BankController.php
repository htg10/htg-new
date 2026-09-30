<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::orderBy('bank')->get();

        $bankNames = $banks->pluck('bank')->toArray();

        $cutoff = '2025-09-01';

        // Income: payment_histories where payment_bank matches
        $phIncome = DB::table('payment_histories')
            ->select('payment_bank', DB::raw('SUM(amount) as total'))
            ->whereIn('payment_bank', $bankNames)
            ->where('payment_date', '>=', $cutoff)
            ->groupBy('payment_bank')
            ->pluck('total', 'payment_bank');

        // Income: entries.receivedamount where entries.payment matches
        // (for entries that pre-date payment_histories or have no payment_history)
        $entryIncome = DB::table('entries')
            ->select('payment', DB::raw('SUM(CAST(receivedamount AS DECIMAL(14,2))) as total'))
            ->whereIn('payment', $bankNames)
            ->where('date', '>=', $cutoff)
            ->where(function ($q) {
                $q->where('receivedamount', '>', 0)
                  ->whereNotNull('receivedamount');
            })
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('payment_histories')
                    ->whereColumn('payment_histories.entry_id', 'entries.id');
            })
            ->groupBy('payment')
            ->pluck('total', 'payment');

        // Expense: expenses table
        $expenseOut = DB::table('expenses')
            ->select('payment_mode', DB::raw('SUM(amount) as total'))
            ->whereIn('payment_mode', $bankNames)
            ->where('date', '>=', $cutoff)
            ->groupBy('payment_mode')
            ->pluck('total', 'payment_mode');

        // Building payments (rent collections = income)
        $buildingIn = DB::table('buildings')
            ->select('payment_mode', DB::raw('SUM(CAST(amount AS DECIMAL(14,2))) as total'))
            ->whereIn('payment_mode', $bankNames)
            ->where('created_at', '>=', $cutoff)
            ->groupBy('payment_mode')
            ->pluck('total', 'payment_mode');

        foreach ($banks as $bank) {
            $name = $bank->bank;
            $income = ($phIncome[$name] ?? 0) + ($entryIncome[$name] ?? 0) + ($buildingIn[$name] ?? 0);
            $expense = $expenseOut[$name] ?? 0;
            $bank->total_income  = $income;
            $bank->total_expense = $expense;
            $bank->current_balance = $bank->opening_balance + $income - $expense;
        }

        return view('admin.bank.index', compact('banks'));
    }

    public function create()
    {
        return view('admin.bank.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank' => 'required',
            'opening_balance' => 'nullable|numeric',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('attachment')) {
            $fileImage = $request->file('attachment');
            $fileImageName = rand() . '.' . $fileImage->getClientOriginalName();
            $fileImage->storeAs('bank/', $fileImageName);
            $imagePath = 'storage/bank/' . $fileImageName;
        }

        Bank::create([
            'bank' => $request->bank,
            'opening_balance' => $request->opening_balance ?? 0,
            'attachment' => $imagePath,
        ]);

        return redirect()->route('bank.index')
            ->with('success', 'Bank added successfully');
    }

    public function edit(Bank $bank)
    {
        return view('admin.bank.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'bank' => 'required',
            'opening_balance' => 'nullable|numeric',
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = $bank->attachment;

        if ($request->hasFile('attachment')) {
            if ($bank->attachment && file_exists(public_path($bank->attachment))) {
                unlink(public_path($bank->attachment));
            }

            $fileImage = $request->file('attachment');
            $fileImageName = rand() . '.' . $fileImage->getClientOriginalName();
            $fileImage->storeAs('bank/', $fileImageName);
            $imagePath = 'storage/bank/' . $fileImageName;
        }

        $bank->update([
            'bank' => $request->bank,
            'opening_balance' => $request->opening_balance ?? 0,
            'attachment' => $imagePath,
        ]);

        return redirect()->route('bank.index')
            ->with('success', 'Bank updated successfully');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return back()->with('success', 'Bank deleted');
    }
}
