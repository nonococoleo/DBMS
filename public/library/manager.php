<!DOCTYPE html>
<head>
    <title>员工管理</title>
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
                <li><a href="reservation.php">预约管理</a></li>
                <li><a href="book.php">借阅图书</a></li>
                <li><a href="booklist.php">书目管理</a></li>
                <li><a href="reader.php">读者管理</a></li>
                <li class="active"><a href="#">员工管理<span class="sr-only">(current)</span></a></li>
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
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "add") echo " active"; ?>"><a href="manager.php?action=add">添加员工</a></li>
    <li class="flora<?php if (!isset($_GET["action"]) || $_GET["action"] == "list") echo " active"; ?>"><a href="manager.php?action=list">员工列表</a></li>
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "ser") echo " active"; ?>"><a href="manager.php?action=ser">搜索员工</a></li>
</ul>
<div class="container">
    <br>
    <?php
    $token = md5($_SESSION["username"] . $_SERVER['HTTP_USER_AGENT'] . session_id());
    if (!isset($_SESSION["token"]) || $_SESSION["token"] != $token) {
        header("Refresh:5;Url=login.php?action=logout");
        echo "<p>登录状态过期，请重新登录</p>";
    } else if (!isset($_SESSION["isRoot"]) || !$_SESSION["isRoot"]) {
        echo "<p>需要员工权限，请重新登录后再试</p>";
        echo "<a href=\"login.php?action=logout\">重新登录</a>";
    } else {
        require_once "function.php";
        //默认展示列表
        if (!isset($_GET['action'])) {
            $_GET['action'] = 'list';
        }
        //添加表单
        if ($_GET["action"] == "add") {
            ?>
            <form action="manager.php?action=insert" method="POST" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>工号</b></span>
                    <input type="number" name="MID" class="form-control" style="width:300px;margin:0 auto;height:45px"
                           required="required">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>密码</b></span>
                    <input type="password" name="password" class="form-control"
                           style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>姓名</b></span>
                    <input type="text" name="mname" class="form-control" style="width:300px;margin:0 auto;height:45px"
                           required="required">
                </div>
                <br>
                <input type="submit" value="添加员工" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary">
            </form>
            <?php
            //搜索表单
        } else if ($_GET["action"] == "ser") {
            ?>
            <form enctype="multipart/form-data" action="manager.php" method="GET" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>工号</b></span>
                    <input type="number" name="MID" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>姓名</b></span>
                    <input type="text" name="mname" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="submit" value="查找员工" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary ">
            </form>
            <?php
            //修改表单
        } else if ($_GET["action"] == "mod") {
            $sql = "SELECT MID,password,mname FROM manager WHERE MID={$_GET['MID']}";
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            if ($num > 0) {
                list($MID, $password, $mname) = $result->fetch(PDO::FETCH_NUM);
            } else {
                echo("<p>没有找到需要修改的员工</p>");
                echo '<script>window.location="' . $_SERVER["HTTP_REFERER"] . '"</script>';
            }
            ?>
            <p class="alsp"><b>修改员工</b></p>
            <form enctype="multipart/form-data" action="manager.php?action=update" method="POST" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>工号</b></span>
                    <input type="number" name="MID" value="<?php echo $MID; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>密码</b></span>
                    <input type="password" name="password" value="<?php echo $password; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>姓名</b></span>
                    <input type="text" name="mname" value="<?php echo $mname; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <input type="submit" value="修改员工" class="btn btn-primary ">
                <a href="<?php echo($_SERVER["HTTP_REFERER"]); ?>">
                    <input type="button" value="返回" class="btn btn-primary ">
                </a>
            </form>
            <?php
        } else if ($_GET["action"] == "insert") {
            $sql = $pdo->prepare("INSERT INTO manager(MID,password,mname) VALUES(?, ?, ?)");

            $cols = array("MID", "password", "mname");
            foreach ($cols as $col) {
                if ($_POST[$col] == "")
                    $_POST[$col] = null;
            }

            $execarr = (array($_POST["MID"], md5($_POST["password"]), $_POST["mname"]));
            $sql->execute($execarr);
            include "inslog.php";

            //修改
        } else if ($_GET["action"] == "update") {
            $cols = array("MID", "password", "mname");
            foreach ($cols as $col) {
                if ($_POST[$col] == "")
                    $_POST[$col] = null;
            }
            $sql = $pdo->prepare("UPDATE manager SET password=?, mname=? WHERE MID=?");
            $execarr = (array($_POST["MID"], md5($_POST["password"]), $_POST["mname"]));
            try {
                $sql->execute($execarr);
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //删除
        } else if ($_GET["action"] == "del") {
            $sql = "DELETE FROM manager WHERE MID='{$_GET["MID"]}'";
            $sql = $pdo->prepare($sql);
            try {
                $sql->execute();
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //列表
        } else {
            $ser = $_GET;
            $where = array();            //声明WHERE从句的查询条件变量

            //根据不同字段检索
            $cols = array("MID", "mname");
            foreach ($cols as $col) {
                if ($ser[$col] == '0' || !empty($ser[$col])) {
                    switch ($col) {
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
            $sql = "SELECT MID, mname FROM manager {$where}";

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
                echo '<tr><th class="text-center">ID</th><th class="text-center">姓名</th><th class="text-center">操作</th>';
                /* 循环数据，将数据表每行数据对应的列转为变量 */
                while (list($MID, $mname) = $result->fetch(PDO::FETCH_NUM)) {
                    echo '<tr>';
                    echo '<td class="text-center">' . $MID . '</td>';
                    echo '<td class="text-center">' . $mname . '</td>';
                    if ($_SESSION["isRoot"])
                        echo '<td class="text-center"><a href="manager.php?action=mod&MID=' . $MID . '">修改</a>/<a onclick="return confirm(\'你确定要删除员工' . $mname . '吗?\')" href="manager.php?action=del&MID=' . $MID . '">删除</a></td>';
                    /*  删除时会弹出确定框  */
                    echo '</tr>';
                }
                echo '<table>';
                echo "<div align=\"center\" style=\"margin: 5px\">";
                echo $page->fpage();
                echo "</div>";
            } else {
                echo '<p>没有员工没被找到</p>';
            }
        }
    }
    ?>
</div>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
