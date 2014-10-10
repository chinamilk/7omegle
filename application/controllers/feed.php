<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if($method != 'post'){
			$data = array(
				'error' => -1,
				'msg' => 'invalid'
			);
			echo json_encode($data);
			die();
		}
		if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest' 
			|| empty($_SERVER['HTTP_REFERER'])){
			die('');
		}
	}

	/**
	 * 发布动态
	 */
	public function create(){
		$this->load->model('feed_model');
		$result = $this->feed_model->create_feed();
		echo json_encode($result);
	}

	/**
	 * 获取时间轴动态更新
	 */
	public function timeline(){
		if(!isset($_POST['min_feed_id'])){
			$min_feed_id = -1;  // -1 代表当前页面不是首页
		} else {
			$min_feed_id = $this->input->post('min_feed_id');			
		}
		$this->load->model('feed_model');
		$result = $this->feed_model->check_update($min_feed_id);
		echo json_encode($result);
	}
}