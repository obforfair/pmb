<?php

/**
 * 用户管理
 */

namespace Api\Service;

use Lib\Ob\Service;
use Lib\Ob\Oauth;
use Lib\Ob\Cache;
class UserService extends Service {

    /**
     * 查看是否已有该用户
     * @param array | int  $filter
     */
    public function ifHasUser($filter) {
        $where = $filter;
        return M('User')->where($where)->count();
    }

    /**
     * 注册手机用户
     * @param type $data
     * @return type
     */
    public function registerUser($data) {
        $data['password'] = $data['password'] ? md5($data['password']) : '';
        //查看手机号是否注册过;
        $data['mobile'] && $user = M('user')->where(array('mobile' => $data['mobile']))->find();
        if ($user) {
            return false;
        }
        if ($data['user_type']) {
            switch ($data['user_type']) {
                case 'mobile':
                    $data['user_type'] = 0;
                    break;
                case 'qq':
                    $data['user_type'] = 1;
                    break;
                case 'weixin':
                    $data['user_type'] = 2;
                    break;
                case 'sina':
                    $data['user_type'] = 3;
                    break;
                default:
                    $data['user_type'] = 9;
                    break;
            }
        }
        return M('user')->add($data);
    }

    /**
     * 注册第三方用户
     * @param type $data
     * @return type
     */
    public function registerOauth($data) {
        return M('user_oauth_token')->add($data);
    }

    /**
     * 
     * @param type $plateform
     * @param type $token
     * @param type $openid
     * @param type $api
     * @return type
     */
    public function getOauthinfo($plateform, $openid, $access_token) {
        $oauth_conf = C('Think_SDK_' . strtoupper($plateform));
        $tokens['access_token'] = $access_token;
        $tokens['openid'] = $openid;
        $social = Oauth::getInstance($plateform, $tokens);
        $data = $social->getUserInfo($oauth_conf['API']['userinfo'], $openid);
        return $data;
    }

    public function getUserInfo($uid){
        $rediskey = C('REDIS_PREFIX_USER').$uid;
        $redis = Cache::getInstance('redis');
        $userinfo = $redis->hget($rediskey);
        if(!$userinfo){
            $baseinfo = M('user')->where(array('user_id'=>$uid,'status'=>0))->find();
            dd($baseinfo);
        }
        
    }
    /**
     * 根据第三方登录注册或直接获取user_id 
     * @param type $plateform
     * @param type $open_id
     * @param type $access_token
     */
    public function getOauthId($plateform, $open_id, $access_token) {
        //检测tooken是否正确
        $data = service('User', 'getOauthinfo', array($plateform, $open_id, $access_token));
        if(!$data){
            return false;
        }
        //检测是否已经注册
        $hasSaved = M('user_oauth_token')->
                where(array('plateform' => $plateform, 'open_id' => $open_id))
                ->find();
        if ($hasSaved) {
            $user_id = $hasSaved['bind_to'] ? $hasSaved['bind_to'] : $hasSaved['user_id'];
            $oauth_id = $hasSaved['oauth_id'];
            //更新Token
            if ($data['access_token'] != $access_token) {
                M('user_oauth_token')->save(array('access_token' => $access_token));
            }
        } else {
            //下载并保存头像
            $image_id = service('Tools', 'saveHttpImage', array($data['avata'], 'avata'));
            $image_id ? $data['avata'] = $image_id : $data['avata'] = '';
            //注册新用户
            $data['user_type'] = $plateform;
            M()->startTrans();
            $user_id = service('User', 'registerUser', array($data));
            $user_id && $oauth_id = service('User', 'registerOauth', array(array('plateform' => $plateform, 'open_id' => $open_id,
                    'access_token' => $access_token, 'user_id' => $user_id)));
            $oauth_id ? M()->commit() : M()->rollback();
            $oauth_id || $user_id = null;
        }
        
        return $user_id && $oauth_id ? array('user_id'=>$user_id,'oauth_id'=>$oauth_id) : false;
    }

}

?>
