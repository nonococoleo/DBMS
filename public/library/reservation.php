<!DOCTYPE html>
<head>
    <title>预约管理</title>
    <meta charset="UTF-8">
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="page.css">
    <script src="1.js"></script>
</head>

<body>
<?php
session_start();
date_default_timezone_set('PRC');
$isRoot = $_SESSION["isRoot"] ? "员工" : "读者";
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
                <li class="active"><a href="#">预约管理<span class="sr-only">(current)</span></a></li>
                <li><a href="book.php">借阅图书</a></li>
                <li><a href="booklist.php">书目管理</a></li>
                <li><a href="reader.php">读者管理</a></li>
                <li><a href="manager.php">员工管理</a></li>
                <li><a href="logs.php">日志管理</a></li>
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
    <li class="flora<?php if (!isset($_GET["action"]) || $_GET["action"] == "list") echo " active"; ?>"><a href="reservation.php?action=list">预约列表</a></li>
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "ser") echo " active"; ?>"><a href="reservation.php?action=ser">搜索预约</a></li>
</ul>
<div class="container">
    <br>
    <?php
    $token = md5($_SESSION["username"] . $_SERVER['HTTP_USER_AGENT'] . session_id());
    if (!isset($_SESSION["token"]) || $_SESSION["token"] != $token) {
        header("Refresh:3;Url=login.php?action=logout");
        echo "<p>登录状态过期，请重新登录</p>";
    } else {
        require_once "function.php";
        //默认展示列表
        if (!isset($_GET['action'])) {
            $_GET['action'] = 'list';
        }

        //搜索表单
        if ($_GET["action"] == "ser") {
            ?>
            <form enctype="multipart/form-data" action="reservation.php" method="GET" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ID</b></span>
                    <input type="number" name="REID" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BID</b></span>
                    <input type="number" name="BID" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>RID</b></span>
                    <input type="number" name="RID" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>有效期</b></span>
                    <input type="date" name="deadline" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="submit" value="查找预约" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary ">
            </form>
            <?php

        } else if ($_GET["action"] == "mod") {
            /* 如果用户的操作是请求修改action=mod，则条件成立，跳转修改表单 */
            $sql = "SELECT REID,RID,BID,deadline FROM reservation WHERE REID={$_GET['REID']}";
            /* 通过ID查找指定的一行记录 */
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            if ($num > 0) {
                list($REID, $RID, $BID, $deadline) = $result->fetch(PDO::FETCH_NUM);
                /* 获取需要修改的记录数据 */
            } else {
                echo("<p>没有找到需要修改的预约</p>");
                echo '<script>window.location="' . $_SERVER["HTTP_REFERER"] . '"</script>';
            }
            ?>
            <p class="alsp"><b>修改预约</b></p>
            <form enctype="multipart/form-data" action="reservation.php?action=update" method="POST" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ID</b></span>
                    <input type="number" name="REID" value="<?php echo $REID; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>RID</b></span>
                    <input type="number" name="RID" value="<?php echo $RID; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BID</b></span>
                    <input type="number" name="BID" value="<?php echo $BID; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>时限</b></span>
                    <input type="date" name="deadline" value="<?php echo $deadline; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <input type="submit" value="修改预约" class="btn btn-primary ">
                <a href="<?php echo($_SERVER["HTTP_REFERER"]); ?>">
                    <input type="button" value="返回" class="btn btn-primary ">
                </a>
            </form>
            <?php

        } elseif ($_GET["action"] == "insert") {
            /* 添加表单的提交后操作，添加课程至数据库 */
            $sql = $pdo->prepare("INSERT INTO reservation(RID, BID, deadline) VALUES(?,?,?)");
            /* 执行INSERT语句 */
            $deadline = date("y-m-d H:i:s", strtotime("+20 day"));
            $execarr = (array($_SESSION["iduser"], $_GET["BID"], $deadline));
            $sql->execute($execarr);
            include "inslog.php";

            //修改
        } else if ($_GET["action"] == "del") {
            $sql = "DELETE FROM reservation WHERE REID='{$_GET["REID"]}'";
            $sql = $pdo->prepare($sql);
            try {
                $sql->execute();
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //列表
        } else if ($_GET["action"] == "update") {
            $sql = $pdo->prepare("UPDATE reservation SET deadline=? WHERE REID=?");
            $execarr = (array($_POST["deadline"], $_POST["REID"]));
            try {
                $sql->execute($execarr);
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //删除
        } else {
            $ser = $_GET;
            $where = array();            //声明WHERE从句的查询条件变量

            if (!$_SESSION["isRoot"])
                $ser["RID"] = $_SESSION["iduser"];
            //根据不同字段检索
            $cols = array("REID", "RID", "BID", "deadline");
            foreach ($cols as $col) {
                if ($ser[$col] == '0' || !empty($ser[$col])) {
                    switch ($col) {
                        case "deadline":
                            $where[] = $col . " >= '{$ser[$col]}'";
                            break;
                        default:
                            $where[] = $col . " = '{$ser[$col]}'";
                    }
                }
            }

            /* 处理是否有搜索的情况 */
            if (!empty($where)) {
                $where = "WHERE " . implode(" and ", $where);
            } else {
                $where = "";
            }

            /* 编写查询语句，使用$where组合查询条件， 使用$page->limit获取LIMIT从句,限制数据条数 */
            $sql = "SELECT REID,RID,BID,deadline FROM reservation {$where}";

            //分页
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            require "page.class.php";
            $page = new Page($num, 10, $_SERVER['QUERY_STRING']);
            $sql = sprintf("%s {$page->limit}", $sql);

            /* 执行查询的SQL语句 */
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            /*处理结果集，打印数据记录 */
            if ($num > 0) {
                $i = 0;
                echo '<table class="table table-striped table-hover table-condensed">';
                echo '<tr><th class="text-center">ID</th><th class="text-center">读者</th><th class="text-center">图书</th><th class="text-center">时限</th><th class="text-center">操作</th></tr>';
                /* 循环数据，将数据表每行数据对应的列转为变量 */
                while (list($REID, $RID, $BID, $deadline) = $result->fetch(PDO::FETCH_NUM)) {
                    echo '<tr>';
                    echo '<td class="text-center">' . $REID . '</td>';
                    echo '<td class="text-center"><a href="reader.php?RID=' . $RID . '">' . getUser($RID) . '</td>';
                    echo '<td class="text-center"><a href="book.php?BID=' . $BID . '">' . getBook($BID) . '</td>';
                    echo '<td class="text-center">' . $deadline . '</td>';
                    if (!$_SESSION["isRoot"])
                        echo '<td class="text-center"><a onclick="return confirm(\'你确定要删除预约 ' . $REID . ' 吗?\')" href="reservation.php?action=del&REID=' . $REID . '">删除</a></td>';
                    else
                        echo '<td class="text-center"><a href="reservation.php?action=mod&REID=' . $REID . '">修改</td>';
                    /*  删除时会弹出确定框  */
                    echo '</tr>';
                }
                echo '<table>';
                echo "<div align=\"center\" style=\"margin: 5px\">";
                echo $page->fpage();
                echo "</div>";
            } else {
                echo '<p>没有预约被找到</p>';
            }
        }
    }
    ?>
</div>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
