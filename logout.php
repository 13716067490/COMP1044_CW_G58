<?php
// 开启 Session
session_start();
// 清除系统中所有记录的用户状态
session_unset();
// 彻底销毁这个 Session
session_destroy();
// 把用户踢回登录页面
header("Location: login.php");
exit();
?>