@extends('layouts.backend.app')

@section('meta')
    <title>All Leads | Admin</title>
@endsection

@section('content')
    @php
        $byStatus = $telecallers->countBy(fn($t) => strtolower(trim($t->deal_status ?? '')));
        $countPending = $byStatus['pending'] ?? 0;
        $countFollow = $byStatus['follow up'] ?? 0;
        $countClosed = $byStatus['deal closed'] ?? 0;
        $countLost = $byStatus['not interested'] ?? 0;

        $conversion = $telecallers->count() ? round(($countClosed / $telecallers->count()) * 100) : 0;
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            All Leads
                            <span class="htg-page-sub">Every lead on record, whoever created it</span>
                        </h4>

                        <div class="page-title-right d-flex align-items-center gap-2">
                            <a href="/admin/lead/create" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Add lead
                            </a>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                <li class="breadcrumb-item active">All Leads</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Pipeline --}}
            <div class="htg-pipe">
                <div class="htg-pipe__cell is-total">
                    <span class="htg-pipe__label">Total leads</span>
                    <span class="htg-pipe__value">{{ $telecallers->count() }}</span>
                    <span class="htg-pipe__hint">{{ $conversion }}% closed</span>
                </div>
                <div class="htg-pipe__cell is-pending">
                    <span class="htg-pipe__label">Pending</span>
                    <span class="htg-pipe__value">{{ $countPending }}</span>
                    <span class="htg-pipe__hint">Not contacted yet</span>
                </div>
                <div class="htg-pipe__cell is-follow">
                    <span class="htg-pipe__label">Follow up</span>
                    <span class="htg-pipe__value">{{ $countFollow }}</span>
                    <span class="htg-pipe__hint">In conversation</span>
                </div>
                <div class="htg-pipe__cell is-closed">
                    <span class="htg-pipe__label">Deal closed</span>
                    <span class="htg-pipe__value">{{ $countClosed }}</span>
                    <span class="htg-pipe__hint">Won</span>
                </div>
                <div class="htg-pipe__cell is-lost">
                    <span class="htg-pipe__label">Not interested</span>
                    <span class="htg-pipe__value">{{ $countLost }}</span>
                    <span class="htg-pipe__hint">Closed lost</span>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">

                    <div class="card htg-filter">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.lead.index') }}">
                                <div class="row g-3">
                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label" for="business">Business</label>
                                        <input type="text" id="business" name="business"
                                            value="{{ request('business') }}" class="form-control"
                                            placeholder="Business name">
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label">Assigned BDM</label>
                                        <select name="bdm_id" class="form-select">
                                            <option value="">All BDMs</option>
                                            @foreach ($users as $bdm)
                                                <option value="{{ $bdm->id }}"
                                                    {{ request('bdm_id') == $bdm->id ? 'selected' : '' }}>
                                                    {{ $bdm->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label">Created by</label>
                                        <select name="created_by" class="form-select">
                                            <option value="">Anyone</option>
                                            @foreach ($users as $bdm)
                                                <option value="{{ $bdm->id }}"
                                                    {{ request('created_by') == $bdm->id ? 'selected' : '' }}>
                                                    {{ $bdm->name }}
                                                </option>
                                            @endforeach
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
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label" for="from_date">Created from</label>
                                        <input type="date" id="from_date" name="from_date"
                                            value="{{ request('from_date') }}" class="form-control">
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <label class="form-label" for="to_date">Created to</label>
                                        <input type="date" id="to_date" name="to_date"
                                            value="{{ request('to_date') }}" class="form-control">
                                    </div>
                                </div>

                                <div class="htg-filter-actions mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter-alt me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('admin.lead.index') }}" class="btn btn-light">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body table-responsive">
                            <table id="telecallerTable" class="table table-hover dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Lead</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>Meeting</th>
                                        <th>Interest</th>
                                        <th>Services</th>
                                        <th>Assigned BDM</th>
                                        <th>Created By</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($telecallers as $key => $telecaller)
                                        @php
                                            $status = strtolower(trim($telecaller->deal_status ?? ''));
                                            $pillClass = match ($status) {
                                                'pending' => 'htg-pill--warn',
                                                'follow up' => 'htg-pill--info',
                                                'deal closed' => 'htg-pill--ok',
                                                'not interested' => 'htg-pill--bad',
                                                default => '',
                                            };

                                            $interest = strtolower(trim($telecaller->interest ?? ''));
                                            $interestClass = match ($interest) {
                                                'high' => 'htg-interest--high',
                                                'moderate' => 'htg-interest--mod',
                                                default => 'htg-interest--low',
                                            };

                                            $mobileDigits = preg_replace('/\D+/', '', $telecaller->mobile ?? '');
                                            $waNumber =
                                                strlen($mobileDigits) === 10 ? '91' . $mobileDigits : $mobileDigits;

                                            $services = $telecaller->products;
                                            if (is_string($services)) {
                                                $services = json_decode($services, true);
                                            }
                                            $services = is_array($services) ? $services : [];

                                            $address = $telecaller->address ?? '';
                                        @endphp

                                        <tr>
                                            <td class="htg-fig">{{ $key + 1 }}</td>

                                            <td class="htg-lead-id">
                                                <a href="{{ route('admin.lead.detail', $telecaller->id) }}" style="text-decoration:none;color:inherit">
                                                    <strong>{{ $telecaller->business ?: '—' }}</strong>
                                                    <span>{{ $telecaller->name }}</span>
                                                </a>
                                            </td>

                                            <td>
                                                @if ($mobileDigits)
                                                    <div class="htg-contact">
                                                        <a href="tel:{{ $mobileDigits }}"
                                                            class="htg-contact__num">{{ $telecaller->mobile }}</a>
                                                        <a href="{{ route('admin.whatsapp.chat') }}?phone={{ $waNumber }}&name={{ urlencode($telecaller->name ?? '') }}"
                                                            class="htg-contact__ico is-wa"
                                                            title="WhatsApp {{ $telecaller->name }}"><i
                                                                class="bx bxl-whatsapp"></i></a>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($address !== '')
                                                    <span class="comment-short">
                                                        {{ Str::limit($address, 30) }}
                                                    </span>

                                                    @if (strlen($address) > 30)
                                                        <span class="comment-full d-none">
                                                            {{ $address }}
                                                        </span>

                                                        <a href="javascript:void(0)"
                                                            class="read-more text-primary fw-semibold"
                                                            onclick="toggleComment(this)">
                                                            Read more
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{--
                                                Carbon::parse(null) returns "now", so a lead with no
                                                meeting used to show today's date as if it were booked.
                                            --}}
                                            <td>
                                                @if ($telecaller->meeting_datetime)
                                                    <span class="htg-when">
                                                        <strong>{{ $telecaller->meeting_datetime->format('d M Y') }}</strong>
                                                        <span>{{ $telecaller->meeting_datetime->format('h:i A') }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not scheduled</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="htg-interest {{ $interestClass }}">
                                                    <span class="htg-interest__bars"><i></i><i></i><i></i></span>
                                                    {{ $telecaller->interest ?: 'Unknown' }}
                                                </span>
                                            </td>

                                            <td>
                                                @if (count($services))
                                                    <div class="htg-chips">
                                                        @foreach (array_slice($services, 0, 2) as $s)
                                                            <span class="htg-chip">{{ $s }}</span>
                                                        @endforeach
                                                        @if (count($services) > 2)
                                                            <span class="htg-chip"
                                                                title="{{ implode(', ', $services) }}">+{{ count($services) - 2 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Both relations are nullable, and both used to fatal on null --}}
                                            <td>{{ $telecaller->user->name ?? '—' }}</td>
                                            <td>{{ $telecaller->telecallerUser->name ?? '—' }}</td>

                                            <td>
                                                @if (!empty($telecaller->location_url))
                                                    <a href="{{ $telecaller->location_url }}" target="_blank"
                                                        rel="noopener" class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-map-pin me-1"></i>Map
                                                    </a>
                                                @elseif (!empty($telecaller->latitude) && !empty($telecaller->longitude))
                                                    <a href="https://www.google.com/maps?q={{ $telecaller->latitude }},{{ $telecaller->longitude }}"
                                                        target="_blank" rel="noopener"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-map-pin me-1"></i>Map
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">Not added</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="htg-pill {{ $pillClass }}">
                                                    {{ $telecaller->deal_status ? ucwords($telecaller->deal_status) : 'Unknown' }}
                                                </span>
                                            </td>

                                            <td style="white-space:nowrap;">
                                                <a href="{{ route('admin.lead.detail', $telecaller->id) }}"
                                                    class="btn btn-soft-primary btn-sm htg-act waves-effect waves-light"
                                                    title="View lead"><i class="bx bx-show" style="font-size:15px"></i></a>
                                                <a href="{{ route('admin.lead.edit', $telecaller->id) }}"
                                                    class="btn btn-soft-info btn-sm htg-act waves-effect waves-light"
                                                    title="Edit lead"><img src="{{ asset('assets/icons/edit.svg') }}"
                                                        alt="Edit"></a>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-soft-danger btn-sm htg-act waves-effect waves-light sa-delete"
                                                    title="Delete lead" data-id="{{ $telecaller->id }}"
                                                    data-link="/admin/lead/delete/"><img
                                                        src="{{ asset('assets/icons/delete.svg') }}" alt="Delete"></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12">
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
                </div> <!-- end col -->

            </div> <!-- end row -->

        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#telecallerTable').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 15,
                lengthMenu: [15, 25, 50, 100],
                language: {
                    search: "",
                    searchPlaceholder: "Search all leads",
                    lengthMenu: "Show _MENU_",
                    info: "_START_–_END_ of _TOTAL_",
                    infoEmpty: "No leads",
                    zeroRecords: "No leads match this search",
                    paginate: { previous: "Prev", next: "Next" }
                }
            });
        });
    </script>

    {{-- Read More Button --}}
    <script>
        function toggleComment(el) {
            let td = el.closest('td');
            let shortText = td.querySelector('.comment-short');
            let fullText = td.querySelector('.comment-full');

            if (fullText.classList.contains('d-none')) {
                shortText.classList.add('d-none');
                fullText.classList.remove('d-none');
                el.innerText = 'Read less';
            } else {
                fullText.classList.add('d-none');
                shortText.classList.remove('d-none');
                el.innerText = 'Read more';
            }
        }
    </script>
@endsection
