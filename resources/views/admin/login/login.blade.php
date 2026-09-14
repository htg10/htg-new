<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <title>Log in | Help Together Group</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow" />

    <link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" />

    <style>
        *,
        *::before,
        *::after { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f0f2f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #333;
            padding: 24px 16px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, .06), 0 0 0 1px rgba(0, 0, 0, .03);
            overflow: hidden;
        }

        /* --- Header with lavender background --- */
        .login-header {
            background: #d8ddf8;
            padding: 36px 36px 42px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            position: relative;
        }

        .login-header__text h1 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 700;
            color: #4a52a0;
        }

        .login-header__text p {
            margin: 0;
            font-size: 13.5px;
            color: #6b71b0;
            line-height: 1.45;
        }

        .login-header__logo img {
            height: 90px;
            width: auto;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,.08));
        }

        /* --- Avatar that overlaps header/body --- */
        .login-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #eff0fa;
            border: 4px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: -38px auto 0;
            box-shadow: 0 2px 10px rgba(0,0,0,.06);
            z-index: 2;
        }

        .login-avatar img {
            height: 40px;
            width: auto;
        }

        /* --- Form body --- */
        .login-body {
            padding: 20px 36px 32px;
        }

        .login-body .form-group {
            margin-bottom: 18px;
        }

        .login-body .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        .login-body .form-control {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            font-size: 14px;
            color: #333;
            background: #f6f7fb;
            border: 1.5px solid #e0e3ef;
            border-radius: 8px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .login-body .form-control:focus {
            border-color: #7c85d0;
            box-shadow: 0 0 0 3px rgba(124, 133, 208, .15);
            background: #fff;
        }

        .login-body .form-control::placeholder {
            color: #a0a5c0;
        }

        /* Password wrapper */
        .password-wrap {
            position: relative;
        }

        .password-wrap .form-control {
            padding-right: 44px;
        }

        .password-toggle {
            position: absolute;
            right: 1px;
            top: 1px;
            bottom: 1px;
            width: 42px;
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9098b8;
            font-size: 18px;
            border-radius: 0 8px 8px 0;
            transition: color .15s;
        }

        .password-toggle:hover { color: #5a60a0; }

        /* Remember checkbox */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #6c72c4;
            cursor: pointer;
        }

        .remember-row label {
            font-size: 13px;
            color: #555;
            cursor: pointer;
            margin: 0;
            user-select: none;
        }

        /* Submit button */
        .btn-login {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            background: #6c72c4;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background .2s, transform .1s;
            letter-spacing: .01em;
        }

        .btn-login:hover { background: #5b60b5; }
        .btn-login:active { transform: scale(.99); }

        /* Alerts */
        .login-alert {
            padding: 10px 14px;
            font-size: 13px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-alert.danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .login-alert.success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12.5px;
            color: #8b8fb0;
            line-height: 1.7;
        }

        .login-footer a {
            color: #6c72c4;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-header { padding: 28px 24px 36px; }
            .login-body { padding: 20px 24px 28px; }
            .login-header__logo img { height: 64px; }
            .login-header__text h1 { font-size: 19px; }
        }
    </style>
</head>

<body>

    <div class="login-card">

        {{-- Lavender header with logo --}}
        <div class="login-header">
            <div class="login-header__text">
                <h1>Welcome Back !</h1>
                <p>Sign in with Help Together Group.</p>
            </div>
            <div class="login-header__logo">
                <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt="Help Together Group">
            </div>
        </div>

        {{-- Avatar overlapping header --}}
        <div class="login-avatar">
            <img src="{{ asset('assets/images/logo/htg_logo.png') }}" alt="">
        </div>

        {{-- Form --}}
        <div class="login-body">

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="login-alert danger">
                        <i class="bx bx-error-circle"></i><span>{{ $error }}</span>
                    </div>
                @endforeach
            @endif

            @if (Session()->has('error'))
                <div class="login-alert danger">
                    <i class="bx bx-error-circle"></i><span>{{ Session('error') }}</span>
                </div>
            @endif

            @if (Session()->has('success'))
                <div class="login-alert success">
                    <i class="bx bx-check-circle"></i><span>{{ Session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="email" name="email" class="form-control" id="username"
                        placeholder="info.helptogethergroup@gmail.com" value="{{ old('email') }}"
                        autocomplete="username" autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input type="password" name="password" class="form-control" id="password"
                            placeholder="Enter password" autocomplete="current-password">
                        <button type="button" class="password-toggle" aria-label="Show password"
                            onclick="togglePasswordVisibility()">
                            <i class="mdi mdi-eye-outline" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember-check" name="remember">
                    <label for="remember-check">Remember me</label>
                </div>

                <button type="submit" class="btn-login">Log In</button>
            </form>
        </div>

    </div>

    {{-- Footer outside the card --}}
    <div class="login-footer">
        &copy; <script>document.write(new Date().getFullYear())</script> Help Together Group.<br>
        Design &amp; Develop by <a href="https://helptogethergroup.com/" target="_blank" rel="noopener">Help Together Group</a>
    </div>

    <script>
        function togglePasswordVisibility() {
            const field = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('mdi-eye-outline', 'mdi-eye-off-outline');
            } else {
                field.type = 'password';
                icon.classList.replace('mdi-eye-off-outline', 'mdi-eye-outline');
            }
        }
    </script>

</body>

</html>
