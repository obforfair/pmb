<?php
/**
 * 用户管理
 */
namespace Api\Service;

use Lib\Ob\Service;

class UserService extends Service{
    
    public function __construct() {
    }
    /**
     * 是否已有该用户
     * @param array | int  $filter
     */
    public function ifHasUser($filter){
        $where = $filter;
        return M('User')->where($where)->count();
    }
    
    public function registerUser($data){
        $data['mobile'] = $data['mobile'] ?  $data['mobile'] : '';
        $data['password'] = $data['password'] ? md5($data['password']) : '';
        return M('user')->add($data);
    }
    
}

?>
