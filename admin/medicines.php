<?php
/**
 * Admin: Manage Medicines
 *
 * Handles CRUD (Create, Read, Update, Delete) operations for medicines.
 * Includes image upload functionality for thumbnails.
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

$medicine = null;
$errors = [];

// Handle POST requests for add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category_id'];
    $manufacturer_id = $_POST['manufacturer_id'] ?: null;
    $price = $_POST['price'];
    $quantity_in_stock = $_POST['quantity_in_stock'];
    $min_stock = $_POST['min_stock'];
    $manufacture_date = $_POST['manufacture_date'] ?: null;
    $expiry_date = $_POST['expiry_date'];
    $batch_number = trim($_POST['batch_number']) ?: null;
    $group_name = trim($_POST['group_name']) ?: null;
    $slug = slugify($name);

    // Basic validation
    if (empty($name) || empty($description) || empty($category_id) || empty($price) || empty($expiry_date)) {
        $errors[] = 'Please fill all required fields.';
    }

    // Handle file upload
    $thumbnail_url = $_POST['existing_thumbnail'] ?? null;
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/medicines/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['thumbnail']['type'];

        if (in_array($file_type, $allowed_types)) {
            $filename = sanitize_filename($_FILES['thumbnail']['name']);
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_dir . $filename)) {
                $thumbnail_url = 'uploads/medicines/' . $filename;
            } else {
                $errors[] = 'Failed to move uploaded file.';
            }
        } else {
            $errors[] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        }
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $sql = "INSERT INTO medicines (name, slug, description, category_id, manufacturer_id, price, quantity_in_stock, min_stock, manufacture_date, expiry_date, batch_number, group_name, thumbnail_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $slug, $description, $category_id, $manufacturer_id, $price, $quantity_in_stock, $min_stock, $manufacture_date, $expiry_date, $batch_number, $group_name, $thumbnail_url]);
            redirect('medicines.php');
        } elseif ($action === 'edit' && $id) {
            $sql = "UPDATE medicines SET name=?, slug=?, description=?, category_id=?, manufacturer_id=?, price=?, quantity_in_stock=?, min_stock=?, manufacture_date=?, expiry_date=?, batch_number=?, group_name=?, thumbnail_url=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $slug, $description, $category_id, $manufacturer_id, $price, $quantity_in_stock, $min_stock, $manufacture_date, $expiry_date, $batch_number, $group_name, $thumbnail_url, $id]);
            redirect('medicines.php');
        }
    }
}

if ($action === 'edit' && $id) {
    $medicine = get_medicine_by_id($pdo, $id);
} elseif ($action === 'delete' && $id) {
    // Note: Deletion is handled by api.php for AJAX, but a fallback can be here.
    // For simplicity, we rely on the AJAX deletion from the list view.
    redirect('medicines.php');
}

$page_title = 'Manage Medicines';
include __DIR__ . '/../templates/header.php';

?>

<h1>Manage Medicines</h1>

<?php if ($action === 'list'): ?>
    <a href="medicines.php?action=add" class="btn btn-primary mb-1">Add New Medicine</a>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Expires</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $medicines = get_medicines($pdo);
            foreach ($medicines as $med): ?>
                <tr>
                    <td><img src="/<?php echo esc($med['thumbnail_url']); ?>" alt="<?php echo esc($med['name']); ?>" width="50"></td>
                    <td><?php echo esc($med['name']); ?></td>
                    <td><?php echo esc($med['category_name']); ?></td>
                    <td><?php echo format_price($med['price']); ?></td>
                    <td><?php echo esc($med['quantity_in_stock']); ?></td>
                    <td><?php echo format_date($med['expiry_date']); ?></td>
                    <td>
                        <a href="medicines.php?action=edit&id=<?php echo $med['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm btn-delete-medicine" data-medicine-id="<?php echo $med['id']; ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <h2><?php echo $action === 'add' ? 'Add New' : 'Edit'; ?> Medicine</h2>

    <?php if (!empty($errors)): ?>
        <div style="color: red; margin-bottom: 1rem;">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <form action="medicines.php?action=<?php echo $action; ?><?php if($id) echo '&id='.$id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" name="name" id="name" value="<?php echo esc($medicine['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description *</label>
            <textarea name="description" id="description" rows="5" required><?php echo esc($medicine['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="category_id">Category *</label>
            <select name="category_id" id="category_id" required>
                <?php
                $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
                foreach ($categories as $cat) {
                    $selected = isset($medicine['category_id']) && $cat['id'] == $medicine['category_id'] ? 'selected' : '';
                    echo "<option value='{$cat['id']}' {$selected}>" . esc($cat['name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="manufacturer_id">Manufacturer</label>
            <select name="manufacturer_id" id="manufacturer_id">
                <option value="">None</option>
                <?php
                $manufacturers = $pdo->query("SELECT id, name FROM manufacturers ORDER BY name")->fetchAll();
                foreach ($manufacturers as $man) {
                    $selected = isset($medicine['manufacturer_id']) && $man['id'] == $medicine['manufacturer_id'] ? 'selected' : '';
                    echo "<option value='{$man['id']}' {$selected}>" . esc($man['name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="price">Price *</label>
            <input type="number" step="0.01" name="price" id="price" value="<?php echo esc($medicine['price'] ?? '0.00'); ?>" required>
        </div>

        <div class="form-group">
            <label for="quantity_in_stock">Quantity in Stock *</label>
            <input type="number" name="quantity_in_stock" id="quantity_in_stock" value="<?php echo esc($medicine['quantity_in_stock'] ?? '0'); ?>" required>
        </div>

        <div class="form-group">
            <label for="min_stock">Minimum Stock Level *</label>
            <input type="number" name="min_stock" id="min_stock" value="<?php echo esc($medicine['min_stock'] ?? '10'); ?>" required>
        </div>

        <div class="form-group">
            <label for="manufacture_date">Manufacture Date</label>
            <input type="date" name="manufacture_date" id="manufacture_date" value="<?php echo esc($medicine['manufacture_date'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="expiry_date">Expiry Date *</label>
            <input type="date" name="expiry_date" id="expiry_date" value="<?php echo esc($medicine['expiry_date'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="batch_number">Batch Number</label>
            <input type="text" name="batch_number" id="batch_number" value="<?php echo esc($medicine['batch_number'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="group_name">Group Name</label>
            <input type="text" name="group_name" id="group_name" value="<?php echo esc($medicine['group_name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="thumbnail">Thumbnail Image</label>
            <input type="file" name="thumbnail" id="thumbnail">
            <?php if (isset($medicine['thumbnail_url'])): ?>
                <input type="hidden" name="existing_thumbnail" value="<?php echo esc($medicine['thumbnail_url']); ?>">
                <img src="/<?php echo esc($medicine['thumbnail_url']); ?>" width="100" class="mt-1">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Save Medicine</button>
        <a href="medicines.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php endif; ?>

<?php
include __DIR__ . '/../templates/footer.php';
?>
