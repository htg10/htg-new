@extends('layouts.backend.app')

@section('meta')
    <title>Users | Admin</title>
@endsection

@section('content')
    <!--[ Page Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">All Users</h4>
                        <div class="page-title-right">
                            <a href="{{ route('admin.register') }}" class="btn btn-primary waves-effect waves-light"><i
                                    class="fas fa-plus"></i> Add New User</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card border">
                        <div class="card-body">
                            <table id="userTable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="col-1">Sr.No.</th>
                                        <th>Username</th>
                                        <th>E-mail</th>
                                        <th class="col-1">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $key => $user)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td> <strong>{{ $user->name }}</strong></td>
                                            <td> {{ $user->email }}</td>
                                            <td><a href="javascript:void(0);"
                                                    class="btn btn-soft-danger btn-sm waves-effect waves-light sa-delete"
                                                    title="Delete Brand" data-id="{{ $user->id }}"
                                                    data-link="/admin/user/delete/"><i
                                                        class="bx bx-trash font-size-16"></i></a></td>
                                        </tr>
                                    @endforeach
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
        $(document).ready(function() {
            $('#userTable').DataTable({
                ordering: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            });
        });
    </script>
@endsection
