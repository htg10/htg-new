@extends('layouts.backend.app')

@section('meta')
    <title>Add New Lead | Admin</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">
                            Add New Lead
                            <span class="htg-page-sub">Capture the business, the meeting and where you met them</span>
                        </h4>

                        <div class="page-title-right">
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

            <form id="leadCreateForm" action="{{ route('admin.lead.store') }}" method="POST"
                enctype="multipart/form-data" class="needs-validation" novalidate>
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
                                            value="{{ old('business') }}" placeholder="Business name" required>
                                        <div class="invalid-feedback">Enter the business name.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="name">Customer name</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                            value="{{ old('name') }}" placeholder="Who you spoke to" required>
                                        <div class="invalid-feedback">Enter the customer name.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="address">Address</label>
                                        <input type="text" id="address" class="form-control" name="address"
                                            value="{{ old('address') }}" placeholder="Shop or office address">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="mobile">Mobile number</label>
                                        <input type="text" id="mobile" class="form-control" name="mobile"
                                            value="{{ old('mobile') }}" placeholder="10-digit number"
                                            inputmode="numeric" required>
                                        <div class="invalid-feedback">Enter a contact number.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="user_id">Assign to BDM</label>
                                        <select class="form-select form-control form-control-sm" name="user_id"
                                            id="user_id" required>
                                            <option value="">Select BDM</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ old('user_id') == $user->id || (!old('user_id') && Auth::user()->name == $user->name) ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">This field is required. </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="meeting_datetime">Meeting date and time</label>
                                        <input type="datetime-local" class="form-control" name="meeting_datetime"
                                            id="meeting_datetime" value="{{ old('meeting_datetime') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label" for="interest">Interest level</label>
                                        <select class="form-select form-control form-control-sm" name="interest"
                                            id="interest" required>
                                            <option value="">Select</option>
                                            <option value="High" {{ old('interest') == 'High' ? 'selected' : '' }}>High
                                            </option>
                                            <option value="Moderate"
                                                {{ old('interest') == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                                            <option value="Low" {{ old('interest') == 'Low' ? 'selected' : '' }}>Low
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">This field is required.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="remark">Remark</label>
                                        <input type="text" id="remark" class="form-control" name="remark"
                                            value="{{ old('remark') }}" placeholder="What did they say?">
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

                                @include('admin.partials.lead-products', [
                                    'selected' => old('products', []),
                                ])
                            </div>
                        </div>

                        <div class="card" id="leadLocationCard">
                            <div class="card-body">
                                <div class="htg-sect mt-0">
                                    <h5>Where this lead was added</h5>
                                </div>

                                <div class="htg-geo">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="getLeadLocation()">
                                        <i class="bx bx-current-location me-1"></i>Use current location
                                    </button>

                                    <span id="location_status" class="htg-geo__state text-muted">
                                        Not captured yet.
                                    </span>

                                    <input type="hidden" name="latitude" id="latitude"
                                        value="{{ old('latitude') }}">
                                    <input type="hidden" name="longitude" id="longitude"
                                        value="{{ old('longitude') }}">
                                    <input type="hidden" name="location_accuracy" id="location_accuracy"
                                        value="{{ old('location_accuracy') }}">
                                    <input type="hidden" name="location_url" id="location_url"
                                        value="{{ old('location_url') }}">
                                </div>

                                <small class="text-muted d-block mt-2">
                                    Required. Your browser will ask permission the first time.
                                </small>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="htg-actionbar">
                    <button type="reset" class="btn btn-light waves-effect waves-light">Clear form</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check me-1"></i>Save lead
                    </button>
                </div>

            </form>

        </div>
    </div>
@endsection

@section('script')
    @include('admin.partials.lead-form-js', ['requireLocation' => true])
@endsection
