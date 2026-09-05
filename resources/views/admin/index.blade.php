@extends('layouts.backend.app')

@section('meta')
    <title>All Contracts | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            All Contracts
                            <span class="htg-page-sub">Search, export and edit every new and renewed contract</span>
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                <li class="breadcrumb-item active">All Contracts</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">

                    {{-- Search --}}
                    <div class="card htg-filter">
                        <div class="card-body">
                            <form action="{{ route('index') }}" method="GET">
                                <div class="row">
                                    <div class="form-group col-lg-3 col-md-6 mb-3">
                                        <label class="form-label" for="company">Company</label>
                                        <input type="text" name="company" id="company" placeholder="Company name"
                                            class="form-control" value="{{ request('company') }}">
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 mb-3">
                                        <label class="form-label">Year</label>
                                        <select name="year" class="form-select">
                                            <option value="" selected>All years</option>
                                            @for ($i = now()->year; $i >= 2000; $i--)
                                                <option value="{{ $i }}"
                                                    {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 mb-3">
                                        <label class="form-label">Month</label>
                                        <select name="month" class="form-select">
                                            <option value="" selected>All months</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                                    {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="inputState" class="form-select">
                                            <option value="">Expired / Pending</option>
                                            <option value="Expired"
                                                {{ request('inputState') == 'Expired' ? 'selected' : '' }}>
                                                Expired</option>
                                            <option value="Pending"
                                                {{ request('inputState') == 'Pending' ? 'selected' : '' }}>
                                                Pending</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="htg-filter-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-search me-1"></i>Search
                                    </button>
                                    <a href="{{ route('index') }}" class="btn btn-light">Reset</a>

                                    {{-- <a href="{{ route('export.contracts', ['inputState' => request('inputState')]) }}"
                                        class="btn btn-success ">Export {{ request('inputState') ?? 'All' }}
                                        Contracts</a> --}}
                                    <a href="{{ route('export.contracts', request()->all()) }}" class="btn btn-success">
                                        <i class="bx bx-spreadsheet me-1"></i>Export
                                        {{ request('inputState') ?? 'All' }} Contracts
                                    </a>
                                </div>

                                <div class="htg-totals">
                                    <div>
                                        <span>Total Amount</span>
                                        <strong><span class="htg-cur">₹</span>{{ number_format($totalAmount, 2) }}</strong>
                                    </div>
                                    <div>
                                        <span>Total Balance Payment</span>
                                        <strong
                                            style="color:{{ $totalBalance > 0 ? 'var(--htg-bad)' : 'var(--htg-ok)' }}">
                                            <span class="htg-cur">₹</span>{{ number_format($totalBalance, 2) }}
                                        </strong>
                                    </div>
                                    <div>
                                        <span>Contracts Listed</span>
                                        <strong>{{ count($entries) }}</strong>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="card">
                        <div class="card-body">
                            <table id="contractsTable" class="table table-hover dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Company Name</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Contact Person</th>
                                        <th>Contact Number</th>
                                        <th>Total Amount</th>
                                        <th>Balance Payment</th>
                                        <th>Documents</th>
                                        {{-- <th>Download</th> --}}
                                        <th>BDM Name</th>
                                        <th>GST No</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entries as $key => $entry)
                                        <tr>
                                            <td class="htg-fig">{{ $key + 1 }}</td>
                                            <td class="htg-strong">{{ $entry->company }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ strtolower($entry->type ?? '') === 'renew' ? 'bg-warning' : 'bg-info' }}">
                                                    {{ $entry->type }}
                                                </span>
                                            </td>
                                            <td class="htg-fig">{{ $entry->date }}</td>
                                            <td>{{ $entry->contact }}</td>
                                            <td class="htg-fig">{{ $entry->contactno }}</td>
                                            <td class="htg-fig htg-strong">
                                                <span class="htg-cur">₹</span>{{ number_format((float) $entry->totalAmount, 2) }}
                                            </td>
                                            <td class="htg-fig"
                                                style="color:{{ (float) $entry->balancePayment > 0 ? 'var(--htg-bad)' : 'var(--htg-ok)' }}">
                                                <span class="htg-cur">₹</span>{{ number_format((float) $entry->balancePayment, 2) }}
                                            </td>
                                            {{-- <td>
                                                @if (!empty($entry->image) && is_array(json_decode($entry->image)))
                                                    @foreach (json_decode($entry->image) as $imagePath)
                                                        <img src="{{ url($imagePath) }}" alt="Image" height="80"
                                                            width="80">
                                                    @endforeach
                                                @else
                                                    <p>No images</p>
                                                @endif
                                            </td> --}}
                                            <td>
                                                <a href="{{ route('download', $entry->id) }}" class="btn btn-light btn-sm">
                                                    <i class="bx bx-download me-1"></i>Download
                                                </a>
                                            </td>
                                            <td>{{ $entry->user->name ?? '—' }}</td>
                                            <td class="htg-fig">{{ $entry->gst }}</td>
                                            <td class="text-center" style="white-space:nowrap;">
                                                <a href="{{ 'entry/' . $entry->id . '/edit' }}"
                                                    class="btn btn-soft-info btn-sm htg-act waves-effect waves-light"
                                                    title="Edit contract"><img
                                                        src="{{ asset('assets/icons/edit.svg') }}" alt="Edit"></a>
                                                {{-- <a href="{{ 'entry/delete/' . $entry->id }}"
                                                    class="btn btn-soft-danger btn-sm waves-effect waves-light"
                                                    title="Delete Brand"><img src="{{ asset('assets/icons/delete.svg') }}"
                                                        alt=""></a> --}}
                                                <a href="javascript:void(0);"
                                                    class="btn btn-soft-danger btn-sm htg-act waves-effect waves-light sa-delete"
                                                    title="Delete contract" data-id="{{ $entry->id }}"
                                                    data-link="/entry/delete/"><img
                                                        src="{{ asset('assets/icons/delete.svg') }}" alt="Delete"></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> <!-- end col -->
            </div> <!-- end row -->

            {{-- Charts --}}
            <div class="htg-sect">
                <h4>Contract mix</h4>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="piechart" style="height:320px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="bdmName" style="height:320px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="productName" style="height:320px;"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#contractsTable').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    search: "",
                    searchPlaceholder: "Search contracts",
                    lengthMenu: "Show _MENU_",
                    info: "_START_–_END_ of _TOTAL_",
                    infoEmpty: "No contracts",
                    zeroRecords: "No contracts match this search",
                    paginate: { previous: "Prev", next: "Next" }
                }
            });
        });
    </script>

    {{--
        NOTE: previously all three chart scripts declared a function called
        drawChart(), so the last definition overwrote the other two and only
        one chart ever rendered. Each now has its own name and a single
        load callback.
    --}}
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });

        const HTG_SERIES = ['#2B59C3', '#0F7A52', '#B26908', '#C33C2E', '#6A4FC7',
                            '#1D8AA8', '#4A5A72', '#8E5BB5', '#2F8F6E', '#A2551C'];

        let htgChartsReady = false;

        function htgPieOptions(title, is3D = false, pieHole = 0.42) {
            const dark = document.documentElement.getAttribute('data-htg-theme') === 'dark';
            const dim = dark ? '#A6B6CC' : '#4A5C74';

            return {
                title: title,
                is3D: is3D,
                pieHole: is3D ? 0 : pieHole,
                colors: HTG_SERIES,
                backgroundColor: 'transparent',
                pieSliceBorderColor: 'transparent',
                fontName: 'Inter',
                legend: {
                    position: 'bottom',
                    textStyle: { fontSize: 11, color: dim, fontName: 'Inter' }
                },
                chartArea: { left: 12, top: 46, width: '90%', height: '68%' },
                titleTextStyle: { fontSize: 12, bold: false, color: dim, fontName: 'Inter' },
                pieSliceTextStyle: { color: '#ffffff', fontSize: 11, fontName: 'Inter' },
                tooltip: { textStyle: { fontName: 'Inter', fontSize: 12 } }
            };
        }

        // New / Renew
        function drawTypeChart() {
            var data = google.visualization.arrayToDataTable([
                ['Type', 'Count'],
                {!! $chartData !!}
            ]);

            new google.visualization.PieChart(document.getElementById('piechart'))
                .draw(data, htgPieOptions('New vs renewal', false));
        }

        // BDM Name
        function drawBdmChart() {
            var data = google.visualization.arrayToDataTable([
                ['BDM Name', 'Count'],
                {!! $bdmDataString !!}
            ]);

            new google.visualization.PieChart(document.getElementById('bdmName'))
                .draw(data, htgPieOptions('By BDM', false));
        }

        // Service Name
        function drawServiceChart() {
            var data = google.visualization.arrayToDataTable([
                ['Service Name', 'Count'],
                {!! $productDataString !!}
            ]);

            new google.visualization.PieChart(document.getElementById('productName'))
                .draw(data, htgPieOptions('By service', true));
        }

        function htgDrawAll() {
            if (!htgChartsReady) return;
            drawTypeChart();
            drawBdmChart();
            drawServiceChart();
        }

        google.charts.setOnLoadCallback(function () {
            htgChartsReady = true;
            htgDrawAll();
        });

        let htgResizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(htgResizeTimer);
            htgResizeTimer = setTimeout(htgDrawAll, 180);
        });

        window.addEventListener('htg:themechange', htgDrawAll);
    </script>
@endsection
