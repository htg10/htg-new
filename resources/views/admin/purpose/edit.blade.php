@extends('layouts.backend.app')

@section('meta')
    <title>Edit Purpose | Admin</title>
@endsection

@section('style')
@endsection
@section('content')
    <!--[ Blog Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">
                    <h4>Edit Purpose</h4>

                    <form method="POST" enctype="multipart/form-data" action="{{ route('purpose.update', $purpose) }}">
                        @csrf @method('POST')

                        <div class="mb-2 col-lg-6">
                            <label>Purpose Name *</label>
                            <input type="text" name="name" value="{{ $purpose->name }}" class="form-control"
                                placeholder="Enter Purpose Name" required>
                        </div>

                        <button class="btn btn-primary">Update Purpose</button>
                    </form>
                </div>
            </div>



        </div>
        <!-- container-fluid -->
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('.dropify').dropify();
        });
    </script>
@endsection