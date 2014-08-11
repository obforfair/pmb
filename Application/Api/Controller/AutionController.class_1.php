<?php

/**
 * 拍品相关接口
 */

namespace Api\Controller;

use Think\Controller;

class AutionController extends Controller {

    protected $user;

    public function __construct() {
        parent::__construct();
        $this->user = service('Tools', 'getSid');
    }

    /** 查看是否是自己上传的拍品 */
    private function _ifMyAution($aid) {
        ($user = $this->user) || jsonReturn(E_UNLOGIN);
        #查看是否是自己上传的拍品
        $aution_info = service('Aution', 'loadAution', array($aid, 'user_id,status'));
        $aution_info || jsonReturn(E_FAILURE, '数据为空');
        ($aution_info['user_id'] == $user['uid']) || jsonReturn(E_NORIGHT);
        ($aution_info['status'] != 0) && jsonReturn(E_FAILURE, '拍品已发布');
    }

    /**
     * 上传拍品 
     */
    public function upload($image_ids, $title, $description = '', $c_id = 1) {
        //TODO::判断权限
        #数据检查
        ($user = $this->user) || jsonReturn(E_UNLOGIN);
        ($image_ids = explode(',', $image_ids)) || jsonReturn(E_FAILURE);
        ($image_ids = service('Tools', 'checkImageids', array($image_ids))) || jsonReturn(E_FAILURE);
        $data['user_id'] = $user['uid'];
        $data['title'] = $title;
        $data['description'] = $description;
        $data['cover_id'] = $image_ids[0];
        $data['gallery_id'] = service('Tools', 'createGallery', array($image_ids));
        $category_id = service('Tools', 'getCategoryId', $c_id);
        $data['category_id'] = $category_id ? $category_id : 1;
        $r = M('aution')->add($data);
        return $r ? jsonReturn(E_SUCCESS, array('aid' => $r)) : jsonReturn(E_FAILURE);
    }

    /**
     * 更新拍品
     */
    public function update($aid, $image_ids, $title) {
        ($user = $this->user) || jsonReturn(E_UNLOGIN);
        #查看是否是自己上传的拍品
        $this->_ifMyAution($aid);
        #数据整合
        ($image_ids = explode(',', $image_ids)) || jsonReturn(E_FAILURE);
        ($image_ids = service('Tools', 'checkImageids', array($image_ids))) || jsonReturn(E_FAILURE);
        I('title') && $data['title'] = I('title');
        !is_null(I('description', NULL)) && $data['description'] = I('description');
        $data['cover_id'] = $image_ids[0];
        $data['gallery_id'] = service('Tools', 'createGallery', array($image_ids));
        #操作
        isset($data) || jsonReturn(E_FAILURE);
        $r = service('Aution', 'updateP', array($aid, $data));
        return $r ? jsonReturn(E_SUCCESS) : jsonReturn(E_FAILURE);
    }

    /**
     * 发布拍品
     * @param type $type
     */
    public function publish($aid) {
        ($user = $this->user) || jsonReturn(E_UNLOGIN);
        #查看是否是自己上传的拍品
        $this->_ifMyAution($aid);
        #数据整合
        I('p_s') ? $data['price_start'] = intval(I('p_s')) : 0;
        I('p_r') ? $data['price_reserver'] = intval(I('p_r')) : 0;
        I('p_fl') ? $data['price_flatly'] = intval(I('p_fl')) : 0;
        I('rt') ? $data['rate'] = intval(I('rt')) : 0;
        $data['aution_id'] = intval($aid);
        $data['time_start'] = I('t_s', '', 'check_time') ? I('t_s') : date('Y-m-d h:i:s');
        $data['time_end'] = I('t_e', '', 'check_time') ? I('t_e') : date('Y-m-d h:i:s', strtotime('+1 days'));
        $data['create_time'] = date('Y-m-d h:i:s');
        $data['status'] = (strtotime($data['time_start']) > time()) ? 1 : 2;
        #判断拍卖期是否大于3天
        (strtotime($data['time_end']) - strtotime($data['time_start'])) < 3 * 86400 || jsonReturn(E_BEYOND_TIME);
//        $r = M('aution')->where(array('aution_id' => $aid))->save($data);
        $r = service('Aution', 'updateP', array($aid, $data));
        return $r ? jsonReturn(E_SUCCESS, array('aution_id' => $r)) : jsonReturn(E_FAILURE);
    }

    /**
     * 删除拍品
     */
    public function delete($aid) {
        ($user = $this->user) || jsonReturn(E_UNLOGIN);
        #查看是否是自己上传的拍品
        $this->_ifMyAution($aid);
        #操作
        $r = service('Aution', 'delP', $aid);
        return $r ? jsonReturn(E_SUCCESS) : jsonReturn(E_FAILURE);
    }

    /**
     * 获取拍品信息
     */
    public function info($aid) {
        $user = $this->user;
        $aution_info = service('Aution', 'loadAution', $aid);
        return $aution_info ? jsonReturn(E_SUCCESS, $aution_info) : jsonReturn(E_FAILURE);
    }

    /**
     * 拍卖列表
     */
    public function listing() {
        
    }
    
    public function aution() {
        
    }

}
