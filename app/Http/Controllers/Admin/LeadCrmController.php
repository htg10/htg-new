<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadNote;
use App\Models\Telecaller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadCrmController extends Controller
{
    public function show($id)
    {
        $lead = Telecaller::with(['user', 'telecallerUser', 'notes.user'])->findOrFail($id);
        $users = User::where('role_id', 2)->get();

        $services = $lead->products;
        if (is_string($services)) {
            $services = json_decode($services, true);
        }
        $services = is_array($services) ? $services : [];

        return view('admin.leads.detail', compact('lead', 'users', 'services'));
    }

    public function storeNote(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'required|in:note,call,meeting,email,whatsapp',
        ]);

        $lead = Telecaller::findOrFail($id);

        $note = LeadNote::create([
            'telecaller_id' => $lead->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'content' => $request->content,
        ]);

        if ($request->ajax()) {
            $note->load('user');
            return response()->json([
                'ok' => true,
                'note' => [
                    'id' => $note->id,
                    'type' => $note->type,
                    'content' => $note->content,
                    'user' => $note->user->name,
                    'created_at' => $note->created_at->diffForHumans(),
                    'created_date' => $note->created_at->format('d M Y, h:i A'),
                ],
            ]);
        }

        return back()->with('success', 'Note added.');
    }

    public function deleteNote($id, $noteId)
    {
        $note = LeadNote::where('telecaller_id', $id)->findOrFail($noteId);
        $note->delete();

        if (request()->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Note deleted.');
    }

    public function updateField(Request $request, $id)
    {
        $lead = Telecaller::findOrFail($id);
        $field = $request->input('field');
        $value = $request->input('value');

        $allowed = ['deal_status', 'interest', 'follow_up_date', 'follow_up_remark', 'meeting_datetime', 'user_id', 'remark'];

        if (!in_array($field, $allowed)) {
            return response()->json(['ok' => false, 'message' => 'Invalid field'], 422);
        }

        if ($field === 'deal_status') {
            $oldStatus = $lead->deal_status;
            $lead->update([$field => $value]);

            if ($oldStatus !== $value) {
                LeadNote::create([
                    'telecaller_id' => $lead->id,
                    'user_id' => Auth::id(),
                    'type' => 'status_change',
                    'content' => 'Status changed from "' . ucwords($oldStatus ?? 'none') . '" to "' . ucwords($value) . '"',
                    'meta' => ['from' => $oldStatus, 'to' => $value],
                ]);
            }

            if ($value !== 'follow up') {
                $lead->update(['follow_up_date' => null]);
            }
        } else {
            $lead->update([$field => $value ?: null]);
        }

        return response()->json(['ok' => true]);
    }

    public function kanban(Request $request)
    {
        $query = Telecaller::with('user')->where('status', 'NEW');

        if ($request->filled('bdm_id')) {
            $query->where('user_id', $request->bdm_id);
        }

        $leads = $query->latest()->get();
        $users = User::where('role_id', 2)->get();

        $columns = [
            'pending' => ['label' => 'Pending', 'color' => 'warn', 'icon' => 'bx-time-five'],
            'follow up' => ['label' => 'Follow Up', 'color' => 'info', 'icon' => 'bx-phone-call'],
            'deal closed' => ['label' => 'Deal Closed', 'color' => 'ok', 'icon' => 'bx-check-circle'],
            'not interested' => ['label' => 'Not Interested', 'color' => 'bad', 'icon' => 'bx-x-circle'],
        ];

        $grouped = [];
        foreach ($columns as $status => $meta) {
            $grouped[$status] = $leads->filter(fn($l) => strtolower(trim($l->deal_status ?? '')) === $status)->values();
        }

        return view('admin.leads.kanban', compact('grouped', 'columns', 'users'));
    }

    public function kanbanUpdate(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:telecallers,id',
            'status' => 'required|in:pending,follow up,deal closed,not interested',
        ]);

        $lead = Telecaller::findOrFail($request->lead_id);
        $oldStatus = $lead->deal_status;

        $lead->update(['deal_status' => $request->status]);

        if ($oldStatus !== $request->status) {
            LeadNote::create([
                'telecaller_id' => $lead->id,
                'user_id' => Auth::id(),
                'type' => 'status_change',
                'content' => 'Status changed from "' . ucwords($oldStatus ?? 'none') . '" to "' . ucwords($request->status) . '"',
                'meta' => ['from' => $oldStatus, 'to' => $request->status],
            ]);

            if ($request->status !== 'follow up') {
                $lead->update(['follow_up_date' => null]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function analytics()
    {
        $leads = Telecaller::with('user')->get();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $total = $leads->count();
        $byStatus = $leads->countBy(fn($l) => strtolower(trim($l->deal_status ?? '')));

        $closed = $byStatus['deal closed'] ?? 0;
        $pending = $byStatus['pending'] ?? 0;
        $followUp = $byStatus['follow up'] ?? 0;
        $lost = $byStatus['not interested'] ?? 0;
        $conversionRate = $total > 0 ? round(($closed / $total) * 100, 1) : 0;

        $thisMonthLeads = $leads->filter(fn($l) => $l->created_at >= $thisMonth)->count();
        $lastMonthLeads = $leads->filter(fn($l) => $l->created_at >= $lastMonth && $l->created_at <= $lastMonthEnd)->count();

        $overdue = $leads->filter(
            fn($l) => strtolower(trim($l->deal_status ?? '')) === 'follow up'
                && !empty($l->follow_up_date)
                && Carbon::parse($l->follow_up_date)->lt($today)
        )->count();

        $meetingsToday = $leads->filter(fn($l) => $l->meeting_datetime && $l->meeting_datetime->isSameDay($today))->count();
        $meetingsWeek = $leads->filter(fn($l) => $l->meeting_datetime && $l->meeting_datetime->between($today, $today->copy()->addDays(7)))->count();

        $byBdm = $leads->groupBy(fn($l) => $l->user->name ?? 'Unassigned')->map(function ($group) {
            return [
                'total' => $group->count(),
                'closed' => $group->filter(fn($l) => strtolower(trim($l->deal_status ?? '')) === 'deal closed')->count(),
                'pending' => $group->filter(fn($l) => strtolower(trim($l->deal_status ?? '')) === 'pending')->count(),
                'follow_up' => $group->filter(fn($l) => strtolower(trim($l->deal_status ?? '')) === 'follow up')->count(),
            ];
        })->sortByDesc('total');

        $byInterest = $leads->countBy(fn($l) => $l->interest ?: 'Unknown');

        $recentNotes = LeadNote::with(['user', 'lead'])->latest()->limit(10)->get();

        return view('admin.leads.analytics', compact(
            'total', 'closed', 'pending', 'followUp', 'lost', 'conversionRate',
            'thisMonthLeads', 'lastMonthLeads', 'overdue', 'meetingsToday', 'meetingsWeek',
            'byBdm', 'byInterest', 'recentNotes'
        ));
    }
}
