<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Profilephotos;
use app\index\model\Shoplist;
class Showphoto extends Controller
{
    public function photodetail(){
        $photoID = input('get.photoID');
        $photo=Profilephotos::get($photoID);
        $this->assign('photo',$photo);
        $shoplists=Shoplist::where("photoID='".$photoID."' and pjstar is not null")->select();
        $this->assign('shoplists',$shoplists);
        return $this->fetch('photodetail');
    }
    
}

