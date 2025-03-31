<?php
session_start();

// Load JSON files
$studentsFile = '../json/students.json';
$adminsFile = '../json/admins.json';
$studentsData = json_decode(file_get_contents($studentsFile), true);
$adminsData = json_decode(file_get_contents($adminsFile), true);

// Get user input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Check if user is a student
foreach ($studentsData['students'] as $student) {
    if ($student['username'] === $username && $student['password'] === $password) {
        $_SESSION['user'] = [
            'username' => $username,
            'role' => 'student'
        ];
        header('Location: dashboard_student.php');
        exit();
    }
}

// Check if user is an admin
foreach ($adminsData['admins'] as $admin) {
    if ($admin['username'] === $username && $admin['password'] === $password) {
        $_SESSION['user'] = [
            'username' => $username,
            'role' => 'admin'
        ];
        header('Location: dashboard_admin.php');
        exit();
    }
}

// Redirect back to login on failure
header('Location: ../login.html?error=invalid_credentials');
exit();
?>