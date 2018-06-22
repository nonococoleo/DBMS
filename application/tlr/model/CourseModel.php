<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2018/6/22
 * Time: 12:57
 */

namespace app\tlr\model;

use think\Model;    //  导入think\Model类


class CourseModel extends Model
{
    protected $table = 'course';


    protected function getContentAttr($content)
    {
        $cont = str_replace("\n", "<br>", $content);
        $cont = str_replace(" ", "&nbsp;&nbsp;", $cont);
        return $cont;
    }
}
