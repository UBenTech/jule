<?php
/**
 * User Account Dashboard
 *
 * This page serves as the main dashboard for logged-in users,
 * displaying their booking history and links to manage their account.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Protect this page: only logged-in users allowed.
if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}
// Also, admins should be in their own dashboard.
if (is_admin()) {
    redirect('/admin/');
}

$pdo = db_connect();
$user_id = $_SESSION['user_id'];

// Fetch user's bookings
$stmt = $pdo->prepare("
    SELECT b.*, m.name as medicine_name, m.slug as medicine_slug
    FROM bookings b
    JOIN medicines m ON b.medicine_id = m.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

$page_title = 'My Account';
include 'templates/header.php';
?>

<h1>Welcome, <?php echo esc($_SESSION['username']); ?>!</h1>
<p>This is your account dashboard. Here you can view your booking history and manage your account details.</p>

<div style="margin-bottom: 2rem;">
    <a href="<?php echo BASE_URL; ?>/edit-profile.php" class="btn btn-secondary">Edit Profile / Change Password</a>
    <a href="<?php echo BASE_URL; ?>/wishlist.php" class="btn btn-primary">View My Wishlist</a>
</div>


<h2>My Booking History</h2>

<?php if (empty($bookings)): ?>
    <p>You have not made any bookings yet. <a href="<?php echo BASE_URL; ?>/">Browse our medicines</a> to get started.</p>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Booking Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo format_date($booking['created_at']); ?></td>
                    <td><a href="<?php echo BASE_URL; ?>/medicine/<?php echo esc($booking['medicine_slug']); ?>"><?php echo esc($booking['medicine_name']); ?></a></td>
                    <td><?php echo esc($booking['quantity']); ?></td>
                    <td><?php echo esc(ucfirst($booking['booking_type'])); ?></td>
                    <td>
                         <span class="badge" style="background-color:
                            <?php
                                switch($booking['status']) {
                                    case 'pending': echo 'orange'; break;
                                    case 'confirmed': echo 'blue'; break;
                                    case 'completed': echo 'green'; break;
                                    case 'cancelled': echo 'red'; break;
                                    default: echo 'gray';
                                }
                            ?>; color: white;">
                            <?php echo ucfirst(esc($booking['status'])); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<?php
include 'templates/footer.php';
?>
