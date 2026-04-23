<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// 1. 获取所有学生
$students_result = mysqli_query($conn, "SELECT * FROM students");

// 2. 获取所有角色为 'Assessor' 的用户 
$assessors_result = mysqli_query($conn, "SELECT user_id, username FROM users WHERE role = 'Assessor'");

// 3. 获取当前的分配情况 
$assignments_sql = "SELECT i.internship_id, s.name as student_name, i.company_name, u.username as assessor_name 
                    FROM internships i
                    JOIN students s ON i.student_id = s.student_id
                    JOIN users u ON i.assessor_id = u.user_id";
$assignments_result = mysqli_query($conn, $assignments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Management</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        select, input, button { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { background-color: #28a745; color: white; border: none; cursor: pointer; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #0056b3; color: white; }
    </style>
</head>
<body>

<div class="header">
    <h2>Admin Control Panel</h2>
    <div>
        <a href="view_results.php" style="background-color: #17a2b8; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 15px;">View All Results</a>
        <a href="logout.php" style="color: red;">Logout</a>
    </div>
</div>

<div class="grid">
    <div class="box">
        <h3>1. Add New Student</h3>
        <form action="add_student.php" method="POST">
            <input type="text" name="student_id" placeholder="Student ID" required>
            <input type="text" name="name" placeholder="Student Name" required>
            <input type="text" name="programme" placeholder="Programme" required>
            <button type="submit">Add Student</button>
        </form>
    </div>

    <div class="box">
        <h3>2. Assign Internship</h3>
        <form action="assign_action.php" method="POST">
            <label>Select Student:</label>
            <select name="student_id" required>
                <?php while($s = mysqli_fetch_assoc($students_result)) {
                    echo "<option value='".$s['student_id']."'>".$s['name']." (".$s['student_id'].")</option>";
                } ?>
            </select>

            <label>Company Name:</label>
            <input type="text" name="company_name" required>

            <label>Assign to Assessor:</label>
            <select name="assessor_id" required>
                <?php 
                // 重新获取结果集指针到开头
                mysqli_data_seek($assessors_result, 0); 
                while($a = mysqli_fetch_assoc($assessors_result)) {
                    echo "<option value='".$a['user_id']."'>".$a['username']."</option>";
                } ?>
            </select>
            <button type="submit" style="background-color: #007bff;">Create Assignment</button>
        </form>
    </div>
    
    <div class="box" style="margin-top: 20px;">
    <h3>Registered Students</h3>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Programme</th>
        </tr>
        <?php 
        // 重新查询一次学生表来展示
        $all_students = mysqli_query($conn, "SELECT * FROM students");
        while($row = mysqli_fetch_assoc($all_students)) { 
        ?>
            <tr>
                <td><?php echo $row['student_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['programme']; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>

<div class="box" style="margin-top: 20px;">
    <h3>3. Current Internship Assignments</h3>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Company</th>
            <th>Assessor (Lecturer)</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($assignments_result)) { ?>
            <tr>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['company_name']; ?></td>
                <td><?php echo $row['assessor_name']; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>