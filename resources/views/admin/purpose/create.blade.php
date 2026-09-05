@extends('layouts.backend.app')

@section('meta')
    <title>Add Purpose | Admin</title>
    <!-- Dropify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropify/dist/css/dropify.min.css">
@endsection

@section('style')
@endsection

@section('content')
    <!--[ Page Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Add Purpose</h4>

                        <div class="page-title-right">
                            <a href="#" class="btn btn-primary waves-effect waves-light"><i class="fas fa-reply-all"></i>
                                Back to list</a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <div class="card">
                <div class="card-body">
                    <h4>Add Purpose</h4>

                    <form method="POST" enctype="multipart/form-data" action="{{ route('purpose.store') }}">
                        @csrf

                        <div class="mb-2 col-lg-6">
                            <label>Purpose Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Purpose Name" required>
                        </div>

                        <button class="btn btn-success">Add Purpose</button>
                    </form>
                </div>
            </div>



        </div>
        <!-- container-fluid -->
    </div>
@endsection


@section('script')
@endsection