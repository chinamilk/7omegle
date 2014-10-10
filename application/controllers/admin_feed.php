<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require VENDORPATH . 'autoload.php';
class Admin_feed extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}

	public function index(){
	}

	public function get_all_feeds(){
		/*
		// 获取页码
		if(intval($this->input->get('p')) && intval($this->input->get('p') >= 1)){
			$page = intval($this->input->get('p'));
		} else {
			$page = 1;
		}
		// 每页显示数量
		$per_page = 28;

		$this->load->model('admin/admin_feed_model');
		$feeds = $this->admin_feed_model->get_all_feeds($page, $per_page);
		// 总页数
		$total_page = ceil($feeds['total_count']/$per_page);
		if($page > $total_page){
			$page = $total_page;
		}
		$data['current_page'] = $page;
		// 是否有下一页
		if($total_page > $page){
			$next_page = $page + 1;
			$data['next_page'] = $next_page;
		} else{
			$data['next_page'] = False;
		}
		// 是否有上一页
		if($page > 1){
			$previous_page = $page - 1;
			$data['previous_page'] = $previous_page;
		} else{
			$data['previous_page'] = False;
		}

		$data['userinfo'] = $this->user_model->check_login();
		$data['title'] = '所有动态 - 7omegle 后台管理';
		$data['feeds'] = $feeds['feeds'];
		$data['total_page'] = $total_page;

		$this->load->view('admin/feeds.php', $data);
		*/
	}
}