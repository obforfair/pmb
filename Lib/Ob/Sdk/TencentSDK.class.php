<?php

// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// | TencentSDK.class.php 2013-02-25
// +----------------------------------------------------------------------

class TencentSDK extends ThinkOauth {

    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://open.t.qq.com/cgi-bin/oauth2/authorize';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://open.t.qq.com/cgi-bin/oauth2/access_token';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://open.t.qq.com/api/';

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api    微博API
     * @param  string $param  调用API的额外参数
     * @param  string $method HTTP请求方法 默认为GET
     * @return json
     */
    public function call($api, $param = '', $method = 'GET', $multi = false) {
        /* 腾讯微博调用公共参数 */
        $params = array(
            'oauth_consumer_key' => $this->AppKey,
            'access_token' => $this->Token['access_token'],
            'openid' => $this->openid(),
            'clientip' => get_client_ip(),
            'oauth_version' => '2.a',
            'scope' => 'all',
            'format' => 'json'
        );

        $vars = $this->param($params, $param);
        $data = $this->http($this->url($api), $vars, $method, array(), $multi);
        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值 
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result, $extend) {
        parse_str($result, $data);
        $data = array_merge($data, $extend);
        if ($data['access_token'] && $data['expires_in'] && $data['openid'])
            return $data;
        else
            throw new Exception("获取腾讯微博 ACCESS_TOKEN 出错：{$result}");
    }

    public function share($openid, $access_token, $feedinfo) {
        /* 腾讯微博调用公共参数 */
        $params = array(
            'oauth_consumer_key' => $this->AppKey,
            'access_token' => $access_token,
            'openid' => $openid,
            'clientip' => get_client_ip(),
            'oauth_version' => '2.a',
            'scope' => 'all',
            'format' => 'json',
            'content'=> @$feedinfo['title'],
            'pic_url' => @$feedinfo['imageurl'],
        );
        $res = $this->http($this->url ('t/add_pic_url'),$params, 'POST');
        $res = json_decode($res, true);       
        if ($res['ret'] === 0) {
            return true;
        } else {
            switch ($res['error_code']) {
                case 1:
                    return E_USER_BIND_ERROR_TOKEN;
                    break;
                default:
                    return E_FAILURE;
                    break;
            }
        }
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid() {
        $data = $this->Token;
        if (isset($data['openid']))
            return $data['openid'];
        else
            throw new Exception('没有获取到openid！');
    }

    public function getUserInfo($api, $openid) {
        $params = array(
            'oauth_consumer_key' => $this->AppKey,
            'access_token' => $this->Token['access_token'],
            'openid' => $openid,
            'clientip' => get_client_ip(),
            'oauth_version' => '2.a',
            'scope' => 'all',
            'format' => 'json'
        );
        $data = $this->http($this->url($api), $params);
        $data = json_decode($data, true);
        return $data['data']['nick'] ? $this->convertUserInfo($data['data']) : false;
    }

    public function convertUserInfo($userinfo) {
        $data['username'] = $userinfo['nick'];
        $data['gender'] = $userinfo['sex'];
        $data['summary'] = $userinfo['introduction'];
        $data['head'] = !empty($userinfo['head'])?$userinfo['head'].'/100':'';
        $data['gender'] = ($userinfo['sex'] == '2') ? 1 : 0;
        $data['gender'] = ($userinfo['sex'] == '1') ? 2 : $data['gender'];
        return $data;
    }

}