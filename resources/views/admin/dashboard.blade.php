@extends('layouts.backend.app')

@section('meta')
    <title>Dashboard | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Dashboard
                            <span class="htg-page-sub">Collections, services and contract activity at a glance</span>
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Total Services</p>
                                            <h4 class="mb-0">{{ $services->count() }}</h4>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bxs-book-content font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Total Contracts</p>
                                            <h4 class="mb-0">{{ $entry->count() }}</h4>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bx-copy-alt font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Optional Amount Cards --}}
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium">Net Balance</p>
                                            <h4 class="mb-0"><span class="htg-cur">₹</span>{{ number_format($netBalance ?? 0, 2) }}</h4>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bx-rupee font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Payment Filter --}}
            <div class="htg-sect">
                <h4>Payments</h4>
            </div>

            <div class="card htg-filter mb-4">
                <div class="card-body">
                    <div class="row align-items-end">

                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="form-label" for="company_search">Company Name</label>
                            <input type="text" id="company_search" class="form-control" placeholder="Search company...">
                        </div>

                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label" for="payment_mode">Payment Mode</label>
                            <select id="payment_mode" class="form-control">
                                <option value="">All</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->bank }}">{{ $b->bank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label" for="from_date">From Date</label>
                            <input type="date" id="from_date" class="form-control">
                        </div>

                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="form-label" for="to_date">To Date</label>
                            <input type="date" id="to_date" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="htg-filter-actions">
                                <button class="btn btn-primary" id="filterBtn">
                                    <i class="bx bx-filter-alt me-1"></i>Apply Filter
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Reset</a>
                                <button class="btn btn-success" id="exportExcel">
                                    <i class="bx bx-spreadsheet me-1"></i>Excel
                                </button>
                                <button class="btn btn-danger" id="exportPdf">
                                    <i class="bx bxs-file-pdf me-1"></i>PDF
                                </button>
                            </div>
                        </div>

                    </div>

                    <div id="filterResult" class="mt-2">
                        <div class="htg-empty">
                            <i class="bx bx-slider-alt"></i>
                            <p>
                                <strong>No filter applied yet</strong>
                                Pick a payment mode or date range, then choose Apply Filter to list the matching
                                income and expense records.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphs --}}
            <div class="htg-sect">
                <h4>Breakdown</h4>
            </div>

            <div class="row">

                <div class="col-lg-6 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="bdmName" style="height:390px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="productName" style="height:390px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="bdmPriceChart" style="height:390px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="servicePriceChart" style="height:390px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="paymentChart" style="height:390px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card h-100 chart-card">
                        <div class="card-body">
                            <div id="dateChart" style="height:390px;"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        google.charts.load('current', {
            packages: ['corechart']
        });

        let bdmCountData = @json($bdmCountChartData);
        let serviceCountData = @json($serviceCountChartData);
        let bdmPriceData = @json($bdmPriceChartData);
        let servicePriceData = @json($servicePriceChartData);
        let paymentChartData = @json($paymentChartData);
        let dateChartData = @json($dateChartData);

        let chartsReady = false;

        // Ledger palette — amber leads, ink anchors, semantics follow.
        const HTG_SERIES = ['#2B59C3', '#0F7A52', '#B26908', '#C33C2E', '#6A4FC7',
                            '#1D8AA8', '#4A5A72', '#8E5BB5', '#2F8F6E', '#A2551C'];

        function htgInk() {
            const dark = document.documentElement.getAttribute('data-htg-theme') === 'dark';
            return {
                text: dark ? '#E4EBF5' : '#142033',
                dim: dark ? '#A6B6CC' : '#4A5C74'
            };
        }

        google.charts.setOnLoadCallback(function () {
            chartsReady = true;
            drawAllCharts();
        });

        function getPieOptions(title, is3D = false) {
            const ink = htgInk();

            return {
                title: title,
                is3D: is3D,
                pieHole: is3D ? 0 : 0.42,
                pieSliceText: 'percentage',
                colors: HTG_SERIES,
                backgroundColor: 'transparent',
                pieSliceBorderColor: 'transparent',
                fontName: 'Inter',
                tooltip: {
                    text: 'both',
                    textStyle: { fontName: 'Inter', fontSize: 12 }
                },
                legend: {
                    position: 'right',
                    textStyle: {
                        fontSize: 11,
                        color: ink.dim,
                        fontName: 'Inter'
                    }
                },
                chartArea: {
                    left: 20,
                    top: 52,
                    width: '84%',
                    height: '76%'
                },
                titleTextStyle: {
                    fontSize: 12,
                    bold: false,
                    color: ink.dim,
                    fontName: 'Inter'
                },
                pieSliceTextStyle: {
                    color: '#ffffff',
                    fontSize: 11,
                    fontName: 'Inter'
                }
            };
        }

        function makeRows(labelName, valueName, chartRows, type = 'count') {
            let rows = [[labelName, valueName]];

            if (chartRows && chartRows.length > 0) {
                chartRows.forEach(item => {
                    let label = item.name;

                    if (type === 'amount') {
                        label = item.name + ' ₹' + Number(item.amount).toLocaleString('en-IN');
                        rows.push([label, Number(item.amount)]);
                    } else {
                        label = item.name + ' [' + Number(item.count) + ']';
                        rows.push([label, Number(item.count)]);
                    }
                });
            } else {
                rows.push(['No Data', 0]);
            }

            return rows;
        }

        function makeDateRows(chartRows) {
            let rows = [['Date', 'Amount']];

            if (chartRows && chartRows.length > 0) {
                chartRows.forEach(item => {
                    let amount = Number(item.amount);
                    rows.push([
                        item.date + ' ₹' + amount.toLocaleString('en-IN'),
                        amount
                    ]);
                });
            } else {
                rows.push(['No Data', 0]);
            }

            return rows;
        }

        function drawAllCharts() {
            if (!chartsReady) return;

            drawBDMCountChart(bdmCountData);
            drawServiceCountChart(serviceCountData);
            drawBDMPriceChart(bdmPriceData);
            drawServicePriceChart(servicePriceData);
            drawPaymentChart(paymentChartData);
            drawDateChart(dateChartData);
        }

        function drawBDMCountChart(chartRows) {
            let data = google.visualization.arrayToDataTable(
                makeRows('BDM', 'Count', chartRows, 'count')
            );

            new google.visualization.PieChart(document.getElementById('bdmName'))
                .draw(data, getPieOptions('Contracts by BDM', false));
        }

        function drawServiceCountChart(chartRows) {
            let data = google.visualization.arrayToDataTable(
                makeRows('Service', 'Count', chartRows, 'count')
            );

            new google.visualization.PieChart(document.getElementById('productName'))
                .draw(data, getPieOptions('Contracts by service', true));
        }

        function drawBDMPriceChart(chartRows) {
            let data = google.visualization.arrayToDataTable(
                makeRows('BDM', 'Amount', chartRows, 'amount')
            );

            new google.visualization.PieChart(document.getElementById('bdmPriceChart'))
                .draw(data, getPieOptions('Collection by BDM', false));
        }

        function drawServicePriceChart(chartRows) {
            let data = google.visualization.arrayToDataTable(
                makeRows('Service', 'Amount', chartRows, 'amount')
            );

            new google.visualization.PieChart(document.getElementById('servicePriceChart'))
                .draw(data, getPieOptions('Collection by service', true));
        }

        function drawPaymentChart(chartRows) {
            let data = google.visualization.arrayToDataTable(
                makeRows('Payment Mode', 'Amount', chartRows, 'amount')
            );

            new google.visualization.PieChart(document.getElementById('paymentChart'))
                .draw(data, getPieOptions('Collection by payment mode', false));
        }

        function drawDateChart(chartRows) {
            let data = google.visualization.arrayToDataTable(
                makeDateRows(chartRows)
            );

            new google.visualization.PieChart(document.getElementById('dateChart'))
                .draw(data, getPieOptions('Collection by date', true));
        }

        function getFilters() {
            return {
                company: document.getElementById('company_search').value,
                payment_mode: document.getElementById('payment_mode').value,
                from_date: document.getElementById('from_date').value,
                to_date: document.getElementById('to_date').value
            };
        }

        document.getElementById('filterBtn').addEventListener('click', function () {
            const btn = this;
            const label = btn.innerHTML;
            let filters = getFilters();

            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Applying';

            fetch("{{ route('dashboard.payment.filter') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(filters)
            })
                .then(res => res.json())
                .then(response => {
                    document.getElementById('filterResult').innerHTML = response.html;

                    bdmCountData = response.bdmCountChartData;
                    serviceCountData = response.serviceCountChartData;
                    bdmPriceData = response.bdmPriceChartData;
                    servicePriceData = response.servicePriceChartData;
                    paymentChartData = response.paymentChartData;
                    dateChartData = response.dateChartData;

                    drawAllCharts();
                })
                .catch(error => {
                    console.error(error);
                    document.getElementById('filterResult').innerHTML =
                        '<div class="alert alert-danger mb-0">' +
                        '<i class="bx bx-error-circle"></i>' +
                        '<span>The filter could not be applied. Check your connection and try again.</span>' +
                        '</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = label;
                });
        });

        document.getElementById('exportExcel').onclick = () => {
            let f = getFilters();

            window.location =
                `{{ route('dashboard.export.excel') }}?company=${encodeURIComponent(f.company)}&payment_mode=${encodeURIComponent(f.payment_mode)}&from_date=${encodeURIComponent(f.from_date)}&to_date=${encodeURIComponent(f.to_date)}`;
        };

        document.getElementById('exportPdf').onclick = () => {
            let f = getFilters();

            window.location =
                `{{ route('dashboard.export.pdf') }}?company=${encodeURIComponent(f.company)}&payment_mode=${encodeURIComponent(f.payment_mode)}&from_date=${encodeURIComponent(f.from_date)}&to_date=${encodeURIComponent(f.to_date)}`;
        };

        // Redraw on resize (debounced) and whenever the colour mode changes.
        let htgResizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(htgResizeTimer);
            htgResizeTimer = setTimeout(drawAllCharts, 180);
        });

        window.addEventListener('htg:themechange', drawAllCharts);
    </script>
@endsection
