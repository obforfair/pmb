<?php

/**
 * 工具
 */

namespace Api\Service;

use Lib\Ob\Service;
use Lib\Ob\Crypt;
use Lib\Ob\Upload;
use Lib\Ob\Image;
use Lib\Ob\Http;

class ToolsService extends Service {

    /**
     * 发送手机消息
     * @param type $mobile
     * @param type $message
     */
    public function sendMessage($mobile, $content) {
        $data = array(
            'userid' => 'mxzd',
            'pwd' => 'mxzd123',
            'mobiles' => $mobile,
            'content' => $content . '【珠宝网】', // iconv('utf-8', 'gbk', $content. '-【珠宝网】'),
            'subcode' => '3007'
        );
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_URL => 'http://ess.mobase.cn/clientapi/sendsms',
            CURLOPT_POSTFIELDS => http_build_query($data),
            //  CURLOPT_HTTPHEADER      => array("charset=utf-8"),
            CURLOPT_RETURNTRANSFER => true
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 发送手机验证码
     * @param type $mobile
     */
    public function sendCode($mobile) {
        $register_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $content = "您的拍卖宝验证码为：" . $register_code . " ，为了您的账号安全，请勿将验证码转发给其他人。";
        if (!cache('PMB_MOBILE_CHECK_FILTER:' . $mobile)) {
//            $this->sendMessage($mobile, $content);
            //每60秒可以发送一次，
            cache('PMB_MOBILE_CHECK_FILTER:' . $mobile, $register_code, 60);
            //code保存时间为600秒
            cache('PMB_MOBILE_CHECK:' . $mobile, $register_code, 600);
        }
    }

    /**
     * 检测手机验证码
     * @param type $mobile
     * @param type $code
     * @return boolean
     */
    public function checkCode($mobile, $code) {
        if ($code == cache('PMB_MOBILE_CHECK:' . $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 对uid进行加密或用session存储
     * @param type $uid
     */
    public function setSid($uid, $plateform) {
        $sid = Crypt::encrypt(serialize(array('uid' => $uid, 'plateform' => $plateform)), C('DATA_CRYPT_KEY'), C('DATA_CRYPT_EXPRITE'));
        return $sid;
    }

    /* session方式
      public function setUid($uid){
      session_start();
      session('uid',$uid);
      return  session_id();
      } */

    /*
     * 对uid进行解密或从session获取
     */

    public function getSid() {
        $sid = I('cookie.sid') ? I('cookie.sid') : I('sid');
        if (!$sid) {
            return null;
        } else {
            $data = unserialize(Crypt::decrypt($sid, C('DATA_CRYPT_KEY')));
            return $data ? $data : false;
        }
    }

    /* session方式
      public function getUid(){
      session_start();
      return session('uid');
      } */

    public function uploadImage($type) {
        $upload = new Upload(C('Upload')); // 实例化上传类
        $upload->maxSize = C('IMAGE_MAX_SIZE'); // 设置附件上传大小   
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型    
        $upload->savePath = strtolower($type) . '/'; // 设置附件上传目录   
        // 上传文件     
        $info = $upload->upload();
        if (!$info) {
            return array('error' => $upload->getError());
        } else {
            $data['image_save_path'] = $info['photo']['savepath'] . $info['photo']['savename'];
            $data['image_type'] = substr($info['photo']['type'], strrpos($info['photo']['type'], '/') + 1);
            $data['image_width'] = strval($info['imageinfo'][0]);
            $data['image_height'] = strval($info['imageinfo'][1]);
            $data['file_size'] = strval($info['photo']['size']);
            $data['save_type'] = $type;
            $r = M('core_images')->add($data);
            $r ? $data['image_id'] = $r : $data['error'] = M('core_images')->getDbError();
            return $data;
        }
    }

    /**
     * 切图by imageids
     * @param type $image_ids
     */
    public function cropImageById($image_ids) {

        $image_ids = (array) $image_ids;
        foreach ($image_ids as $image_id) {
            $image_info = M('core_images')->find($image_id);
            if (!$image_info) {
                continue;
            }
            $crop_config = C('IMAGE_CROP_' . strtoupper($image_info['save_type']));
            if ($crop_config) {
                $crop_infos = $this->cropImage($image_info['image_save_path'], $crop_config);
            }
            if (isset($crop_infos)) {
                M('core_images')->where(array('image_id' => $image_id))->save(array('crop_infos' => serialize($crop_infos)));
            }
        }
    }

    /**
     * 切图
     * @param type $image_file
     * @param type $crop_config
     * @return array
     */
    public function cropImage($image_file, $crop_config) {
        $crop_infos = array();
        $crop_confs = explode(',', $crop_config);
        $dir_file = substr($image_file, 0, strrpos($image_file, '.'));
        foreach ($crop_confs as $crop_conf) {
            $image = new Image();
            $image->open(C('IMAGE_SAVE_PATH') . $image_file);
            $alias = $func = $params = $crop_image = null;
            list($alias, $func, $params) = explode(':', $crop_conf);
            $params = explode('_', $params);
            $image_save_path = 'cache/' . $dir_file . '_' . $alias . '.' . $image->type();
            mkdirs(dirname(C('IMAGE_SAVE_PATH') . $image_save_path));
            $image = call_user_func_array(array($image, $func), $params);
            $info = $image->save(C('IMAGE_SAVE_PATH') . $image_save_path);
            $crop_image = getimagesize(C('IMAGE_SAVE_PATH') . $image_save_path);
            if ($crop_image) {
                $crop_image_info['image_save_path'] = $image_save_path;
                $crop_image_info['image_width'] = $crop_image[0];
                $crop_image_info['image_height'] = $crop_image[1];
                $crop_image_info['image_type'] = basename($crop_image['mime']);
                $crop_infos[$alias] = $crop_image_info;
            }
        }
        return $crop_infos;
    }

    /**
     * 保存到图片库
     * @param type $image
     * @param type $type
     * @return boolean
     */
    public function saveImage($image, $type) {
        $info = getimagesize(C('IMAGE_SAVE_PATH') . $image);
        if ($info) {
            $data['image_save_path'] = $image;
            $data['image_type'] = basename($info['mime']);
            $data['image_width'] = strval($info[0]);
            $data['image_height'] = strval($info[1]);
            $data['file_size'] = strval($info['bits']);
            $data['save_type'] = $type;
            $r = M('core_images')->add($data);
            return $r ? $r : false;
        } else {
            return false;
        }
    }

    /**
     * 保存网络图片
     * @param type $http_image
     * @param type $save_path
     */
    public function saveHttpImage($http_image, $type) {
        $tmpname = TEMP_PATH . '/' . uniqid();
        mkdirs(TEMP_PATH);
        Http::curlDownload($http_image, $tmpname);
        //再次下载
        if (!file_exists($tmpname)) {
            Http::curlDownload($http_image, $tmpname);
        }
        $image_info = getimagesize($tmpname);
        if (!$image_info) {
            return false;
        }
        $extension = $image_info['mime'] == 'image/gif' ? 'jpeg' : basename($image_info['mime']);
        $save_dir = strtolower($type) . '/' . date('Y-m-d') . '/';
        $sql_save_name = $save_dir . uniqid() . '.' . $extension;
        $save_name = C('IMAGE_SAVE_PATH') . $sql_save_name;
        mkdirs(C('IMAGE_SAVE_PATH') . $save_dir);
        //gif转换成jpeg
        if ($image_info['mime'] == 'image/gif') {
            $image = new Image();
            $image->open($tmpname);
            $r = $image->save($save_name, $extension);
        } else {
            $r = copy($tmpname, $save_name);
        }
        if ($r) {
            unlink($tmpname);
            $r = $this->saveImage($sql_save_name, $type);
            $r && $this->cropImageById($r);
        }
        return $r ? $r : false;
    }

    /**
     * 获取图片信息
     * @param type $image_id
     * @return type
     */
    public function getImage($image_id) {
        $key = C('PREFIX_IMAGE').$image_id;
        if($data = cache()->get($key)){
            return $data;
        }
        $image_info = M('core_images')->where(array('image_id' => $image_id, 'status' => 0))->find();
        if ($image_info) {
            $data['image_info']['image_id'] = $image_info['image_id'];
            $data['image_info']['file_size'] = $image_info['file_size'];
            $data['image_default']['image_path'] = $image_info['image_save_path'] ? C('IMAGE_HOST') . '/' . $image_info['image_save_path'] : '';
            $data['image_default']['image_height'] = $image_info['image_height'];
            $data['image_default']['image_width'] = $image_info['image_width'];
            $data['image_default']['image_type'] = $image_info['image_type'];
            $crop_data = !empty($image_info['crop_infos']) ? unserialize($image_info['crop_infos']) : array();
            foreach ($crop_data as $k => $v) {
                $crop_data[$k]['image_path'] = C('IMAGE_HOST') . '/' . $crop_data[$k]['image_save_path'];
                unset($crop_data[$k]['image_save_path']);
            }
            !empty($crop_data) && ($data = array_merge($data, $crop_data));
        }
        isset($data) && cache()->set($key, json_encode($data),C('EXPIRES_IMAGE'));
        return isset($data) ? $data : false;
    }
    
    /**
     * 获取图册信息
     * @param type $image_ids
     * @return type
     */
    public function getImages($image_ids = array()){
        $image_ids && $image_ids = is_array($image_ids) ? : explode(',',$image_ids);
        $data = array();
        foreach($image_ids as $image_id){
            $data[] = $this->getImage($image_id);
        }
        return $data;
    }
    /**
     * 验证图片是否存在
     * @param type $image_ids
     * @return type
     */
    public function checkImageids($image_ids) {
        $image_ids = M('core_images')->field('image_id')->where(array('image_id' => array('in', $image_ids)))->getfield('image_id', true);
        return $image_ids;
    }

    /**
     * 图片id保存为gallery
     * @param type $image_ids
     */
    public function createGallery($image_ids) {
        $image_ids = implode(',', $image_ids);
        $r = M('core_gallery')->add(array('image_ids' => $image_ids));
        return $r ? $r : false;
    }

    /**
     * 获取类id
     * @param type $category
     */
    public function getCategoryId($category) {
        $categorys = M('category')->getField('category_id,name');
        if (is_numeric($category)) {
            return isset($categorys[$category]) ? $category : false;
        } else {
            $categorys = array_flip($categorys);
            return isset($categorys[$category]) ? $categorys[$category] : false;
        }
    }

    public function getStatusInfo($type, $key) {
        $status = C('STATUS');
        return $r = isset($status[$type][$key]) ? $r : NULL;
    }

}

?>
