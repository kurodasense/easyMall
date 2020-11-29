<?php
namespace app\index\model;

use think\Model;

class Showshoplist extends Model
{
    public function showshoplist(){
        return $this->hasMany('showshoplist','orderID','SLID');
    }
    
}

