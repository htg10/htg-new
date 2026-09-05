@extends('layouts.backend.app')

@section('meta')
    <title>Edit Bank | Admin</title>
@endsection

@section('style')
@endsection
@section('content')
    <!--[ Blog Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">
                    <h4>Edit Bank</h4>

                    <form method="POST" enctype="multipart/form-data" action="{{ route('bank.update', $bank) }}">
                        @csrf @method('POST')

                        <div class="mb-2 col-lg-6">
                            <label>Bank Name *</label>
                            <input type="text" name="bank" value="{{ $bank->bank }}" class="form-control"
                                placeholder="Enter Bank Name" required>
                        </div>
                        <div class="mb-2 col-lg-6">
                            <label>Attachment</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <button class="btn btn-primary">Update Bank</button>
                    </form>
                </div>
            </div>



        </div>
        <!-- container-fluid -->
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
        });
    </script>
@endsection
