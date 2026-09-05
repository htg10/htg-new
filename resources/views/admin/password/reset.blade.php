{{-- @extends('layouts.backend.app')
@section('content') --}}


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | MbizSpare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Abhisan Technology" />

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
                                        <p>Sign in with MbizSpare.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/admin/images/profile-img.png') }}" alt=""
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>


                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <a href="/" class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('assets/admin/images/logo-light.svg') }}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>

                                <a href="/" class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ asset('assets/admin/images/icon.png') }}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </a>
                            </div>


                            <div class="mt-5">
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
                                <form action="{{ route('login.post') }}" method="POST" class="form-horizontal">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    @if (session('status'))
                                    <div class="alert alert-ssuccess">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                    <div class="">
                                        <label for="email"
                                            class="col-form-label text-md-end">{{ __('Email Address') }}</label>
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ $email ?? old('email') }}" required autocomplete="email"
                                            autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="">
                                        <label for="password"
                                            class="col-form-label text-md-end">{{ __('Password') }}</label>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="">
                                        <label for="password-confirm"
                                            class="col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                                        <input id="password-confirm" type="password" class="form-control"
                                            name="password_confirmation" required autocomplete="new-password">
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="text-center">
                        <p class="m-0">©
                            <script>
                                document.write(new Date().getFullYear())
                            </script> Starklikes.
                        </p>
                        <p> Design & Develop by <a href="https://abhisan.com/" target="_blank">Abhisan Technology</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.backend.partials.script')
    @yield('script')
</body>

</html>
{{-- @endsection --}}
