<?php

namespace app\tlr\controller;

use app\tlr\model\CourseModel;
use app\tlr\model\EnrollModel;
use app\tlr\model\LogModel;
use app\tlr\model\PayModel;
use app\tlr\model\RefundModel;
use app\tlr\model\SemesterModel;
use gmars\rbac\Rbac;
use think\Controller;
use think\Request;

class System extends Controller
{
    //首页 统计信息
    public function index(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "index/index", null, 3);
            exit();
        }
        $seme = $request->param("seme");
        if (!$seme)
            $seme = session("cur_semester");
        $query = ["seme" => $seme];
        $Enroll = new EnrollModel();
        $enroll = $Enroll->alias("e")->where("e.delflag", "=", 0)->join("course c", "c.cid=e.cid")->where("semester", "=", $seme)->field("count(*) sum,cname,price*unit price,price*unit*count(*) rev")->group("e.cid")->order("rev", "desc")->paginate(10, false, ['type' => 'bootstrap', 'query' => $query]);
        $page = $enroll->render();
        $Pay = new PayModel();
        $pay = $Pay->where("delflag", "=", 0)->where("semester", "=", $seme)->field("sum(fee) price")->select();
        $Refund = new RefundModel();
        $refund = $Refund->where("delflag", "=", 0)->where("semester", "=", $seme)->field("sum(fee) price")->select();
        $Semester = new SemesterModel();
        $semester = $Semester->where("id", ">", 0)->select();
        $data = ["enroll" => $enroll, "semester" => $semester, "seme" => $seme, "pay" => $pay[0], "refund" => $refund[0], "page" => $page];
        $this->assign($data);
        $htmls = $this->fetch('index');
        return $htmls;
    }

    //设定学期、导入课程页面
    public function semester(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "index/index", null, 1);
            exit();
        }
        $Semester = new SemesterModel();
        $semester = $Semester->where("id", ">", 0)->select();
        $data = ["semester" => $semester];
        $this->assign($data);
        $htmls = $this->fetch('semester');
        return $htmls;
    }

    //设置当前学期 接口
    public function setseme(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "system/semester", null, 1);
            exit();
        }
        $seme = $request->param("seme");
        if ($seme) {
            $Semester = new SemesterModel();
            $Log = new LogModel();
            try {
                $Semester->where("id", "=", $seme)->setField("current", 1);
                $Log->save(["uid" => session('uid'), "action" => $Semester->getlastsql(), "time" => date("Y-m-d H:i:s")]);
                $Semester->where("id", "<>", $seme)->setField("current", 0);
                session("cur_semester", $seme);
            } catch (\Exception $e) {
                $this->error("修改失败", null, null, 1);
            }
            $this->success("更改成功", null, null, 1);
        } else {
            $this->error("确定学期", 'system/semester', null, 1);
        }
    }

    //添加学期
    public function addseme(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "system/semester", null, 1);
            exit();
        }
        $seme = $request->param("name");
        if ($seme) {
            $Semester = new SemesterModel();
            $Log = new LogModel();
            try {
                $Semester->save(["name" => $seme]);
                $Log->save(["uid" => session('uid'), "action" => $Semester->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            } catch (\Exception $e) {
                $this->error("修改失败", null, null, 1);
            }
            $this->success("添加成功", null, null, 1);
        } else {
            $this->error("确定学期", 'system/semester', null, 1);
        }
    }

    //发布公告
    public function announce(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "system/semester", null, 1);
            exit();
        }
        $content = $request->param("name");
        if ($content) {
            $Semester = new SemesterModel();
            $Log = new LogModel();
            try {
                $Semester->save(["announce" => $content], ["current" => 1]);
                $Log->save(["uid" => session('uid'), "action" => $Semester->getlastsql(), "time" => date("Y-m-d H:i:s")]);
            } catch (\Exception $e) {
                $this->error("修改失败", null, null, 1);
            }
            $this->success("添加成功", null, null, 1);
        } else {
            $this->error("确定内容", 'system/semester', null, 1);
        }
    }

//导出缴费csv
    public function csv_pay(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "system/semester", null, 1);
            exit();
        }

        $seme = $request->param("seme");
        $Pay = new PayModel();
        $data = $Pay->where("semester", $seme)->select();

        $RootDir = $_SERVER['DOCUMENT_ROOT'];
        $filenametest = $RootDir . '/uploads/test.csv';
        $filename = './缴费明细.csv';
        $file = fopen($filenametest, 'w');
        fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        $head = ['pid', 'delflag', 'sid', 'semester', 'fee', 'detail', 'method', 'iid', 'rid', 'date', 'uid', 'memo'];
        fputcsv($file, $head);
        foreach ($data as $line) {
            fputcsv($file, $line->toArray());
        }
        fclose($file);

        //告诉浏览器这是一个文件流格式的文件
        Header("Content-type: application/octet-stream");
        //请求范围的度量单位
        Header("Accept-Ranges: bytes");
        //Content-Length是指定包含于请求或响应中数据的字节长度
        Header("Accept-Length: " . filesize($filenametest));
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header("Content-Disposition: attachment; filename=" . $filename);

        $file = fopen($filenametest, 'r');
        echo fread($file, filesize($filenametest));
        fclose($file);
        exit();
    }

//    导出退费csv
    public function csv_refund(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "system/semester", null, 1);
            exit();
        }

        $seme = $request->param("seme");
        $Refund = new RefundModel();
        $data = $Refund->where("semester", $seme)->select();
        $RootDir = $_SERVER['DOCUMENT_ROOT'];
        $filenametest = $RootDir . '/uploads/test.csv';
        $filename = './退费明细.csv';
        $file = fopen($filenametest, 'w');
        fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        $head = ['rid', 'delflag', 'pid', 'semester', 'fee', 'detail', 'method', 'card', 'bank', 'person', 'date', 'state', 'uid', 'memo'];
        fputcsv($file, $head);
        foreach ($data as $line) {
            fputcsv($file, $line->toArray());
        }
        fclose($file);

        //告诉浏览器这是一个文件流格式的文件
        Header("Content-type: application/octet-stream");
        //请求范围的度量单位
        Header("Accept-Ranges: bytes");
        //Content-Length是指定包含于请求或响应中数据的字节长度
        Header("Accept-Length: " . filesize($filenametest));
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header("Content-Disposition: attachment; filename=" . $filename);

        $file = fopen($filenametest, 'r');
        echo fread($file, filesize($filenametest));
        fclose($file);
        exit();
    }


    //    上传csv文件批量导入课程
    public function upload(Request $request)
    {
        $rbacObj = new Rbac();
        if (!$rbacObj->can($request->path())) {
            $this->error("没有权限", "System/semester", null, 1);
            exit();
        }
        $upload = request()->file('file');
        if (empty($upload)) {
            $this->error('请选择上传文件');
        }
        $replacename = session('uid') . '_' . time() . '_' . $_FILES["file"]['name'];
        $file = $upload->move(ROOT_PATH . 'public' . DS . 'uploads', $replacename, false);

        $filename = $file->getPath() . "/" . $replacename;
        $file = fopen($filename, 'r');

        //读取内容
        $count = 0;
        while (!feof($file)) {
            $line = fgetcsv($file);

            if ($count == 0) {
                $seq = array();
                $seq['cid'] = 0;
                $seq['delflag'] = 0;
                $seq['cname'] = 0;
                $seq['time'] = 0;
                $seq['date'] = 0;
                $seq['semester'] = 0;
                $seq['campus'] = 0;
                $seq['room'] = 0;
                $seq['price'] = 0;
                $seq['unit'] = 0;
                $seq['tid'] = 0;
                $seq['fee'] = 0;
                $seq['memo'] = 0;
            }
            for ($i = 0; $i < count($line); $i++) {
                if ($line[$i] == 'cname')
                    $seq['cname'] = $i;
                if ($line[$i] == 'time')
                    $seq['time'] = $i;
                if ($line[$i] == 'date')
                    $seq['date'] = $i;
                if ($line[$i] == 'semester')
                    $seq['semester'] = $i;
                if ($line[$i] == 'campus')
                    $seq['campus'] = $i;
                if ($line[$i] == 'room')
                    $seq['room'] = $i;
                if ($line[$i] == 'price')
                    $seq['price'] = $i;
                if ($line[$i] == 'unit')
                    $seq['unit'] = $i;
                if ($line[$i] == 'tid')
                    $seq['tid'] = $i;
                if ($line[$i] == 'fee')
                    $seq['fee'] = $i;
                if ($line[$i] == 'memo')
                    $seq['memo'] = $i;
            }

            $data['cid'] = null;
            $data['delflag'] = 0;
            $data['cname'] = $line[$seq['cname']];
            $data['time'] = $line[$seq['time']];
            $data['date'] = $line[$seq['date']];
            $data['semester'] = $line[$seq['semester']];
            $data['campus'] = $line[$seq['campus']];
            $data['room'] = $line[$seq['room']];
            $data['price'] = $line[$seq['price']];
            $data['unit'] = $line[$seq['unit']];
            $data['tid'] = $line[$seq['tid']];
            $data['fee'] = $line[$seq['fee']];
            $data['memo'] = $line[$seq['memo']];

            $count++;
            if ($count == 1) {
                continue;//去掉首行记录
            }
            $courses[$count - 2] = $data;
        }
        fclose($file);
        unset($courses[$count - 2]);//去掉尾行记录

//批量添加
        $Course = new CourseModel();
        $Log = new LogModel();
        $Course->saveAll($courses);
        $Log->save(['uid' => session('uid'), "action" => $Course->getLastSql(), "time" => date("Y-m-d H:i:s")]);
        $this->success("添加成功", null, null, 1);
    }
}
