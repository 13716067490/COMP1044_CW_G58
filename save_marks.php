<?php
session_start();
include 'db.php';

// 安全门禁
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Assessor') {
    die("Access Denied");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. 接收所有从表单传过来的分数和数据
    $internship_id = mysqli_real_escape_string($conn, $_POST['internship_id']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    
    // 获取 8 项分数并确保它们是数字格式
    $s_tasks = floatval($_POST['score_tasks']);
    $s_safety = floatval($_POST['score_safety']);
    $s_conn = floatval($_POST['score_connectivity']);
    $s_report = floatval($_POST['score_report']);
    $s_lang = floatval($_POST['score_language']);
    $s_life = floatval($_POST['score_lifelong']);
    $s_proj = floatval($_POST['score_project_mgt']);
    $s_time = floatval($_POST['score_time_mgt']);

    // 2. 核心数学计算：应用作业规定的精确权重
    // 10% = 0.10, 15% = 0.15
    $total_score = ($s_tasks * 0.10) + 
                   ($s_safety * 0.10) + 
                   ($s_conn * 0.10) + 
                   ($s_report * 0.15) + 
                   ($s_lang * 0.10) + 
                   ($s_life * 0.15) + 
                   ($s_proj * 0.15) + 
                   ($s_time * 0.15);

    // 3. 将分数存入 assessments 表
    // 为了防止老师重复打分报错，我们先检查是否已经打过分
    $check_sql = "SELECT * FROM assessments WHERE internship_id = '$internship_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // 如果已经有成绩了，就执行 UPDATE (更新)
        $sql = "UPDATE assessments SET 
                score_tasks='$s_tasks', score_safety='$s_safety', score_connectivity='$s_conn', 
                score_report='$s_report', score_language='$s_lang', score_lifelong='$s_life', 
                score_project_mgt='$s_proj', score_time_mgt='$s_time', comments='$comments', 
                total_score='$total_score' 
                WHERE internship_id='$internship_id'";
    } else {
        // 如果是第一次打分，就执行 INSERT (插入)
        $sql = "INSERT INTO assessments 
                (internship_id, score_tasks, score_safety, score_connectivity, score_report, score_language, score_lifelong, score_project_mgt, score_time_mgt, comments, total_score) 
                VALUES 
                ('$internship_id', '$s_tasks', '$s_safety', '$s_conn', '$s_report', '$s_lang', '$s_life', '$s_proj', '$s_time', '$comments', '$total_score')";
    }

    if (mysqli_query($conn, $sql)) {
        
        echo "<script>
                alert('Marks saved successfully! The calculated Final Score is: " . number_format($total_score, 2) . "');
                window.location.href = 'assessor_dashboard.php';
              </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>