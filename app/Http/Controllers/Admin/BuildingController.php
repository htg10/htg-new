<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $buildings = Building::query();

        //filter by date
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $buildings->whereBetween('date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        $buildings = $buildings->latest()->get();

        // Calculate total amount
        $totalAmount = $buildings->sum('amount');

        return view('admin.building.index', compact('buildings', 'totalAmount'));
    }

    public function create()
    {
        $banks = Bank::all();
        return view('admin.building.create', compact('banks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:10',
            'building' => 'nullable|string|max:255',
            'amount' => 'nullable|string',
            'payment_mode' => 'required|string',
            'date' => 'nullable|date',
        ]);

        $building = new Building([
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'building' => $request->input('building'),
            'date' => $request->input('date'),
            'amount' => $request->input('amount'),
            'payment_mode' => $request->payment_mode,
        ]);

        $building->save();

        return redirect('/admin/rent/index')->with('success', 'Add successfully.');
    }


    public function edit($id)
    {
        $buildings = Building::findOrFail($id);
        $banks = Bank::all();
        return view('admin.building.edit', compact('buildings', 'banks'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:10',
            'building' => 'nullable|string|max:255',
            'amount' => 'nullable|string',
            'payment_mode' => 'required|string',
            'date' => 'nullable|date',
        ]);

        $building = Building::findOrFail($id);
        // $data = $request->all();

        $building->update([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'building' => $validated['building'],
            'amount' => $validated['amount'],
            'payment_mode' => $request->payment_mode,
            'date' => $validated['date'],
        ]);
        return redirect('/admin/rent/index')->with('success', 'Update successfully.');
    }

    function delete($id)
    {
        $building = Building::find($id);
        $building->delete();
        return redirect('/admin/rent/index')->with('success', 'Delete successfully.');
    }
}
