<?php
/**
 * index.php
 * Router utama - hanya untuk root path
 */
require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/session.php';

if (check_session()) {
    header('Location: /pages/dashboard.php');
} else {
    header('Location: /pages/login.php');
}
exit();
?>
