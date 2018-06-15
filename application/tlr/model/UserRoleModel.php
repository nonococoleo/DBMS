<?php
namespace app\tlr\model;
use think\Model;    //  导入think\Model类


class UserRoleModel extends Model
{
    protected $table='user_role';
    protected function getContentAttr($content){
        $cont = str_replace("\n", "<br>", $content);
        $cont = str_replace(" ", "&nbsp;&nbsp;", $cont);
        return $cont;
    }
}
