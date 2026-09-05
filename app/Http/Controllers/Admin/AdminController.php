<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Telecaller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.index');
    }

    public function listUsers(Request $request)
    {

        $users = User::where('role_id', 2)->orderBy('id', 'desc')->get();
        return view('admin.user.index', compact('users'));
    }

    public function delete($id)
    {
        $review = User::find($id);
        $review->delete();
    }


    // public function assignedLeads()
    // {
    //     $leads = Telecaller::where('status', 'NEW')
    //         ->latest()
    //         ->paginate(10);

    //     return view('admin.leads.index', compact('leads'));
    // }

    public function assignedLeads(Request $request)
    {
        $leads = Telecaller::where('status', 'NEW');

        // Business filter
        if ($request->filled('business')) {
            $leads->where('business', 'like', '%' . $request->business . '%');
        }

        // Customer Name filter
        if ($request->filled('name')) {
            $leads->where('name', 'like', '%' . $request->name . '%');
        }

        // Interest filter
        if ($request->filled('interest')) {
            $leads->where('interest', $request->interest);
        }

        // Deal Status filter
        if ($request->filled('deal_status')) {
            $leads->where('deal_status', $request->deal_status);
        }

        // Date range filter (meeting date)
        if ($request->filled('from_date')) {
            $leads->whereDate('meeting_datetime', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $leads->whereDate('meeting_datetime', '<=', $request->to_date);
        }

        $leads = $leads->latest()->get();

        return view('admin.leads.index', compact('leads'));
    }


    public function createFromLead($id)
    {
        $lead = Telecaller::findOrFail($id);
        $users = User::where('role_id', 2)->get();
        $banks = Bank::all();

        return view('admin.addnew-from-lead', compact('lead', 'users', 'banks'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'deal_status' => 'required|in:pending,follow up,deal closed,not interested',
            'follow_up_date' => 'nullable|date',
            'follow_up_remark' => 'nullable',
        ]);

        $data = [
            'deal_status' => $request->deal_status,
            'follow_up_remark' => $request->follow_up_remark,
        ];

        if ($request->deal_status === 'follow up') {
            $data['follow_up_date'] = $request->follow_up_date;
        } else {
            $data['follow_up_date'] = null;
        }

        Telecaller::where('id', $id)
            ->update($data);

        return back()->with('success', 'Status updated successfully');
    }


    // fore leads Create, edit and delete
    public function index(Request $request)
    {
        $telecallers = Telecaller::query();

        if ($request->filled('business')) {
            $telecallers->where('business', 'like', '%' . $request->business . '%');
        }

        if ($request->filled('bdm_id')) {
            $telecallers->where('user_id', $request->bdm_id);
        }

        if ($request->filled('created_by')) {
            $telecallers->where('created_by', $request->created_by);
        }

        // Filter by interest
        if ($request->filled('interest')) {
            $telecallers->where('interest', 'like', '%' . $request->interest . '%');
        }

        // Filter by deal status
        if ($request->filled('deal_status')) {
            $telecallers->where('deal_status', $request->deal_status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $telecallers->whereBetween('created_at', [
                \Carbon\Carbon::parse($request->from_date)->startOfDay(),
                \Carbon\Carbon::parse($request->to_date)->endOfDay(),
            ]);
        } elseif ($request->filled('from_date')) {
            $telecallers->whereDate('created_at', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $telecallers->whereDate('created_at', '<=', $request->to_date);
        }

        $telecallers = $telecallers->latest()->get();
        $users = User::all();

        // $telecallers = Telecaller::latest()->paginate(10);
        return view('admin.leads.show.index', compact('telecallers', 'users'));
    }

    public function create()
    {
        $users = User::where('role_id', 2)->get();
        return view('admin.leads.show.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'meeting_datetime' => 'nullable|date',
            'interest' => 'required|string',
            'remark' => 'nullable|string|max:255',

            'products' => 'nullable|array',
            'products.*' => 'nullable|string|max:255',

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_accuracy' => 'nullable|string|max:255',
            'location_url' => 'required|string',
        ]);

        Telecaller::create([
            'business' => $validated['business'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'mobile' => $validated['mobile'],
            'user_id' => $validated['user_id'],
            'created_by' => Auth::id(),
            'meeting_datetime' => $validated['meeting_datetime'] ?? null,
            'interest' => $validated['interest'] ?? null,
            'remark' => $validated['remark'] ?? null,
            'products' => $validated['products'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'location_accuracy' => $validated['location_accuracy'] ?? null,
            'location_url' => $validated['location_url'] ?? null,
            'status' => 'NEW',
        ]);

        return redirect('/admin/lead/index')->with('success', 'Add successfully.');
    }


    public function edit($id)
    {
        $telecallers = Telecaller::findOrFail($id);
        $users = User::where('role_id', 2)->get();
        // dd($telecallers);
        return view('admin.leads.show.edit', compact('users', 'telecallers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'business' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'meeting_datetime' => 'nullable|date',
            'interest' => 'required|string',
            'remark' => 'nullable|string|max:255',

            'products' => 'nullable|array',
            'products.*' => 'nullable|string|max:255',

            // 'latitude' => 'nullable|numeric',
            // 'longitude' => 'nullable|numeric',
            // 'location_accuracy' => 'nullable|string|max:255',
            // 'location_url' => 'nullable|string',
        ]);

        $telecaller = Telecaller::findOrFail($id);

        $telecaller->update([
            'business' => $validated['business'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'mobile' => $validated['mobile'],
            'user_id' => $validated['user_id'],
            'meeting_datetime' => $validated['meeting_datetime'] ?? null,
            'interest' => $validated['interest'],
            'remark' => $validated['remark'] ?? null,
            'products' => $validated['products'] ?? null,
            // 'latitude' => $validated['latitude'] ?? null,
            // 'longitude' => $validated['longitude'] ?? null,
            // 'location_accuracy' => $validated['location_accuracy'] ?? null,
            // 'location_url' => $validated['location_url'] ?? null,
        ]);
        return redirect('/admin/lead/index')->with('success', 'Update successfully.');
    }

    function delete1($id)
    {
        $telecaller = Telecaller::find($id);
        $telecaller->delete();
        return redirect('/admin/lead/index')->with('success', 'Delete successfully.');
    }
}
