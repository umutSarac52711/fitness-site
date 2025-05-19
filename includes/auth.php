<?php
function login($user) {
    $_SESSION['user'] = [
        'id'   => $user['id'],
        'name' => $user['name'],
        'role' => $user['role']
    ];
}
function logout() { unset($_SESSION['user']); }

function require_login() {
    if (empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}
function require_admin() {
    require_login();
    if ($_SESSION['user']['role'] !== 'admin') {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
