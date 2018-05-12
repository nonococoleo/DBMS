<!DOCTYPE html>
<head>
    <title>书目管理</title>
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
                <li class="active"><a href="#">书目管理<span class="sr-only">(current)</span></a></li>
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
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "add") {
        echo " active";
    } ?>"><a href="booklist.php?action=add">添加书目</a></li>
    <li class="flora<?php if (!isset($_GET["action"]) || $_GET["action"] == "list") {
        echo " active";
    } ?>"><a href="booklist.php?action=list">书目列表</a></li>
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "ser") {
        echo " active";
    } ?>"><a href="booklist.php?action=ser">搜索书目</a></li>
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
            <form action="booklist.php?action=insert" method="POST" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ISBN</b></span>
                    <input type="text" name="ISBN" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>书名</b></span>
                    <input type="text" name="bname" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>出版社</b></span>
                    <input type="text" name="publisher" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>作者</b></span>
                    <input type="text" name="writer" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>出版时间</b></span>
                    <input type="date" name="ptime" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="hidden" name="operator" value="<?php echo $_SESSION["iduser"]; ?>">
                <input type="submit" value="添加书目" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary">
            </form>
            <?php

            //搜索表单
        } else if ($_GET["action"] == "add" && (!isset($_SESSION["isRoot"]) || !$_SESSION["isRoot"])) {
            echo "<p>需要员工权限，请重新登录后再试</p>";
            echo "<a href=\"login.php?action=logout\">重新登录</a>";
        } elseif ($_GET["action"] == "ser") {
            ?>
            <form enctype="multipart/form-data" action="booklist.php" method="GET" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ISBN</b></span>
                    <input type="text" name="ISBN" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>书名</b></span>
                    <input type="text" name="bname" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>出版社</b></span>
                    <input type="text" name="publisher" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>作者</b></span>
                    <input type="text" name="writer" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>出版时间</b></span>
                    <input type="date" name="ptime" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>数量</b></span>
                    <input type="number" name="number" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="submit" value="查找书目" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary ">
            </form>
            <?php

            //修改表单
        } elseif ($_GET["action"] == "mod") {
            $sql = "SELECT ISBN, bname, publisher, writer, ptime, number, operator FROM booklist WHERE ISBN={$_GET["ISBN"]}";
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            if ($num > 0) {
                list($ISBN, $bname, $publisher, $writer, $ptime, $number, $operator) = $result->fetch(PDO::FETCH_NUM);
            } else {
                echo("<p>没有找到需要修改的书目</p>");
                echo '<script>window.location="' . $_SERVER["HTTP_REFERER"] . '"</script>';
            } ?>
            <p class="alsp"><b>修改书目</b></p>
            <form enctype="multipart/form-data" action="booklist.php?action=update" method="POST" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ISBN</b></span>
                    <input type="text" name="ISBN" value="<?php echo $ISBN; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>书名</b></span>
                    <input type="text" name="bname" value="<?php echo $bname; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>出版社</b></span>
                    <input type="text" name="publisher" value="<?php echo $publisher; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>作者</b></span>
                    <input type="text" name="writer" value="<?php echo $writer; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>出版时间</b></span>
                    <input type="date" name="ptime" value="<?php echo $ptime; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <input type="hidden" name="operator" value="<?php echo $_SESSION["iduser"]; ?>">
                <input type="submit" value="修改书目" class="btn btn-primary ">
                <a href="<?php echo($_SERVER["HTTP_REFERER"]); ?>">
                    <input type="button" value="返回" class="btn btn-primary ">
                </a>
            </form>
            <?php
        } elseif ($_GET["action"] == "insert") {
            $sql = $pdo->prepare("INSERT INTO booklist(ISBN, bname, publisher, writer, ptime,  operator) VALUES(?,?,?,?,?,?)");

            $cols = array("publisher", "writer", "ptime");
            foreach ($cols as $col) {
                if ($_POST[$col] == "") {
                    $_POST[$col] = null;
                }
            }
            $execarr = (array($_POST["ISBN"], $_POST["bname"], $_POST["publisher"], $_POST["writer"], $_POST["ptime"], $_POST["operator"]));
            $sql->execute($execarr);
            include "inslog.php";

            //修改
        } elseif ($_GET["action"] == "update") {
            /*  修改表单提交后操作，更新数据库数据  */
            $cols = array("publisher", "writer", "ptime");
            foreach ($cols as $col) {
                if ($_POST[$col] == "") {
                    $_POST[$col] = null;
                }
            }
            $sql = $pdo->prepare("UPDATE booklist SET bname=?,publisher=?,writer=?,ptime=?,operator=? WHERE ISBN=?");
            $execarr = (array($_POST["bname"], $_POST["publisher"], $_POST["writer"], $_POST["ptime"], $_POST["operator"], $_POST["ISBN"]));
            try {
                $sql->execute($execarr);
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //删除
        } elseif ($_GET["action"] == "del") {
            $sql = "DELETE FROM booklist WHERE ISBN='{$_GET["ISBN"]}'";
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
            $cols = array("ISBN", "bname", "publisher", "writer", "ptime", "number");
            foreach ($cols as $col) {
                if ($ser[$col] == '0' || !empty($ser[$col])) {
                    switch ($col) {
                        case "ISBN":
                            $where[] = $col . " = '{$ser[$col]}'";
                            break;
                        case "number":
                            $where[] = $col . " >= '{$ser[$col]}'";
                            break;
                        case "ptime":
                            $where[] = $col . " <= '{$ser[$col]}'";
                            break;
                        default:
                            $where[] = $col . " like '%{$ser[$col]}%'";
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
            $sql = "SELECT ISBN, bname, publisher, writer, ptime, number, operator FROM booklist {$where}";

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
                echo '<table class="table table-striped table-hover table-condensed">';
                echo '<tr><th class="text-center">ISBN</th><th class="text-center">书名</th><th class="text-center">出版社</th><th class="text-center">作者</th><th class="text-center">出版时间</th><th class="text-center">数量</th><th class="text-center">操作</th></tr>';
                /* 循环数据，将数据表每行数据对应的列转为变量 */
                while (list($ISBN, $bname, $publisher, $writer, $ptime, $number, $operator) = $result->fetch(PDO::FETCH_NUM)) {
                    $time = date("Y-m", strtotime($ptime));
                    echo '<tr>';
                    echo '<td class="text-center">' . $ISBN . '</td>';
                    echo '<td class="text-center"><a target="_blank" href="http://douban.com/isbn/' . $ISBN . '/">' . $bname . '</td>';
                    echo '<td class="text-center">' . $publisher . '</td>';
                    echo '<td class="text-center">' . $writer . '</td>';
                    echo '<td class="text-center">' . $time . '</td>';
                    echo '<td class="text-center">' . $number . '</td>';
                    if ($_SESSION["isRoot"]) {
                        echo '<td class="text-center"><a href="booklist.php?action=mod&ISBN=' . $ISBN . '">修改</a>/<a href="book.php?action=add&ISBN=' . $ISBN . '">添加</a>';
                        if ($number == 0)
                            echo '/<a onclick="return confirm(\'你确定要删除书目 ' . $bname . ' 吗?\')" href="booklist.php?action=del&ISBN=' . $ISBN . '">删除</a></td>';
                        else
                            echo '</td>';
                    } elseif ($number > 0) {
                        echo '<td class="text-center"><a href="book.php?ISBN=' . $ISBN . '">详细</a></td>';
                    } /*  删除时会弹出确定框  */
                    else {
                        echo '<td></td>';
                    }
                    echo '</tr>';
                }
                echo '<table>';
                echo "<div align=\"center\" style=\"margin: 5px\">";
                echo $page->fpage();
                echo "</div>";
            } else {
                echo '<p>没有书目被找到</p>';
            }
        }
    }
    ?>
</div>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
