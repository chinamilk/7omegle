<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require VENDORPATH . 'autoload.php';
class Online extends CI_Controller{
	public function index(){
		header('Content-type:text/html;charset=utf8');
		
		// 在线人数统计
		$this->load->model('member_model');
		$online = $this->member_model->get_online_member_count();
		echo "<div>当前在线人数：{$online}</div>";
		

		// echo '<div>';
		// echo '<pre>';
		// print_r($result);
		// echo '<hr/>';
		// var_dump($result);
		// echo '</pre>';
		// echo '</div>';
	}
}


