@extends('layouts.backend.app')

@section('meta')
    <title>{{ $lead->business ?: $lead->name }} | Lead Detail</title>
@endsection

@section('content')
    @php
        $status = strtolower(trim($lead->deal_status ?? ''));
        $statusClass = match ($status) {
            'pending' => 'htg-pill--warn',
            'follow up' => 'htg-pill--info',
            'deal closed' => 'htg-pill--ok',
            'not interested' => 'htg-pill--bad',
            default => '',
        };

        $interest = strtolower(trim($lead->interest ?? ''));
        $interestClass = match ($interest) {
            'high' => 'htg-interest--high',
            'moderate' => 'htg-interest--mod',
            default => 'htg-interest--low',
        };

        $mobileDigits = preg_replace('/\D+/', '', $lead->mobile ?? '');
        $waNumber = strlen($mobileDigits) === 10 ? '91' . $mobileDigits : $mobileDigits;

        $today = \Carbon\Carbon::today();
        $followOverdue = $status === 'follow up' && !empty($lead->follow_up_date) && \Carbon\Carbon::parse($lead->follow_up_date)->lt($today);
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            <a href="{{ route('admin.lead.index') }}" class="text-muted me-1"><i class="bx bx-arrow-back"></i></a>
                            Lead Detail
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.lead.edit', $lead->id) }}" class="btn btn-soft-info btn-sm">
                                <i class="bx bx-edit me-1"></i>Edit
                            </a>
                            @if ($status === 'deal closed')
                                <a href="{{ route('entry.fromLead', $lead->id) }}" class="btn btn-success btn-sm">
                                    <i class="bx bx-file me-1"></i>Create Contract
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Left: Lead profile --}}
                <div class="col-lg-4">

                    {{-- Profile card --}}
                    <div class="card htg-lead-profile">
                        <div class="card-body text-center">
                            <div class="htg-lead-profile__avatar">
                                <span>{{ strtoupper(substr($lead->name, 0, 1)) }}</span>
                            </div>
                            <h5 class="mt-3 mb-1">{{ $lead->name }}</h5>
                            @if ($lead->business)
                                <p class="text-muted mb-2">{{ $lead->business }}</p>
                            @endif

                            <span class="htg-pill {{ $statusClass }} mb-3" id="leadStatusBadge">
                                {{ $lead->deal_status ? ucwords($lead->deal_status) : 'Unknown' }}
                            </span>

                            {{-- Quick actions --}}
                            <div class="htg-lead-profile__actions">
                                @if ($mobileDigits)
                                    <a href="tel:{{ $mobileDigits }}" class="htg-quick-action" title="Call">
                                        <i class="bx bx-phone-call"></i>
                                        <span>Call</span>
                                    </a>
                                    <a href="{{ route('admin.whatsapp.chat') }}?phone={{ $waNumber }}&name={{ urlencode($lead->name ?? $lead->business ?? '') }}" class="htg-quick-action is-wa" title="WhatsApp">
                                        <i class="bx bxl-whatsapp"></i>
                                        <span>WhatsApp</span>
                                    </a>
                                @endif
                                <a href="{{ route('admin.lead.edit', $lead->id) }}" class="htg-quick-action" title="Edit">
                                    <i class="bx bx-edit"></i>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Details card --}}
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Lead Information</h6>

                            <div class="htg-detail-list">
                                <div class="htg-detail-item">
                                    <span class="htg-detail-item__label"><i class="bx bx-phone"></i> Mobile</span>
                                    <span class="htg-detail-item__value">{{ $lead->mobile ?: '—' }}</span>
                                </div>
                                <div class="htg-detail-item">
                                    <span class="htg-detail-item__label"><i class="bx bx-map"></i> Address</span>
                                    <span class="htg-detail-item__value">{{ $lead->address ?: '—' }}</span>
                                </div>
                                <div class="htg-detail-item">
                                    <span class="htg-detail-item__label"><i class="bx bx-bar-chart-alt-2"></i> Interest</span>
                                    <span class="htg-detail-item__value">
                                        <span class="htg-interest {{ $interestClass }}">
                                            <span class="htg-interest__bars"><i></i><i></i><i></i></span>
                                            {{ $lead->interest ?: 'Unknown' }}
                                        </span>
                                    </span>
                                </div>
                                <div class="htg-detail-item">
                                    <span class="htg-detail-item__label"><i class="bx bx-user"></i> Assigned BDM</span>
                                    <span class="htg-detail-item__value">{{ $lead->user->name ?? '—' }}</span>
                                </div>
                                <div class="htg-detail-item">
                                    <span class="htg-detail-item__label"><i class="bx bx-user-plus"></i> Created by</span>
                                    <span class="htg-detail-item__value">{{ $lead->telecallerUser->name ?? '—' }}</span>
                                </div>
                                <div class="htg-detail-item">
                                    <span class="htg-detail-item__label"><i class="bx bx-calendar"></i> Created</span>
                                    <span class="htg-detail-item__value">{{ $lead->created_at?->format('d M Y, h:i A') ?? '—' }}</span>
                                </div>
                                @if ($lead->meeting_datetime)
                                    <div class="htg-detail-item">
                                        <span class="htg-detail-item__label"><i class="bx bx-calendar-event"></i> Meeting</span>
                                        <span class="htg-detail-item__value">{{ $lead->meeting_datetime->format('d M Y, h:i A') }}</span>
                                    </div>
                                @endif
                                @if ($lead->remark)
                                    <div class="htg-detail-item">
                                        <span class="htg-detail-item__label"><i class="bx bx-comment-detail"></i> Remark</span>
                                        <span class="htg-detail-item__value">{{ $lead->remark }}</span>
                                    </div>
                                @endif
                                @if ($lead->location_url || ($lead->latitude && $lead->longitude))
                                    <div class="htg-detail-item">
                                        <span class="htg-detail-item__label"><i class="bx bx-map-pin"></i> Location</span>
                                        <span class="htg-detail-item__value">
                                            <a href="{{ $lead->location_url ?: 'https://www.google.com/maps?q=' . $lead->latitude . ',' . $lead->longitude }}"
                                               target="_blank" rel="noopener" class="text-primary">
                                                <i class="bx bx-link-external me-1"></i>View on Map
                                            </a>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Services --}}
                    @if (count($services))
                        <div class="card">
                            <div class="card-body">
                                <h6 class="htg-card-title">Interested Services</h6>
                                <div class="htg-chips">
                                    @foreach ($services as $s)
                                        <span class="htg-chip">{{ $s }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Status change --}}
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Update Status</h6>
                            <select id="leadStatusSelect" class="form-select" onchange="updateLeadField('deal_status', this.value)">
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="follow up" {{ $status === 'follow up' ? 'selected' : '' }}>Follow up</option>
                                <option value="deal closed" {{ $status === 'deal closed' ? 'selected' : '' }}>Deal closed</option>
                                <option value="not interested" {{ $status === 'not interested' ? 'selected' : '' }}>Not interested</option>
                            </select>

                            <div id="followUpSection" class="mt-3" style="{{ $status === 'follow up' ? '' : 'display:none' }}">
                                <label class="form-label">Follow-up date</label>
                                <input type="date" id="followUpDate" class="form-control" value="{{ $lead->follow_up_date }}" onchange="updateLeadField('follow_up_date', this.value)">
                                @if ($followOverdue)
                                    <small class="text-danger mt-1 d-block">
                                        <i class="bx bx-error-circle"></i> Overdue since {{ \Carbon\Carbon::parse($lead->follow_up_date)->format('d M') }}
                                    </small>
                                @endif
                                <label class="form-label mt-2">Follow-up remark</label>
                                <input type="text" id="followUpRemark" class="form-control" value="{{ $lead->follow_up_remark }}" placeholder="What was agreed?" onchange="updateLeadField('follow_up_remark', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Activity timeline & notes --}}
                <div class="col-lg-8">

                    {{-- Add note --}}
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Add Activity</h6>
                            <div class="htg-note-form">
                                <div class="htg-note-form__types">
                                    <button type="button" class="htg-note-type active" data-type="note" onclick="selectNoteType(this)">
                                        <i class="bx bx-note"></i> Note
                                    </button>
                                    <button type="button" class="htg-note-type" data-type="call" onclick="selectNoteType(this)">
                                        <i class="bx bx-phone-call"></i> Call
                                    </button>
                                    <button type="button" class="htg-note-type" data-type="meeting" onclick="selectNoteType(this)">
                                        <i class="bx bx-calendar-event"></i> Meeting
                                    </button>
                                    <button type="button" class="htg-note-type" data-type="whatsapp" onclick="selectNoteType(this)">
                                        <i class="bx bxl-whatsapp"></i> WhatsApp
                                    </button>
                                    <button type="button" class="htg-note-type" data-type="email" onclick="selectNoteType(this)">
                                        <i class="bx bx-envelope"></i> Email
                                    </button>
                                </div>
                                <textarea id="noteContent" class="form-control mt-2" rows="3" placeholder="What happened? Add a note, log a call, or record a meeting..."></textarea>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="submitNote()">
                                        <i class="bx bx-plus me-1"></i>Add Activity
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="card">
                        <div class="card-body">
                            <h6 class="htg-card-title">Activity Timeline</h6>

                            <div class="htg-timeline" id="activityTimeline">
                                @forelse ($lead->notes as $note)
                                    @include('admin.leads.partials.timeline-item', ['note' => $note])
                                @empty
                                    <div class="htg-empty" id="timelineEmpty">
                                        <i class="bx bx-history"></i>
                                        <p><strong>No activity yet</strong> Add a note or log a call to start tracking.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
<script>
const LEAD_ID = {{ $lead->id }};
const CSRF = '{{ csrf_token() }}';
let selectedType = 'note';

function selectNoteType(btn) {
    document.querySelectorAll('.htg-note-type').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedType = btn.dataset.type;
}

async function submitNote() {
    const content = document.getElementById('noteContent').value.trim();
    if (!content) return;

    const btn = event.target;
    btn.disabled = true;

    try {
        const res = await fetch(`/admin/lead/${LEAD_ID}/notes`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: JSON.stringify({ content, type: selectedType })
        });
        const data = await res.json();

        if (data.ok) {
            document.getElementById('noteContent').value = '';
            const empty = document.getElementById('timelineEmpty');
            if (empty) empty.remove();

            const timeline = document.getElementById('activityTimeline');
            const item = createTimelineItem(data.note);
            timeline.insertBefore(item, timeline.firstChild);
        }
    } finally {
        btn.disabled = false;
    }
}

function createTimelineItem(note) {
    const icons = {
        note: 'bx-note', call: 'bx-phone-call', meeting: 'bx-calendar-event',
        whatsapp: 'bxl-whatsapp', email: 'bx-envelope', status_change: 'bx-transfer'
    };
    const colors = {
        note: 'var(--htg-accent)', call: 'var(--htg-ok)', meeting: 'var(--htg-warn)',
        whatsapp: '#25D366', email: 'var(--htg-accent)', status_change: '#6c757d'
    };

    const div = document.createElement('div');
    div.className = 'htg-timeline__item';
    div.dataset.noteId = note.id;
    div.innerHTML = `
        <div class="htg-timeline__dot" style="background:${colors[note.type] || 'var(--htg-accent)'}">
            <i class="bx ${icons[note.type] || 'bx-note'}"></i>
        </div>
        <div class="htg-timeline__card">
            <div class="htg-timeline__head">
                <span class="htg-timeline__type">${note.type.replace('_', ' ')}</span>
                <span class="htg-timeline__meta">${note.user} &middot; ${note.created_at}</span>
                <button class="htg-timeline__del" onclick="deleteNote(${note.id})" title="Delete"><i class="bx bx-trash"></i></button>
            </div>
            <div class="htg-timeline__body">${escapeHtml(note.content)}</div>
        </div>
    `;
    return div;
}

async function deleteNote(noteId) {
    if (!confirm('Delete this activity?')) return;
    const res = await fetch(`/admin/lead/${LEAD_ID}/notes/${noteId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.ok) {
        const item = document.querySelector(`[data-note-id="${noteId}"]`);
        if (item) item.remove();
    }
}

async function updateLeadField(field, value) {
    const res = await fetch(`/admin/lead/${LEAD_ID}/update-field`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ field, value })
    });

    if (field === 'deal_status') {
        const section = document.getElementById('followUpSection');
        section.style.display = value === 'follow up' ? '' : 'none';

        const badge = document.getElementById('leadStatusBadge');
        const classes = { pending: 'htg-pill--warn', 'follow up': 'htg-pill--info', 'deal closed': 'htg-pill--ok', 'not interested': 'htg-pill--bad' };
        badge.className = 'htg-pill ' + (classes[value] || '') + ' mb-3';
        badge.textContent = value.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
