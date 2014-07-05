<?php

namespace Home\Model;

use Think\Model;

class UserModel extends Model {
    
    protected $connection = 'DB2';
    protected $tableName = 'user';
    protected $fields = array(
        'id', 'username',
        '_pk' => 'id', '_autoinc' => true);
    

}

?>
