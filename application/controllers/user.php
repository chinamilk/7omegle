<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}

	public function index(){
	}

	/**
	 * 登录
	 */
	public function login(){
		$this->load->model('user_model');
		if($this->user_model->check_login()){
			$this->load->helper('url');
			redirect('/');
		}
		if(!$this->input->post()){
			$data['title'] = '登录 - 7omegle';
			$this->load->view('login', $data);
		} else {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$data['title'] = '登录 - 7omegle';
			$data['username'] = $username;
			$data['password'] = $password;
			$this->load->model('user_model');
			$result = $this->user_model->login($username, $password);
			if($result['error'] != 0){
				$data['errorMsg'] = $result['msg'];
			}
			$this->load->view('login', $data);
		}
	}

	/**
	 * 注册
	 */
	public function register(){
		$this->load->model('user_model');
		if($this->user_model->check_login()){
			$this->load->helper('url');
			redirect('/');
		}
		$data['title'] = '注册 - 7omegle';
		if(!$this->input->post()){
			$this->load->view('register', $data);
		} else {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$repassword = $this->input->post('repassword');
			$this->load->model('user_model');
			$result = $this->user_model->register($username, $password, $repassword);
			if($result['error'] != 0){
				$data['errorMsg'] = $result['msg'];
			}
			$this->load->view('register', $data);
		}
	}

	/**
	 * 退出
	 */
	public function logout(){
		$this->load->model('user_model');
		$this->user_model->logout();
	}

}