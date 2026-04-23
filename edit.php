<?php
session_start();
require_once 'models/Student.php';

$studentModel = new Student();
$errors = [];

// Get student ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['message'] = 'Invalid student ID';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Fetch student data
$student = $studentModel->getById($id);

if (!$student) {
    $_SESSION['message'] = 'Student not found';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$formData = $student;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'id' => $id,
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?? '',
        'enrollment_date' => $_POST['enrollment_date'] ?? '',
        'program' => trim($_POST['program'] ?? '')
    ];
    
    if ($studentModel->validate($formData)) {
        if ($studentModel->update($id, $formData)) {
            $_SESSION['message'] = 'Student updated successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Failed to update student. Please try again.';
        }
    } else {
        $errors = $studentModel->getErrors();
    }
}

include 'views/layout/header.php';
?>

<div class="content-header">
    <h2>Edit Student</h2>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $field => $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="edit.php?id=<?php echo $id; ?>" class="form">
    <div class="form-group">
        <label for="first_name">First Name *</label>
        <input type="text" id="first_name" name="first_name" 
               value="<?php echo htmlspecialchars($formData['first_name']); ?>" 
               required maxlength="50">
    </div>
    
    <div class="form-group">
        <label for="last_name">Last Name *</label>
        <input type="text" id="last_name" name="last_name" 
               value="<?php echo htmlspecialchars($formData['last_name']); ?>" 
               required maxlength="50">
    </div>
    
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" 
               value="<?php echo htmlspecialchars($formData['email']); ?>" 
               required>
    </div>
    
    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" 
               value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="date_of_birth">Date of Birth *</label>
            <input type="date" id="date_of_birth" name="date_of_birth" 
                   value="<?php echo htmlspecialchars($formData['date_of_birth']); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="enrollment_date">Enrollment Date *</label>
            <input type="date" id="enrollment_date" name="enrollment_date" 
                   value="<?php echo htmlspecialchars($formData['enrollment_date']); ?>" 
                   required>
        </div>
    </div>
    
    <div class="form-group">
        <label for="program">Program *</label>
        <select id="program" name="program" required>
            <option value="">Select a program</option>
            <?php
            $programs = ['Computer Science', 'Information Technology', 'Software Engineering', 'Data Science', 'Cybersecurity'];
            foreach ($programs as $program):
                $selected = $formData['program'] === $program ? 'selected' : '';
            ?>
                <option value="<?php echo $program; ?>" <?php echo $selected; ?>><?php echo $program; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Student</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php include 'views/layout/footer.php'; ?>