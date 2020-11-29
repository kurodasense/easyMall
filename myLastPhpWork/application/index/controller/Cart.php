<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
class Cart extends Controller
{
    public function cart(){
        if(empty(session('email'))){
            $this->error("请先登录！", 'login/login');
        }
        $data=Db::table('vcart')->where('email', session('email'))->select();//查询视图vcart
        $this->assign('result', $data);
        return $this->fetch();
    }
    
    public function clearCart(){
        $result=Db::execute("delete from cart where email='" .session('email'). "'");
        $this->redirect(url('cart/cart'));
    }
    
    public function deleteCart(){
        $param = input('get.');
        $result=Db::execute("delete from cart where cartID=".$param['cartID']);
        $this->redirect(url('cart/cart'));
    }
    
    public function updateCart(){
        $param = input('get.');
        $result=Db::execute("update cart set num=".$param['num']." where cartID=".$param['cartID']);
        $this->redirect(url('cart/cart'));
    }
    
    public function addCart(){
        //先判断是否登录（获取登陆的session信息，若获取为空则是没登陆）
        $param = input('get.');// 获取的是数组
        if(empty(session('email'))){
            $this->error('请先登录！', 'login/login');
        }
        
        // 判断是否选择商品
        if(empty(input('get.photoID'))){
            $this->error('请选择商品!', 'index/index');
        }
        
        $data = Db::table('cart')->where('email', session('email'))->where('photoID', $param['photoID'])->find();
         
        //判断购物车是否存放了该用户所选的商品
        if(empty($data)){//如果不存在就将该商品放入购物车
            $result = Db::execute("insert into cart(cartID, email, photoID, num) values(null,'".session('email')."','".$param['photoID']."',1)");
            //dump($result);
        }else{//如果存在则把原来数量+1
            $result=Db::execute("update cart set num=num+1 where email='" .session('email'). "' and photoID='" . $param['photoID'] . "'");
            //dump($result);
        }
        
        //跳转到index方法
        $this->redirect(url('cart/cart'));
    }
}

