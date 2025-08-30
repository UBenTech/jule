<?php
/**
 * Group Page
 *
 * Lists all medicines belonging to a specific group name.
 * The group is identified by its name in the URL.
 *
 * URL: /group/{group-name}
 * Example: /group/group%20three
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$group_name = $_GET['group'] ?? null;

if (!$group_name) {
    header("Location: /");
    exit();
}

$pdo = db_connect();
// The group name from the URL might be URL-encoded (e.g., "group%20three").
// $_GET automatically decodes it, so we can use it directly.
$medicines = get_medicines($pdo, ['group_name' => $group_name]);

$page_title = 'Group: ' . esc($group_name);
include 'templates/header.php';
?>

<h1>Medicines in Group: "<?php echo esc($group_name); ?>"</h1>

<?php if (empty($medicines)): ?>
    <p>There are no medicines available in this group.</p>
<?php else: ?>
    <div class="medicine-grid">
        <?php foreach ($medicines as $medicine): ?>
            <?php include 'templates/med_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
include 'templates/footer.php';
?>
