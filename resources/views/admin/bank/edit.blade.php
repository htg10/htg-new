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

                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label class="form-label">Bank Name *</label>
                                <input type="text" name="bank" value="{{ $bank->bank }}" class="form-control" placeholder="Enter Bank Name" required>
                            </div>
                            <div class="mb-3 col-lg-6">
                                <label class="form-label">Opening Balance</label>
                                <input type="number" name="opening_balance" value="{{ $bank->opening_balance }}" class="form-control" placeholder="0.00" step="0.01">
                            </div>
                            <div class="mb-3 col-lg-6">
                                <label class="form-label">Logo / Attachment</label>
                                <input type="file" name="attachment" class="form-control">
                                @if ($bank->attachment)
                                    <small class="text-muted mt-1 d-block">Current: <a href="{{ asset($bank->attachment) }}" target="_blank">View</a></small>
                                @endif
                            </div>
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
