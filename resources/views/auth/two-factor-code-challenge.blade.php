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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            background: url('assets/img/background-kict2.jpg') no-repeat center center fixed;
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
                                <img class="img-fluid mb-3" src="assets/img/ahglogobg.png" alt="Logo" style="max-height: 100px;">
                                <h2 class="fw-bold text-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Two-Factor Authentication
                                </h2>
                                <p class="text-muted">Please enter the 6-digit code sent to your email.</p>
                            </div>

                            @if (session('error'))
                            <div style="color:red;">
                                {{ session('error') }}
                            </div>
                            @endif

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('two-factor.store') }}">
                                @csrf

                                <!-- Email Input -->
                                <div class="mb-3">
                                    <label for="two_factor_code" class="form-label">
                                        <i class="bi bi-envelope me-2"></i>Two-Factor Code
                                    </label>
                                    <input
                                        id="two_factor_code" type="text" name="two_factor_code" class="form-control @error('two_factor_code') is-invalid @enderror" required autofocus>

                                    @error('two_factor_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>


                                <!-- Verify Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Verify
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
