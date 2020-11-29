<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\captcha\Captcha;
class Register extends Controller
{
    public function register(){
        return view();
    }
    
    public function doRegister(){
        $param = input('post.');
        if(empty($param['email'])){
            $this->error('emaill不能为空');
        }
        if(empty($param['passw1'])){
            $this->error('密码不能为空');
        }
        if(empty($param['passw2'])){
            $this->error('确认密码不能为空');
        }
        if($param['passw1'] != $param['passw2']){
            $this->error('两次密码必须一致！');
        }
        $data = db('tb_member')->where('email', $param['email'])->find();
        if(!empty($data)){
            $this->error('该用户已被注册!');
        }
        $code = $param['yanzhengma'];
        //验证码
        $captcha=new Captcha();
        if(!$captcha->check($code))	{
            $this->error('验证码错误');
        }
        
        
        $result = Db::execute("insert into tb_member(email,password,mname,mobile,address,jifen,ye) values('" .$param['email']. "','" .$param['passw1']. "','" .$param['mname']. "','" .$param['mobile']. "','" .$param['address']. "',0,0)");
        $this->success('注册成功', url('login/login'));
    }
}

