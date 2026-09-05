@extends('layouts.backend.app')

@section('meta')
    <title>Edit Lead | Admin</title>
@endsection

@section('content')
    @php
        $selectedProducts = $telecallers->products ?? [];

        if (is_string($selectedProducts)) {
            $selectedProducts = json_decode($selectedProducts, true) ?? [];
        }
        if (!is_array($selectedProducts)) {
            $selectedProducts = [];
        }

        $oldProducts = old('products', $selectedProducts);
        if (!is_array($oldProducts)) {
            $oldProducts = [];
        }

        $status = strtolower(trim($telecallers->deal_status ?? ''));
        $pillClass = match ($status) {
            'pending' => 'htg-pill--warn',
            'follow up' => 'htg-pill--info',
            'deal closed' => 'htg-pill--ok',
            'not interested' => 'htg-pill--bad',
            default => '',
        };
    @endphp

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Edit Lead
                            <span class="htg-page-sub">{{ $telecallers->business ?: $telecallers->name }}</span>
                        </h4>

                        <div class="page-title-right d-flex align-items-center gap-2">
                            @if ($status)
                                <span class="htg-pill {{ $pillClass }}">{{ ucwords($telecallers->deal_status) }}</span>
                            @endif
                            <a href="/admin/lead/index" class="btn btn-light waves-effect waves-light">
                                <i class="fas fa-reply-all me-1"></i> Back to list
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-3">
                    <i class="bx bx-check-circle"></i><span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.lead.update', $telecallers->id) }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @method('PATCH')
                @csrf

                <div class="row">

                    {{-- Lead details --}}
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">

                                <div class="htg-sect mt-0">
                                    <h5>Lead details</h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="business">Business name</label>
                                        <input type="text" id="business" class="form-control" name="business"
                                            placeholder="Business name"
                                            value="{{ old('business', $telecallers->business) }}" required>
                                        <div class="invalid-feedback">Enter the business name.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="name">Customer name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            placeholder="Customer name" value="{{ old('name', $telecallers->name) }}"
                                            required>
                                        <div class="invalid-feedback">Enter the customer name.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" id="address" class="form-control" name="address"
                                            placeholder="Shop or office address"
                                            value="{{ old('address', $telecallers->address) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="mobile">Mobile number</label>
                                        <input type="text" id="mobile" class="form-control" name="mobile"
                                            placeholder="10-digit number" inputmode="numeric"
                                            value="{{ old('mobile', $telecallers->mobile) }}" required>
                                        <div class="invalid-feedback">Enter a contact number.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="user_id">Assign to BDM</label>
                                        <select class="form-select form-control form-control-sm" name="user_id"
                                            id="user_id" required>
                                            <option value="">Select BDM</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('user_id', $telecallers->user_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">This field is required.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="meeting_datetime">Meeting date and time</label>
                                        <input type="datetime-local" class="form-control" name="meeting_datetime"
                                            id="meeting_datetime"
                                            value="{{ old('meeting_datetime', optional($telecallers->meeting_datetime)->format('Y-m-d\TH:i')) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="interest">Interest level</label>
                                        <select class="form-select form-control form-control-sm" name="interest"
                                            id="interest" required>
                                            <option value="">Select</option>
                                            <option value="High"
                                                {{ old('interest', $telecallers->interest) == 'High' ? 'selected' : '' }}>
                                                High</option>
                                            <option value="Moderate"
                                                {{ old('interest', $telecallers->interest) == 'Moderate' ? 'selected' : '' }}>
                                                Moderate</option>
                                            <option value="Low"
                                                {{ old('interest', $telecallers->interest) == 'Low' ? 'selected' : '' }}>
                                                Low</option>
                                        </select>
                                        <div class="invalid-feedback">This field is required.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="remark">Remark</label>
                                        <input type="text" id="remark" class="form-control" name="remark"
                                            value="{{ old('remark', $telecallers->remark) }}"
                                            placeholder="What did they say?">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Services + location --}}
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="htg-sect mt-0">
                                    <h5>Interested in</h5>
                                </div>

                                @include('admin.partials.lead-products', ['selected' => $oldProducts])
                            </div>
                        </div>

                        <div class="card" id="leadLocationCard">
                            <div class="card-body">
                                <div class="htg-sect mt-0">
                                    <h5>Where this lead was added</h5>
                                </div>

                                @if ($telecallers->location_url || ($telecallers->latitude && $telecallers->longitude))
                                    @php
                                        $savedMap =
                                            $telecallers->location_url ?:
                                            'https://www.google.com/maps?q=' .
                                                $telecallers->latitude .
                                                ',' .
                                                $telecallers->longitude;
                                    @endphp
                                    <div class="htg-geo">
                                        <span class="htg-geo__state text-success">
                                            <i class="bx bx-check-circle"></i> Location saved with this lead.
                                        </span>
                                        <a href="{{ $savedMap }}" target="_blank" rel="noopener"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bx bx-map-pin me-1"></i>Open in Maps
                                        </a>
                                    </div>
                                @else
                                    <div class="htg-geo">
                                        <span class="htg-geo__state text-muted">
                                            <i class="bx bx-map-alt"></i> No location was captured for this lead.
                                        </span>
                                    </div>
                                @endif

                                {{--
                                    The original had the "update location" button commented out, so
                                    these stay hidden pass-throughs — the saved coordinates survive
                                    the update instead of being wiped.
                                --}}
                                <input type="hidden" name="latitude" id="latitude"
                                    value="{{ old('latitude', $telecallers->latitude) }}">
                                <input type="hidden" name="longitude" id="longitude"
                                    value="{{ old('longitude', $telecallers->longitude) }}">
                                <input type="hidden" name="location_accuracy" id="location_accuracy"
                                    value="{{ old('location_accuracy', $telecallers->location_accuracy) }}">
                                <input type="hidden" name="location_url" id="location_url"
                                    value="{{ old('location_url', $telecallers->location_url) }}">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="htg-actionbar">
                    <button type="reset" class="btn btn-light waves-effect waves-light">Clear form</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check me-1"></i>Update lead
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection

@section('script')
    @include('admin.partials.lead-form-js', ['requireLocation' => false])
@endsection
