<?php
/**
 * QQ Weibo strategy for Opauth
 * based on http://wiki.open.t.qq.com/index.php
 * 
 * More information on Opauth: http://opauth.org
 * 
 * @link         http://opauth.org
 * @package      Opauth.QQStrategy
 * @license      MIT License
 */

class QQWeiboStrategy extends OpauthStrategy{
	
	/**
	 * Compulsory config keys, listed as unassociative arrays
	 */
	public $expects = array('key', 'secret');
	
	/**
	 * Optional config keys with respective default values, listed as associative arrays
	 */
	public $defaults = array(
		'redirect_uri' => '{complete_url_to_strategy}qqweibo_callback'
	);

	/**
	 * Auth request
	 */
	public function request(){
		$url = 'https://open.t.qq.com/cgi-bin/oauth2/authorize';
		$params = array(
			'client_id' => $this->strategy['key'],
			'redirect_uri' => $this->strategy['redirect_uri'],
			'response_type' => 'code',
			'scope' => $this->strategy['scope'],
			'state' => $this->strategy['state'],
		);
		$this->clientGet($url, $params);
	}
	
	/**
	 * Internal callback, after QQWeibo's OAuth
	 */
	public function qqweibo_callback(){
		if (array_key_exists('code', $_GET) && !empty($_GET['code'])){
			$url = 'https://open.t.qq.com/cgi-bin/oauth2/access_token';
			$params = array(
				'client_id' =>$this->strategy['key'],
				'client_secret' => $this->strategy['secret'],
				'redirect_uri'=> $this->strategy['redirect_uri'],
				'code' => $_GET['code'],       
				'grant_type' => 'authorization_code'
			);
			$response = $this->serverPost($url, $params, null, $headers);
			if (empty($response)){
				$error = array(
					'code' => 'Get access token error',
					'message' => 'Failed when attempting to get access token',
					'raw' => array(
						'headers' => $headers
					)
				);
				$this->errorCallback($error);
			}
			
			parse_str($response, $results);
      $qquser = $this->getuserinfo($results); 

			$this->auth = array(
				'provider' => 'QQWeibo',
				'uid' => $qquser->name,
				'info' => array(
					'name' => $userinfo->name,
					'location' => $userinfo->location,
					'nickname' => $userinfo->nick,
					'image' => $userinfo->head
				),
				'credentials' => array(
					'token' => $results['access_token'],
					'expires' => date('c', time() + $results['expires_in'])
				),
				'raw' => $qquser
			);
			$this->callback();
		}
		else
		{
			$error = array(
				'code' => $_GET['error'],
				'message' => $_GET['error_description'],
				'raw' => $_GET
			);

			$this->errorCallback($error);
		}
	}

	private function getuserinfo($results){
		$params = array(
			'oauth_consumer_key' =>$this->strategy['key'],
			'access_token'=> $results['access_token'],
			'openid'=> $results['openid'],      
			'oauth_version' => '2.a',
			'scope' => 'all',
			'appfrom' => 'opauth-qqweibo',
			'seqid' => time(),
		);
		$qquser = $this->serverget('http://open.t.qq.com/api/user/info', $params);
		if (!empty($qquser)){
			$response = json_decode($qquser);
			return $response->data;
		}
		else{
			$error = array(
				'code' => 'Get User error',
				'message' => 'Failed when attempting to query for user information',
				'raw' => array(
					'access_token' => $results['access_token'],	
					'headers' => $headers
				)
			);

			$this->errorCallback($error);
		}
	} 
}
