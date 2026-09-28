@extends('layouts.backend.app')

@section('meta')
    <title>Assigned Leads | Admin</title>
@endsection

@section('content')
    @php
        /*
         * Counts for the pipeline strip. Derived from the collection the
         * controller already passes — no extra queries, no controller change.
         */
        $byStatus = $leads->countBy(fn($l) => strtolower(trim($l->deal_status ?? '')));
        $countPending = $byStatus['pending'] ?? 0;
        $countFollow = $byStatus['follow up'] ?? 0;
        $countClosed = $byStatus['deal closed'] ?? 0;
        $countLost = $byStatus['not interested'] ?? 0;

        $today = \Carbon\Carbon::today();

        // Follow-ups whose date has already passed — the thing that actually
        // costs money if nobody looks at it.
        $overdue = $leads
            ->filter(
                fn($l) => strtolower(trim($l->deal_status ?? '')) === 'follow up' &&
                    !empty($l->follow_up_date) &&
                    \Carbon\Carbon::parse($l->follow_up_date)->lt($today),
            )
            ->count();

        // Meetings scheduled for today.
        $meetingsToday = $leads
            ->filter(fn($l) => $l->meeting_datetime && $l->meeting_datetime->isSameDay($today))
            ->count();

        $activeStatus = strtolower(trim(request('deal_status', '')));
        $keep = request()->except(['deal_status', 'page']);
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <!-- Breadcrumb -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Assigned Leads
                            <span class="htg-page-sub">Work the pipeline — set a status, book a follow-up, convert
                                what closes</span>
                        </h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">Home</li>
                            <li class="breadcrumb-item active">Leads</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Pipeline. Each cell is a filter — click to narrow the table. --}}
            <div class="htg-pipe">
                <a href="{{ route('leads', $keep) }}"
                    class="htg-pipe__cell is-total {{ $activeStatus === '' ? 'is-active' : '' }}">
                    <span class="htg-pipe__label">All leads</span>
                    <span class="htg-pipe__value">{{ $leads->count() }}</span>
                    <span class="htg-pipe__hint">
                        @if ($meetingsToday)
                            {{ $meetingsToday }} meeting{{ $meetingsToday === 1 ? '' : 's' }} today
                        @else
                            No meetings today
                        @endif
                    </span>
                </a>

                <a href="{{ route('leads', array_merge($keep, ['deal_status' => 'pending'])) }}"
                    class="htg-pipe__cell is-pending {{ $activeStatus === 'pending' ? 'is-active' : '' }}">
                    <span class="htg-pipe__label">Pending</span>
                    <span class="htg-pipe__value">{{ $countPending }}</span>
                    <span class="htg-pipe__hint">Not contacted yet</span>
                </a>

                <a href="{{ route('leads', array_merge($keep, ['deal_status' => 'follow up'])) }}"
                    class="htg-pipe__cell is-follow {{ $activeStatus === 'follow up' ? 'is-active' : '' }}">
                    <span class="htg-pipe__label">Follow up</span>
                    <span class="htg-pipe__value">{{ $countFollow }}</span>
                    <span class="htg-pipe__hint">
                        @if ($overdue)
                            <span class="text-danger">{{ $overdue }} overdue</span>
                        @else
                            All on schedule
                        @endif
                    </span>
                </a>

                <a href="{{ route('leads', array_merge($keep, ['deal_status' => 'deal closed'])) }}"
                    class="htg-pipe__cell is-closed {{ $activeStatus === 'deal closed' ? 'is-active' : '' }}">
                    <span class="htg-pipe__label">Deal closed</span>
                    <span class="htg-pipe__value">{{ $countClosed }}</span>
                    <span class="htg-pipe__hint">Ready to convert</span>
                </a>

                <a href="{{ route('leads', array_merge($keep, ['deal_status' => 'not interested'])) }}"
                    class="htg-pipe__cell is-lost {{ $activeStatus === 'not interested' ? 'is-active' : '' }}">
                    <span class="htg-pipe__label">Not interested</span>
                    <span class="htg-pipe__value">{{ $countLost }}</span>
                    <span class="htg-pipe__hint">Closed lost</span>
                </a>
            </div>

            <!-- Filters -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card htg-filter">
                        <div class="card-body">
                            <form method="GET" action="{{ route('leads') }}">
                                <div class="row g-3">
                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label" for="business">Business</label>
                                        <input type="text" id="business" name="business" class="form-control"
                                            placeholder="Business name" value="{{ request('business') }}">
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label" for="name">Customer</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            placeholder="Customer name" value="{{ request('name') }}">
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label">Interest</label>
                                        <select name="interest" class="form-select">
                                            <option value="">All interest</option>
                                            <option value="High" {{ request('interest') == 'High' ? 'selected' : '' }}>
                                                High</option>
                                            <option value="Moderate"
                                                {{ request('interest') == 'Moderate' ? 'selected' : '' }}>Moderate
                                            </option>
                                            <option value="Low" {{ request('interest') == 'Low' ? 'selected' : '' }}>
                                                Low</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label">Status</label>
                                        <select name="deal_status" class="form-select">
                                            <option value="">All status</option>
                                            <option value="pending"
                                                {{ request('deal_status') == 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="follow up"
                                                {{ request('deal_status') == 'follow up' ? 'selected' : '' }}>Follow up
                                            </option>
                                            <option value="deal closed"
                                                {{ request('deal_status') == 'deal closed' ? 'selected' : '' }}>Deal
                                                closed</option>
                                            <option value="not interested"
                                                {{ request('deal_status') == 'not interested' ? 'selected' : '' }}>Not
                                                interested</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label" for="from_date">Meeting from</label>
                                        <input type="date" id="from_date" name="from_date"
                                            value="{{ request('from_date') }}" class="form-control">
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label" for="to_date">Meeting to</label>
                                        <input type="date" id="to_date" name="to_date"
                                            value="{{ request('to_date') }}" class="form-control">
                                    </div>
                                </div>

                                <div class="htg-filter-actions mt-3">
                                    <button class="btn btn-primary">
                                        <i class="bx bx-filter-alt me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('leads') }}" class="btn btn-light">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card">
                        <div class="card-body">
                            <table id="assignLeads" class="table table-hover dt-responsive w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Lead</th>
                                        <th>Mobile</th>
                                        <th>Interest</th>
                                        <th>Meeting</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($leads as $key => $lead)
                                        @php
                                            $status = strtolower(trim($lead->deal_status ?? ''));

                                            $statusClass = match ($status) {
                                                'pending' => 's-pending',
                                                'follow up' => 's-follow',
                                                'deal closed' => 's-closed',
                                                'not interested' => 's-lost',
                                                default => '',
                                            };

                                            $interest = strtolower(trim($lead->interest ?? ''));
                                            $interestClass = match ($interest) {
                                                'high' => 'htg-interest--high',
                                                'moderate' => 'htg-interest--mod',
                                                default => 'htg-interest--low',
                                            };

                                            // Relative wording beats a raw timestamp when the whole
                                            // job is knowing what needs a call today.
                                            $meet = $lead->meeting_datetime;
                                            $meetClass = '';
                                            $meetHint = '';

                                            if ($meet) {
                                                if ($meet->isToday()) {
                                                    $meetClass = 'is-today';
                                                    $meetHint = 'Today';
                                                } elseif ($meet->isPast()) {
                                                    $meetClass = 'is-due';
                                                    $meetHint = $meet->diffForHumans();
                                                } elseif ($meet->lte($today->copy()->addDays(3))) {
                                                    $meetClass = 'is-soon';
                                                    $meetHint = $meet->diffForHumans();
                                                } else {
                                                    $meetHint = $meet->diffForHumans();
                                                }
                                            }

                                            $mobileDigits = preg_replace('/\D+/', '', $lead->mobile ?? '');
                                            $waNumber = strlen($mobileDigits) === 10 ? '91' . $mobileDigits : $mobileDigits;

                                            $followOverdue =
                                                $status === 'follow up' &&
                                                !empty($lead->follow_up_date) &&
                                                \Carbon\Carbon::parse($lead->follow_up_date)->lt($today);
                                        @endphp

                                        <tr>
                                            <td class="htg-fig">{{ $key + 1 }}</td>

                                            <td class="htg-lead-id">
                                                <a href="{{ route('admin.lead.detail', $lead->id) }}" style="text-decoration:none;color:inherit">
                                                    <strong>{{ $lead->business ?: '—' }}</strong>
                                                    <span>{{ $lead->name }}</span>
                                                </a>
                                            </td>

                                            <td>
                                                @if ($mobileDigits)
                                                    <div class="htg-contact">
                                                        <a href="tel:{{ $mobileDigits }}"
                                                            class="htg-contact__num">{{ $lead->mobile }}</a>
                                                        <a href="tel:{{ $mobileDigits }}" class="htg-contact__ico"
                                                            title="Call {{ $lead->name }}"><i class="bx bx-phone"></i></a>
                                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                                                            rel="noopener" class="htg-contact__ico is-wa"
                                                            title="WhatsApp {{ $lead->name }}"><i
                                                                class="bx bxl-whatsapp"></i></a>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="htg-interest {{ $interestClass }}">
                                                    <span class="htg-interest__bars"><i></i><i></i><i></i></span>
                                                    {{ $lead->interest ?: 'Unknown' }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($meet)
                                                    <span class="htg-when {{ $meetClass }}">
                                                        <strong>{{ $meet->format('d M Y') }}</strong>
                                                        <span>{{ $meet->format('h:i A') }} · {{ $meetHint }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not scheduled</span>
                                                @endif
                                            </td>

                                            <td class="htg-status-cell">
                                                <form method="POST"
                                                    action="{{ route('leads.updateStatus', $lead->id) }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <select name="deal_status" onchange="this.form.submit()"
                                                        class="form-select {{ $statusClass }}">
                                                        <option value="pending"
                                                            {{ $status === 'pending' ? 'selected' : '' }}>Pending
                                                        </option>
                                                        <option value="follow up"
                                                            {{ $status === 'follow up' ? 'selected' : '' }}>Follow up
                                                        </option>
                                                        <option value="deal closed"
                                                            {{ $status === 'deal closed' ? 'selected' : '' }}>Deal
                                                            closed</option>
                                                        <option value="not interested"
                                                            {{ $status === 'not interested' ? 'selected' : '' }}>Not
                                                            interested</option>
                                                    </select>

                                                    @if ($status === 'follow up')
                                                        <div class="htg-followup">
                                                            <div>
                                                                <label
                                                                    for="fu_date_{{ $lead->id }}">Follow-up
                                                                    date</label>
                                                                <input type="date" id="fu_date_{{ $lead->id }}"
                                                                    name="follow_up_date"
                                                                    value="{{ $lead->follow_up_date }}"
                                                                    class="form-control" onchange="this.form.submit()">
                                                            </div>

                                                            <div>
                                                                <label
                                                                    for="fu_note_{{ $lead->id }}">Remark</label>
                                                                <input type="text" id="fu_note_{{ $lead->id }}"
                                                                    name="follow_up_remark"
                                                                    value="{{ $lead->follow_up_remark }}"
                                                                    class="form-control" placeholder="What was agreed?"
                                                                    onchange="this.form.submit()">
                                                            </div>

                                                            @if ($followOverdue)
                                                                <span class="text-danger" style="font-size:11.5px;">
                                                                    <i class="bx bx-error-circle"></i>
                                                                    Overdue since
                                                                    {{ \Carbon\Carbon::parse($lead->follow_up_date)->format('d M') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </form>
                                            </td>

                                            <td>
                                                @if ($status === 'deal closed')
                                                    <a href="{{ route('entry.fromLead', $lead->id) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="bx bx-file me-1"></i>Create contract
                                                    </a>
                                                @else
                                                    <span class="text-muted" style="font-size:12px;">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- colspan was 5 against 8 columns, which broke the row --}}
                                        <tr>
                                            <td colspan="7">
                                                <div class="htg-empty">
                                                    <i class="bx bx-user-voice"></i>
                                                    <p>
                                                        <strong>No leads match this view</strong>
                                                        Clear the filters, or add a lead to get started.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#assignLeads').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 15,
                lengthMenu: [15, 25, 50, 100],
                language: {
                    search: "",
                    searchPlaceholder: "Search these leads",
                    lengthMenu: "Show _MENU_",
                    info: "_START_–_END_ of _TOTAL_",
                    infoEmpty: "No leads",
                    zeroRecords: "No leads match this search",
                    paginate: { previous: "Prev", next: "Next" }
                }
            });
        });
    </script>
@endsection
