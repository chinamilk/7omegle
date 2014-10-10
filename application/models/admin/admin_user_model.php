<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_user_model extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->model('user_model');
		$this->userinfo = $this->user_model->check_login();
	}

	/**
	 * 在线人数统计
	 */
	public function online_count(){
		$this->load->model('member_model');
		$online = $this->member_model->get_online_member_count();
		return $online;
	}

	/**
	 * 锁定用户
	 * @param  mixed $user_data 锁定的用户信息, 'username' 或 'user id'
	 */
	public function block($user_data){
		if(!$this->check_admin()){
			header('Location: /');
		}
		
	}

	/**
	 * 检查是不是管理员
	 */
	public function check_admin(){
		// 检查是否登录
		if(!$this->userinfo){
			header('Location: /');
		}
		// 检查是否是管理员
		if($this->userinfo['user_type'] != 1){
			return False;
		} else {
			return True;
		}
	}

	public function test(){
		return 'admin_user_model->test';
	}
}