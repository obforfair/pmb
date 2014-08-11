<?php

/**
 * 拍卖
 */

namespace Api\Service;

use Lib\Ob\Service;
use Lib\Ob\Crypt;
use Lib\Ob\Upload;
use Lib\Ob\Image;
use Lib\Ob\Http;

class AutionService extends Service {

    /** 获取缓存信息 */
    public function info($aid, $params = array()) {
        $key = C('PREFIX_AUTION') . $aid;
        $params && ($params = is_array($params) ? : explode(',', $params));
        $autioninfo = $this->getMCache($key, $params);
        if (empty($autioninfo)) {
            $autioninfo = M('aution')->find($aid);
            cache()->hmset($key, $autioninfo, C('EXPIRES_AUTION'));
            $params && $autioninfo = array_intersect_key($autioninfo, array_combine($params, $params));
        }
        return $autioninfo;
    }
    
    /**
     * 获取全部信息
     * @param type $aid
     */
    public function allInfo($aid){
        $aution_info = $this->info($aid);
        $data['aution_info'] = $aution_info;
        $data['user_info'] = service('User','loadUserInfo',$aution_info['user_id']);
        $data['user_info']['avata'] = service('Tools','getImage',$data['user_info']['avata']);
        $data['gallery'] = service('Tools','getImages',$aution_info['image_ids']);
        return $data;
    }
    /** 按主键更新信息 */
    public function updateP($aid, $data) {
        $key = C('PREFIX_AUTION') . $aid;
        $r = M('aution')->where(array('aution_id' => $aid))->save($data);
        $r && $this->upMCache($key, $data);
        return $r;
    }

    /** 根据主键删除信息 */
    public function delP($aid) {
        $key = C('PREFIX_AUTION') . $aid;
        $r = M('aution')->delete($aid);
        $r && cache()->delete($key);
        return $r;
    }

    /**
     * 获取拍卖id
     * @param type $where
     */
    public function getIds($where,$limit=30,$order=null) {
        $order = $order ? : "aution_id desc";
        $ids = M('aution')->field('aution_id')->where($where)->order($order)->limit($limit)->getField('aution_id',true);
        return $ids;
    }

}

?>
