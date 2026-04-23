<?php
session_start();
require_once 'models/Student.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['message'] = 'Invalid student ID';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

$studentModel = new Student();

// Check if student exists
$student = $studentModel->getById($id);

if (!$student) {
    $_SESSION['message'] = 'Student not found';
    $_SESSION['message_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Perform deletion
if ($studentModel->delete($id)) {
    $_SESSION['message'] = 'Student deleted successfully!';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Failed to delete student';
    $_SESSION['message_type'] = 'error';
}

header('Location: index.php');
exit;