<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
class Customer extends Controller
{
    public function addCustomer(){
        $email=session('email');
        $addName=input('post.addName/s');
        $addSex=input('post.addSex/s');
        $addZip=input('post.addZip/s');
        $addPhone=input('post.addPhone/s');
        $address=input('post.address/s');
        $data = ['email' => $email, 'sname' => $addName, 'sex'=>$addSex, 'mobile'=>$addPhone, 'address'=>$address,'zip'=>$addZip,'cdefault'=>'0'];
        Db::table('tb_customer')->insert($data);
        $this->success('添加成功', url('order/order'));
    }
    
    public function editCustomer(){
        $email=session('email');
        $addName=input('post.addName/s');
        $addSex=input('post.addSex/s');
        $addZip=input('post.addZip/s');
        $addPhone=input('post.addPhone/s');
        $address=input('post.address/s');
        $data = ['email' => $email, 'sname' => $addName, 'sex'=>$addSex, 'mobile'=>$addPhone, 'address'=>$address,'zip'=>$addZip,'cdefault'=>'0'];
        db('tb_customer')->where('sname',$addName)->update(['email' => $email, 'sname' => $addName, 'sex'=>$addSex, 'mobile'=>$addPhone, 'address'=>$address,'zip'=>$addZip,'cdefault'=>'0']);
        $this->success('修改成功', url('order/order'));
    }
    public function deleteCustomer(){
        $id=input('post.custID/d');
        db('tb_customer')->where('custID',$id)->delete();
        $this->success('删除成功', url('order/order'));
    }
    
    public function setDefault(){
        $custID=input('post.custID/d');
        db('tb_customer')->where('custID',$custID)->update(['cdefault' => '1']);
        $this->success('设置成功', url('order/order'));
    }
    public function editMember(){
        $email = session('email');
        if(empty($email)){
            $this->error('请先登录!','login/login');
        }
        $mname = input('post.name');
        $mobile = input('post.phone');
        $member = MemberModel::get($email);
        $member->mname=$mname;
        $member->mobile=$mobile;
        $member->save();
        $this->success('修改成功', url('order/order'));
    }
}

