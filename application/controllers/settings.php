<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller{
	public function __construct(){
		parent::__construct();
		// 检查是否登录
		$this->load->model('user_model');
		$this->userinfo = $this->user_model->check_login();
		// 如果未登录，跳转到首页
		if(!$this->userinfo){
			header('Location: /');
		}
	}

	/**
	 * 设置页
	 */
	public function index(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '设置';
		$this->load->model('member_model');
		$data['current_friend'] = $this->member_model->get_current_friend($this->userinfo['id']);
		
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if($method == 'post'){
			$this->load->model('settings_model');
			$result = $this->settings_model->index($this->userinfo['id']);
			if($result['error'] == 0){
				header('Location: /settings');
			} else {
				$data['error'] = $result['error'];
				$data['errorMsg'] = $result['msg'];
			}
		}
		$this->load->view('settings', $data);
	}

	/**
	 * 设置头像
	 */
	public function avatar(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '设置头像';

		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if($method == "post" && $_FILES['userfile']['name']){
			$this->load->model('settings_model');
			$result = $this->settings_model->set_avatar($data['userinfo']['id']);
			if($result['error'] == 0){
				// 重定向到当前页面
				header('Location: /settings/avatar');				
			} else {
				$data['errorMsg'] = $result['msg'];
			}
		}
		$this->load->view('settings_avatar', $data);
	}

	/**
	 * 设置密码
	 */
	public function password(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '设置密码';
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if($method == 'post'){
			$this->load->model('settings_model');
			$password_current = $this->input->post('password_current');
			$password_new = $this->input->post('password_new');
			$password_confirm = $this->input->post('password_confirm');
			$result = $this->settings_model->set_password($this->userinfo['id'], $password_current, $password_new, $password_confirm);
			$data['error'] = $result['error'];
			$data['errorMsg'] = $result['msg'];
		}
		$this->load->view('settings_password', $data);
	}

	/**
	 * 设置用户状态为拒绝任何人匹配
	 */
	public function status_refuse(){
		$this->load->model('settings_model');
		$result = $this->settings_model->set_status_refuse($this->userinfo['id']);
		header('Location: /settings');
	}

	/**
	 * 从'拒绝任何人匹配'状态切换回'空闲'状态
	 */
	public function status_free(){
		$this->load->model('settings_model');
		$result = $this->settings_model->cancel_status_refuse($this->userinfo['id']);
		header('Location: /settings');
	}
}
