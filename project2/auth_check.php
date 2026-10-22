<?php
// auth_check.php - include at the very top of every protected page (e.g. manage.php)
session_start();
if (empty($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
