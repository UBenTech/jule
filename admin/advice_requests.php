<?php
/**
 * Admin: View Advice Requests
 *
 * Allows administrators to view and manage user-submitted advice requests.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

$pdo = db_connect();

// Handle marking a request as read
if (isset($_GET['action']) && $_GET['action'] === 'mark_read' && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $stmt = $pdo->prepare("UPDATE advice_requests SET is_read = 1 WHERE id = ?");
    $stmt->execute([$request_id]);
    redirect('/admin/advice_requests.php');
}

// Fetch all advice requests
$requests = $pdo->query("
    SELECT *
    FROM advice_requests
    ORDER BY created_at DESC
")->fetchAll();

$page_title = 'Manage Advice Requests';
include __DIR__ . '/../templates/admin_header.php';
?>

<h1>Manage Advice Requests</h1>
<p>Review and manage questions submitted by users.</p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>From</th>
            <th>Contact</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($requests)): ?>
            <tr>
                <td colspan="7" class="text-center">No advice requests found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($requests as $request): ?>
                <tr style="<?php echo $request['is_read'] ? 'background-color: #f8f9fa;' : 'font-weight: bold;'; ?>">
                    <td><?php echo format_date($request['created_at']); ?></td>
                    <td><?php echo esc($request['name']); ?></td>
                    <td>
                        <a href="mailto:<?php echo esc($request['email']); ?>"><?php echo esc($request['email']); ?></a><br>
                        <?php echo esc($request['phone']); ?>
                    </td>
                    <td><?php echo esc($request['subject']); ?></td>
                    <td style="white-space: pre-wrap; max-width: 400px;"><?php echo esc($request['message']); ?></td>
                    <td>
                        <?php if ($request['is_read']): ?>
                            <span class="badge" style="background-color: gray; color: white;">Read</span>
                        <?php else: ?>
                            <span class="badge" style="background-color: green; color: white;">New</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$request['is_read']): ?>
                            <a href="<?php echo BASE_URL; ?>/admin/advice_requests.php?action=mark_read&id=<?php echo $request['id']; ?>" class="btn btn-secondary btn-sm">Mark as Read</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
include __DIR__ . '/../templates/admin_footer.php';
?>
