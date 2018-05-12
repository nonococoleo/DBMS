<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>日志管理</title>

    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="page.css">
</head>

<body>
<?php
session_start();
date_default_timezone_set('PRC');
$isRoot = $_SESSION["isRoot"] ? "管理员" : "操作员";
?>
<!--顶部导航条-->
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
            </button>
            <a class="navbar-brand" href="#">Brand</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="return.php">归还图书</a></li>
                <li><a href="reservation.php">预约管理</a></li>
                <li><a href="book.php">借阅图书</a></li>
                <li><a href="booklist.php">书目管理</a></li>
                <li><a href="reader.php">读者管理</a></li>
                <li><a href="manager.php">员工管理</a></li>
                <li class="active"><a href="logs.php">日志管理</a><span class="sr-only">(current)</span></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">您好, <?php echo "{$isRoot}: $_SESSION[username]" ?></a></li>
                <li><a href="index.php">返回主页</a></li>
                <li><a href="login.php?action=logout">退出系统</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<!--功能导航条-->
<ul class="nav nav-tabs">
    <li class="flora active"><a href="#">搜索日志</a></li>
</ul>

<div class="container">
    <br>
    <?php
    $token = md5($_SESSION["username"] . $_SERVER['HTTP_USER_AGENT'] . session_id());
    //登录验证
    if (!isset($_SESSION["token"]) || $_SESSION["token"] != $token) {
        header("Refresh:5;Url=login.php?action=logout");
        echo "<p>登录状态过期，请重新登录</p>";
    } else if (!isset($_SESSION["isRoot"]) || !$_SESSION["isRoot"]) {
        //判断权限，以下内容按需修改
        echo "<p>需要管理员权限，请重新登录后再试</p>";
        echo "<a href=\"login.php?action=logout\">重新登录</a>";
    } else {
        //连接数据库
        require_once "conn.php";
        require_once "function.php";
        echo "<form name=\"search\" method=\"get\">
             开始时间<input name=\"start\" type=\"date\" style=\"width:300px;margin:0 auto;height:45px\" value='{$_GET['start']}'>
             &nbsp;结束时间<input name=\"end\" type=\"date\" style=\"width:300px;margin:0 auto;height:45px\" value='{$_GET['end']}'>
             &nbsp;操作者<input name=\"op\" type=\"text\" style=\"width:300px;margin:0 auto;height:45px\" value='{$_GET['op']}'>
             &nbsp;<input name=\"submit\" type=\"submit\" value=\"查询\" class=\"btn btn-primary \"><br><br>";
        if (isset($_GET["submit"])) {
            if ($_GET["start"] != "" && $_GET["end"] != "" && $_GET["op"] != "") {
                $sql = "SELECT * from logs where timeofoperation between " . "'$_GET[start]'" . " and " . "'$_GET[end]' and operator in (select iduser from user where name like '%" . $_GET["op"] . "%')";
            } elseif ($_GET["op"] == "") {
                $sql = "SELECT * from logs where timeofoperation between " . "'$_GET[start]'" . " and " . "'$_GET[end]'";
            } else {
                $sql = "SELECT * from logs where operator in (select iduser from user where name like '%" . $_GET["op"] . "%')";
            }
            $results = $pdo->query($sql);
            $rows = $results->fetchAll();
            if ($results->rowCount() > 0) {
                echo '<table class="table table-striped table-hover table-condensed">';
                echo "<colgroup>
                <col style=\"width:4%\">
                <col style=\"width:70%\">
                <col style=\"width:10%\">
                <col style=\"width:6%\">
                <col style=\"width:10%\">
            </colgroup>";
// 显示字段名称
                echo "<tr>";
                echo "<th>编号</th><th>操作</th><th>日期</th><th>操作者</th><th>备注</th>";
                echo "</tr>";

// 循环取出记录
                foreach ($rows as $row) {
                    echo "<tr>";
                    for ($i = 0; $i <= 5; $i++) {
                        if ($i == 1) {
                            continue;
                        }
                        echo '<td>';
                        if ($i != 4) {
                            echo $row[$i];
                        } else {
                            echo $row[$i];
                        }
                        echo '</td>';
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<br>无记录";
            }
        }
    }
    ?>
</div>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
