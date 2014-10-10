<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cookie_model extends CI_Model{
	/**
	 * 设置登录cookie
	 * @param integer $id 用户id
	 * @param string $username 用户名
	 * @param string $cookie_hash cookie的hash值
	 */
	public function set_login_cookie($id, $username, $cookie_hash){
		$hash = md5(md5($id.$username).$cookie_hash);
		$cookie_value = base64_encode($id.'|'.$username.'|'.$hash);
		$expire = time() + 60*60*24*365;
		setcookie('7omegle', $cookie_value, $expire, '/', $this->config->item('cookie_domain'));
	}

	/**
	 * 获取登录后设置的cookie
	 */
	public function get_login_cookie(){
		$cookie_value = !empty($_COOKIE['7omegle']) ? $_COOKIE['7omegle'] : False;
		if(!$cookie_value)
			return False;
		$cookie = explode('|', base64_decode($cookie_value));
		return $cookie;
	}

	/**
	 * 删除cookie
	 */
	public function delcookie(){
		// setcookie('name');
		// setcookie('name');
	}

	/**
	 * 注销登录, 清除cookie
	 */
	public function logout(){
		setcookie('7omegle', '', time()-1, '/', $this->config->item('cookie_domain'));
	}
}