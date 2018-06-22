<?php
/**
 * Created by PhpStorm.
 * User: nono
 * Date: 2018/6/22
 * Time: 15:11
 */

namespace app\tlr\model;

use think\Model;    //  导入think\Model类
class LogModel extends Model
{
    protected $table = 'log';

    protected function getContentAttr($content)
    {
        $cont = str_replace("\n", "<br>", $content);
        $cont = str_replace(" ", "&nbsp;&nbsp;", $cont);
        return $cont;
    }

}
