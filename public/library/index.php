<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>主页</title>
    <link rel="stylesheet" href="index.css">
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<?php
session_start();
if (!isset($_SESSION["isRoot"])) {
    ?>
    <table>
        <th><p class='alsp'><b><strong>图书管理系统</strong></b></p></th>
        <tr>
            <td>
                <form action="login.php?action=login" method="post">
                    <div class="input-group input-group-sm" style="width:300px;margin:0 auto;height:45px">
                        <span class="input-group-addon" id="basic-addon1"><b class='alsp2'>用户名</b></span>
                        <input type="text" name="username" class="form-control" aria-describedby="basic-addon1"
                               style="width:300px;margin:0 auto;height:45px">
                    </div>
                    <br>
                    <div class="input-group input-group-sm" style="width:300px;margin:0 auto"
                         style="width:300px;margin:0 auto;height:45px">
                    <span class="input-group-addon" id="basic-addon1"><b
                                class='alsp2'>密&nbsp;&nbsp;&nbsp;&nbsp;码</b></span>
                        <input type="password" name="password" class="form-control" aria-describedby="basic-addon1"
                               style="width:300px;margin:0 auto;height:45px">
                    </div>
                    <br>
                    <div class="input-group input-group-sm" style="width:300px;margin:0 auto">
                        <button type="submit" class="btn btn-primary" style="width:140px;"><b class='alsp2'>登录</b>
                        </button>
                        <a href="register.php">
                            <button type="button" class="btn btn-primary" style="width:140px;"><b class='alsp2'>注册</b>
                            </button>
                        </a>
                    </div>
                </form>
            </td>
        </tr>
    </table>
    <?php
} else {
    $isRoot = $_SESSION["isRoot"] ? "管理员" : "读者";
    ?>
    <table>
        <th><p class='alsp'><b><strong>您好, <?php echo "{$isRoot}: $_SESSION[username]" ?></strong></b></p></th>
        <tr>
            <td>
                <ul>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a href="return.php">归还图书</a>
                            </button>
                        </p>
                    </li>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a href="reservation.php">预约管理</a>
                            </button>
                        </p>
                    </li>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a href="book.php">借阅图书</a>
                            </button>
                        </p>
                    </li>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a href="booklist.php">书目管理</a>
                            </button>
                        </p>
                    </li>
                </ul>
            </td>
            <td>
                <ul>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a href="reader.php">读者管理</a>
                            </button>
                        </p>
                    </li>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a href="manager.php">员工管理</a>
                            </button>
                        </p>
                    </li>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a href="logs.php">日志管理</a>
                            </button>
                        </p>
                    </li>
                    <li>
                        <p class='alsp2'>
                            <button type="button" class="btn btn-default btn-lg"><a
                                        href="login.php?action=logout">退出系统</a>
                            </button>
                        </p>
                    </li>
                </ul>
            </td>
        </tr>
    </table>
    <?php
}
?>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="bootstrap.min.js"></script>
</body>
