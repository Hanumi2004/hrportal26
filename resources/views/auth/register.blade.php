<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AHG HR Portal Registration</title>

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
                                    <i class="bi bi-person-plus-fill me-2"></i>Register
                                </h2>
                                <p class="text-muted">Welcome to AHG HR Portal! Please create your account.</p>
                            </div>

                            <!-- Validation Errors -->
                            <x-validation-errors class="mb-4" />

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- Employee ID -->
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">
                                        <i class="bi bi-person-badge me-2"></i>Employee ID
                                    </label>
                                    <input id="employee_id"
                                        class="form-control @error('employee_id') is-invalid @enderror" type="text"
                                        name="employee_id" value="{{ old('employee_id') }}" required autofocus
                                        autocomplete="employee_id">
                                    @error('employee_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        <i class="bi bi-person me-2"></i>Name
                                    </label>
                                    <input id="name" class="form-control @error('name') is-invalid @enderror"
                                        type="text" name="name" value="{{ old('name') }}" required
                                        autocomplete="name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope me-2"></i>Email Address
                                    </label>
                                    <input id="email" class="form-control @error('email') is-invalid @enderror"
                                        type="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="username">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-2"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <input id="password"
                                            class="form-control @error('password') is-invalid @enderror" type="password"
                                            name="password" required autocomplete="new-password">
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

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        <i class="bi bi-lock-fill me-2"></i>Confirm Password
                                    </label>
                                    <input id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        type="password" name="password_confirmation" required
                                        autocomplete="new-password">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Terms and Privacy Policy -->
                                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                                    <div class="mb-3">
                                        <label for="terms" class="form-label">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="terms"
                                                    id="terms" required>
                                                <label class="form-check-label" for="terms">
                                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                                        'terms_of_service' =>
                                                            '<a target="_blank" href="' .
                                                            route('terms.show') .
                                                            '" class="underline text-primary">' .
                                                            __('Terms of Service') .
                                                            '</a>',
                                                        'privacy_policy' =>
                                                            '<a target="_blank" href="' .
                                                            route('policy.show') .
                                                            '" class="underline text-primary">' .
                                                            __('Privacy Policy') .
                                                            '</a>',
                                                    ]) !!}
                                                </label>
                                            </div>
                                        </label>
                                    </div>
                                @endif

                                <!-- Register Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-person-plus-fill me-2"></i>Register
                                    </button>
                                </div>
                            </form>

                            <!-- Login Link -->
                            <div class="text-center mt-3">
                                Already registered?
                                <a href="{{ route('login') }}" class="text-primary">
                                    Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
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

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout> --}}
