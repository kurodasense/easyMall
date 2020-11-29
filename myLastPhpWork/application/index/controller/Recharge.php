<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
class Recharge extends Controller
{
    public function recharge(){
        return view();
    }
    public function doRecharge(){
        $result = Db::execute("update tb_member	set	ye=".input('post.recharge')." where	email='".session('email')."'");
        $this->success('充值成功', url('recharge/recharge'));
    }
}

