<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function check_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
            die('Invalid CSRF token');
        }
    }
}

function make_slug(string $text): string {
    // 1) Convert to ASCII (drops accents, e.g. “é” → “e”)
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

    // 2) Lowercase
    $text = strtolower($text);

    // 3) Remove non-alphanumeric, replace spaces with hyphens
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    // 4) Trim hyphens from ends
    $text = trim($text, '-');

    return $text ?: 'n-a';
}

function isCurrentPage($link) {
        $currentPage = $_SERVER['PHP_SELF'];
        $link = str_replace(BASE_URL, '', $link);
        return (strpos($currentPage, $link) !== false);
    }

function set_flash_message(string $message, string $type = 'info') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Ensure session is started
    }
    $_SESSION['flash_messages'][] = ['message' => $message, 'type' => $type];
}

function display_flash_message() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Ensure session is started
    }
    if (isset($_SESSION['flash_messages'])) {
        foreach ($_SESSION['flash_messages'] as $flash_message) {
            $alert_type = htmlspecialchars($flash_message['type']);
            $message = htmlspecialchars($flash_message['message']);
            echo "<div class='alert alert-{$alert_type} alert-dismissible fade show' role='alert'>
                    {$message}
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
        }
        unset($_SESSION['flash_messages']); // Clear messages after displaying
    }
}

function redirect(string $url) {
    header("Location: " . $url);
    exit;
}

