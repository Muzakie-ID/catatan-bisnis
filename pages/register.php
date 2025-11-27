<?php
/**
 * pages/register.php
 * Halaman registrasi
 */
require_once '../backend/config.php';
require_once '../backend/session.php';

require_logout(); // Jika sudah login, arahkan ke dashboard
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Manajemen Stok HP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .register-card {
            width: 100%;
            max-width: 450px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: slideIn 0.5s ease;
        }

        .register-header {
            background: white;
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 2px solid #f0f0f0;
        }

        .register-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2563eb;
            margin: 0.5rem 0;
        }

        .register-header p {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0;
        }

        .register-body {
            background: white;
            padding: 2rem 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
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

        .form-text {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .btn-register {
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
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .register-footer {
            background: #f9fafb;
            padding: 1.5rem;
            text-align: center;
            border-radius: 0 0 12px 12px;
        }

        .register-footer p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .register-footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .register-card {
                max-width: 100%;
                margin: 1rem;
                border-radius: 8px;
            }

            .register-header {
                padding: 1.5rem 1rem 1rem;
            }

            .register-header h1 {
                font-size: 1.5rem;
            }

            .register-body {
                padding: 1.5rem 1rem;
            }

            .register-footer {
                padding: 1rem;
            }

            .form-group {
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>📱 StokHP</h1>
                <p>Buat Akun Baru</p>
            </div>

            <div class="register-body">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<div>• ' . htmlspecialchars($error) . '</div>';
                        }
                        unset($_SESSION['errors']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Register -->
                <form method="POST" action="../backend/auth.php">
                    <input type="hidden" name="action" value="register">

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" 
                               placeholder="Masukkan username" 
                               required autofocus>
                        <small class="form-text">Minimal 3 karakter, hanya huruf, angka, dan underscore</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" 
                               placeholder="Masukkan email Anda" 
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" 
                               placeholder="Masukkan password" 
                               required>
                        <small class="form-text">Minimal 6 karakter</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="confirm_password" 
                               placeholder="Konfirmasi password" 
                               required>
                    </div>

                    <button type="submit" class="btn-register">Daftar</button>
                </form>
            </div>

            <div class="register-footer">
                <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
