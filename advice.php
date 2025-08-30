<?php
/**
 * Advice Request Page
 *
 * Provides a form for users to submit questions or requests for advice.
 * Submissions are stored in the database for an admin to review.
 *
 * To edit brand color or DB settings, see config.php.
 */

require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pdo = db_connect();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $request_message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($request_message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } else {
        try {
            $sql = "INSERT INTO advice_requests (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $phone, $subject, $request_message]);
            $message = 'Your request has been submitted successfully! An administrator will review it shortly.';
            // In a real application, you would also trigger an email notification here.
        } catch (PDOException $e) {
            $error = 'We could not process your request at this time. Please try again later.';
            // Log the error
            error_log('Advice Request Error: ' . $e->getMessage());
        }
    }
}

$page_title = 'Request Professional Advice';
include 'templates/header.php';
?>

<h1><?php echo $page_title; ?></h1>
<p>Have a question about a medicine or need health advice? Fill out the form below, and one of our qualified staff will get back to you.</p>

<?php if ($message): ?>
    <div style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if (!$message): // Hide form on success ?>
<form action="<?php echo BASE_URL; ?>/advice.php" method="POST" style="max-width: 600px;">
    <div class="form-group">
        <label for="name">Full Name *</label>
        <input type="text" id="name" name="name" required value="<?php echo esc($_POST['name'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="email">Email Address *</label>
        <input type="email" id="email" name="email" required value="<?php echo esc($_POST['email'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="phone">Phone Number (Optional)</label>
        <input type="tel" id="phone" name="phone" value="<?php echo esc($_POST['phone'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="subject">Subject *</label>
        <input type="text" id="subject" name="subject" required value="<?php echo esc($_POST['subject'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="message">Your Question/Message *</label>
        <textarea id="message" name="message" rows="6" required><?php echo esc($_POST['message'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit Request</button>
</form>
<?php endif; ?>

<?php
include 'templates/footer.php';
?>
