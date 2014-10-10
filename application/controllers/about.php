<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('user_model');
		$this->userinfo = $this->user_model->check_login();
	}

	/**
	 * index
	 */
	public function index(){
		$this->about();
	}

	/**
	 * about
	 */
	public function about(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '关于';
		$this->load->view('about/about', $data);
	}

	/**
	 * faq
	 */
	public function faq(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '帮助';
		$this->load->view('about/faq', $data);
	}

	/**
	 * contact
	 */
	public function contact(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '联系';
		$this->load->view('about/contact', $data);
	}

	/**
	 * feedback
	 */
	public function feedback(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '反馈';
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if($method == "post"){
			$this->load->model('feedback_model');
			$result = $this->feedback_model->create_feedback();
			$data['result'] = $result;
		}
		$this->load->view('about/feedback', $data);
	}

	/**
	 * special 特别页面
	 */
	public function special(){
		$data['userinfo'] = $this->userinfo;
		$data['title'] = '特别页面';
		$this->load->view('about/special', $data);
	}
}