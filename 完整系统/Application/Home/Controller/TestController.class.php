<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller
{
   
    public function index()
    {
           require_once dirname(__FILE__).'/ApiController.class.php';
            $Api = new ApiController();
            $Api->index();
            
            file_put_contents('test.txt', time() . PHP_EOL);
        // $this->fanshui();
    }
    
}
?>