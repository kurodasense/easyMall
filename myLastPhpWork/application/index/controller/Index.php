<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
class Index extends Controller{
    
    public function index()
    {
        $fclass=Db::table('profile_photos')->distinct('true')->field('pclass')->select();
        $this->assign('fclasses', $fclass);
        $this->assign('version', PHP_VERSION);
        $fname=input('post.fname');
        $fcls=input('post.fclass');
        $minprice = input('post.minprice');
        $maxprice = input('post.maxprice');
        
        if(empty($maxprice)){
            $maxprice=100000;
        }
        if(empty($minprice)){
            $minprice=0;
        }
        
        $searchstr = 'price between '. $minprice.' and '.$maxprice;
        if(!empty($fcls)){
            $searchstr.=" and pclass='".$fcls ."'";
        }
        if(!empty($fname)){
            $searchstr.=" and pname like '%" . $fname . "%'";   //模糊查询
        }
        $data=Db::table('profile_photos')->where($searchstr)->select();
        $this->assign("photos", $data);
        
        
      
        return $this->fetch();  //带数据用这个，没带用return view()
    }
}
