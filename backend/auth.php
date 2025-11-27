<?php
/**
 * backend/auth.php
 * Handler untuk register dan login
 */

require_once 'config.php';
require_once 'session.php';

// Validasi input
function validate_input($username, $email, $password) {
    $errors = [];
    
    if (empty($username) || strlen($username) < 3) {
        $errors[] = 'Username minimal 3 karakter';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid';
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }
    
    // Username hanya huruf, angka, underscore
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username hanya boleh berisi huruf, angka, dan underscore';
    }
    
    return $errors;
}

// REGISTER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Validasi
    $errors = validate_input($username, $email, $password);
    
    if ($password !== $confirm_password) {
        $errors[] = 'Password tidak cocok';
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: /pages/register.php');
        exit();
    }
    
    // Cek username sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['errors'] = ['Username atau email sudah terdaftar'];
        header('Location: /pages/register.php');
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert user baru
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Akun berhasil dibuat! Silakan login.';
        header('Location: /pages/login.php');
        exit();
    } else {
        $_SESSION['errors'] = ['Gagal membuat akun. Coba lagi.'];
        header('Location: /pages/register.php');
        exit();
    }
}

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $_SESSION['errors'] = ['Username dan password harus diisi'];
        header('Location: /pages/login.php');
        exit();
    }
    
    // Cari user
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            set_session($user['id'], $user['username']);
            $_SESSION['success'] = 'Login berhasil!';
            header('Location: /pages/dashboard.php');
            exit();
        }
    }
    
    // Login gagal
    $_SESSION['errors'] = ['Username/email atau password salah'];
    header('Location: /pages/login.php');
    exit();
}

// LOGOUT
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    destroy_session();
    header('Location: /pages/login.php');
    exit();
}
?>
