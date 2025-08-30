<?php
/**
 * Wishlist Page
 *
 * Displays items the user has added to their wishlist.
 * Works for both logged-in users and guests (using PHP sessions).
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pdo = db_connect();

// Require user to be logged in to access this page
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = '/wishlist.php';
    redirect('/login.php');
}

$user_id = $_SESSION['user_id'];
$wishlist_items = [];

$sql = "
    SELECT m.*
    FROM medicines m
    JOIN wishlists w ON m.id = w.medicine_id
    WHERE w.user_id = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$wishlist_items = $stmt->fetchAll();

$page_title = 'My Wishlist';
include 'templates/header.php';
?>

<h1>My Wishlist</h1>

<?php if (empty($wishlist_items)): ?>
    <p>Your wishlist is empty. You can add items to your wishlist from any medicine page.</p>
<?php else: ?>
    <div class="medicine-grid">
        <?php foreach ($wishlist_items as $medicine): ?>
            <?php include 'templates/med_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
include 'templates/footer.php';
?>
