<?php

namespace app\tlr\controller;

use app\tlr\model\InvoiceModel;
use think\Controller;
use think\Request;

class Invoice extends Controller
{
    // 发票页面
    public function index(Request $request)
    {
        return $this->fetch('index');
    }

    // 获取发票列表
    public function invoices(Request $request)
    {
        if (!request()->isGet()) {
            $this->error("404 not found");
            exit();
        }
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $invoice = new InvoiceModel;
        $invoices = $invoice->page($page, 10)->select();
        $totalPage = ceil(db('invoice')->count() / 10);
        echo json_encode(array("invoices" => $invoices, "totalPage" => $totalPage, "success" => true));
    }


}
