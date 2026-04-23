<?php
session_start();
include 'db.php';

// 安全检查
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("Access Denied");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $assessor_id = mysqli_real_escape_string($conn, $_POST['assessor_id']);

    // 插入到实习表
    $sql = "INSERT INTO internships (student_id, company_name, assessor_id) 
            VALUES ('$student_id', '$company_name', '$assessor_id')";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>