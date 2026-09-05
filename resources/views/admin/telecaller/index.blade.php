@extends('layouts.backend.app')

@section('meta')
    <title>All Telecallers</title>
@endsection

@section('content')
    <!--[ Page Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">All Telecallers</h4>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <div class="row">
                <div class="col-xl-12">
                    <div class="card border">
                        <div class="card-body">

                            <table id="telecallerTable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Telecaller Name</th>
                                        <th>Email</th>
                                        <th class="col-1">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($telecallers as $key => $telecaller)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td class="text-wrap">{{ $telecaller->name }}</td>
                                            <td class="text-wrap">{{ $telecaller->email }}</td>
                                            <td class="text-center">
                                                <a href="javascript:void(0);"
                                                    class="btn btn-soft-danger btn-sm waves-effect waves-light sa-delete"
                                                    title="Delete Brand" data-id="{{ $telecaller->id }}"
                                                    data-link="/admin/telecaller/delete/"><i
                                                        class="bx bx-trash font-size-16"></i></a>
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
        <!-- container-fluid -->
    </div>
@endsection
@section('script')
     <script>
        $(document).ready(function() {
            $('#telecallerTable').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });
        });
    </script>
@endsection
