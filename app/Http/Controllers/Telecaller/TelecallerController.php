<?php

namespace App\Http\Controllers\Telecaller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Telecaller;
use Illuminate\Support\Facades\Auth;

class TelecallerController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();

        $telecallers = Telecaller::with(['bdm', 'telecallerUser']);

        /*
        |--------------------------------------------------------------------------
        | Role Wise Data Access
        |--------------------------------------------------------------------------
        | role_id = 1 Admin       => all leads
        | role_id = 2 BDM         => leads assigned to this BDM
        | role_id = 3 Telecaller  => leads created by this Telecaller
        |--------------------------------------------------------------------------
        */
        if ($authUser->role_id == 3) {
            $telecallers->where('created_by', $authUser->id);
        } elseif ($authUser->role_id == 2) {
            $telecallers->where('user_id', $authUser->id);
        } elseif ($authUser->role_id == 1) {
            // Admin can see all data
        } else {
            abort(403, 'Unauthorized access');
        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        if ($request->filled('business')) {
            $telecallers->where('business', 'like', '%' . $request->business . '%');
        }

        if ($request->filled('bdm_id')) {
            $telecallers->where('user_id', $request->bdm_id);
        }

        if ($request->filled('interest')) {
            $telecallers->where('interest', $request->interest);
        }

        if ($request->filled('deal_status')) {
            $telecallers->where('deal_status', $request->deal_status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $telecallers->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        } elseif ($request->filled('from_date')) {
            $telecallers->whereDate('created_at', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $telecallers->whereDate('created_at', '<=', $request->to_date);
        }

        $telecallers = $telecallers->latest()->get();

        $users = User::where('role_id', 2)->get();

        return view('telecaller.index', compact('telecallers', 'users'));
    }

    public function create()
    {
        $users = User::where('role_id', 2)->get();
        return view('telecaller.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'meeting_datetime' => 'required|date',
            'interest' => 'required|string',
            'remark' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*' => 'nullable|string|max:255',

            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_accuracy' => 'nullable|string|max:255',
            'location_url' => 'nullable|string',
        ]);

        $telecaller = new Telecaller([
            'business' => $request->input('business'),
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'mobile' => $request->input('mobile'),
            'user_id' => $request->input('user_id'),
            'created_by' => Auth::id(),
            'meeting_datetime' => $request->input('meeting_datetime'),
            'interest' => $request->input('interest'),
            'remark' => $validated['remark'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'location_accuracy' => $validated['location_accuracy'] ?? null,
            'location_url' => $validated['location_url'] ?? null,
            'status' => 'NEW'
        ]);

        $telecaller->save();

        return redirect('/telecaller/index')->with('success', 'Add successfully.');
    }


    public function edit($id)
    {
        $authUser = Auth::user();

        $telecaller = Telecaller::query();

        if ($authUser->role_id == 3) {
            $telecaller->where('created_by', $authUser->id);
        } elseif ($authUser->role_id == 2) {
            $telecaller->where('user_id', $authUser->id);
        } elseif ($authUser->role_id == 1) {
            // Admin can edit all
        } else {
            abort(403, 'Unauthorized access');
        }

        $telecallers = $telecaller->findOrFail($id);

        $users = User::where('role_id', 2)->get();

        return view('telecaller.edit', compact('users', 'telecallers'));
    }

    public function update(Request $request, $id)
    {
        $authUser = Auth::user();

        $validated = $request->validate([
            'business' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'meeting_datetime' => 'required|date',
            'interest' => 'required|string',
            'remark' => 'nullable|string|max:255',
        ]);

        $telecallerQuery = Telecaller::query();

        if ($authUser->role_id == 3) {
            $telecallerQuery->where('created_by', $authUser->id);
        } elseif ($authUser->role_id == 2) {
            $telecallerQuery->where('user_id', $authUser->id);
        } elseif ($authUser->role_id == 1) {
            // Admin can update all
        } else {
            abort(403, 'Unauthorized access');
        }

        $telecaller = $telecallerQuery->findOrFail($id);

        $telecaller->update([
            'business' => $validated['business'],
            'name' => $validated['name'],
            'address' => $validated['address'],
            'mobile' => $validated['mobile'],
            'user_id' => $validated['user_id'],
            'meeting_datetime' => $validated['meeting_datetime'],
            'interest' => $validated['interest'],
            'remark' => $validated['remark'] ?? null,
        ]);

        return redirect()
            ->route('telecaller.index')
            ->with('success', 'Lead updated successfully.');
    }

    function delete($id)
    {
        $telecaller = Telecaller::find($id);
        $telecaller->delete();
        return redirect('/telecaller/index')->with('success', 'Delete successfully.');
    }


}
