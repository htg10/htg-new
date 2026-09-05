<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <title>Log in | Help Together Group</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow" />

    @include('layouts.backend.partials.style')
    @yield('style')
</head>

<body data-sidebar="dark" data-layout-mode="light">

    <div class="htg-auth">

        {{-- Left: ruled ledger panel --}}
        <div class="htg-auth__ink">

            <div class="htg-auth__brand">
                <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt="Help Together Group">
                <span class="htg-wordmark">
                    <b>Help Together</b>
                    <small>Ledger</small>
                </span>
            </div>

            <div class="htg-auth__lead">
                <h1>Every contract,<br>lead and rupee —<br><em>on one line.</em></h1>
                <p>
                    The internal console for Help Together Group. Track contracts from first call to renewal,
                    assign leads to the right BDM, and keep collections and expenses reconciled.
                </p>
            </div>

            <div class="htg-auth__rows">
                <div>
                    <span>Contracts</span>
                    <b>New &amp; renewal</b>
                </div>
                <div>
                    <span>Leads</span>
                    <b>Assign &amp; track</b>
                </div>
                <div>
                    <span>Finance</span>
                    <b>Income &amp; expense</b>
                </div>
            </div>

        </div>

        {{-- Right: the form --}}
        <div class="htg-auth__form">
            <div class="htg-auth__card">

                <h2>Log in</h2>
                <p>Use the work email your account was created with.</p>

                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger mb-3">
                            <i class="bx bx-error-circle"></i><span>{{ $error }}</span>
                        </div>
                    @endforeach
                @endif

                @if (Session()->has('error'))
                    <div class="alert alert-danger mb-3">
                        <i class="bx bx-error-circle"></i><span>{{ Session('error') }}</span>
                    </div>
                @endif

                @if (Session()->has('success'))
                    <div class="alert alert-success mb-3">
                        <i class="bx bx-check-circle"></i><span>{{ Session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="form-horizontal">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="username"
                            placeholder="name@helptogethergroup.com" value="{{ old('email') }}" autocomplete="username"
                            autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group auth-pass-inputgroup">
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="Enter password" aria-label="Password" autocomplete="current-password">
                            <button class="btn btn-light" type="button" aria-label="Show password"
                                onclick="togglePasswordVisibility('password', 'password-addon1')">
                                <i class="mdi mdi-eye-outline" id="password-addon1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="remember-check" name="remember">
                        <label class="form-check-label" for="remember-check">Keep me signed in</label>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary waves-effect waves-light" type="submit">Log in</button>
                    </div>

                    {{-- <div class="mt-4 text-center">
                        <a href="{{ url('/admin/forgot-password') }}" class="text-muted"><i
                                class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                    </div> --}}
                </form>

                <div class="htg-auth__foot">
                    <script>document.write(new Date().getFullYear())</script> © Help Together Group.<br>
                    Design &amp; Develop by <a href="https://helptogethergroup.com/" target="_blank" rel="noopener">Help
                        Together Group</a>
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
