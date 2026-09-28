@extends('layouts.backend.app')

@section('meta')
    <title>Edit Rent Payment | Admin</title>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Edit Rent Payment</h4>
                    <a href="{{ route('admin.rent.index') }}" class="btn btn-light">
                        <i class="bx bx-arrow-back me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-1">
            <div class="card col-lg-5 mt-2">
                <div class="form-section mx-3 my-2">
                    <form action="{{ route('admin.rent.update', $payment->id) }}" method="POST" class="needs-validation row g-3" novalidate>
                        @method('PATCH')
                        @csrf

                        <div class="col-md-12 mt-3">
                            <label class="form-label">Building *</label>
                            <select id="propertySelect" class="form-select" required onchange="updateTenants()">
                                <option value="">Select Building</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}"
                                        data-tenants='@json($property->tenants)'
                                        {{ $payment->property_id == $property->id ? 'selected' : '' }}>
                                        {{ $property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tenant *</label>
                            <select name="tenant_id" id="tenantSelect" class="form-select" required>
                                <option value="">Select Tenant</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Amount *</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="amount" id="amountField"
                                       value="{{ $payment->amount }}" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Mode *</label>
                            <select name="payment_mode" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->bank }}" {{ $payment->payment_mode == $bank->bank ? 'selected' : '' }}>
                                        {{ $bank->bank }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" name="date" value="{{ $payment->date }}" required>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="card action-btn text-start">
                                <div class="card-body p-2">
                                    <button type="submit" class="btn btn-success">Update Payment</button>
                                    <a href="{{ route('admin.rent.index') }}" class="btn btn-light">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var preselectedTenantId = {{ $payment->tenant_id ?? 'null' }};
var skipAmountFill = true;

function updateTenants() {
    var sel = document.getElementById('propertySelect');
    var tSel = document.getElementById('tenantSelect');
    tSel.innerHTML = '<option value="">Select Tenant</option>';

    var opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.dataset.tenants) return;

    var tenants = JSON.parse(opt.dataset.tenants);
    tenants.forEach(function(t) {
        var o = document.createElement('option');
        o.value = t.id;
        o.textContent = t.name + (t.unit ? ' (' + t.unit + ')' : '') + ' — ₹' + Number(t.rent_amount).toLocaleString('en-IN');
        o.dataset.rent = t.rent_amount;
        if (preselectedTenantId === t.id) o.selected = true;
        tSel.appendChild(o);
    });
}

document.getElementById('tenantSelect').addEventListener('change', function() {
    if (skipAmountFill) { skipAmountFill = false; return; }
    var opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.rent) {
        document.getElementById('amountField').value = opt.dataset.rent;
    }
});

updateTenants();
</script>
@endsection
