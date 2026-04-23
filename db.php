<?php
// 本地服务器地址
$host = "localhost"; 
// XAMPP 默认的数据库用户名是 root
$user = "root"; 
// XAMPP 默认的数据库密码是空的
$pass = ""; 

$dbname = "internship_system"; 

// 尝试连接数据库
$conn = mysqli_connect($host, $user, $pass, $dbname);

// 检查是否连接成功
if (!$conn) {
    die("数据库连接失败: " . mysqli_connect_error());
}
?>