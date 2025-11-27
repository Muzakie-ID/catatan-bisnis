<?php
/**
 * pages/login.php
 * Halaman login
 */
require_once __DIR__ . '/../backend/config.php';
require_once __DIR__ . '/../backend/session.php';

require_logout(); // Jika sudah login, arahkan ke dashboard
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Stok HP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: slideIn 0.5s ease;
        }

        .login-header {
            background: white;
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 2px solid #f0f0f0;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2563eb;
            margin: 0.5rem 0;
        }

        .login-header p {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0;
        }

        .login-body {
            background: white;
            padding: 2rem 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            font-weight: 600;
            font-size: 1rem;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .login-footer {
            background: #f9fafb;
            padding: 1.5rem;
            text-align: center;
            border-radius: 0 0 12px 12px;
        }

        .login-footer p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .login-footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .login-card {
                max-width: 100%;
                margin: 1rem;
                border-radius: 8px;
            }

            .login-header {
                padding: 1.5rem 1rem 1rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .login-body {
                padding: 1.5rem 1rem;
            }

            .login-footer {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>📱 StokHP</h1>
                <p>Manajemen Stok HP Anda</p>
            </div>

            <div class="login-body">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<div>• ' . htmlspecialchars($error) . '</div>';
                        }
                        unset($_SESSION['errors']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        ✓ <?php echo htmlspecialchars($_SESSION['success']); ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Form Login -->
                <form method="POST" action="../backend/auth.php">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label class="form-label">Username atau Email</label>
                        <input type="text" class="form-control" name="username" 
                               placeholder="Masukkan username atau email" 
                               required autofocus>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" 
                               placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn-login">Masuk</button>
                </form>
            </div>

            <div class="login-footer">
                <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
