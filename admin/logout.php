<?php
session_start();
session_destroy();
require_once __DIR__ . '/inc/security.php';
remember_me_clear();
header('Location: /portfolio_with_backend/admin/login.php');
exit;