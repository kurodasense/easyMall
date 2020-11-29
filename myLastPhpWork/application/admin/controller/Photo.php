<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\index\model\Profilephotos as PhotoModel;

class Photo extends Controller
{
    public function index(){
        if(empty(session('username')))
        {
            $this->error('请先登录','adminlogin/index');
        }
        $photos=PhotoModel::all();
        $this->assign('photos',$photos);
        return $this->fetch();
    }
    public function photoadd(){
        return $this->fetch();
    }
    
    public function addPhoto(Request $request){
        $photoID=$request->param('photoID');
        if(empty($photoID)){
            $this->error('请填写鲜花编号');
        }
        $photo1=PhotoModel::get($photoID);
        if(!empty($photo1)){
            $this->error('您填写鲜花编号已存在！');
        }
        $photo=new PhotoModel();
        $photo->photoID=$photoID;
        $photo->pname=$request->param('pname');
        $photo->pclass=$request->param('pclass');
        
        $photo->price=$request->param('price');
        
    
        $picture=$request->file('picture');
        if	(empty($picture)){
            $this->error('请选择上传文件');
        }
        $info=$picture->validate(['ext'=>'jpg,png'])->move(ROOT_PATH.'public/static/images/profile_photos'.DS.'','');
        $photo->picture=$info->getSaveName();
    
    
        $photo->save();
        $this->success('添加成功！','photo/index');
    }
    
    public function photoDelete(){
        $flower=PhotoModel::get(input('get.photoID'));
        $flower->delete();
        $this->redirect('photo/index');
    }
    
    public function photoupdate(){
        $photo=PhotoModel::get(input('get.photoID'));
        $this->assign('photo',$photo);
        return $this->fetch();
    }
    
    
    public function updatePhoto(Request $request){
        $photo=PhotoModel::get(input('post.photoID'));
        $photo->pname=$request->param('pname');
        $photo->pclass=$request->param('pclass');

        $photo->price=$request->param('price');

         
        $picture=$request->file('picture');
        if(!empty($picture)){
            $info=$picture->validate(['ext'=>'jpg,png'])->move(ROOT_PATH.'public/static'.DS.'picture','');
            $photo->picture=$info->getSaveName();
        }
    
     
        $photo->save();
        $this->success('修改成功！','photo/index');
    }
    
    
    
}

