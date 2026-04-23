<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// 1. 构建基础 SQL 查询语句 
$sql = "SELECT s.student_id, s.name, i.company_name, u.username as assessor_name, a.* FROM assessments a
        JOIN internships i ON a.internship_id = i.internship_id
        JOIN students s ON i.student_id = s.student_id
        JOIN users u ON i.assessor_id = u.user_id
        WHERE 1=1"; 

// 2. 权限：如果是老师，只能看自己的学生
if ($role == 'Assessor') {
    $sql .= " AND i.assessor_id = '$user_id'";
}

// 3. 搜索：如果用户在搜索框输入了内容
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    // 使用 LIKE 实现模糊搜索：名字包含该字符，或者学号包含该字符
    $sql .= " AND (s.name LIKE '%$search_query%' OR s.student_id LIKE '%$search_query%')";
}

// 按最终成绩从高到低排序
$sql .= " ORDER BY a.total_score DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Results</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto; /* 表格太宽时允许横向滚动 */ }
        .search-bar { margin-bottom: 20px; display: flex; gap: 10px; }
        .search-bar input[type="text"] { padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        .search-bar button { padding: 10px 15px; background-color: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #343a40; color: white; font-size: 14px; }
        .total { font-weight: bold; color: #d9534f; font-size: 16px; }
        .btn-back { background-color: #6c757d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>

<div class="header">
    <h2>Internship Results Database</h2>
    <div>
        <?php 
        // 动态返回按钮：管理员回管理页，老师回老师页
        $back_link = ($role == 'Admin') ? "admin_dashboard.php" : "assessor_dashboard.php";
        echo "<a href='$back_link' class='btn-back'>&larr; Back to Dashboard</a>"; 
        ?>
    </div>
</div>

<div class="box">
    <form class="search-bar" action="view_results.php" method="GET">
        <input type="text" name="search" placeholder="Search by Student Name or ID..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
        <a href="view_results.php" style="padding: 10px; text-decoration: none; color: #17a2b8;">Clear</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <?php if($role == 'Admin') echo "<th>Assessor</th>"; // 管理员可以看到是谁打的分 ?>
                <th>Company</th>
                <th title="10%">Tasks (10)</th>
                <th title="10%">Safety (10)</th>
                <th title="10%">Connectivity (10)</th>
                <th title="15%">Report (15)</th>
                <th title="10%">Language (10)</th>
                <th title="15%">Lifelong (15)</th>
                <th title="15%">Project Mgt (15)</th>
                <th title="15%">Time Mgt (15)</th>
                <th>Total Score (100)</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['student_id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    if($role == 'Admin') echo "<td>" . $row['assessor_name'] . "</td>";
                    echo "<td>" . $row['company_name'] . "</td>";
                    echo "<td>" . $row['score_tasks'] . "</td>";
                    echo "<td>" . $row['score_safety'] . "</td>";
                    echo "<td>" . $row['score_connectivity'] . "</td>";
                    echo "<td>" . $row['score_report'] . "</td>";
                    echo "<td>" . $row['score_language'] . "</td>";
                    echo "<td>" . $row['score_lifelong'] . "</td>";
                    echo "<td>" . $row['score_project_mgt'] . "</td>";
                    echo "<td>" . $row['score_time_mgt'] . "</td>";
                    echo "<td class='total'>" . number_format($row['total_score'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                    echo "</tr>";
                }
            } else {
                $cols = ($role == 'Admin') ? 14 : 13;
                echo "<tr><td colspan='$cols'>No results found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>