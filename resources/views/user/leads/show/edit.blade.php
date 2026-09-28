@extends('layouts.backend.app')

@section('meta')
    <title>Edit Lead | User</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Edit Lead</h4>
                    </div>
                </div>
            </div>

            @php
    $all_products = \App\Models\Service::active()->ordered()->pluck('name')->toArray();

    $selectedProducts = $telecallers->products ?? [];

    if (is_string($selectedProducts)) {
        $selectedProducts = json_decode($selectedProducts, true) ?? [];
    }
            @endphp

            <div class="row g-1">
                <div class="card col-lg-7 mt-2">
                    <div class="form-section mx-3 my-2">

                        <form action="{{ route('user.lead.update', $telecallers->id) }}" method="POST"
                              enctype="multipart/form-data" class="needs-validation row g-3" novalidate>
                            @method('PATCH')
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="col-md-6 mt-3">
                                <label for="business">Business Name :</label>
                                <input type="text" class="form-control" name="business" placeholder="Enter Business Name"
                                       value="{{ old('business', $telecallers->business) }}" required>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label for="name">Customer Name :</label>
                                <input type="text" class="form-control" name="name" placeholder="Customer Name"
                                       value="{{ old('name', $telecallers->name) }}" required>
                            </div>

                            <div class="col-12 mt-3">
                                <label for="address">Address :</label>
                                <input type="text" class="form-control" name="address" placeholder="Address"
                                       value="{{ old('address', $telecallers->address) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="mobile">Mobile Number :</label>
                                <input type="text" class="form-control" name="mobile" placeholder="Mobile Number"
                                       value="{{ old('mobile', $telecallers->mobile) }}" required>
                            </div>

                            <div class="col-md-5">
                                <label for="user_id">BDM :</label>
                                <select class="form-select form-control form-control-sm" name="user_id" id="user_id" required>
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
                                <label for="meeting_datetime">Meeting Date and Time :</label>
                                <input type="datetime-local" class="form-control" name="meeting_datetime"
                                       id="meeting_datetime"
                                       value="{{ old('meeting_datetime', optional($telecallers->meeting_datetime)->format('Y-m-d\TH:i')) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="interest">Interest Level :</label>
                                <select class="form-select form-control form-control-sm" name="interest" id="interest" required>
                                    <option value="">Select</option>
                                    <option value="High" {{ old('interest', $telecallers->interest) == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Moderate" {{ old('interest', $telecallers->interest) == 'Moderate' ? 'selected' : '' }}>Moderate</option>
                                    <option value="Low" {{ old('interest', $telecallers->interest) == 'Low' ? 'selected' : '' }}>Low</option>
                                </select>
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
                            <div class="col-12 mt-3">
                                <label for="remark">Remark :</label>
                                <input type="text" class="form-control" name="remark" value="{{ old('remark', $telecallers->remark) }}"
                                    placeholder="Enter Remark">
                            </div>

                            <!-- PRODUCTS / SERVICES CARD -->
                            <div class="col-12 mt-3">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <strong>Select Products / Services</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($all_products as $product)
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="products[]"
                                                               value="{{ $product }}"
                                                               id="product_{{ $loop->index }}"
                                                               {{ in_array($product, old('products', $selectedProducts)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="product_{{ $loop->index }}">
                                                            {{ $product }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- LOCATION CARD -->
                            <div class="col-12 mt-3">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <strong>Lead Added Location</strong>
                                    </div>
                                    <div class="card-body">
                                        {{-- <button type="button" class="btn btn-primary btn-sm" onclick="getLeadLocation()">
                                            Update Current Location
                                        </button>

                                        <span id="location_status" class="ms-2 text-muted small">
                                            @if($telecallers->latitude && $telecallers->longitude)
                                                Location already saved.
                                            @else
                                                Location not captured yet.
                                            @endif
                                        </span> --}}

                                        @if($telecallers->location_url)
                                            <div class="mt-2">
                                                <a href="{{ $telecallers->location_url }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                    View Saved Location
                                                </a>
                                            </div>
                                        @endif

                                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $telecallers->latitude) }}">
                                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $telecallers->longitude) }}">
                                        <input type="hidden" name="location_accuracy" id="location_accuracy" value="{{ old('location_accuracy', $telecallers->location_accuracy) }}">
                                        <input type="hidden" name="location_url" id="location_url" value="{{ old('location_url', $telecallers->location_url) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <div class="card action-btn text-center">
                                    <div class="card-body p-2">
                                        <button type="submit" class="btn btn-success m-0">Update Lead</button>
                                        <button type="reset" class="btn btn-warning waves-effect waves-light">Clear Form</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function getLeadLocation() {
    const status = document.getElementById('location_status');

    if (!navigator.geolocation) {
        status.innerHTML = 'Geolocation is not supported by this browser.';
        status.classList.add('text-danger');
        return;
    }

    status.innerHTML = 'Fetching location...';
    status.classList.remove('text-danger', 'text-success');

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;
            const mapUrl = `https://www.google.com/maps?q=${lat},${lng}`;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('location_accuracy').value = accuracy;
            document.getElementById('location_url').value = mapUrl;

            status.innerHTML = `Location updated successfully. Accuracy: ${Math.round(accuracy)} meters`;
            status.classList.add('text-success');
        },
        function(error) {
            status.classList.add('text-danger');

            if (error.code === error.PERMISSION_DENIED) {
                status.innerHTML = 'Location permission denied.';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                status.innerHTML = 'Location unavailable.';
            } else if (error.code === error.TIMEOUT) {
                status.innerHTML = 'Location request timed out.';
            } else {
                status.innerHTML = 'Unable to get location.';
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}
</script>
@endsection