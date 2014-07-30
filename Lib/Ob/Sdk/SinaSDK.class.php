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
// | SinaSDK.class.php 2013-02-25
// +----------------------------------------------------------------------
namespace Lib\Ob\Sdk;
use Lib\Ob\Oauth;

class SinaSDK extends Oauth{
	/**
	 * 获取requestCode的api接口
	 * @var string
	 */
	protected $GetRequestCodeURL = 'https://api.weibo.com/oauth2/authorize';

	/**
	 * 获取access_token的api接口
	 * @var string
	 */
	protected $GetAccessTokenURL = 'https://api.weibo.com/oauth2/access_token';

	/**
	 * API根路径
	 * @var string
	 */
	protected $ApiBase = 'https://api.weibo.com/2/';
	
        /**
	 * 组装接口调用参数 并调用接口
	 * @param  string $api    微博API
	 * @param  string $param  调用API的额外参数
	 * @param  string $method HTTP请求方法 默认为GET
	 * @return json
	 */
	public function call($api, $param = '', $method = 'GET', $multi = false){		
		/* 新浪微博调用公共参数 */
		$params = array(
			'access_token' => $this->Token['access_token'],
                        'uid' => $this->openid(),
		);		
		$vars = $this->param($params, $param);
		$data = $this->http($this->url($api, '.json'), $vars, $method, array(), $multi);
		return json_decode($data, true);
	}
	
	/**
	 * 解析access_token方法请求后的返回值
	 * @param string $result 获取access_token的方法的返回值
	 */
	protected function parseToken($result, $extend){
		$data = json_decode($result, true);
		if($data['access_token'] && $data['expires_in'] && $data['remind_in'] && $data['uid']){
			$data['openid'] = $data['uid'];
			unset($data['uid']);
			return $data;
		} else
			throw new Exception("获取新浪微博ACCESS_TOKEN出错：{$data['error']}");
	}
	
	/**
	 * 获取当前授权应用的openid
	 * @return string
	 */
	public function openid(){
		$data = $this->Token;
		if(isset($data['openid'])){                     
			return $data['openid'];
		}elseif($data['access_token']){
			$data = $this->http($this->url('account/get_uid', '.json'), array('access_token' => $data['access_token']));
			$data = json_decode($data, true);
			if(isset($data['uid'])){
                            $this->Token['openid'] = $data['uid'];
                            return $data['uid'];
			}else{
				throw new Exception("获取用户openid出错：{$data['error_description']}");
                       }
		}else{
			throw new Exception('没有获取到新浪微博用户ID！');
                }
	}
        
        public function share($openid,$access_token,$feedinfo){
            $res = $this->http($this->url('statuses/update', '.json'), array('access_token' => $access_token,'status'=>$feedinfo['title']),'POST');
            //$res = $this->http($this->url('statuses/upload_url_text', '.json'), array('access_token' => $access_token,'status'=>$feedinfo['title'],'url'=>$feedinfo['imageurl']),'POST');
            $res = json_decode($res,true);
            if(isset($res['id'])){
               return true; 
            }else{
                switch ($res['error_code']) {
                    case 10014:
                        return E_SHARE_NO_PER;
                        break;
                    case 21332:
                       return E_USER_BIND_ERROR_TOKEN;
                       break;
                   case 20019:
                       return E_SHARE_REPEAT;
                    default:
                      return E_FAILURE;
                      break;
                }
            }
        }
        
        public function getUserInfo($api,$openid) {
            $data = $this->Token;
            $params['access_token'] = $data['access_token'];
            $params['uid'] = $openid;
            $data = json_decode($this->http($this->url($api,'.json'), $params),true);
            return $data['screen_name'] ? $this->convertUserInfo($data) : false;
        }
        
        public function convertUserInfo($userinfo) {
            $data['nickname'] = $userinfo['name'];
            $data['gender'] = $userinfo['gender'];
            $data['description'] = $userinfo['description'];
            $data['avata'] = $userinfo['avatar_large'];
            $data['gender'] = ($userinfo['gender'] == 'm') ? 1 : 0;
            $data['gender'] = ($userinfo['gender'] == 'f') ? 2 : $data['gender'];  
            return $data;            
        }
}