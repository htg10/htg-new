<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::ordered()->get();
        return view('admin.service.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:services,name']);

        $maxOrder = Service::max('sort_order') ?? -1;

        Service::create([
            'name' => $request->name,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Service added successfully');
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
        ]);

        $service->update(['name' => $request->name]);

        return back()->with('success', 'Service updated successfully');
    }

    public function toggle(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        return back()->with('success', $service->name . ($service->is_active ? ' activated' : ' deactivated'));
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $i => $id) {
            Service::where('id', $id)->update(['sort_order' => $i]);
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted');
    }
}
