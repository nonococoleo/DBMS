<?php
namespace app\tlr\model;
use think\Model;    //  导入think\Model类


class ControllerModel extends Model
{
    protected $table='controller';
    protected function getContentAttr($content){
        $cont = str_replace("\n", "<br>", $content);
        $cont = str_replace(" ", "&nbsp;&nbsp;", $cont);
        return $cont;
    }
}
