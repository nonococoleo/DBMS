<?php
namespace app\news\controller;
use think\Controller;
use app\news\model\QjwbModel;
use think\Request;
class Qjwb extends Controller
{
//    public function index()
//    {
//        // 取回打包后的数据
//        $htmls = $this->fetch();
//
//        // 将数据返回给用户
//        return $htmls;
//    }

    public function ser(Request $request)
    {
        $Qjwb=new QjwbModel;
        $keyword=$request->param('keyword');
        $date=$request->param('date');
        if($keyword&&$date){
            $date=date_format(date_create($date),"Y-m-d");
            $query=["keyword"=>$keyword,"date"=>$date];
            $qjwb=$Qjwb->where('da','=',"$date")->where('key1|key2|key3','=',"$keyword")->order('ID','asc')->paginate(25,false,['type'=>'bootstrap','query'=>$query]);
            $string="日期：".$date."&nbsp;&nbsp;&nbsp;关键词：".$keyword;
            $title=$date."&nbsp;".$keyword;
            $this->assign('keyword', $keyword);
            $this->assign('date', $date);
        }
        else if($keyword){
            $query=["keyword"=>$keyword];
            $qjwb=$Qjwb->where('key1|key2|key3','=',"$keyword")->order('da','desc')->paginate(25,false,['type'=>'bootstrap','query'=>$query]);
            $string="关键词：".$keyword;
            $title=$keyword;
            $this->assign('keyword', $keyword);
            $this->assign('date', null);
        }else{
            if(!$date){
                $date=date("Y-m-d");
            }
            $query=["date"=>$date];
            $date=date_format(date_create($date),"Y-m-d");
            $qjwb=$Qjwb->where('da','=',"$date")->order('ID','asc')->paginate(25,false,['type'=>'bootstrap','query'=>$query]);
            $string="日期：".$date;
            $title=$date;
            $this->assign('keyword', null);
            $this->assign('date', $date);
        }
        $page = $qjwb->render();
        // 向V层传数据
        $this->assign('title', $title);
        $this->assign('qjwb', $qjwb);
        $this->assign('page', $page);

        // 取回打包后的数据
        $htmls = $this->fetch();

        // 将数据返回给用户
        return $htmls;
    }

    public function detail(Request $request){
        $Qjwb=new QjwbModel;
        $id=$request->param('id');
        $qjwb=$Qjwb->where('ID','=',"$id")->find();
        $this->assign('qjwb', $qjwb);
        $this->assign('string',$_SERVER['HTTP_REFERER']);
        $this->assign('date',date_format(date_create($qjwb->da),"Y-m-d"));
        // 取回打包后的数据
        $htmls = $this->fetch();

        // 将数据返回给用户
        return $htmls;
    }
}
