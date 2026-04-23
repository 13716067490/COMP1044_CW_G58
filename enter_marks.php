<?php
session_start();
include 'db.php';

// 安全门禁：确保是 Assessor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Assessor') {
    die("Access Denied");
}

// 从网址(URL)上获取传过来的 internship_id
if (!isset($_GET['id'])) {
    die("No student selected.");
}
$internship_id = mysqli_real_escape_string($conn, $_GET['id']);
$assessor_id = $_SESSION['user_id'];

// 查询这个学生的详细信息，并确认这个学生确实是分配给当前老师的 (防止老师改网址偷看别人的学生)
$sql = "SELECT i.internship_id, s.name, s.student_id, i.company_name 
        FROM internships i 
        JOIN students s ON i.student_id = s.student_id 
        WHERE i.internship_id = '$internship_id' AND i.assessor_id = '$assessor_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Invalid access or student not assigned to you.");
}
$student_info = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Marks</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        .info { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        /* 提示文字显示权重，告知老师 */
        .weight { color: #666; font-size: 0.9em; font-weight: normal; }
        input[type="number"], textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
    </style>
</head>
<body>

<div class="box">
    <h2>Evaluation Form</h2>
    <div class="info">
        <strong>Student:</strong> <?php echo $student_info['name']; ?> (<?php echo $student_info['student_id']; ?>)<br>
        <strong>Company:</strong> <?php echo $student_info['company_name']; ?>
    </div>

    <form action="save_marks.php" method="POST">
        <input type="hidden" name="internship_id" value="<?php echo $internship_id; ?>">

        <div class="form-group">
            <label>1. Undertaking Tasks/Projects <span class="weight">(10%)</span></label>
            <input type="number" name="score_tasks" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label>2. Health and Safety Requirements <span class="weight">(10%)</span></label>
            <input type="number" name="score_safety" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label>3. Connectivity and Use of Theoretical Knowledge <span class="weight">(10%)</span></label>
            <input type="number" name="score_connectivity" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label>4. Presentation of the Report <span class="weight">(15%)</span></label>
            <input type="number" name="score_report" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label>5. Clarity of Language and Illustration <span class="weight">(10%)</span></label>
            <input type="number" name="score_language" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label>6. Lifelong Learning Activities <span class="weight">(15%)</span></label>
            <input type="number" name="score_lifelong" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label>7. Project Management <span class="weight">(15%)</span></label>
            <input type="number" name="score_project_mgt" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label>8. Time Management <span class="weight">(15%)</span></label>
            <input type="number" name="score_time_mgt" min="0" max="100" required>
        </div>

        <div class="form-group">
            <label>Qualitative Comments (Feedback):</label>
            <textarea name="comments" rows="4" required placeholder="Provide meaningful feedback here..."></textarea>
        </div>

        <button type="submit">Submit Marks & Calculate Total</button>
    </form>
    <br>
    <a href="assessor_dashboard.php" style="text-decoration: none; color: #007bff;">&larr; Back to Dashboard</a>
</div>

</body>
</html>