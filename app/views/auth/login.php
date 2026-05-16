<?php
$appName = app_config('app_name', 'School SMS');
$errors = flash('errors', []);
$error = flash('error');
$success = flash('success');
$oldEmail = old('email');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title><?php echo e($appName); ?> - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-1: #0f172a;
            --bg-2: #1d4ed8;
            --bg-3: #f8fafc;
            --card: rgba(255, 255, 255, 0.92);
            --text: #0f172a;
            --muted: #64748b;
            --primary: #2563eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.28), transparent 28%),
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.22), transparent 24%),
                linear-gradient(135deg, var(--bg-1) 0%, var(--bg-2) 46%, var(--bg-3) 100%);
            color: var(--text);
        }

        .page-shell {
            min-height: 100vh;
            padding: 24px 12px;
        }

        .auth-card {
            border: 0;
            border-radius: 1.75rem;
            overflow: hidden;
            background: var(--card);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22);
            backdrop-filter: blur(10px);
        }

        .brand-panel {
            position: relative;
            color: #fff;
            background:
                linear-gradient(160deg, rgba(15, 23, 42, 0.96), rgba(37, 99, 235, 0.96)),
                url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            min-height: 100%;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.12), rgba(15, 23, 42, 0.46));
        }

        .brand-content {
            position: relative;
            z-index: 1;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem;
        }

        .brand-title {
            letter-spacing: -0.03em;
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #fff;
            font-size: .875rem;
            backdrop-filter: blur(6px);
        }

        .feature-list {
            display: grid;
            gap: .9rem;
            margin-top: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .85rem 1rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .feature-icon {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.14);
            flex: 0 0 auto;
        }

        .login-panel {
            padding: 2rem;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control,
        .input-group-text {
            border-radius: 0.95rem;
        }

        .form-control {
            padding: 0.85rem 1rem;
            border-color: #dbe3ef;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: rgba(37, 99, 235, 0.45);
            box-shadow: 0 0 0 .25rem rgba(37, 99, 235, 0.12);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: 0;
            border-radius: 0.95rem;
            padding: 0.9rem 1.1rem;
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.24);
        }

        .btn-primary:hover {
            filter: brightness(1.03);
        }

        .subtle-link {
            color: var(--primary);
            text-decoration: none;
        }

        .subtle-link:hover {
            text-decoration: underline;
        }

        .auth-footer {
            color: var(--muted);
            font-size: .9rem;
        }

        .alert {
            border-radius: 1rem;
        }

        .toggle-password {
            cursor: pointer;
            user-select: none;
        }

        @media (max-width: 767.98px) {
            .brand-panel {
                min-height: auto;
            }

            .brand-content,
            .login-panel {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="page-shell d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card auth-card">
                    <div class="row g-0">
                        <div class="col-lg-5 brand-panel">
                            <div class="brand-content">
                                <div>
                                    <span class="brand-pill mb-4">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        Secure School Access
                                    </span>

                                    <h1 class="brand-title display-6 fw-semibold mb-3">
                                        <?php echo e($appName); ?>
                                    </h1>

                                    <p class="lead mb-0" style="max-width: 28rem; opacity: .92;">
                                        Sign in to access Super Admin, Teacher, and Student dashboards.
                                        First login requires a password change for account security.
                                    </p>

                                    <div class="feature-list">
                                        <div class="feature-item">
                                            <div class="feature-icon"><i class="fa-solid fa-user-shield"></i></div>
                                            <div>
                                                <div class="fw-semibold">Role-based access</div>
                                                <div class="small opacity-75">Separate dashboard experience for each user type.</div>
                                            </div>
                                        </div>

                                        <div class="feature-item">
                                            <div class="feature-icon"><i class="fa-solid fa-lock"></i></div>
                                            <div>
                                                <div class="fw-semibold">Forced password change</div>
                                                <div class="small opacity-75">Temporary credentials are not left hanging around like school socks.</div>
                                            </div>
                                        </div>

                                        <div class="feature-item">
                                            <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
                                            <div>
                                                <div class="fw-semibold">Fast and lightweight</div>
                                                <div class="small opacity-75">Plain PHP, MySQL, and Bootstrap with clean structure.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="auth-footer mt-4">
                                    <div>Plain PHP • MySQL • Bootstrap</div>
                                    <div>Production-ready authentication flow</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7 login-panel">
                            <div class="mb-4">
                                <h2 class="h3 fw-bold mb-2">Welcome back</h2>
                                <p class="text-muted mb-0">Use your email and temporary password to continue.</p>
                            </div>

                            <?php if ($success): ?>
                                <div class="alert alert-success d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-circle-check mt-1"></i>
                                    <div><?php echo e($success); ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if ($error): ?>
                                <div class="alert alert-danger d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-triangle-exclamation mt-1"></i>
                                    <div><?php echo e($error); ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($errors) && is_array($errors)): ?>
                                <div class="alert alert-warning">
                                    <div class="fw-semibold mb-2">Please fix the following:</div>
                                    <ul class="mb-0 ps-3">
                                        <?php foreach ($errors as $message): ?>
                                            <li><?php echo e($message); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="<?php echo e(url('?page=login')); ?>" class="mt-3" novalidate>
                                <?php echo csrf_field(); ?>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-medium">Email address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fa-regular fa-envelope text-muted"></i>
                                        </span>
                                        <input
                                            type="email"
                                            name="email"
                                            id="email"
                                            class="form-control border-start-0"
                                            placeholder="name@example.com"
                                            value="<?php echo e($oldEmail); ?>"
                                            autocomplete="email"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="password" class="form-label fw-medium">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fa-solid fa-key text-muted"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="password"
                                            id="password"
                                            class="form-control border-start-0 border-end-0"
                                            placeholder="Enter your password"
                                            autocomplete="current-password"
                                            required
                                        >
                                        <button
                                            class="btn btn-outline-secondary toggle-password"
                                            type="button"
                                            id="togglePassword"
                                            aria-label="Show or hide password"
                                        >
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="rememberMe" disabled>
                                        <label class="form-check-label text-muted" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>
                                    <span class="small text-muted">Forgot password will come later</span>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa-solid fa-right-to-bracket me-2"></i>
                                        Sign in
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4 p-3 rounded-4 bg-light border">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="fa-solid fa-circle-info text-primary mt-1"></i>
                                    <div>
                                        <div class="fw-semibold mb-1">First login rule</div>
                                        <div class="text-muted small">
                                            After signing in with the temporary password, the system will force a password change before access is granted to the dashboard.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        const toggle = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const icon = toggle.querySelector('i');

        if (toggle && password) {
            toggle.addEventListener('click', function () {
                const isHidden = password.getAttribute('type') === 'password';
                password.setAttribute('type', isHidden ? 'text' : 'password');
                icon.className = isHidden ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            });
        }
    })();
</script>
</body>
</html>