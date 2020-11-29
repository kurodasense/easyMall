<?php
namespace app\index\model;

use think\Model;

class Member extends Model{
    //	设置完整的数据表名（包含前缀）
    protected $table = 'tb_member';
    public function Member(){
        return $this->hasMany('customer','email','custID');
    }
}

?>
