<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AHG HR Portal Login</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/ahglogonobg.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #0056b3;
            --body-bg: #f4f4f4;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: url('assets/img/menarahidayah.png') no-repeat center center fixed;
            background-size: cover;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <img class="img-fluid mb-3" src="assets/img/ahglogobg.png" alt="Logo"
                                    style="max-height: 100px;">
                                <h2 class="fw-bold text-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </h2>
                                <p class="text-muted">Welcome to AHG HR Portal! Please enter your credentials.</p>
                            </div>

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Email Input -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Password Input -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-2"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                            aria-pressed="false" aria-label="Show password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Remember Me and Forgot Password -->
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Remember Me
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="text-primary">
                                        Forgot Password?
                                    </a>
                                </div>

                                <!-- Login Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-3">
                                If you don't have an account, please contact HR.
                            
                                <small class="text-muted d-block mt-4">
                                    &copy; {{ date('Y') }} AHG HR Portal. All rights reserved. 
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional, for future interactions) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#password');

            if (!toggleBtn || !passwordInput) return;

            toggleBtn.addEventListener('click', function() {
                const isText = passwordInput.type === 'text';
                passwordInput.type = isText ? 'password' : 'text';
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
                this.setAttribute('aria-pressed', (!isText).toString());
                this.setAttribute('aria-label', isText ? 'Show password' : 'Hide password');
            });
        });
    </script>
</body>

</html>

{{-- <x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout> --}}
