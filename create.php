<?php
session_start();
require_once 'models/Student.php';

$studentModel = new Student();
$errors = [];
$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'date_of_birth' => '',
    'enrollment_date' => date('Y-m-d'),
    'program' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?? '',
        'enrollment_date' => $_POST['enrollment_date'] ?? '',
        'program' => trim($_POST['program'] ?? '')
    ];
    
    if ($studentModel->validate($formData)) {
        if ($studentModel->create($formData)) {
            $_SESSION['message'] = 'Student added successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Failed to add student. Please try again.';
        }
    } else {
        $errors = $studentModel->getErrors();
    }
}

include 'views/layout/header.php';
?>

<div class="content-header">
    <h2>Add New Student</h2>
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

<form method="POST" action="create.php" class="form">
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
               value="<?php echo htmlspecialchars($formData['phone']); ?>">
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
            <option value="Computer Science" <?php echo $formData['program'] === 'Computer Science' ? 'selected' : ''; ?>>Computer Science</option>
            <option value="Information Technology" <?php echo $formData['program'] === 'Information Technology' ? 'selected' : ''; ?>>Information Technology</option>
            <option value="Software Engineering" <?php echo $formData['program'] === 'Software Engineering' ? 'selected' : ''; ?>>Software Engineering</option>
            <option value="Data Science" <?php echo $formData['program'] === 'Data Science' ? 'selected' : ''; ?>>Data Science</option>
            <option value="Cybersecurity" <?php echo $formData['program'] === 'Cybersecurity' ? 'selected' : ''; ?>>Cybersecurity</option>
        </select>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php include 'views/layout/footer.php'; ?>