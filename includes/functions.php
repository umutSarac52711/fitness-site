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
function get_latest_posts(int $limit = 5): array
{
    global $pdo;
    $debug_info = [];

    if (!$pdo instanceof PDO) {
        $debug_info[] = "get_latest_posts: PDO connection is not available or not a PDO instance.";
        return ['data' => [], 'debug' => $debug_info];
    }

    // Modified SQL to include comment_count
    $sql = "SELECT p.id, p.title, p.slug, p.created_at, p.cover_img, COUNT(c.id) AS comment_count
            FROM posts p
            LEFT JOIN comments c ON p.id = c.post_id
            GROUP BY p.id, p.title, p.slug, p.created_at, p.cover_img
            ORDER BY p.created_at DESC
            LIMIT :lim";
    $debug_info[] = "SQL: " . htmlspecialchars($sql);

    try {
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            $debug_info[] = "PDO::prepare() failed. PDO ErrorInfo: " . htmlspecialchars(print_r($pdo->errorInfo(), true));
            return ['data' => [], 'debug' => $debug_info];
        }
        $debug_info[] = "Statement prepared.";

        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $debug_info[] = "Bound :lim with value: $limit.";
        
        $execute_result = $stmt->execute();

        if ($execute_result) {
            $debug_info[] = "Statement executed successfully.";
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $debug_info[] = "fetchAll(PDO::FETCH_ASSOC) called. Number of posts fetched: " . count($posts) . ".";
            $rowCount = $stmt->rowCount();
            $debug_info[] = "PDOStatement::rowCount() after fetchAll: " . $rowCount . ". (Note: rowCount behavior can vary for SELECT).";

            if (count($posts) === 0 && $rowCount > 0) {
                $debug_info[] = "Warning: rowCount was $rowCount, but fetchAll returned 0 posts.";
            }
            if (empty($posts)) {
                 $debug_info[] = "Posts array is empty after fetchAll.";
            }

            return ['data' => $posts ?: [], 'debug' => $debug_info];
        } else {
            $debug_info[] = "get_latest_posts: Statement execution failed.";
            $debug_info[] = "Statement ErrorInfo: " . htmlspecialchars(print_r($stmt->errorInfo(), true));
            return ['data' => [], 'debug' => $debug_info];
        }

    } catch (PDOException $e) {
        $debug_info[] = "PDOException in get_latest_posts: " . htmlspecialchars($e->getMessage());
        return ['data' => [], 'debug' => $debug_info];
    } catch (Throwable $e) {
        $debug_info[] = "General Throwable in get_latest_posts: " . htmlspecialchars($e->getMessage());
        return ['data' => [], 'debug' => $debug_info];
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
    $_SESSION['flash_messages'][] = [
        'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
        'type' => htmlspecialchars($type, ENT_QUOTES, 'UTF-8')
    ];
}

function display_flash_message() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Ensure session is started
    }
    if (isset($_SESSION['flash_messages'])) {
        foreach ($_SESSION['flash_messages'] as $flash_message) {
            $alert_type = htmlspecialchars($flash_message['type']);
            $message = htmlspecialchars($flash_message['message']);
            // Changed data-bs-dismiss to data-dismiss for broader compatibility (e.g., Bootstrap 4)
            echo "<div class='alert alert-{$alert_type} alert-dismissible fade show' role='alert'>
                    {$message}
                    <button type='button' class='btn-close' data-dismiss='alert' aria-label='Close'></button>
                  </div>";
        }
        unset($_SESSION['flash_messages']); // Clear messages after displaying
    }
}

function redirect(string $url) {
    header("Location: " . $url);
    exit;
}

function get_trainers(): array
{
    global $pdo; // Assuming $pdo is your global PDO connection object from config.php

    if (!$pdo) {
        // Log error or handle missing PDO connection
        error_log("get_trainers: PDO connection is not available.");
        return []; // Return empty array or throw an exception
    }

    $sql = "SELECT id, full_name, profile_picture
            FROM users
            WHERE role = 'trainer' AND is_active = 1
            ORDER BY full_name ASC"; // Added ORDER BY for consistent ordering

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log database error
        error_log("Error in get_trainers: " . $e->getMessage());
        return []; // Return empty array on error
    }
}


function file_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder for uploads.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk. Check permissions.';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload.';
        default:
            return 'Unknown upload error.';
    }
}