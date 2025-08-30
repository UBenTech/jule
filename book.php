<?php
/**
 * Booking Page
 *
 * Provides a form for users to book a medicine pickup or consultation.
 * The form submission is handled via AJAX by assets/js/app.js.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pdo = db_connect();
$medicine_id = $_GET['medicine_id'] ?? null;
$medicine = null;
if ($medicine_id) {
    $medicine = get_medicine_by_id($pdo, $medicine_id);
}

$page_title = 'Book a Medicine or Consultation';
include 'templates/header.php';
?>

<h1><?php echo $page_title; ?></h1>

<?php if ($medicine): ?>
    <p>You are booking for: <strong><?php echo esc($medicine['name']); ?></strong>. Please fill out the form below.</p>
<?php else: ?>
    <p>Please fill out the form below to book a medicine or request a consultation.</p>
<?php endif; ?>

<form id="booking-form" style="max-width: 600px;">
    <div class="form-group">
        <label for="medicine_id">Medicine</label>
        <select id="medicine_id" name="medicine_id" required>
            <option value="">Select a medicine...</option>
            <?php
            // Fetch all medicines for the dropdown
            $all_medicines = get_medicines($pdo);
            foreach ($all_medicines as $med_item) {
                $selected = ($med_item['id'] == $medicine_id) ? 'selected' : '';
                echo "<option value=\"" . esc($med_item['id']) . "\" $selected>" . esc($med_item['name']) . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required>
    </div>

    <div class="form-group">
        <label for="email">Email Address (Optional)</label>
        <input type="email" id="email" name="email">
    </div>

    <div class="form-group">
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" min="1" value="1" required>
    </div>

    <div class="form-group">
        <label for="booking_type">Booking Type</label>
        <select id="booking_type" name="booking_type" required>
            <option value="pickup">Pickup</option>
            <option value="consultation">Consultation Request</option>
        </select>
    </div>

    <div class="form-group">
        <label for="notes">Notes (Optional)</label>
        <textarea id="notes" name="notes" rows="4"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit Booking</button>

    <div id="booking-response" class="mt-1"></div>
</form>

<?php
include 'templates/footer.php';
?>
