<?php
namespace app\index\controller;

use think\Controller;
class Login extends Controller
{
    public function login(){
        return view();
    }
    
    public function doLogin(){
        if(empty(input('post.email'))){
            $this->error('email不能为空');
        }
        
        $param=input('post.');
        // 在tb_member表中找是否有这个email,有的话就返回一行用户信息;没有就返回空
        $rs=db('tb_member')->where('email', $param['email'])->find();
        if(empty($rs)){
            $this->error('用户名错误');// 没有找到就会返回空，则提示错误
        }
        
        if($rs['password']!=$param['passw']){
            $this->error('密码错误');// 如果email存在，则判断密码是否匹配
        }
        
        //如果用户名和密码正确，则将登录的邮箱存入session
        session('email', $rs['email']);
        session('name', $rs['mname']);
        //跳转到主页面
        $this->redirect(url('index/index'));
    }
    
    public function LogOut(){
        session('email', null);
        session('name', null);
        $this->redirect(url('index/index'));
    }
}

