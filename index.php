<?php
/**
 * index.php
 * Redirect ke login atau dashboard
 */
require_once 'backend/config.php';
require_once 'backend/session.php';

if (check_session()) {
    header('Location: pages/dashboard.php');
} else {
    header('Location: pages/login.php');
}
exit();
?>
