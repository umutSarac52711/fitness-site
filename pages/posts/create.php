<?php
$page_title = "Create New Blog Post";
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/functions.php';

// Ensure user is logged in and is an admin or trainer
if (!is_logged_in() || !in_array($_SESSION['user']['role'], ['admin', 'trainer'])) {
    set_flash_message('You do not have permission to access this page.', 'danger');
    redirect(BASE_URL . '/pages/static/blog.php');
}

$user_id = $_SESSION['user']['id'];
$errors = [];
$title = '';
$content = '';
$category_id = null; // Optional: Add category functionality later

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $slug = make_slug($title); // Generate slug from title
    // $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null; // Optional

    // Validate input
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($content)) {
        $errors[] = 'Content is required.';
    }

    // Image upload handling (simplified)
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = BASE_PATH . '/uploads/blog_images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = uniqid('post_') . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = '/uploads/blog_images/' . $filename; // Store relative path
        } else {
            $errors[] = 'Failed to upload image.';
        }
    }

    if (empty($errors)) {
        try {
            // Add slug to the SQL query and parameters
            $stmt = $pdo->prepare('INSERT INTO posts (author_id, title, content, cover_img, slug, created_at, updated_at) VALUES (:user_id, :title, :content, :image_path, :slug, NOW(), NOW())');
            $stmt->execute([
                ':user_id' => $user_id,
                ':title' => $title,
                ':content' => $content,
                ':image_path' => $image_path,
                ':slug' => $slug // Bind the slug parameter
                // ':category_id' => $category_id // Optional
            ]);
            set_flash_message('Blog post created successfully!', 'success');
            redirect(BASE_URL . '/pages/static/blog.php');
        } catch (PDOException $e) {
            error_log("Error creating post: " . $e->getMessage());
            $errors[] = 'Failed to create post. Please try again. Possible duplicate slug.'; // Added note about duplicate slug
        }
    }
}

require_once BASE_PATH . '/templates/file-start.php';
require_once BASE_PATH . '/templates/header.php';
require_once BASE_PATH . '/templates/breadcrumb.php';
?>

<section class="account-section"> <!-- Changed class -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="account-content"> <!-- Changed structure -->
                    <h3 class="content-title">Create New Blog Post</h3>
                    <?php display_flash_message(); ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data" class="account-form">
                        <div class="form-group">
                            <label for="title">Post Title</label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <label for="content">Content</label>
                            <textarea style="resize:vertical; width: 100%;" id="content" name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Featured Image (Optional)</label>
                            <input type="file" id="image" name="image" class="form-control-file">
                        </div>
                        
                        <?php /* Optional: Category Selection
                        <div class="form-group">
                            <label for="category_id">Category (Optional)</label>
                            <select name="category_id" id="category_id">
                                <option value="">Select Category</option>
                                <?php
                                // Example: Fetch categories from DB
                                // $cat_stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
                                // while ($category = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {
                                //     echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                                // }
                                ?>
                            </select>
                        </div>
                        */ ?>

                        <button type="submit" class="primary-btn account-btn">Publish Post</button> <!-- Changed class -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once BASE_PATH . '/templates/footer.php';
require_once BASE_PATH . '/templates/script.php';
?>
