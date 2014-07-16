<?php

namespace Api\Model;

use Think\Model;

class UserModel extends Model {
    protected $connection = 'db';
    protected $tableName = 'user';
    protected $fields = array(
        'id', 'name',
        '_pk' => 'id', '_autoinc' => true
    );
    
    protected $_validate = array(
        array('name', array('1','2','3'), 'sdfsf',2,'in'), 
    );
    
    protected $_auto = array(
        array('name', 'formateName', 1,'callback') 
    );
    
    public function formateName($name){
        return 'a'.$name;
        
    }
}

?>
