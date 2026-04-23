<?php
session_start();
include 'db.php';

// 再次安全检查
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("Access Denied");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 接收表单发来的数据
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $programme = mysqli_real_escape_string($conn, $_POST['programme']);

    // 写入数据库的 SQL 语句
    $sql = "INSERT INTO students (student_id, name, programme) VALUES ('$student_id', '$name', '$programme')";

    if (mysqli_query($conn, $sql)) {
        // 如果成功，自动跳回到 dashboard 页面
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // 如果失败，报错
        echo "Error: " . mysqli_error($conn);
    }
}
?>