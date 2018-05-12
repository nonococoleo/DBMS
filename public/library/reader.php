<!DOCTYPE html>
<head>
    <title>读者管理</title>
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
                <li class="active"><a href="#">读者管理<span class="sr-only">(current)</span></a></li>
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
    <li class="flora<?php if (!isset($_GET["action"]) || $_GET["action"] == "list") echo " active"; ?>"><a href="reader.php?action=list">读者列表</a></li>
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "ser") echo " active"; ?>"><a href="reader.php?action=ser">搜索读者</a></li>
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
        //搜索表单
        if ($_GET["action"] == "ser") {
            ?>
            <form enctype="multipart/form-data" action="reader.php" method="GET" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ID</b></span>
                    <input type="number" name="RID" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>账号</b></span>
                    <input type="text" name="username" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>姓名</b></span>
                    <input type="text" name="rname" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>电话</b></span>
                    <input type="text" name="phone" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>邮箱</b></span>
                    <input type="email" name="email" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="submit" value="查找读者" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary ">
            </form>
            <?php
            //修改表单
        } else if ($_GET["action"] == "mod") {
            $sql = "SELECT username, password, rname, phone, email FROM reader WHERE RID={$_GET['RID']}";
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            if ($num > 0) {
                list($username, $password, $rname, $phone, $email) = $result->fetch(PDO::FETCH_NUM);
            } else {
                echo("<p>没有找到需要修改的读者</p>");
                echo '<script>window.location="' . $_SERVER["HTTP_REFERER"] . '"</script>';
            }
            ?>
            <p class="alsp"><b>修改读者</b></p>
            <form enctype="multipart/form-data" action="reader.php?action=update" method="POST" align="center" role="form" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ID</b></span>
                    <input type="number" name="RID" value="<?php echo $_GET['RID']; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>账号</b></span>
                    <input type="text" name="username" value="<?php echo $username; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>密码</b></span>
                    <input type="password" name="password" value="<?php echo $password; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>姓名</b></span>
                    <input type="text" name="rname" value="<?php echo $rname; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>电话</b></span>
                    <input type="text" name="phone" value="<?php echo $phone; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>邮箱</b></span>
                    <input type="email" name="email" value="<?php echo $email; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="submit" value="修改读者" class="btn btn-primary ">
                <a href="<?php echo($_SERVER["HTTP_REFERER"]); ?>">
                    <input type="button" value="返回" class="btn btn-primary ">
                </a>
            </form>
            <?php
        } else if ($_GET["action"] == "update") {
            $cols = array("rname", "phone", "email");
            foreach ($cols as $col) {
                if ($_POST[$col] == "")
                    $_POST[$col] = null;
            }
            $sql = $pdo->prepare("UPDATE reader SET username=?, password=?, rname=?, phone=?, email=? WHERE RID=?");
            $execarr = (array($_POST["username"], md5($_POST["password"]), $_POST["rname"], $_POST["phone"], $_POST["email"], $_POST["RID"]));
            try {
                $sql->execute($execarr);
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //删除
        } else if ($_GET["action"] == "del") {
            $sql = "DELETE FROM reader WHERE RID='{$_GET["RID"]}'";
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
            $cols = array("RID", "username", "rname", "phone", "email");
            foreach ($cols as $col) {
                if ($ser[$col] == '0' || !empty($ser[$col])) {
                    switch ($col) {
                        case "rname":
                        case "phone":
                        case "email":
                            $where[] = $col . " like '%{$ser[$col]}%'";
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
            $sql = "SELECT RID,rname,phone,email FROM reader {$where}";

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
                echo '<tr><th class="text-center">ID</th><th class="text-center">姓名</th><th class="text-center">电话</th><th class="text-center">邮箱</th><th class="text-center">操作</th></tr>';
                /* 循环数据，将数据表每行数据对应的列转为变量 */
                while (list($RID, $rname, $phone, $email) = $result->fetch(PDO::FETCH_NUM)) {
                    echo '<tr>';
                    echo '<td class="text-center">' . $RID . '</td>';
                    echo '<td class="text-center">' . $rname . '</td>';
                    echo '<td class="text-center">' . $phone . '</td>';
                    echo '<td class="text-center">' . $email . '</td>';
                    if ($_SESSION["isRoot"])
                        echo '<td class="text-center"><a href="reader.php?action=mod&RID=' . $RID . '">修改</a>/<a onclick="return confirm(\'你确定要删除读者' . $rname . '吗?\')" href="reader.php?action=del&RID=' . $RID . '">删除</a></td>';
                    /*  删除时会弹出确定框  */
                    echo '</tr>';
                }
                echo '<table>';
                echo "<div align=\"center\" style=\"margin:5px\">";
                echo $page->fpage();
                echo "</div>";
            } else {
                echo '<p>没有读者没被找到</p>';
            }
        }
    }
    ?>
</div>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
