<?php
$appName = $brandTitle ?? app_config('app_name', 'School SMS');
$pageTitle = $pageTitle ?? 'Login';
$brandLead = $brandLead ?? '';
$brandNote = $brandNote ?? '';
$brandFeatures = $brandFeatures ?? [];
$bgImage = asset('assets/images/skt.jpeg');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($appName); ?> - <?php echo e($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --panel-bg: rgba(255, 255, 255, 0.92);
            --text-dark: #0f172a;
            --muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.82), rgba(29, 78, 216, 0.62)),
                url('<?php echo e($bgImage); ?>') center center / cover no-repeat fixed;
            color: var(--text-dark);
        }

        .auth-shell {
            min-height: 100vh;
            padding: 24px 12px;
            display: flex;
            align-items: center;
        }

        .auth-card {
            border: 0;
            border-radius: 1.75rem;
            overflow: hidden;
            background: var(--panel-bg);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
            backdrop-filter: blur(10px);
        }

        .brand-panel {
            position: relative;
            color: #fff;
            background: linear-gradient(160deg, rgba(15, 23, 42, 0.94), rgba(37, 99, 235, 0.90));
            min-height: 100%;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.10), rgba(15, 23, 42, 0.45));
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

        .auth-panel {
            padding: 2rem;
            background: rgba(255, 255, 255, 0.82);
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

        .alert {
            border-radius: 1rem;
        }

        .brand-muted {
            color: rgba(255, 255, 255, 0.78);
        }

        @media (max-width: 767.98px) {
            .brand-content,
            .auth-panel {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="auth-shell">
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
                                        School Management System
                                    </span>

                                    <h1 class="display-6 fw-semibold mb-3"><?php echo e($appName); ?></h1>
                                    <p class="lead mb-0 brand-muted">
                                        <?php echo e($brandLead); ?>
                                    </p>

                                    <div class="feature-list">
                                        <?php foreach ($brandFeatures as $feature): ?>
                                            <div class="feature-item">
                                                <div class="feature-icon">
                                                    <i class="<?php echo e($feature['icon'] ?? 'fa-solid fa-circle'); ?>"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold"><?php echo e($feature['title'] ?? 'Feature'); ?></div>
                                                    <div class="small brand-muted"><?php echo e($feature['text'] ?? ''); ?></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="small brand-muted mt-4">
                                    <?php echo e($brandNote); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7 auth-panel">
                            <?php echo $content; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>