<?php
// 启动 Session，用于记住用户的登录状态
session_start();
// 引入刚刚写的数据库连接文件
include 'db.php'; 

// 检查是否是通过 POST 方法提交的表单
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单里的账号密码，并进行基础的防注入处理
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // 去数据库里查找这个账号和密码
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    // 如果查到了一行数据（说明账号密码匹配）
    if (mysqli_num_rows($result) == 1) {
        // 把数据提取出来
        $user = mysqli_fetch_assoc($result);
        
        // 把用户信息存进系统“记忆(Session)”里
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // 根据角色分配去不同的页面
        if ($user['role'] == 'Admin') {
            header("Location: admin_dashboard.php"); // 跳转到管理员后台
        } else {
            header("Location: assessor_dashboard.php"); // 跳转到评估老师后台
        }
        exit();
    } else {
        // 密码错误，弹窗提示并返回登录页
        echo "<script>alert('Invalid username or password!'); window.location.href='login.php';</script>";
    }
}
?>