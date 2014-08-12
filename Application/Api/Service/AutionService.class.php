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

    private function _switchOrder($order) {
        switch ($order) {
            case 1:
                $order = 'end_time desc';
                break;
            case 2:
                $order = 'end_time asc';
                break;
            case 3:
                $order = 'start_time desc';
                break;
            case 4:
                $order = 'start_time asc';
                break;
            case 5:
                $order = 'aution_id desc';
                break;
            case 6:
                $order = 'aution_id asc';
                break;
            default:
                $order = 'end_time desc';
        }
        return $order;
    }

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
    public function allInfo($aid) {
        $aution_info = $this->info($aid);
        $data['aution_info'] = $aution_info;
        $data['user_info'] = service('User', 'loadUserInfo', $aution_info['user_id']);
        $data['user_info']['avata'] = service('Tools', 'getImage', $data['user_info']['avata']);
        $data['gallery'] = service('Tools', 'getImages', $aution_info['image_ids']);
        return $data;
    }

    /**
     * 获取集市信息
     * @param type $aid
     * @return type
     */
    public function marketInfo($aid) {
        $aution_info = $this->info($aid);
        $data['aution_info'] = $aution_info?:array();
        $data['counter'] = $this->loadAutionCounter($aid)?:array();
        $data['gallery'] = service('Tools', 'getImages', $aution_info['image_ids'])?:array();
        return $data;
    }

    public function loadAutionCounter($aid) {
        $key = C('PREFIX_AUTION_COUNTER') . $aid;
        $counter = $this->getMCache($key);
        if (!$counter) {
            $counter = M('auton_counter')->find($aid);
            $counter && $this->upMCache($key, $counter);
        }
        return $counter;
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
    public function loadAutionIds($status, $order, $first_id) {
        //TODO::当redis不可用时可能会多数据或少数据
        $key = C('PREFIX_AUTION_LIST_ID') . $status . '_' . $order . '_' . $first_id;
        $key_default = C('PREFIX_AUTION_LIST_ID') . $status . '_' . $order . '_';
        $ids = cache()->get($key);
        if (!$ids) {
            $sorder = $this->_switchOrder($order);
            $ids = M('aution')->field('aution_id')->where(array('status' => $status))->order($sorder)->getField('aution_id', true);
            $ids && $first_id = $ids[0];
            $key = C('PREFIX_AUTION_LIST_ID') . $status . '_' . $order . '_' . $first_id;
            cache()->set($key, json_encode($ids), C('EXPIRES_AUTION_LIST_ID'));
            cache()->set($key_default, json_encode($ids), C('EXPIRES_AUTION_LIST_ID'));
        } else {
            #增加过期时间
            cache()->setTimeout($key, C('EXPIRES_AUTION_LIST_ID'));
        }
        return $ids;
    }

}

?>
