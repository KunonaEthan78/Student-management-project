<?php
session_start();
require_once 'models/Student.php';

$studentModel = new Student();
$students = $studentModel->getAll();

// Check for messages
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

include 'views/layout/header.php';
?>

<div class="content-header">
    <h2>Student Directory</h2>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if (empty($students)): ?>
    <div class="empty-state">
        <p>No students found. <a href="create.php">Add your first student</a></p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Program</th>
                    <th>Enrollment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['program']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?></td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                            <button onclick="confirmDelete(<?php echo $student['id']; ?>)" class="btn btn-sm btn-delete">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
        window.location.href = 'delete.php?id=' + id;
    }
}
</script>

<?php include 'views/layout/footer.php'; ?>