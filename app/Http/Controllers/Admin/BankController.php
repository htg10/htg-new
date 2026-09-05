<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::latest()->paginate(15);
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
            'attachment' => 'nullable|file|mimes:jpg,png,pdf'
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
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = $bank->attachment;

        if ($request->hasFile('attachment')) {

            // ❌ remove old image
            if ($bank->attachment && file_exists(public_path($bank->attachment))) {
                unlink(public_path($bank->attachment));
            }

            // ✅ YOUR REQUIRED STORAGE STYLE
            $fileImage = $request->file('attachment');
            $fileImageName = rand() . '.' . $fileImage->getClientOriginalName();
            $fileImage->storeAs('bank/', $fileImageName);
            $imagePath = 'storage/bank/' . $fileImageName;
        }

        $bank->update([
            'bank' => $request->bank,
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
