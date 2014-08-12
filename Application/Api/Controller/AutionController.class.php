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
        $aution_info = service('Aution', 'info', array($aid, 'user_id,status'));
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
        $data['image_ids'] = $image_ids;
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
        $data['image_ids'] = $image_ids;
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
        $data['status'] = (strtotime($data['time_start']) > time()) ? 2 : 1;
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
        $aution_info = service('Aution', 'info', $aid);
        return $aution_info ? jsonReturn(E_SUCCESS, $aution_info) : jsonReturn(E_FAILURE);
    }

    /**
     * @param type $status 0
     * @param type $order_type  1:'end_time desc';2:'end_time asc';
                             3:'start_time desc';4: 'start_time asc';5:'aution_id desc';
                             6: 'aution_id asc';default:'end_time desc';
     * @param type $page
     * @param type $first_id
     */
    public function listing($status = 1, $first_id = '', $last_id = '', $order_type = '1', $limit = 20) {
        $user = $this->user;
        $status = in_array($status,array(1,2)) ? $status: 1;
        $aution_ids = service('Aution', 'loadAutionIds', array($status, $order_type, $first_id));
        $aution_ids || jsonReturn(E_NULLGET);
        #组织数据
        $data['data_info']['uid'] = $user['uid'] ? : '';
        $data['data_info']['first_id'] = $aution_ids[0];
        $data['data_info']['last_id'] = '';
        $data['data_info']['hast_data'] = '0';
        $index = $last_id ? array_search($last_id, $aution_ids) : 0;
        $aution_ids = array_slice($aution_ids, $index);
        #获取详细信息
        $datas = array();
        $count = 0;
        foreach ($aution_ids as $aution_id) {
            $info = service('Aution', 'marketInfo', $aution_id);
            if(!$info){
                continue;
            }
            if ($info['aution_info']['status'] == $status) {
                $datas[] = $info;
                $count++;
            }
            #获取$limit+1个数据，有最后一个数据表示有下一页
            if ($count > $limit) {
                $data['data_info']['last_id'] = $aution_id;
                $data['data_info']['hast_data'] = '1';
                array_pop($datas);
                break;
            }
        }
        $data['datas'] = $datas;
        jsonReturn(E_SUCCESS, $data);
    }

    public function aution() {
        
    }

}
