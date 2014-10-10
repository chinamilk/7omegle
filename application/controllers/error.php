<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}

	/**
	 * 404 页
	 */
	public function index(){
		$this->load->model('user_model');
		$data['userinfo'] = $this->user_model->check_login();
		$data['title'] = '页面不存在';
		$this->load->view('error_404', $data);
	}
}