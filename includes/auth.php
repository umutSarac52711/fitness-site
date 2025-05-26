<?php
function login($user) {
    $_SESSION['user'] = [
        'id'          => $user['id'],
        'username'    => $user['username'] ?? null,
        'full_name'   => $user['full_name'] ?? null,
        'email'       => $user['email'] ?? null,
        'role'        => $user['role']
    ];
}

function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

function logout() { 
    unset($_SESSION['user']); 
    // It's also good practice to destroy the session on logout
    // session_destroy(); 
    // And regenerate the session ID
    // session_regenerate_id(true);
}

function require_login() {
    if (empty($_SESSION['user'])) {
        // Store the intended URL before redirecting
        $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '/pages/auth/login.php');
        exit;
    }
}

function require_admin() {
    require_login();
    
    if ($_SESSION['user']['role'] !== 'admin') {
        // Optionally, redirect to an "access denied" page or home
        header('Location: ' . BASE_URL . '/index.php'); 
        exit;
    }
}
