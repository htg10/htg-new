@extends('layouts.backend.app')

@section('meta')
    <title>Convert Lead | Admin</title>
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
                            Convert Lead to Contract
                            <span class="htg-page-sub">Details carried over from
                                <strong>{{ $lead->name }}</strong></span>
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

            <form action="{{ route('addnew.store') }}" method="POST" enctype="multipart/form-data"
                class="needs-validation" novalidate>
                @csrf

                <input type="hidden" name="telecaller_id" value="{{ $lead->id }}">

                <div class="row">

                    {{-- Client --}}
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">

                                <div class="htg-sect mt-0">
                                    <h5>Client</h5>
                                </div>

                                <div class="alert alert-info mb-3">
                                    <i class="bx bx-transfer-alt"></i>
                                    <span>Name and number came from the lead. Fill in the rest before saving.</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="inputEmail4">Date</label>
                                        <input type="date" name="date" class="form-control" id="inputEmail4">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="type">Type</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="new" selected>New</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" name="company"
                                            value="{{ $lead->name }}" placeholder="Company Name">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address"
                                            value="{{ $lead->address }}" placeholder="Address">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">GST Number</label>
                                        <input type="text" class="form-control" name="gst"
                                            placeholder="GST Number">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Person</label>
                                        <input type="text" class="form-control" name="contact"
                                            value="{{ $lead->name }}" placeholder="Contact Person">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="contactno"
                                            value="{{ $lead->mobile }}" placeholder="Contact Number">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Email</label>
                                        <input type="text" class="form-control" name="email"
                                            placeholder="Contact Email" required>
                                        <div class="invalid-feedback">Enter the client's email.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Self / Tally</label>
                                        <select name="type1" class="form-select">
                                            <option value="">Self/Tally</option>
                                            <option value="self">Self</option>
                                            <option value="tally">Tally</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="payment">Payment Mode</label>
                                        <select name="payment" id="payment" class="form-select">
                                            <option value="">Payment Mode</option>
                                            @foreach ($banks as $b)
                                                <option value="{{ $b->bank }}">{{ $b->bank }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="user_id">BDM</label>
                                        <select class="form-select form-control form-control-sm" name="user_id"
                                            id="user_id" required>
                                            <option value="{{ auth()->id() }}" selected>
                                                {{ auth()->user()->name }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">This field is required. </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Remark</label>
                                        <input type="text" class="form-control" name="remark" placeholder="Remark">
                                    </div>

                                    <div class="col-12 image">
                                        <label class="form-label">Documents</label>
                                        <input type="file" name="image[]" class="form-control" multiple>
                                        <small class="text-muted">Attach the signed contract or payment proof.</small>
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

    <script>
        function calculateTotals() {
            let totalAmount = 0;
            let receivedAmount = 0;

            // Sum all total_amount fields
            document.querySelectorAll('.total-amount').forEach(input => {
                const value = parseFloat(input.value) || 0;
                totalAmount += value;
            });

            // Sum all paid_amount fields
            document.querySelectorAll('.paid-amount').forEach(input => {
                const value = parseFloat(input.value) || 0;
                receivedAmount += value;
            });

            // Update the total and received amount fields
            document.getElementById('totalAmount').value = totalAmount.toFixed(2);
            document.getElementById('receivedAmount').value = receivedAmount.toFixed(2);

            const bal = document.getElementById('balanceAmount');
            if (bal) bal.textContent = (totalAmount - receivedAmount).toFixed(2);
        }
    </script>
@endsection
