@extends('layouts.backend.app')

@section('meta')
    <title>Edit Contract | User</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Edit Contract
                            <span class="htg-page-sub">{{ $entries->company }}</span>
                        </h4>

                        <div class="page-title-right">
                            <a href="{{ url('user/index') }}" class="btn btn-light waves-effect waves-light">
                                <i class="fas fa-reply-all me-1"></i> Back to list
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ url('user/entry/' . $entries->id) }}" method="POST" enctype="multipart/form-data"
                class="needs-validation" novalidate>
                @method('PATCH')
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
                                        <input type="date" name="date" value="{{ $entries->date }}"
                                            class="form-control" id="inputEmail4">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="type">Type</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="">New/Renew</option>
                                            <option value="new" {{ $entries->type == 'new' ? 'selected' : '' }}>New
                                            </option>
                                            <option value="renew" {{ $entries->type == 'renew' ? 'selected' : '' }}>
                                                Renew</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" name="company"
                                            value="{{ $entries->company }}" placeholder="Company Name">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address"
                                            value="{{ $entries->address }}" placeholder="Address">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">GST Number</label>
                                        <input type="text" class="form-control" name="gst"
                                            value="{{ $entries->gst }}" placeholder="GST Number">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Person</label>
                                        <input type="text" class="form-control" name="contact"
                                            value="{{ $entries->contact }}" placeholder="Contact Person">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" name="contactno"
                                            value="{{ $entries->contactno }}" placeholder="Contact Number">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Contact Email</label>
                                        <input type="text" class="form-control" name="email"
                                            value="{{ $entries->email }}" placeholder="Contact Email">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Self / Tally</label>
                                        <select name="type1" class="form-select">
                                            <option value="">Self/Tally</option>
                                            <option value="self" {{ $entries->type1 == 'self' ? 'selected' : '' }}>
                                                Self</option>
                                            <option value="tally" {{ $entries->type1 == 'tally' ? 'selected' : '' }}>
                                                Tally</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="payment">Payment Mode</label>
                                        <select name="payment" id="payment" class="form-select" required>
                                            <option value="">Payment Mode</option>
                                            @foreach ($banks as $b)
                                                <option value="{{ $b->bank }}"
                                                    {{ $entries->payment == $b->bank ? 'selected' : '' }}>
                                                    {{ $b->bank }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="user_id">BDM</label>
                                        <select class="form-select form-control form-control-sm" name="user_id"
                                            id="user_id">
                                            <option value="">Select User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ $user->id == $entries->user_id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Remark</label>
                                        <input type="text" class="form-control" name="remark"
                                            value="{{ $entries->remark }}" placeholder="Remark">
                                    </div>

                                    @php
                                        $existingImages = [];
                                        if (!empty($entries->image)) {
                                            $decoded = json_decode($entries->image, true);
                                            if (is_array($decoded)) {
                                                $existingImages = $decoded;
                                            }
                                        }
                                    @endphp

                                    @if (count($existingImages))
                                        <div class="col-12">
                                            <label class="form-label">Documents on file</label>
                                            <div class="htg-thumbs">
                                                @foreach ($existingImages as $imagePath)
                                                    <a href="{{ url($imagePath) }}" target="_blank" rel="noopener">
                                                        <img src="{{ url($imagePath) }}" alt="Document">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12 image">
                                        <label class="form-label">Add documents</label>
                                        <input type="file" name="image[]" class="form-control" multiple>
                                    </div>
                                </div>

                                <div class="htg-sect">
                                    <h5>Billing</h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Total Amount</label>
                                        <input type="text" class="form-control htg-fig" name="totalamount"
                                            value="{{ $totalAmount }}" placeholder="Total Amount" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Received Amount</label>
                                        <input type="text" class="form-control htg-fig" name="receivedamount"
                                            value="{{ $totalPayment }}" placeholder="Received Amount" readonly>
                                    </div>
                                </div>

                                <div class="htg-totals">
                                    <div>
                                        <span>Outstanding Balance</span>
                                        <strong
                                            style="color:{{ (float) $totalBalance > 0 ? 'var(--htg-bad)' : 'var(--htg-ok)' }}">
                                            <span class="htg-cur">₹</span>{{ number_format((float) $totalBalance, 2) }}
                                        </strong>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Products --}}
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                @include('admin.partials.product-matrix', [
                                    'mode' => 'edit',
                                    'existing' => $products,
                                ])

                                <div class="alert alert-warning mt-3 mb-0">
                                    <i class="bx bx-info-circle"></i>
                                    <span><strong>Already Paid</strong> is what the client has settled so far and can't
                                        be edited. Put any fresh payment in <strong>New Payment</strong> — it is added
                                        on top.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="htg-actionbar">
                    <button type="reset" class="btn btn-light waves-effect waves-light">Clear form</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check me-1"></i>Save changes
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection

@section('script')
    @include('admin.partials.product-matrix-js')
@endsection
