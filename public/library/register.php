<!DOCTYPE html>
<head>
    <title>读者注册</title>
    <meta charset="UTF-8">
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="page.css">
</head>

<body>
<?php
date_default_timezone_set('PRC');
/*  连接数据库  */
require_once "function.php";
//默认展示列表
if (!isset($_GET['action'])) {
?>
<form action="register.php?action=insert" method="POST" align="center" role="form" class="alsp">
    <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
        <span class="input-group-addon"><b>账号</b></span>
        <input type="text" name="username" value="" class="form-control"
               style="width:300px;margin:0 auto;height:45px" placeholder="必须包含非数字" required="required">
    </div>
    <br>
    <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
        <span class="input-group-addon"><b>密码</b></span>
        <input type="password" name="password" value="" class="form-control"
               style="width:300px;margin:0 auto;height:45px" required="required">
    </div>

    <br>
    <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
        <span class="input-group-addon"><b>姓名</b></span>
        <input type="text" name="rname" value="" class="form-control"
               style="width:300px;margin:0 auto;height:45px" required="required">
    </div>
    <br>
    <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
        <span class="input-group-addon"><b>电话</b></span>
        <input type="text" name="phone" value="" class="form-control"
               style="width:300px;margin:0 auto;height:45px" required="required">
    </div>
    <br>
    <div class="input-group input-group-sm" style="width:150px;margin:0 auto;height:45px">
        <span class="input-group-addon"><b>邮箱</b></span>
        <input type="email" name="email" value="" class="form-control"
               style="width:300px;margin:0 auto;height:45px" required="required">
    </div>
    <br>
    <input type="submit" value="注册" class="btn btn-primary ">
    <input type="reset" value="重置" class="btn btn-primary">
</form>
<?php
} else if ($_GET["action"] = "insert") {
    /* 添加表单的提交后操作，添加学生至数据库 */
    $sql = $pdo->prepare("INSERT INTO reader(username, password, rname, phone, email) VALUES(?, ?, ?, ?, ?)");
    /* 执行INSERT语句 */
    $execarr = (array($_POST["username"], md5($_POST["password"]), $_POST["rname"], $_POST["phone"], $_POST["email"]));
    $sql->execute($execarr);
    header("refresh:1;url=./");
    $num = $sql->rowCount();
    $t = $num > 0 ? "注册成功" : "注册失败";
    echo $t;
}
