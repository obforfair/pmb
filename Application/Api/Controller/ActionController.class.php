<?php
/**
 * 操作控制类
 */
namespace Api\Controller;

use Think\Controller;
use Lib\Ob\Log;
use Lib\Ob\Auth;
use Lib\Ob\Upload;

class ActionController extends Controller {

    /**
     * 图片上传
     * @param type $image_type
     */
    public function uploadImage($image_type) {

        $allowed_types = C('IMAGE_ALLOWED_TYPES');
        if (!in_array($image_type, $allowed_types)) {
            jsonReturn(E_FAILURE);
        }
        $info = service('Tools', 'uploadImage', $image_type);
        if (isset($info['error'])) {
            // 上传错误提示错误信息
            jsonReturn(E_FAILURE, $info['error']);
        } else {
            // 上传成功
            service('Tools','cropImageById',$info['image_id']);
            $image_info = service('Tools','getImage',$info['image_id']);
            jsonReturn(E_UPLOAD_SUCCESS, $image_info);
        }
    }
    
    public function upload() {
        $this->display();
    }

}
