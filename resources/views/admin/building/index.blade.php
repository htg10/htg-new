@extends('layouts.backend.app')

@section('meta')
    <title>All Rent Lists | Admin</title>
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
                    <div class="text-end mb-3 me-3"><a href="{{ route('admin.rent.create') }}" class="btn btn-primary">
                            + Add Rent
                        </a></div>
                    <div class="card border">
                        <form method="GET" action="{{ route('admin.rent.index') }}" class="row g-2 mt-2 mx-2">

                            <div class="col-md-3">
                                <input type="date" name="from_date" value="{{ request('from_date') }}"
                                    class="form-control" placeholder="From Date">
                            </div>

                            <div class="col-md-3">
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control"
                                    placeholder="To Date">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.rent.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-info m-0">
                                    <strong>Total Rent Amount:</strong> ₹ {{ number_format($totalAmount, 2) }}
                                </div>
                            </div>
                        </form>

                        <div class="card-body table-responsive">
                            <table id="rentTable" class="table table-bordered dt-responsive nowrap w-100 mt-3">
                                <thead>
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Customer Name</th>
                                        <th>Mobile Number</th>
                                        <th>Building Name</th>
                                        <th>Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($buildings as $key => $building)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $building->name }}</td>
                                            <td>{{ $building->mobile }}</td>
                                            <td>{{ $building->building }}</td>
                                            <td>{{ $building->amount }}</td>
                                            <td>{{ $building->payment_mode }}</td>
                                            <td>{{ $building->date }}</td>
                                            <td>
                                                <a href="{{ route('admin.rent.edit', $building->id) }}"
                                                    class="btn btn-soft-info btn-sm waves-effect waves-light"><img
                                                        src="{{ asset('assets/icons/edit.svg') }}" alt=""></a>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-soft-danger btn-sm waves-effect waves-light sa-delete"
                                                    title="Delete Brand" data-id="{{ $building->id }}"
                                                    data-link="/admin/rent/delete/"><img
                                                        src="{{ asset('assets/icons/delete.svg') }}" alt=""></a>
                                            </td>
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
            $('#rentTable').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
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
