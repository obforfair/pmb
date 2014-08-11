<?php

/**
 * 用户管理
 */

namespace Api\Service;

use Lib\Ob\Service;
use Lib\Ob\Oauth;
use Lib\Ob\Cache;

class UserService extends Service {
    
    protected $model = 'User';

    /**
     * 根据过滤条件直接获取用户信息
     * @param array | int  $filter
     */
    public function getFilterUser($filter) {
        $where = $filter;
        return M('User')->where($where)->find();
    }

    /**
     * 获取用户信息，并缓存
     * @param type $uid
     */
    public function loadUserInfo($uid) {
        $cache = cache();
        $key = C('PREFIX_USER') . $uid;
        $userinfo = $cache->hgetall($key);
        if (!$userinfo) {
            $userinfo = M('user')->where(array('user_id' => $uid, 'status' => 0))->find();
            $userinfo['gender'] = inverse_gender($userinfo['gender']);
            unset($userinfo['password']);
            $cache->hmset($key, $userinfo, C('EXPIRES_USER'));
        }
 
        return $userinfo ? $userinfo : '';
    }

    /**
     * 获取用户类型
     * @param type $user_type
     * @return type
     */
    public function switchUsertype($user_type) {
        $user_types = C('THINK_USER_TYPES');
        if (is_int($user_type)) {
            $type = array_search($user_type, $user_types);
        } else {
            $type = isset($user_types[$user_type]) ? $user_types[$user_type] : false;
        }
        return $type;
    }

    /**
     * 注册手机用户
     * @param type $data
     * @return type
     */
    public function registerUser($data) {
        $data['password'] = isset($data['password']) ? md5($data['password']) : '';
        //查看手机号是否注册过;
        isset($data['mobile']) && $data['user_type'] = 'mobile';
        isset($data['mobile']) && $hasuser = M('user')->where(array('mobile' => $data['mobile']))->find();
        if (isset($hasuser)) {
            return false;
        }
        $user_types = C('THINK_USER_TYPES');
        $data['user_type'] = $user_types[$data['user_type']];
        return ($data['user_type'] !== false) ? M('user')->add($data) : false;
    }

    /**
     * 复制用户数据并注册新用户
     * @param type $user_id
     * @param type $extra_data
     * @return int|boolen $user_id
     */
    public function copyUser($user_id, $add_data = array(), $unset_data = array()) {
        $data = M('User')->where(array('user_id' => $user_id, 'status' => 0))->find();
        !empty($add_data) && $data = array_merge($data, $add_data);
        !empty($unset_data) && $data = array_diff($data, $unset_data);
        unset($data['user_id']);
        return $this->registerUser($data);
    }

    /**
     * 登录后记录日志
     * @param type $user_id
     */
    public function loginAction($user_id, $type, $sid) {
        $login_time = date('Y-m-d H:i:s');
        M('User')->where(array('user_id' => $user_id))
                ->save(array('last_login' => $login_time));
        M('user_login_record')->add(array(
            'user_id' => $user_id,
            'login_type' => $type,
            'login_token' => $sid,
            'login_time' => $login_time
        ));
    }

    /**
     * 注册第三方登录TOKEN
     * @param type $data
     * @return type
     */
    public function registerOauth($data) {
        return M('user_oauth_token')->add($data);
    }

    /**
     * 根据TOKEN从第三方获取基本信息，已此来验证TOKEN是否有效
     * @param type $plateform
     * @param type $token
     * @param type $openid
     * @param type $api
     * @return type
     */
    public function checkOauth($plateform, $openid, $access_token) {
        $oauth_conf = C('Think_SDK_' . strtoupper($plateform));
        $tokens['access_token'] = $access_token;
        $tokens['openid'] = $openid;
        $social = Oauth::getInstance($plateform, $tokens);
        $data = $social->getUserInfo($oauth_conf['API']['userinfo'], $openid);
        return $data;
    }

    /**
     * 更新用户信息
     * @param type $uid
     * @param type $data
     * @return type
     */
    public function updateUserInfo($uid, $data) {
        $r = M('user')->where(array('user_id' => $uid))->save($data);
        $key = C('PREFIX_USER') . $uid;
        cache()->delete($key);
        return $r ? true : false;
    }

    /**
     * 删除缓存
     * @param type $user_id
     */
    public function delCache($type, $id) {
        $key = C('PREFIX_' . strtoupper($type)) . $id;
        return cache()->delete($key);
    }

    /**
     * 根据第三方登录注册或直接获取oauth_info
     * @param type $plateform
     * @param type $open_id
     * @param type $access_token
     */
    public function getOauthInfo($plateform, $open_id) {
        //检测是否已经注册
        $oauth_data = M('user_oauth_token')->
                where(array('plateform' => $plateform, 'open_id' => $open_id))
                ->find();
        return $oauth_data ? $oauth_data : false;
    }

    public function updateOauthInfo($oauth_id, $data) {
        $r = M('user_oauth_token')->where(array('oauth_id' => $oauth_id))
                ->save($data);
        return $r ? $r : false;
    }

    /**
     * 下载头像，并注册用户
     */
    public function oauthRegister($oauth, $plateform) {
        $image_id = service('Tools', 'saveHttpImage', array($oauth['avata'], 'avata'));
        $image_id ? $data['avata'] = $image_id : $data['avata'] = '';
        $data = array_merge($oauth, array('avata' => $image_id));
        //注册新用户
        $data['user_type'] = $plateform;
        $user_id = service('User', 'registerUser', array($data));
        return $user_id ? $user_id : false;
    }

    /**
     * 绑定
     * @param type $user_id
     * @param type $bind_to
     */
    public function bind($user_id, $bind_to) {
        $r = M('User')->where(array('user_id' => $user_id))->save(array('bind_to' => $bind_to));
        if ($r) {
            $this->delCache('user', $user_id);
            $this->delCache('user', $bind_to);
        }
        return $r;
    }

}

?>
