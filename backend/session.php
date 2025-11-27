<?php
/**
 * backend/session.php
 * Manajemen session dengan durasi 1 bulan
 */

session_start();

define('SESSION_TIMEOUT', 60 * 60 * 24 * 30); // 30 hari dalam detik

/**
 * Cek dan validasi session
 * Jika tidak aktif selama 30 hari, destroy session
 */
function check_session() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $current_time = time();
    $last_activity = isset($_SESSION['last_activity']) ? $_SESSION['last_activity'] : 0;
    
    // Jika session timeout (30 hari tidak aktif)
    if (($current_time - $last_activity) > SESSION_TIMEOUT) {
        destroy_session();
        return false;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = $current_time;
    
    return true;
}

/**
 * Set session setelah login sukses
 */
function set_session($user_id, $username) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    
    // Cookie untuk "remember me" selama 30 hari
    setcookie('user_session', session_id(), time() + SESSION_TIMEOUT, '/');
}

/**
 * Destroy session saat logout
 */
function destroy_session() {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
    setcookie('user_session', '', time() - 3600, '/');
}

/**
 * Redirect jika tidak ada session
 */
function require_login() {
    if (!check_session()) {
        header('Location: /');
        exit();
    }
}

/**
 * Check jika sudah login (tidak ada redirect)
 */
function require_logout() {
    // Fungsi ini hanya cek, tidak redirect
    // Redirect logic ada di index.php
    return !check_session();
}

// Cek session pada setiap load halaman
if (isset($_SESSION['user_id'])) {
    check_session();
}
?>
