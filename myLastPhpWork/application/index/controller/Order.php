<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Customer;
use app\index\model\Member;
use app\index\model\Showorder;
use app\index\model\Shoplist;
use app\index\model\Cart;
use app\index\model\Myorder;
use app\index\model\Flower;
use app\index\model\Profilephotos;
use think\Db;
use think\Request;
class Order extends Controller
{
    public function order(){
        //	判断是否登录
        if(empty(session('email'))){
            $this->error('请先登录!','login/login');
        }
    
    
        //	查询该email登录用户的所有收货地址
        $data = Customer::where('email',session('email'))->select();
        $this->assign('Customers',$data);
    
        //	查询该email登录用户的用户信息
        $mem = Member::get(session('email'));
        $this->assign('member',$mem);
    
        //	获取购物车中所选商品，查找vcart获取购买鲜花的信息
        $cartIDs=input('post.cartID/a');
    
        session('cartIDs',$cartIDs);
    
        $vcart=Db::table('vcart')->where('cartID', 'in', $cartIDs)->select();
        $this->assign('vcart',$vcart);
        $isSelect = Db::table('tb_customer')->where('email', session('email'))->select();
        $this->assign('isSelected', $isSelect);
        return $this->fetch();
    }
    
    public function addOrder(){
        //判断是否登录
        if(empty(session('email'))){
            $this->error('请先登录!','login/login');
        }
    
        Db::transaction(function () {
            // (1)添加订单信息到myorder表
            // (1.1)新建myorder对象，并将获取的表单值绑定到对应的属性。
            $order = new Myorder();
            $order->email = session('email');
            $order->custID = input('post.custID');
            $order->shifu = input('post.total');
            $order->inputtime = date("Y-m-d H:i:s");
            $order->peisongday = input('post.date');
            $order->peisongtime = input('post.time');
            $order->buy_name = input('post.buy_name');
            $order->pay_with = input('post.pay_with');
            $order->status = '未付款';
            $order->cltime = $order->inputtime;
            // (1.2)添加订单
            $order->save();
            // (1.3)查找新添加的订单编号
            $sch = "email='" . session('email') . "' and inputtime='" . $order->inputtime . "'";
            $orderN = Myorder::where($sch)->find();
            $orderID = $orderN->orderID;
            echo $orderID;
    
            // （2）将购买的商品信息及数量添加到shoplist表
            // （2.1）根据选择的商品编号查看cart表
            $cartIDs = session('cartIDs');
            $carts = Cart::where('cartID', 'in', $cartIDs)->select();
    
            // (2.2)遍历cart
            foreach ($carts as $cart) {
                // (2.3)新建shoplist表对象$shoplist
                $shoplist = new Shoplist();
                // (2.4)绑定orderID、email、flowerID、num属性属性
                $shoplist->orderID = $orderID;
                $shoplist->photoID = $cart->photoID;
                $shoplist->email = session('email');
                $shoplist->num = $cart->num;
                // (2.5) 添加到shoplist表
                $shoplist->save();
                $photo = Profilephotos::get($cart->photoID);
                $photo->save();
                // (4) 在购物车中删除对于的商品
                $cart->delete();
            }
        });
        return "success";
    
    }
    
    public function showorder(){
        //判断是否登录
        if (empty(session('email'))) {
            $this->error('请先登录', 'login/login');
        }
        $orders = Showorder::where('email', session('email'))->order('orderID desc')->paginate(3);
        $page = $orders->render();
        $this->assign('showorder', $orders);
        $this->assign('page', $page);
    
        $orderlists = array();
        foreach ($orders as $order) {
            $shoplistitems = array();
            foreach ($order->showshoplist as $shoplist) {
                if($order->orderID ==  $shoplist->orderID){
                    array_push($shoplistitems, $shoplist);
                }
            }
            array_push($orderlists, $shoplistitems);
        }
        //         var_dump($orderlists);
        $this->assign('orderlists', $orderlists);
        return $this->fetch();
    
    }
    
    public function pay(){
        
        $orderID = input("get.id");
        
        $order = Myorder::get($orderID);
        
        $order->status = "已付款";
        $order->cltime = date("Y-m-d H:i:s");
        $order->save();
        
        $this->redirect('order/showorder');
    }
    
    public function delete(){
        Db::transaction(function () {
            
            $orderID = input('post.orderID');
            
            $shoplists = Shoplist::where('orderID', $orderID)->select();
            
            foreach($shoplists as $shoplist){
                $photoID = $shoplist->photoID;

                
                $photo = Profilephotos::get($photoID);

                $photo->save();
                
                $shoplist->delete();
            }
            $order = Myorder::get($orderID);
            $order->delete();
        });
        return 'success';
    }
    
    public function orderupdate(){
        $orderID=input('get.orderID/d');
        $order=Myorder::get($orderID);
        $order->status='未评价';
        $order->cltime=date('Y-m-d H:i:s');
        $order->save();
        $this->redirect('order/showorder');
    }
    
    public function evaluate(){
        $orderID = input('get.orderID/d');
        $data = Db::table('showshoplist')->where('orderID', $orderID)->select();
        $this->assign('results', $data);
        return $this->fetch();
    }
    
    public function doEvaluate(Request $request){
        $orderID = input('post.orderID/d');
        $datas = Shoplist::where('orderID', $orderID)->select();
        foreach ($datas as $shoplist) {
            $SLID=$shoplist->SLID;
            $shoplist->email = session('email');
            $shoplist->pjstar = $request->param('pjstar'.$SLID);
            $shoplist->pjcontent = $request->param('pjcontent'.$SLID);
            $shoplist->pjip = $request->ip();
            $shoplist->pjtime = date('Y-m-d H:i:s');
            $shoplist->save();
        }
        $order = Myorder::get($shoplist->orderID);
        $order->status = '已评价';
        $order->cltime = date('Y-m-d H:i:s');
        $order->save();
        $this->redirect('order/showorder');
    }
}

