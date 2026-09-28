<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Building;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $monthEnd = Carbon::parse($month . '-01')->endOfMonth();

        $properties = Property::with(['tenants' => function ($q) {
            $q->orderBy('name');
        }])->withCount(['tenants as active_tenants_count' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        // Get payments for the selected month grouped by tenant
        $monthlyPayments = Building::whereBetween('date', [$monthStart, $monthEnd])
            ->whereNotNull('tenant_id')
            ->selectRaw('tenant_id, SUM(amount) as paid')
            ->groupBy('tenant_id')
            ->pluck('paid', 'tenant_id');

        // Calculate stats per property
        foreach ($properties as $property) {
            $monthlyRent = 0;
            $collected = 0;

            foreach ($property->tenants as $tenant) {
                if ($tenant->is_active) {
                    $monthlyRent += $tenant->rent_amount;
                }
                $tenantPaid = $monthlyPayments->get($tenant->id, 0);
                $tenant->month_paid = $tenantPaid;
                $collected += $tenantPaid;
            }

            $property->monthly_rent = $monthlyRent;
            $property->collected = $collected;
            $property->pending = $monthlyRent - $collected;
        }

        $totalProperties = $properties->count();
        $totalTenants = $properties->sum('active_tenants_count');
        $totalMonthlyRent = $properties->sum('monthly_rent');
        $totalCollected = $properties->sum('collected');
        $totalPending = $totalMonthlyRent - $totalCollected;

        return view('admin.building.index', compact(
            'properties', 'month', 'totalProperties', 'totalTenants',
            'totalMonthlyRent', 'totalCollected', 'totalPending'
        ));
    }

    // --- Property CRUD ---

    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        Property::create($validated);

        return redirect()->route('admin.rent.index')->with('success', 'Property added successfully.');
    }

    public function updateProperty(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        Property::findOrFail($id)->update($validated);

        return redirect()->route('admin.rent.index')->with('success', 'Property updated successfully.');
    }

    public function deleteProperty($id)
    {
        Property::findOrFail($id)->delete();

        return redirect()->route('admin.rent.index')->with('success', 'Property deleted successfully.');
    }

    // --- Tenant CRUD ---

    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:15',
            'unit' => 'nullable|string|max:100',
            'rent_amount' => 'required|numeric|min:0',
        ]);

        Tenant::create($validated);

        return redirect()->route('admin.rent.index')->with('success', 'Tenant added successfully.');
    }

    public function updateTenant(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:15',
            'unit' => 'nullable|string|max:100',
            'rent_amount' => 'required|numeric|min:0',
        ]);

        Tenant::findOrFail($id)->update($validated);

        return redirect()->route('admin.rent.index')->with('success', 'Tenant updated successfully.');
    }

    public function toggleTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['is_active' => !$tenant->is_active]);

        return redirect()->route('admin.rent.index')->with('success', 'Tenant status updated.');
    }

    public function deleteTenant($id)
    {
        Tenant::findOrFail($id)->delete();

        return redirect()->route('admin.rent.index')->with('success', 'Tenant removed successfully.');
    }

    // --- Rent Payment CRUD ---

    public function create()
    {
        $banks = Bank::all();
        $properties = Property::active()->with('tenants')->get();

        return view('admin.building.create', compact('banks', 'properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|string',
            'date' => 'required|date',
        ]);

        $tenant = Tenant::with('property')->findOrFail($validated['tenant_id']);

        Building::create([
            'property_id' => $tenant->property_id,
            'tenant_id' => $tenant->id,
            'name' => $tenant->name,
            'mobile' => $tenant->mobile,
            'building' => $tenant->property->name,
            'amount' => $validated['amount'],
            'payment_mode' => $validated['payment_mode'],
            'date' => $validated['date'],
        ]);

        return redirect()->route('admin.rent.index')->with('success', 'Payment recorded successfully.');
    }

    public function edit($id)
    {
        $payment = Building::findOrFail($id);
        $banks = Bank::all();
        $properties = Property::active()->with('tenants')->get();

        return view('admin.building.edit', compact('payment', 'banks', 'properties'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|string',
            'date' => 'required|date',
        ]);

        $tenant = Tenant::with('property')->findOrFail($validated['tenant_id']);
        $payment = Building::findOrFail($id);

        $payment->update([
            'property_id' => $tenant->property_id,
            'tenant_id' => $tenant->id,
            'name' => $tenant->name,
            'mobile' => $tenant->mobile,
            'building' => $tenant->property->name,
            'amount' => $validated['amount'],
            'payment_mode' => $validated['payment_mode'],
            'date' => $validated['date'],
        ]);

        return redirect()->route('admin.rent.index')->with('success', 'Payment updated successfully.');
    }

    public function delete($id)
    {
        Building::findOrFail($id)->delete();

        return redirect()->route('admin.rent.index')->with('success', 'Payment deleted successfully.');
    }
}
