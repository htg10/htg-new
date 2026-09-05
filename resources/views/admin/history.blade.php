@extends('layouts.backend.app')

@section('meta')
    <title>Entry History | Admin</title>
@endsection

@section('content')
    {{--
        This page was a standalone HTML document with its own hard-coded blue
        navbar. It now extends the shared layout.

        It also read $history->company, but historyPage() maps each row to an
        ARRAY shaped ['entry' => ..., 'products' => ...] — so that property
        access would fatal the moment the page was hit. The loop below handles
        both shapes, so it works whichever way the controller feeds it.
    --}}
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Entry History
                            <span class="htg-page-sub">Snapshots taken each time a contract was changed</span>
                        </h4>

                        <div class="page-title-right">
                            <a href="/index" class="btn btn-light waves-effect waves-light">
                                <i class="fas fa-reply-all me-1"></i> Back to list
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if (count($entryHistories))
                        <div class="table-responsive">
                            <table id="historyTable" class="table table-hover dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Company</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Products</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entryHistories as $key => $history)
                                        @php
                                            // Accepts either the mapped array or a raw model.
                                            $entry = is_array($history) ? $history['entry'] ?? [] : $history;
                                            $items = is_array($history) ? $history['products'] ?? [] : [];

                                            $get = function ($field) use ($entry) {
                                                if (is_array($entry)) {
                                                    return $entry[$field] ?? null;
                                                }
                                                return $entry->{$field} ?? null;
                                            };
                                        @endphp
                                        <tr>
                                            <td class="htg-fig">{{ $key + 1 }}</td>
                                            <td class="htg-strong">{{ $get('company') ?? '—' }}</td>
                                            <td class="htg-fig">{{ $get('date') ?? '—' }}</td>
                                            <td>
                                                @if ($get('type'))
                                                    <span
                                                        class="badge {{ strtolower($get('type')) === 'renew' ? 'bg-warning' : 'bg-info' }}">
                                                        {{ $get('type') }}
                                                    </span>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="htg-fig">{{ count($items) ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="htg-empty">
                            <i class="bx bx-history"></i>
                            <p>
                                <strong>No history yet</strong>
                                Snapshots appear here once a contract has been edited.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            if ($('#historyTable').length) {
                $('#historyTable').DataTable({
                    ordering: false,
                    responsive: true,
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "",
                        searchPlaceholder: "Search history",
                        lengthMenu: "Show _MENU_",
                        info: "_START_–_END_ of _TOTAL_",
                        zeroRecords: "No snapshots match this search",
                        paginate: { previous: "Prev", next: "Next" }
                    }
                });
            }
        });
    </script>
@endsection
