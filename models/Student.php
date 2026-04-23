<?php
require_once __DIR__ . '/../config/database.php';

class Student {
    private $db;
    private $errors = [];
    
    // Properties
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $date_of_birth;
    public $enrollment_date;
    public $program;
    public $created_at;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Validate student data
     */
    public function validate($data) {
        $this->errors = [];
        
        // First name validation
        if (empty($data['first_name'])) {
            $this->errors['first_name'] = 'First name is required';
        } elseif (strlen($data['first_name']) > 50) {
            $this->errors['first_name'] = 'First name must be less than 50 characters';
        }
        
        // Last name validation
        if (empty($data['last_name'])) {
            $this->errors['last_name'] = 'Last name is required';
        } elseif (strlen($data['last_name']) > 50) {
            $this->errors['last_name'] = 'Last name must be less than 50 characters';
        }
        
        // Email validation
        if (empty($data['email'])) {
            $this->errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format';
        } else {
            // Check for duplicate email
            $sql = "SELECT id FROM students WHERE email = :email";
            if (isset($data['id']) && $data['id']) {
                $sql .= " AND id != :id";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $data['email']);
            if (isset($data['id']) && $data['id']) {
                $stmt->bindParam(':id', $data['id']);
            }
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $this->errors['email'] = 'Email already exists';
            }
        }
        
        // Phone validation (optional but validated if provided)
        if (!empty($data['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $data['phone']);
            if (strlen($phone) < 10 || strlen($phone) > 15) {
                $this->errors['phone'] = 'Phone number must be between 10 and 15 digits';
            }
        }
        
        // Date of birth validation
        if (empty($data['date_of_birth'])) {
            $this->errors['date_of_birth'] = 'Date of birth is required';
        } else {
            $dob = strtotime($data['date_of_birth']);
            if ($dob === false) {
                $this->errors['date_of_birth'] = 'Invalid date format';
            } elseif ($dob > time()) {
                $this->errors['date_of_birth'] = 'Date of birth cannot be in the future';
            }
        }
        
        // Enrollment date validation
        if (empty($data['enrollment_date'])) {
            $this->errors['enrollment_date'] = 'Enrollment date is required';
        } else {
            $enrollment = strtotime($data['enrollment_date']);
            if ($enrollment === false) {
                $this->errors['enrollment_date'] = 'Invalid date format';
            }
        }
        
        // Program validation
        if (empty($data['program'])) {
            $this->errors['program'] = 'Program is required';
        }
        
        return empty($this->errors);
    }
    
    /**
     * Create a new student
     */
    public function create($data) {
        $sql = "INSERT INTO students (first_name, last_name, email, phone, date_of_birth, enrollment_date, program) 
                VALUES (:first_name, :last_name, :email, :phone, :date_of_birth, :enrollment_date, :program)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            // Sanitize inputs
            $first_name = htmlspecialchars(strip_tags($data['first_name']));
            $last_name = htmlspecialchars(strip_tags($data['last_name']));
            $email = htmlspecialchars(strip_tags($data['email']));
            $phone = !empty($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : null;
            $program = htmlspecialchars(strip_tags($data['program']));
            
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $stmt->bindParam(':enrollment_date', $data['enrollment_date']);
            $stmt->bindParam(':program', $program);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all students
     */
    public function getAll() {
        $sql = "SELECT * FROM students ORDER BY created_at DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get all error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single student by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM students WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update student
     */
    public function update($id, $data) {
        $sql = "UPDATE students SET 
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                date_of_birth = :date_of_birth,
                enrollment_date = :enrollment_date,
                program = :program
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            // Sanitize inputs
            $first_name = htmlspecialchars(strip_tags($data['first_name']));
            $last_name = htmlspecialchars(strip_tags($data['last_name']));
            $email = htmlspecialchars(strip_tags($data['email']));
            $phone = !empty($data['phone']) ? htmlspecialchars(strip_tags($data['phone'])) : null;
            $program = htmlspecialchars(strip_tags($data['program']));
            
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $stmt->bindParam(':enrollment_date', $data['enrollment_date']);
            $stmt->bindParam(':program', $program);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete student
     */
    public function delete($id) {
        $sql = "DELETE FROM students WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get validation errors
     */
    public function getErrors() {
        return $this->errors;
    }
}