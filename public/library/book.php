<!DOCTYPE html>
<head>
    <title>图书管理</title>
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
                <li class="active"><a href="#">借阅图书<span class="sr-only">(current)</span></a></li>
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
    <li class="flora<?php if (!isset($_GET["action"]) || $_GET["action"] == "list") echo " active"; ?>"><a href="book.php?action=list">单册列表</a></li>
    <li class="flora<?php if (isset($_GET["action"]) && $_GET["action"] == "ser") echo " active"; ?>"><a href="book.php?action=ser">搜索单册</a></li>
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
        if ($_GET["action"] == "add") {
            ?>
            <form  action="book.php?action=insert" method="POST" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ISBN</b></span>
                    <input type="text" name="ISBN" value="<?php echo $_GET["ISBN"]; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>地点</b></span>
                    <input type="text" name="location" value="" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>状态</b></span>
                    <select name="state" style="width:300px;margin:0 auto;height:45px" class="form-control">
                        <option value="1">在库</option>
                        <option value="2">借出</option>
                        <option value="3">保留</option>
                        <option value="4">处理</option>
                    </select>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>数量</b></span>
                    <input type="number" name="number" value="1" class="form-control"
                           style="width:300px;margin:0 auto;height:45px" required="required">
                </div>
                <br>
                <input type="hidden" name="operator" value="<?php echo $_SESSION["iduser"]; ?>">
                <input type="submit" value="添加单册" class="btn btn-primary">
                <input type="reset" value="重置" class="btn btn-primary ">
            </form>
            <?php

            //搜索表单
        } elseif ($_GET["action"] == "ser") {
            ?>
            <form  action="book.php" method="GET" class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ID</b></span>
                    <input type="number" name="BID" value="" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ISBN</b></span>
                    <input type="text" name="ISBN" value="" class="form-control"
                           style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>地点</b></span>
                    <input type="text" name="location" value="" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>状态</b></span>
                    <select name="state" style="width:300px;margin:0 auto;height:45px" class="form-control">
                        <option value="0">全部</option>
                        <option value="1">在库</option>
                        <option value="2">借出</option>
                        <option value="3">保留</option>
                        <option value="4">处理</option>
                    </select>
                </div>
                <br>
                <input type="submit" value="查找单册" class="btn btn-primary ">
                <input type="reset" value="重置" class="btn btn-primary ">
            </form>
            <?php

            //修改表单
        } elseif ($_GET["action"] == "mod") {
            $sql = "SELECT BID, ISBN, location, state, operator FROM book WHERE BID={$_GET["BID"]}";
            $result = $pdo->query($sql);
            $num = $result->rowCount();
            if ($num > 0) {
                list($BID, $ISBN, $location, $state, $operator) = $result->fetch(PDO::FETCH_NUM);
            } else {
                echo("<p>没有找到需要修改的图书</p>");
                echo '<script>window.location="' . $_SERVER["HTTP_REFERER"] . '"</script>';
            } ?>
            <p class="alsp"><b>修改图书</b></p>
            <form action="book.php?action=update" method="POST"  class="alsp">
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>BID</b></span>
                    <input type="number" name="BID" value="<?php echo $BID; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>ISBN</b></span>
                    <input type="text" name="ISBN" value="<?php echo $ISBN; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px" readonly>
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>存放位置</b></span>
                    <input type="text" name="location" value="<?php echo $location; ?>" class="form-control" style="width:300px;margin:0 auto;height:45px">
                </div>
                <br>
                <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon"><b>状态</b></span>
                    <select name="state" style="width:300px;margin:0 auto;height:45px" class="form-control">
                        <option value="1" <?php if ($state == 1) echo 'selected="selected"'; ?>>在库</option>
                        <option value="2" <?php if ($state == 2) echo 'selected="selected"'; ?>>借出</option>
                        <option value="3" <?php if ($state == 3) echo 'selected="selected"'; ?>>保留</option>
                        <option value="4" <?php if ($state == 4) echo 'selected="selected"'; ?>>处理</option>
                    </select>
                </div>
                <br>
                <input type="hidden" name="operator" value="<?php echo $_SESSION["iduser"]; ?>">
                <input type="submit" value="修改单册" class="btn btn-primary ">
                <a href="<?php echo($_SERVER["HTTP_REFERER"]); ?>">
                    <input type="button" value="返回" class="btn btn-primary ">
                </a>
            </form>
            <?php

        } elseif ($_GET["action"] == "insert") {
            $sql = $pdo->prepare("CALL new_book(?,?,?,?,?)");
            $execarr = (array($_POST["ISBN"], $_POST["location"], $_POST["state"], $_POST["operator"], $_POST["number"]));
            try {
                $sql->execute($execarr);
            } catch (PDOException $e) {
                echo "外键约束";
            }

            $sql = $pdo->prepare("UPDATE booklist SET number=number+? WHERE ISBN=?");
            $execarr = (array($_POST["number"], $_POST["ISBN"]));
            $sql->execute($execarr);
            include "inslog.php";

            //修改
        } elseif ($_GET["action"] == "update") {
            /*  修改表单提交后操作，更新数据库数据  */
            $sql = $pdo->prepare("UPDATE book SET location=?, state=?, operator=? WHERE BID=?");
            $execarr = (array($_POST["location"], $_POST["state"], $_POST["operator"], $_POST["BID"]));
            try {
                $sql->execute($execarr);
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //删除
        } elseif ($_GET["action"] == "del") {
            /* 如果用户的操作是请求删除课程action=del，则条件成立，执行删除操作 */
            $sql = "DELETE FROM book WHERE BID='{$_GET["BID"]}'";
            $execarr = (array(getISBN($_GET["BID"])));
            $sql = $pdo->prepare($sql);
            try {
                $sql->execute();
            } catch (PDOException $e) {
                echo "外键约束";
            }
            include "inslog.php";

            //列表
        } elseif ($_GET["action"] == "1") {
            if (count($_POST["pro"]) > 0)
                foreach ($_POST["pro"] as $temp) {
                    $sql = $pdo->prepare("UPDATE book SET state=1, operator=? WHERE BID=?");
                    $execarr = (array($_SESSION["iduser"], $temp));
                    try {
                        $sql->execute($execarr);
                    } catch (PDOException $e) {
                        echo "外键约束";
                    }
                }
            echo "上架成功";
            echo "<script>ret()</script>";

        } elseif ($_GET["action"] == "0") {
            if (count($_POST["pro"]) > 0)
                foreach ($_POST["pro"] as $temp) {
                    borrow($temp);
                }
            echo "借阅成功";
            echo "<script>ret()</script>";

        } else {
            $ser = $_GET;
            $where = array();            //声明WHERE从句的查询条件变量

                //根据不同字段检索
                $cols = array("BID", "ISBN", "location", "state");
                foreach ($cols as $col) {
                    if (!empty($ser[$col])) {
                        switch ($col) {
                            case "BID":
                                $where[] = "b." . $col . " like '%{$ser[$col]}%'";
                                break;
                            case "location":
                                $where[] = $col . " like '%{$ser[$col]}%'";
                                break;
                            default:
                                $where[] = $col . " = {$ser[$col]}";
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
                $sql = "SELECT b.BID, ISBN, location, state,RID FROM book AS b LEFT JOIN reservation AS r ON b.BID=r.BID {$where}";

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
                    echo '<form action="book.php?action=' . $_SESSION["isRoot"] . '" method="POST">';
                    echo '<table class="table table-striped table-hover table-condensed">';
                    echo '<tr><th class="text-center">选中</th><th class="text-center">图书编号</th><th class="text-center">ISBN</th><th class="text-center">书名</th><th class="text-center">存放位置</th><th class="text-center">状态</th><th class="text-center">操作</th></tr>';
                    /* 循环数据，将数据表每行数据对应的列转为变量 */
                    while (list($BID, $ISBN, $location, $state, $RID) = $result->fetch(PDO::FETCH_NUM)) {
                        echo '<tr>';
                        echo '<td class="text-center"><input type="checkbox" name="pro[]" value=' . $BID;
                        if (($_SESSION["isRoot"] && $state < 4) || (!$_SESSION["isRoot"] && $state > 1))
                            echo ' disabled="disabled"></td>';
                        else
                            echo '></td>';
                        echo '<td class="text-center">' . $BID . '</td>';
                        echo '<td class="text-center"><a  href="./book.php?ISBN=' . $ISBN . '">' . $ISBN . '</td>';
                        echo '<td class="text-center"><a href="booklist.php?ISBN=' . $ISBN . '">' . getBook($BID) . '</td>';
                        echo '<td class="text-center">' . $location . '</td>';
                        echo '<td class="text-center">' . getState($state) . '</td>';
                        if ($_SESSION["isRoot"]) {
                            if ($state != 2)
                                echo '<td class="text-center"><a href="book.php?action=mod&BID=' . $BID . '">修改</a>/<a onclick="return confirm(\'你确定要删除单册' . $BID . '吗?\')" href="book.php?action=del&BID=' . $BID . '">删除</a></td>';
                            else
                                echo "<td></td>";
                        } else {
                            echo '<td class="text-center">';
                            if (($state == 1) || ($state == 3 && $RID == $_SESSION["iduser"])) {
                                echo '<a onclick="return confirm(\'你确定要借阅图书 ' . getBook($BID) . ' 吗?\')" href="return.php?action=insert&BID=' . $BID . '">借阅</a>';
                            } else if ($state == 2) {
                                echo '<a onclick="return confirm(\'你确定要预约图书 ' . getBook($BID) . ' 吗?\')" href="reservation.php?action=insert&BID=' . $BID . '">预约</a>';
                            }
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                    echo '<table>';
                    echo "<div align=\"center\" style=\"margin: 5px\">";
                    echo $page->fpage();
                    if ($_SESSION["isRoot"])
                        echo '<input type="submit" class="btn btn-primary " value="上架"> ';
                    else
                        echo '<input type="submit" class="btn btn-primary " value="借阅"> ';
                    echo '<input type="reset" value="重置" class="btn btn-primary ">';
                    echo "</div></form>";
                } else {
                    echo '<p>没有图书被找到</p>';
                }
            }
    }
    ?>
</div>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
