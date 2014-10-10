<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->load->model('user_model');
		$userinfo = $this->user_model->check_login();
		$data['userinfo'] = $userinfo;
		if(!$userinfo){
			$data['title'] = '欢迎来到 7omegle';
			$this->load->view('welcome', $data);
		} else {
			// 获取页码
			if(intval($this->input->get('p')) && intval($this->input->get('p') >= 1)){
				$page = intval($this->input->get('p'));
			} else {
				$page = 1;
			}
			// 每页显示数量
			$per_page = 30;

			$this->load->model('member_model');
			$this->load->model('feed_model');

			// 总页数
			$feed_count = $this->feed_model->get_feed_count($userinfo['id']);
			$total_page = ceil($feed_count/$per_page);

			if($page > $total_page){
				$page = $total_page;
			}

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

			$current_friend = $this->member_model->get_current_friend($userinfo['id']);
			$feeds = $this->feed_model->get_feeds($userinfo['id'], $page, $per_page);

			$data['current_friend'] = $current_friend;
			$data['feeds'] = $feeds;
			$rerandom_permission = $this->member_model->check_rerandom_permission();
			$data['rerandom_permission'] = $rerandom_permission['permission'];
			$data['current_page'] = $page;
			$data['total_page'] = $total_page;
			$data['title'] = '7omegle';
			$this->load->view('home', $data);
		}
	}
}