<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purpose;
use Illuminate\Http\Request;

class PurposeController extends Controller
{
    public function index()
    {
        $purposes = Purpose::latest()->paginate(15);
        return view('admin.purpose.index', compact('purposes'));
    }

    public function create()
    {
        return view('admin.purpose.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Purpose::create([
            'name' => $request->name,
        ]);

        return redirect()->route('purpose.index')
            ->with('success', 'Purpose added successfully');
    }

    public function edit(Purpose $purpose)
    {
        return view('admin.purpose.edit', compact('purpose'));
    }

    public function update(Request $request, Purpose $purpose)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $purpose->update([
            'name' => $request->name,
        ]);

        return redirect()->route('purpose.index')
            ->with('success', 'Purpose updated successfully');
    }

    public function destroy(Purpose $purpose)
    {
        $purpose->delete();
        return back()->with('success', 'Purpose deleted');
    }
}
