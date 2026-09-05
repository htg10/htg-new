@extends('layouts.backend.app')

@section('meta')
    <title>Telecaller | New</title>
@endsection

@section('content')
    <!--[ Page Content ] start -->
    <div class="page-content">
        <div class="container-fluid">

            <!-- [ breadcrumb ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Add New Telecaller</h4>
                        <div class="page-title-right">
                            <a href="{{ url('/admin/telecallers') }}" class="btn btn-primary waves-effect waves-light"> Show
                                Telecallers</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <div class="container">
                <h2>Add New Telecaller</h2>

                @if ($errors->any())
                    <div class="alert alert-danger col-lg-6">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.telecaller.store') }}" method="POST">
                    @csrf
                    <div class="mb-3 col-lg-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="name" name="name" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                    <div class="mb-3 col-lg-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email">
                    </div>

                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Password</label>
                        <div class="input-group auth-pass-inputgroup">
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="Enter password" aria-label="Password">
                            <button class="btn btn-light" type="button"
                                onclick="togglePasswordVisibility('password', 'password-addon1')">
                                <i class="mdi mdi-eye-outline" id="password-addon1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 col-lg-6">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group auth-pass-inputgroup">
                            <input type="password" name="password_confirmation" class="form-control" id="confirm-password"
                                placeholder="Confirm password" aria-label="Password">
                            <button class="btn btn-light" type="button"
                                onclick="togglePasswordVisibility('confirm-password', 'password-addon2')">
                                <i class="mdi mdi-eye-outline" id="password-addon2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 col-lg-6 d-grid">
                        <button class="btn btn-primary waves-effect waves-light" type="submit">Create Telecaller</button>
                    </div>
                </form>
            </div>

        </div>
        <!-- container-fluid -->
    </div>
@endsection


@section('script')
    <script>
        function togglePasswordVisibility(passwordFieldId, iconId) {
            const passwordField = document.getElementById(passwordFieldId);
            const icon = document.getElementById(iconId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.replace("mdi-eye-outline", "mdi-eye-off-outline");
            } else {
                passwordField.type = "password";
                icon.classList.replace("mdi-eye-off-outline", "mdi-eye-outline");
            }
        }
    </script>
@endsection
