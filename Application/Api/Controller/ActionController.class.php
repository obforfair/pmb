<?php

namespace Api\Controller;

use Think\Controller;
use Lib\Ob\Log;
use Lib\Ob\Auth;
use Lib\Ob\Upload;

class ActionController extends Controller {

    public function uploadImage() {
        $type = I('type');
        $allowed_types = array('avata');
        if (!in_array($type, $allowed_types)) {
            jsonReturn(E_FAILURE);
        }
        $info = service('Tools', 'uploadImage', $type);
        if (isset($info['error'])) {
            // 上传错误提示错误信息
            jsonReturn(E_FAILURE, $info['error']);
        } else {
            // 上传成功
            service('Tools','cropImageById',$info['image_id']);
            jsonReturn(E_UPLOAD_SUCCESS, $info);
        }
    }
    
    public function upload() {
        $this->display();
    }

}
