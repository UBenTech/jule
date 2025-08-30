<?php
/**
 * Admin: Manage Bookings
 *
 * Allows administrators to view all customer bookings and update their status.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

$pdo = db_connect();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];
    $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $booking_id]);
        // Optional: Add a success message to the session to show after redirect
    }
    // Redirect to the same page to prevent form resubmission
    redirect('/admin/bookings.php');
}


// Fetch all bookings with medicine details
$bookings = $pdo->query("
    SELECT b.*, m.name as medicine_name
    FROM bookings b
    JOIN medicines m ON b.medicine_id = m.id
    ORDER BY b.created_at DESC
")->fetchAll();

$page_title = 'Manage Bookings';
include __DIR__ . '/../templates/admin_header.php';
?>

<h1>Manage Customer Bookings</h1>
<p>View all incoming bookings and update their status as you process them.</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Customer Name</th>
            <th>Contact</th>
            <th>Medicine Booked</th>
            <th>Qty</th>
            <th>Type</th>
            <th>Notes</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($bookings)): ?>
            <tr>
                <td colspan="9" class="text-center">No bookings found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo format_date($booking['created_at']); ?></td>
                    <td><?php echo esc($booking['name']); ?></td>
                    <td>
                        <?php echo esc($booking['phone']); ?><br>
                        <?php echo esc($booking['email']); ?>
                    </td>
                    <td><?php echo esc($booking['medicine_name']); ?></td>
                    <td><?php echo esc($booking['quantity']); ?></td>
                    <td><?php echo esc($booking['booking_type']); ?></td>
                    <td><?php echo esc($booking['notes']); ?></td>
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
                    <td>
                        <form action="<?php echo BASE_URL; ?>/admin/bookings.php" method="POST">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php if($booking['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="confirmed" <?php if($booking['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                <option value="completed" <?php if($booking['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                <option value="cancelled" <?php if($booking['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
include __DIR__ . '/../templates/admin_footer.php';
?>
