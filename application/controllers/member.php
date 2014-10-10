<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}

	/**
	 * 列出所有会员
	 */
	public function get_all_members(){
		// 获取页码
		if(intval($this->input->get('p')) && intval($this->input->get('p') >= 1)){
			$page = intval($this->input->get('p'));
		} else {
			$page = 1;
		}
		// 每页显示数量
		$per_page = 28;

		$this->load->model('user_model');
		$this->load->model('member_model');

		// 总页数
		$member_count = $this->member_model->get_member_count();
		$total_page = ceil($member_count/$per_page);

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


		$data['userinfo'] = $this->user_model->check_login();
		$data['title'] = '所有用户 - 7omegle';
		$data['members'] = $this->member_model->get_all_members($page, $per_page);
		$data['total_page'] = $total_page;

		$this->load->view('members', $data);
	}

	/**
	 * 会员个人主页
	 * @param string $username 用户名
	 */
	public function get_member($username){
		$this->load->model('user_model');
		$data['userinfo'] = $this->user_model->check_login();
		$data['title'] = '个人主页 - 7omegle';

		$this->load->model('member_model');
		$member_info = $this->member_model->get_member($username, 'username');
		// 修复用户状态，如果用户很久未登录暂时不会自动修改 `profile` 表中的 `status` 字段，所以需要在这里显示给用户正确的状态供其他用户查看此用户信息
		$current_friend = $this->member_model->get_current_friend($member_info['id']);
		if($current_friend){
			$member_info['status'] = 1;
		} else {
			if($member_info['status'] == 1){
				// 修复用户状态
				$this->load->model('settings_model');
				$this->settings_model->set_status_free($member_info['id']);
			}
			if($member_info['status'] != 2){
				$member_info['status'] = 0;
			}
		}
		$data['member_info'] = $member_info;
		$this->load->view('member', $data);
	}

	/**
	 * 随机匹配一位好友
	 */
	public function random(){
		$this->load->model('user_model');
		$data['userinfo'] = $this->user_model->check_login();
		$data['title'] = '随机匹配';

		$this->load->model('member_model');
		$result = $this->member_model->get_random();
		if($result['error'] != 0){
			$data['error'] = True;
			$data['errorMsg'] = $result['msg'];
		} else {
			$data['error'] = False;
			$data['match_friend'] = $result['match_friend'];
		}
		$this->load->view('random', $data);
	}

	/**
	 * 申请重新分配好友
	 */
	public function rerandom(){
		$this->load->model('user_model');
		$data['userinfo'] = $this->user_model->check_login();
		$data['title'] = '申请重新匹配好友';

		$this->load->model('member_model');
		$result = $this->member_model->rerandom($data['userinfo']['id']);
		if($result['error'] != 0){
			$data['error'] = True;
			$data['errorMsg'] = $result['msg'];
		} else if($result['error'] == 0) {
			// 允许重新分配好友
			$data['error'] = False;
			$data['errorMsg'] = $result['msg'];
		}
		$this->load->view('rerandom', $data);
	}
}
