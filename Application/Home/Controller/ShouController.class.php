<?php
namespace Home\Controller;

use Think\Controller;

header('content-type:text/html;charset=utf-8');

class ShouController extends BaseController
{
    public function index()
    {
        $this->display();
    }
}
?>