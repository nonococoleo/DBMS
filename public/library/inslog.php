<?php
header("refresh:1;url=$_SERVER[SCRIPT_NAME]");
$num = $sql->rowCount();
ob_start();
$sql->debugDumpParams();
$s = ob_get_contents();
ob_end_clean();
date_default_timezone_set('PRC');
if ($num > 0) {
    $t = "数据写入成功!";
    $date = date("y-m-d H:i:s");
    /*  获取当前时间  */
    $sq = str_replace("'", "''", $s);
    $result = $pdo->prepare("INSERT INTO logs(operation,timeofoperation,operator) VALUES ('$sq','$date',{$_SESSION["iduser"]})");
    $result->execute();
    /*  将添加操作添加至操作日志  */
} else {
    $t = "数据录入失败!";
}
echo $t;

