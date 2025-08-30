<?php
/**
 * Admin: Manage Categories
 *
 * Handles CRUD operations for medicine categories.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

$pdo = db_connect();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

$category = null;
$error = '';

// Handle POST requests for add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $slug = slugify($name);

    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        if ($action === 'add') {
            $sql = "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $slug, $description]);
            redirect('/admin/categories.php');
        } elseif ($action === 'edit' && $id) {
            $sql = "UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $slug, $description, $id]);
            redirect('/admin/categories.php');
        }
    }
}

// Handle delete action
if ($action === 'delete' && $id) {
    // Add CSRF token check here in a real application
    try {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // If the category is in use, the foreign key constraint will prevent deletion.
        // We should handle this gracefully.
        $error = "Cannot delete category as it is currently assigned to one or more medicines.";
        // To show the error, we'll fall through to the list view.
        $action = 'list';
    }

    if(empty($error)) redirect('/admin/categories.php');
}

if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
}

$page_title = 'Manage Categories';
include __DIR__ . '/../templates/header.php';
?>

<h1>Manage Categories</h1>

<?php if ($error): ?>
    <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <h2><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Category</h2>
    <form action="<?php echo BASE_URL; ?>/admin/categories.php?action=<?php echo $action; ?><?php if($id) echo '&id='.$id; ?>" method="POST" style="max-width: 600px;">
        <div class="form-group">
            <label for="name">Category Name *</label>
            <input type="text" name="name" id="name" value="<?php echo esc($category['name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"><?php echo esc($category['description'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Category</button>
        <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php endif; ?>


<?php if ($action === 'list'): ?>
    <a href="<?php echo BASE_URL; ?>/admin/categories.php?action=add" class="btn btn-primary mb-1">Add New Category</a>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
            while ($row = $stmt->fetch()): ?>
                <tr>
                    <td><?php echo esc($row['name']); ?></td>
                    <td><?php echo esc($row['description']); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/admin/categories.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                        <a href="<?php echo BASE_URL; ?>/admin/categories.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>


<?php
include __DIR__ . '/../templates/footer.php';
?>
