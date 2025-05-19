<?php
function require_admin() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
