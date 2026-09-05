@extends('layouts.backend.app')

@section('meta')
    <title>Expense | Admin</title>
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
                        <h4 class="mb-sm-0 font-size-18">Add Expense</h4>

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
                    <h4>Add Expense</h4>

                    <form method="POST" enctype="multipart/form-data" action="{{ route('expense.store') }}">
                        @csrf

                        <div class="mb-2 col-lg-6">
                            <label>Purpose *</label>

                            <select name="purpose" id="purpose" class="form-select" required>

                                <option value="">Select Purpose</option>

                                @foreach($purposes as $purpose)

                                    <option value="{{ $purpose->name }}" {{ old('purpose', $expense->purpose ?? '') == $purpose->name ? 'selected' : '' }}>

                                        {{ $purpose->name }}

                                    </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Amount *</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-2 col-lg-6">
                            <label>Payment Mode *</label>
                            <select name="payment_mode" id="payment_mode" class="form-select" required>
                                <option value="">Payment Mode</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->bank }}">{{ $bank->bank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Date *</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control"></textarea>
                        </div>

                        <div class="mb-2 col-lg-6">
                            <label>Attachment</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>

                        <button class="btn btn-success">Save Expense</button>
                    </form>
                </div>
            </div>



        </div>
        <!-- container-fluid -->
    </div>
@endsection


@section('script')
@endsection