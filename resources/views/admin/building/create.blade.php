@extends('layouts.backend.app')

@section('meta')
    <title>Add New Rent| Admin</title>
@endsection
@section('content')
    <!--[ Page Content ] start -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Add New Rent</h4>

                        <div class="page-title-right">
                            {{-- <a href="{{ url('user/index') }}" class="btn btn-primary waves-effect waves-light"><i
                                    class="fas fa-reply-all"></i> Back to list</a> --}}
                        </div>

                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <div class="row g-1">
                <div class="card col-lg-5 mt-2">
                    <div class="form-section mx-3 my-2">
                        <form action="{{ route('admin.rent.store') }}" method="POST" enctype="multipart/form-data"
                            class="needs-validation row g-3" novalidate>
                            @csrf

                            <div class="col-md-6 mt-3">
                                <label for="address">Customer Name :</label>
                                <input type="text" class="form-control" name="name" placeholder="Customer Name">
                            </div>

                            <div class="col-md-6">
                                <label for="mobile">Mobile Number :</label>
                                <input type="text" class="form-control" name="mobile" placeholder="Mobile Number">
                            </div>

                            <div class="col-md-6 mt-3">
                                <label for="building">Bulding Name :</label>
                                <input type="text" class="form-control" name="building"
                                    placeholder="Enter Building Name">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label>Payment Mode *</label>
                                <select name="payment_mode" id="payment_mode" class="form-select" required>
                                    <option value="">Payment Mode</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->bank }}">{{ $bank->bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="amount">Amount :</label>
                                <input type="text" class="form-control" name="amount" placeholder="Enter Amount Name">
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="date">Date :</label>
                                <input type="date" class="form-control" name="date">
                            </div>

                            <div class="col-lg-12 mt-3">
                                <div class="card action-btn text-start">
                                    <div class="card-body p-2">
                                        <button type="submit" class="btn btn-success m-0">Submit Form</button>
                                        <button type="reset" class="btn btn-warning waves-effect waves-light">Clear
                                            Form</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @endsection
