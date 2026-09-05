@extends('layouts.backend.app')

@section('meta')
    <title>Add New Contract | Admin</title>
@endsection

@section('content')
    <!--[ Page Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Add New Contract
                            <span class="htg-page-sub">Record a new sale — client details on the left, billed products
                                on the right</span>
                        </h4>

                        <div class="page-title-right">
                            <a href="{{ url('/index') }}" class="btn btn-light waves-effect waves-light">
                                <i class="fas fa-reply-all me-1"></i> Back to list
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            {{--
                One form now wraps both columns. Previously the <form> opened
                inside the left card and closed inside the right one, so the
                markup only worked because browsers repair it.
            --}}
            <form action="{{ route('addnew.store') }}" method="POST" enctype="multipart/form-data"
                class="needs-validation" novalidate>
                @csrf

                <div class="row">

                    {{-- Client --}}
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">

                                <div class="htg-sect mt-0">
                                    <h5>Client</h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="inputEmail4">Date</label>
                                        <input type="date" name="date" class="form-control" id="inputEmail4">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Type</label>
                                        <select name="type" class="form-select">
                                            <option value="new" selected>New</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="company_name">Company Name</label>
                                        <input type="text" id="company_name" class="form-control" name="company"
                                            placeholder="Start typing to reuse an existing client"
                                            autocomplete="off">
                                        <datalist id="company_list"></datalist>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" id="address" class="form-control" name="address"
                                            placeholder="Address">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="gst">GST Number</label>
                                        <input type="text" id="gst" class="form-control" name="gst"
                                            placeholder="GST Number">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="contact">Contact Person</label>
                                        <input type="text" id="contact" class="form-control" name="contact"
                                            placeholder="Contact Person">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="contactno">Contact Number</label>
                                        <input type="text" id="contactno" class="form-control" name="contactno"
                                            placeholder="Contact Number">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="email">Contact Email</label>
                                        <input type="text" id="email" class="form-control" name="email"
                                            placeholder="Contact Email" required>
                                        <div class="invalid-feedback">Enter the client's email.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="type1">Self / Tally</label>
                                        <select name="type1" id="type1" class="form-select">
                                            <option value="">Self/Tally</option>
                                            <option value="self">Self</option>
                                            <option value="tally">Tally</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="payment">Payment Mode</label>
                                        <select name="payment" id="payment" class="form-select" required>
                                            <option value="">Payment Mode</option>
                                            @foreach ($banks as $b)
                                                <option value="{{ $b->bank }}">{{ $b->bank }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">Choose how the client paid.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="user_id">BDM</label>
                                        <select class="form-select form-control form-control-sm" name="user_id"
                                            id="user_id" required>
                                            <option value="">Select BDM</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">This field is required. </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="remark">Remark</label>
                                        <input type="text" id="remark" class="form-control" name="remark"
                                            placeholder="Remark">
                                    </div>

                                    <div class="col-12 image">
                                        <label class="form-label">Documents</label>
                                        <input type="file" name="image[]" class="form-control" multiple>
                                        <small class="text-muted">Attach the signed contract or payment proof. You can
                                            pick several files.</small>
                                    </div>
                                </div>

                                <div class="htg-sect">
                                    <h5>Billing</h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="totalAmount">Total Amount</label>
                                        <input type="text" class="form-control htg-fig" id="totalAmount"
                                            name="totalamount" placeholder="0.00" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="receivedAmount">Received Amount</label>
                                        <input type="text" class="form-control htg-fig" id="receivedAmount"
                                            name="receivedamount" placeholder="0.00" readonly>
                                    </div>
                                </div>

                                <div class="htg-totals">
                                    <div>
                                        <span>Balance</span>
                                        <strong><span class="htg-cur">₹</span><span id="balanceAmount">0.00</span></strong>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Products --}}
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                @include('admin.partials.product-matrix', ['mode' => 'create'])
                            </div>
                        </div>
                    </div>

                </div>

                <div class="htg-actionbar">
                    <button type="reset" class="btn btn-light waves-effect waves-light">Clear form</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check me-1"></i>Save contract
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection

@section('script')
    @include('admin.partials.product-matrix-js')

    {{-- autofill get data --}}
    @include('admin.partials.company-autofill-js', [
        'suggestUrl' => url('/get-company-suggestions'),
        'dataUrl' => url('/get-company-data'),
    ])
@endsection
