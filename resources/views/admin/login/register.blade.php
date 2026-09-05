<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Register | Review</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="PSP Education" />

    @include('layouts.backend.partials.style')
    @yield('style')

</head>


<body data-sidebar="dark" data-layout-mode="light">


    <div class="account-pages">
        <div class="container">
            <div class="row justify-content-center align-items-center h-100v">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p>Sign in with Help Together Group.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt=""
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>


                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <a href="/" class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>

                                <a href="/" class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt=""
                                                class="rounded-circle" height="50">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="mt-0">
                                @if ($errors->any())
                                    <div class="col-12">
                                        @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger">{{ $error }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if (Session()->has('error'))
                                    <div class="alert alert-danger">{{ Session('error') }}</div>
                                @endif
                                @if (Session()->has('success'))
                                    <div class="alert alert-success">{{ Session('success') }}</div>
                                @endif
                            </div>

                            <div class="p-2">
                                <form action="{{ route('admin.register.store') }}" method="POST"
                                    class="form-horizontal">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="name" name="name" class="form-control" id="name"
                                            placeholder="Enter Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" id="email"
                                            placeholder="Enter Email">
                                    </div>

                                    <div class="mb-3">
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

                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" name="password_confirmation" class="form-control"
                                                id="confirm-password" placeholder="Confirm password"
                                                aria-label="Password">
                                            <button class="btn btn-light" type="button"
                                                onclick="togglePasswordVisibility('confirm-password', 'password-addon2')">
                                                <i class="mdi mdi-eye-outline" id="password-addon2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light"
                                            type="submit">Register</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="text-center">
                        <p class="m-0">©
                            <script>
                                document.write(new Date().getFullYear())
                            </script> Help Together Group.
                        </p>
                        <p> Design & Develop by <a href="https://helptogethergroup.com/" target="_blank">Help Together
                                Group</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.backend.partials.script')
    @yield('script')

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
</body>

</html>
