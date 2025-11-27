<?php
/**
 * index.php
 * Router utama
 */
require_once 'backend/config.php';
require_once 'backend/session.php';

if (check_session()) {
    // Sudah login, buka dashboard
    require_once 'pages/dashboard.php';
} else {
    // Belum login, buka login page
    require_once 'pages/login.php';
}
?>
