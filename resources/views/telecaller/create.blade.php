@extends('layouts.backend.app')

@section('meta')
    <title>Add New Lead | Telecaller</title>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Add New Lead</h4>
                    </div>
                </div>
            </div>

            @php
                $all_products = [
                    'Local Keyword SEO',
                    'Virtual Tour',
                    'Google Business Profile Management',
                    'Zonal Keyword SEO',
                    'Google Ads',
                    'Google Ads Recharge',
                    'Meta Ads Management',
                    'Facebook Ads Recharge',
                    'Social Media Management',
                    'Website Design',
                    'Custom Development',
                    'Website Amc',
                    'Product Photography',
                    'Domain',
                    'Hosting',
                    'QR Code',
                    'Web SEO',
                    'Others'
                ];
            @endphp

            <div class="row g-1">
                <div class="card col-lg-7 mt-2">
                    <div class="form-section mx-3 my-2">

                        <form id="leadCreateForm" action="{{ route('telecaller.store') }}" method="POST"
                            enctype="multipart/form-data" class="needs-validation row g-3" novalidate>
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="col-md-6 mt-3">
                                <label for="business">Business Name :</label>
                                <input type="text" class="form-control" name="business" placeholder="Enter Business Name"
                                    required>
                            </div>

                            <div class="col-md-6 mt-3">
                                <label for="name">Customer Name :</label>
                                <input type="text" class="form-control" name="name" placeholder="Enter Customer Name"
                                    required>
                            </div>

                            <div class="col-12 mt-3">
                                <label for="address">Address :</label>
                                <input type="text" class="form-control" name="address" placeholder="Enter Address">
                            </div>

                            <div class="col-md-6">
                                <label for="mobile">Mobile Number :</label>
                                <input type="text" class="form-control" name="mobile" placeholder="Mobile Number" required>
                            </div>

                            <div class="col-md-5">
                                <label for="user_id">BDM :</label>
                                <select class="form-select form-control form-control-sm" name="user_id" id="user_id"
                                    required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ Auth::user()->name == $user->name ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">This field is required.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="meeting_datetime">Meeting Date and Time :</label>
                                <input type="datetime-local" class="form-control" name="meeting_datetime"
                                    id="meeting_datetime">
                            </div>

                            <div class="col-md-6">
                                <label for="interest">Interest Level :</label>
                                <select class="form-select form-control form-control-sm" name="interest" id="interest"
                                    required>
                                    <option value="">Select</option>
                                    <option value="High">High</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Low">Low</option>
                                </select>
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
                            <div class="col-12 mt-3">
                                <label for="remark">Remark :</label>
                                <input type="text" class="form-control" name="remark" placeholder="Enter Remark">
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
                                                        <input class="form-check-input" type="checkbox" name="products[]"
                                                            value="{{ $product }}" id="product_{{ $loop->index }}">
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
                                        <button type="button" class="btn btn-primary btn-sm" onclick="getLeadLocation()">
                                            Get Current Location
                                        </button>

                                        <span id="location_status" class="ms-2 text-muted small">
                                            Location not captured yet.
                                        </span>

                                        <input type="hidden" name="latitude" id="latitude" required>
                                        <input type="hidden" name="longitude" id="longitude" required>
                                        <input type="hidden" name="location_accuracy" id="location_accuracy">
                                        <input type="hidden" name="location_url" id="location_url">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <div class="card action-btn text-center">
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
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    const mapUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('location_accuracy').value = accuracy;
                    document.getElementById('location_url').value = mapUrl;

                    status.innerHTML = `Location captured successfully. Accuracy: ${Math.round(accuracy)} meters`;
                    status.classList.add('text-success');
                },
                function (error) {
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

    <script>
        document.getElementById('leadCreateForm').addEventListener('submit', function (e) {
            let latitude = document.getElementById('latitude').value;
            let longitude = document.getElementById('longitude').value;
            let status = document.getElementById('location_status');

            if (!latitude || !longitude) {
                e.preventDefault();

                status.innerHTML = 'Please capture location before submitting lead.';
                status.classList.remove('text-muted', 'text-success');
                status.classList.add('text-danger');

                alert('Please click on Get Current Location before submitting.');
                return false;
            }
        });
    </script>
@endsection