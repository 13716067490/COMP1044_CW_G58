<?php
// 1. 开启 Session 并连接数据库
session_start();
include 'db.php';

// 2. 安全检查：如果没登录，或者登录的不是 Assessor，直接踢回登录页
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Assessor') {
    header("Location: login.php");
    exit();
}

// 3. 获取当前登录老师的 ID
$current_assessor_id = $_SESSION['user_id'];

// 4. 核心 SQL 语句：只查询分配给“我”的学生
// 我们连接 internships 表和 students 表，通过 assessor_id 进行过滤
$sql = "SELECT i.internship_id, s.student_id, s.name as student_name, i.company_name, s.programme 
        FROM internships i
        JOIN students s ON i.student_id = s.student_id
        WHERE i.assessor_id = '$current_assessor_id'";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assessor Dashboard - My Students</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #28a745; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .btn-score { background-color: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-score:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="header">
    <h2>Assessor: <?php echo $_SESSION['username']; ?></h2>
    <div>
        <a href="view_results.php" style="background-color: #17a2b8; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-right: 15px;">View My Students' Results</a>
        <a href="logout.php" style="color: red; text-decoration: none; margin-left: 10px;">Logout</a>
    </div>
</div>

<div class="box">
    <h3>Students to Assess</h3>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Programme</th>
                <th>Company</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 如果有分配的学生，循环显示出来
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['student_id'] . "</td>";
                    echo "<td>" . $row['student_name'] . "</td>";
                    echo "<td>" . $row['programme'] . "</td>";
                    echo "<td>" . $row['company_name'] . "</td>";
                    // 这里的链接会跳转到打分页面，并带上 internship_id 参数
                    echo "<td><a href='enter_marks.php?id=" . $row['internship_id'] . "' class='btn-score'>Enter Marks</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>You have no students assigned yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>