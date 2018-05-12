<?php
require "conn.php";

function getUser($usrid)
{
    if (!$usrid) {
        return NULL;
    }
    global $pdo;
    $temp = $pdo->query("SELECT * FROM reader WHERE RID=" . $usrid);
    foreach ($temp as $value) {
        return $value["rname"];
    }
    return null;
}

function getISBN($bid)
{
    if (!$bid) {
        return NULL;
    }
    global $pdo;
    $temp = $pdo->query("SELECT * FROM book WHERE BID=" . $bid);
    foreach ($temp as $value) {
        return $value["ISBN"];
    }
    return null;
}

function getBook($bid)
{
    if (!$bid) {
        return NULL;
    }
    global $pdo;
    $temp = $pdo->query("SELECT * FROM book as b,booklist as l WHERE BID=" . $bid . " and b.ISBN=l.ISBN");
    foreach ($temp as $value) {
        return $value["bname"];
    }
    return null;
}

function is_md5($password)
{
    return preg_match("/^[a-z0-9]{32}$/", $password);
}

function getState($state)
{
    switch ($state) {
        case 1:
            return "在库";
            break;
        case 2:
            return "借出";
            break;
        case 3:
            return "保留";
            break;
        case 4:
            return "处理";
            break;
    }
    return null;
}

function borrow($bid)
{
    global $pdo;
    $sql = $pdo->prepare("INSERT INTO borrow(BID,RID,btime,deadline) VALUES(?, ?, ?,?)");
    $time = date("y-m-d H:i:s");
    $deadline = date("y-m-d H:i:s", strtotime("+30 day"));
    /* 执行INSERT语句 */
    $execarr = (array($bid, $_SESSION["iduser"], $time, $deadline));
    try {
        $sql->execute($execarr);
    } catch (PDOException $e) {
        echo "外键约束";
    }
    $sql = $pdo->prepare("UPDATE book SET state=2 WHERE BID=?");
    $execarr = (array($bid));
    $sql->execute($execarr);
}

function ret($rid, $bid, $operator)
{
    global $pdo;
    $sql = "SELECT RID FROM reservation WHERE BID={$bid}";
    $result = $pdo->query($sql);
    $num = $result->rowCount();

    $sql = "SELECT BWID FROM borrow WHERE RID={$rid} AND BID={$bid} AND rtime is null";
    $result = $pdo->query($sql);
    list($BWID) = $result->fetch(PDO::FETCH_NUM);
    $sql = $pdo->prepare("UPDATE borrow SET rtime=?,operator=? WHERE BWID=?");
    $time = date("y-m-d H:i:s");
    if ($operator == "")
        $operator = null;
    $execarr = (array($time, $operator, $BWID));
    try {
        $sql->execute($execarr);
    } catch (PDOException $e) {
        echo "外键约束";
    }

    $sql = "SELECT deadline,rtime FROM borrow WHERE BWID=$BWID";
    $result = $pdo->query($sql);
    list($deadline, $rtime) = $result->fetch(PDO::FETCH_NUM);
    if ($deadline < $rtime) {
        $temp = (strtotime($rtime) - strtotime($deadline)) / (24 * 60 * 60);
        echo "<script type=\"text/javascript\">window.alert(\"$temp\");</script>";
    }

    if ($num > 0) {
        $sql = $pdo->prepare("UPDATE book SET state=3 WHERE BID=?");
        echo "<script type=\"text/javascript\">window.alert(\"通知预约者\");</script>";
    } else
        $sql = $pdo->prepare("UPDATE book SET state=4 WHERE BID=?");
    $execarr = (array($bid));
    $sql->execute($execarr);
}
