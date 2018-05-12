<!DOCTYPE html>
<head>
    <title>归还图书</title>
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
                <li class="active"><a href="#">归还图书<span class="sr-only">(current)</span></a></li>
                <li><a href="reservation.php">预约管理</a></li>
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
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "add") echo " active"; ?>"><a href="return.php?action=add">确认归还</a></li>
    <li class="flora<?php if (!isset($_GET["action"]) || $_GET["action"] == "list") echo " active"; ?>"><a href="return.php?action=list">借阅列表</a></li>
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "ser") echo " active"; ?>"><a href="return.php?action=ser">搜索借阅</a></li>
</ul>
<div class="container">
    <br>
    <?php
    $token = md5($_SESSION["username"] . $_SERVER['HTTP_USER_AGENT'] . session_id());
    if (!isset($_SESSION["token"]) || $_SESSION["token"] != $token) {
        header("Refresh:5;Url=login.php?action=logout");
        echo "<p>登录状态过期，请重新登录</p>";
    } else {
        require_once "function.php";
        //默认展示列表
        if (!isset($_GET['action'])) {
            $_GET['action'] = 'list';
        }

        //添加表单
        if ($_GET["action"] == "add" && $_SESSION["isRoot"]) {
            ?>
            <form enctype="multipart/form-data" action="return.php" method="GET" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BID</b></span>
                    <input type="number" name="BID" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>RID</b></span>
                    <input type="number" name="RID" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="hidden" value="<?php echo $_SESSION["iduser"]; ?>" name="operator">
                <input type="hidden" value="del" name="action">
                <input type="submit" value="确认归还" class="btn btn-primary">
                <input type="reset" value="重置" class="btn btn-primary">
            </form>
            <?php

            //搜索表单
        } else if ($_GET["action"] == "add" && (!isset($_SESSION["isRoot"]) || !$_SESSION["isRoot"])) {
            echo "<p>需要员工权限，请重新登录后再试</p>";
            echo "<a href=\"login.php?action=logout\">重新登录</a>";
        }
        else if ($_GET["action"] == "ser") {
            ?>
            <form  action="return.php" method="GET" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BWID</b></span>
                    <input type="number" name="BWID" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BID</b></span>
                    <input type="number" name="BID" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>RID</b></span>
                    <input type="number" name="RID" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>借阅时间</b></span>
                    <input type="date" name="btime" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>截止时间</b></span>
                    <input type="date" name="deadline" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>归还时间</b></span>
                    <input type="date" name="rtime" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="submit" value="查找借阅" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary ">
            </form>
            <?php

            //修改表单
        } else if ($_GET["action"] == "mod") {
            $sql = "SELECT BID,RID,deadline,operator FROM borrow WHERE BWID={$_GET['BWID']}";
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            if ($num > 0) {
                list($BID, $RID, $deadline, $opeartor) = $result->fetch(PDO::FETCH_NUM);
            } else {
                echo("<p>没有找到需要修改的记录</p>");
                echo '<script>window.location="' . $_SERVER["HTTP_REFERER"] . '"</script>';
            }
            ?>
            <p class="alsp"><b>续借</b></p>
            <form  action="return.php?action=update" method="POST" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BWID</b></span>
                    <input type="number" name="BWID" value="<?php echo $_GET['BWID']; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BID</b></span>
                    <input type="number" name="BID" value="<?php echo $BID; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>RID</b></span>
                    <input type="number" name="RID" value="<?php echo $RID; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>截止时间</b></span>
                    <input type="date" name="deadline" value="<?php echo $deadline; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="hidden" value="<?php echo $_SESSION["iduser"]; ?>" name="operator">
                <input type="submit" value="修改记录" class="btn btn-primary ">
                <a href="<?php echo($_SERVER["HTTP_REFERER"]); ?>">
                    <input type="button" value="返回" class="btn btn-primary ">
                </a>
            </form>
            <?php

        } else if ($_GET["action"] == "insert") {
            /* 添加表单的提交后操作，添加图书至数据库 */
            $sql = "SELECT count(*) FROM borrow WHERE RID={$_SESSION["iduser"]} AND rtime is null";
            $result = $pdo->query($sql);
            list($count) = $result->fetch(PDO::FETCH_NUM);
            if ($count < 10) {
                borrow($_GET["BID"]);
                echo "借阅成功";
                echo "<script>ret()</script>";
            } else {
                echo '<p>只能借10本书</p>';
                header("refresh:1;url=$_SERVER[SCRIPT_NAME]");
            }

            //修改
        } else if ($_GET["action"] == "update") {
            $cols = array("BWID", "BID", "RID", "deadline", "operator");
            foreach ($cols as $col) {
                if ($_POST[$col] == "")
                    $_POST[$col] = null;
            }
            $sql = $pdo->prepare("UPDATE borrow SET BID=?, RID=?,deadline=?,operator=? WHERE BWID=?");
            $execarr = (array($_POST["BID"], $_POST["RID"], $_POST["deadline"], $_POST["operator"], $_POST["BWID"]));
            try {
                $sql->execute($execarr);
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //删除
        } else if ($_GET["action"] == "del") {
            ret($_GET['RID'], $_GET['BID'], $_GET['operator']);
            echo "还书成功";
            echo "<script>ret()</script>";


        } elseif ($_GET["action"] == "0") {
            if (count($_POST["pro"]) > 0)
                foreach ($_POST["pro"] as $temp) {
                    ret($_SESSION["iduser"], $temp, null);
                }
            echo "还书成功";
            echo "<script>ret()</script>";
            //列表
        } else {
            $ser = $_GET;
            $where = array();            //声明WHERE从句的查询条件变量

            if (!$_SESSION["isRoot"])
                $ser["RID"] = $_SESSION["iduser"];
            //根据不同字段检索
            $cols = array("BWID", "BID", "RID", "btime", "deadline", "rtime", "opeartor");
            foreach ($cols as $col) {
                if ($ser[$col] == '0' || !empty($ser[$col])) {
                    switch ($col) {
                        case "btime":
                        case "rtime":
                            $where[] = $col . " <= '{$ser[$col]}'";
                            break;
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
            $sql = "SELECT BWID,BID, RID, btime, deadline, rtime FROM borrow {$where} ORDER BY deadline DESC";

            //分页
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            require "page.class.php";
            $page = new Page($num, 15, $_SERVER['QUERY_STRING']);
            $sql = sprintf("%s {$page->limit}", $sql);

            /* 执行查询的SQL语句 */
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            /*处理结果集，打印数据记录 */
            if ($num > 0) {
                $i = 0;
                echo '<form action="return.php?action=' . $_SESSION["isRoot"] . '" method="POST">';
                echo '<table class="table table-striped table-hover table-condensed">';
                echo '<tr><th class="text-center">选中</th><th class="text-center">ID</th><th class="text-center">书名</th><th class="text-center">读者</th><th class="text-center">借阅时间</th><th class="text-center">截止时间</th><th class="text-center">归还时间</th><th class="text-center">操作</th>';
                /* 循环数据，将数据表每行数据对应的列转为变量 */
                while (list($BWID, $BID, $RID, $btime, $deadline, $rtime) = $result->fetch(PDO::FETCH_NUM)) {
                    echo '<tr>';
                    echo '<td class="text-center"><input type="checkbox" name="pro[]" value=' . $BID;
                    if (($_SESSION["isRoot"]) || (!$_SESSION["isRoot"] && $rtime))
                        echo ' disabled="disabled"></td>';
                    else
                        echo '></td>';
                    echo '<td class="text-center">' . $BWID . '</td>';
                    echo '<td class="text-center"><a href="book.php?action=list&BID=' . $BID . '">' . getBook($BID) . '</td>';
                    echo '<td class="text-center"><a href="reader.php?RID=' . $RID . '">' . getUser($RID) . '</td>';
                    echo '<td class="text-center">' . $btime . '</td>';
                    echo '<td class="text-center">' . $deadline . '</td>';
                    echo '<td class="text-center">' . $rtime . '</td>';

                    if (!$rtime)
                        if ($_SESSION["isRoot"])
                            echo '<td class="text-center"><a href="return.php?action=mod&BWID=' . $BWID . '">修改</a></td>';
                        else
                            echo '<td class="text-center"><a href="return.php?action=del&RID=' . $RID . '&BID=' . $BID . '">归还</a></td>';
                    else
                        echo '<td></td>';
                    /*  删除时会弹出确定框  */
                    echo '</tr>';
                }
                echo '<table>';
                echo "<div align=\"center\" style=\"margin: 5px\">";
                echo $page->fpage();
                if (!$_SESSION["isRoot"]) {
                    echo '<input type="submit" class="btn btn-primary " value="还书"> ';
                    echo '<input type="reset" value="重置" class="btn btn-primary ">';
                }
                echo "</div></form>";
            } else {
                echo '<p>没有记录</p>';
            }
        }
    }
    ?>
</div>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
