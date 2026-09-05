@extends('layouts.backend.app')

@section('meta')
    <title>Dashboard | Admin</title>
@endsection
@section('style')
    <style>
        .dataTables_wrapper {
            position: relative;
            clear: both;
            margin-top: 10px;
        }
    </style>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card border">
                        <div class="card-body">
                            <form action="{{ route('user.index') }}" method="GET">
                                <div class="row mb-2">
                                    <div class="form-group col-lg-3">
                                        <input type="text" name="company" id="company" placeholder="Company Name"
                                            class="form-control" value="{{ request('company') }}">
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <select name="year" class="form-select">
                                            <option value="" selected>Select Year</option>
                                            @for ($i = now()->year; $i >= 2000; $i--)
                                                <option value="{{ $i }}"
                                                    {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <select name="month" class="form-select">
                                            <option value="" selected>Select Month</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                                    {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <select name="inputState" class="form-select">
                                            <option value="">Expired/Pending</option>
                                            <option value="Expired"
                                                {{ request('inputState') == 'Expired' ? 'selected' : '' }}>
                                                Expired</option>
                                            <option value="Pending"
                                                {{ request('inputState') == 'Pending' ? 'selected' : '' }}>
                                                Pending</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="{{ route('user.index') }}" class="btn btn-danger">Reset</a>
                            </form>
                            <table id="bdmTable" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                                <thead>
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Company Name</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Contact Person</th>
                                        <th>Contact Number</th>
                                        <th>Total Amount</th>
                                        <th>Balance Payment</th>
                                        <th>Images</th>
                                        <th>GST No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entries as $key => $entry)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $entry->company }}</td>
                                            <td>{{ $entry->type }}</td>
                                            <td>{{ $entry->date }}</td>
                                            <td>{{ $entry->contact }}</td>
                                            <td>{{ $entry->contactno }}</td>
                                            <td>{{ $entry->totalAmount }}</td>
                                            <td>{{ $entry->balancePayment }}</td>
                                            <td><a href="{{ route('user.download', $entry->id) }}"
                                                    class="btn btn-primary">Download</a></td>
                                            <td>{{ $entry->gst }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end col -->

            </div> <!-- end row -->

        </div>
    </div>
    </div>
@endsection
@section('script')
     <script>
        $(document).ready(function() {
            $('#bdmTable').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });
        });
    </script>
@endsection
