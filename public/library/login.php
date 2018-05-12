<?php
session_start();
date_default_timezone_set('PRC');
require_once "conn.php";

if ($_GET["action"] == "login") {
    $_SESSION['token'] = '';
    $username = $_POST["username"];
    if (is_numeric($username)) {
        $sql = "SELECT MID,mname,password from manager where MID='{$username}'";
    } else {
        $sql = "SELECT RID,rname,password from reader where username='{$username}'";
    }
    $result = $pdo->query($sql);
    if ($result->rowCount() == 1) {
        $row = $result->fetch();
        if (md5($_POST["password"]) == $row[2]) {
            $_SESSION['iduser'] = $row[0];
            $_SESSION['username'] = $row[1];
            if (is_numeric($username)) {
                $_SESSION['isRoot'] = 1;
            } else {
                $_SESSION['isRoot'] = 0;
            }
            $token = $_SESSION['username'] . $_SERVER['HTTP_USER_AGENT'] . session_id();
            $_SESSION['token'] = md5($token);
            header("Location:index.php");
        } else {
            echo("<p>用户名或密码错误！</p>");
            echo "<a href=\"login.php?action=logout\">重新登录</a>";
        }
    } else {
        echo("<p>用户名错误！</p>");
        echo "<a href=\"login.php?action=logout\">重新登录</a>";
    }
} else if ($_GET["action"] == "logout") {
    session_destroy();
    header("Location:index.php");
}

