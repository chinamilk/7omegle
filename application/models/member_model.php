<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}

	/**
	 * 获取所有会员
	 * @param  integer $page 获取的页数
	 * @param  integer $per_page 每页数量, 默认28
	 */
	public function get_all_members($page, $per_page = 28){
		$this->load->model('user_model');

		$page = intval($page);
		if($page < 1){
			$page = 1;
		}
		$start = ($page-1)*$per_page;
		$sql = "SELECT * FROM `user` LIMIT ?,?";
		// $sql = "SELECT * FROM `user` ORDER BY `time` DESC LIMIT ?,?";
		// $sql = "SELECT * FROM `user` ORDER BY RAND() DESC LIMIT ?,?";
		$members_object = $this->db->query($sql, array($start, $per_page));
		$members = $members_object->result_array();
		foreach ($members as $k => $v) {
			$members[$k] = $this->user_model->get_userinfo($v['id'], 'id');
		}
		return $members;
	}

	/**
	 * 获取总的会员数
	 */
	public function get_member_count(){
		$sql = "SELECT count(*) as `count` FROM `user`";
		$count_object = $this->db->query($sql);
		$result_array = $count_object->row_array();
		$count = $result_array['count'];
		return $count;
	}

	/**
	 * 获取在线会员数
	 */
	public function get_online_member_count(){
		// 2 分钟之内算作在线
		$min_time = date('Y-m-d H:i:s', time()-2*60);
		$sql = "SELECT count(*) as `count` FROM `profile` WHERE `last_online_time`>?";
		$query = $this->db->query($sql, array($min_time));
		$result_array = $query->row_array();
		return $result_array['count'];
	}

	/**
	 * 获取单个会员信息
	 * @param  string $username_or_id 用户名或用户id
	 * @param  string $by 通过什么查找会员, 可选值: 'username' or 'id'
	 */
	public function get_member($username_or_id, $by = 'id'){
		// 账户信息
		$this->load->model('user_model');
		$member_info = $this->user_model->get_userinfo($username_or_id, $by);
		// 如果用户不存在
		if(!$member_info){
			return False;
		}
		// 匹配过的好友, 获取最近 5 个
		$sql = "SELECT * FROM `user_friend` WHERE `user_id`=? OR `friend_id`=? ORDER BY `time` DESC LIMIT 5";
		$query = $this->db->query($sql, array($member_info['id'], $member_info['id']));
		$match_friend = $query->result_array();
		if($match_friend){
			foreach ($match_friend as $k => $v) {
				if($v['user_id'] != $member_info['id']){
					$match_friend[$k] = $this->user_model->get_userinfo($v['user_id'], 'id');
				} else if($v['friend_id'] != $member_info['id']){
					$match_friend[$k] = $this->user_model->get_userinfo($v['friend_id'], 'id');
				}
				$match_friend[$k]['match_info'] = $v;
			}
		}
		$member_info['friend_history'] = $match_friend;

		return $member_info;
	}

	/**
	 * 获取当前匹配的未过期的正在交流的好友
	 * @param  integer $user_id 用户id
	 */
	public function get_current_friend($user_id){
		$member = $this->get_member($user_id, 'id');
		$friend_history = $member['friend_history'];
		if(empty($friend_history)){
			return False;
		}
		$last_friend = $friend_history[0];
		// 判断是否已失效
		if($last_friend['match_info']['is_useless'] == 1){
			return False;
		}
		// 判断是否已过期
		$diff_time = time() - strtotime($last_friend['match_info']['time']);
		if($diff_time < 7*60*60*24){
			return $last_friend;
		} else {
			return False;
		}
	}

	/**
	 * 为当前登录用户随机匹配一个好友
	 * 
	 * To Do: 匹配到好友以后发送邮件
	 */
	public function get_random(){
		$this->load->model('user_model');
		$userinfo = $this->user_model->check_login();
		if(!$userinfo['id']){
			$data = array(
				'error' => -1,
				'msg' => '你还没有登录'
			);
			return $data;
		}
		// 检查现在是否还有未过期的好友
		$current_friend = $this->get_current_friend($userinfo['id']);
		if($current_friend){
			$data = array(
				'error' => -1,
				'msg' => '你当前正在与一位好友交流, 满7天后才可以再次匹配'
			);
			return $data;
		}
		/*
		$sql = "SELECT * FROM `profile` WHERE `status`=? AND `user_id`!={$userinfo['id']} ORDER BY RAND() LIMIT 1";
		$query = $this->db->query($sql, array(0));
		$match_friend = $query->row_array();    // 匹配到的好友
		if(!$match_friend){
			$data = array(
				'error' => -1,
				'msg' => '当前没有处于空闲状态的好友'
			);
			return $data;
		}
		// 检查随机匹配的好友是否正在与别人交流; 因为有可能出现 `status` 修改不及时的情况
		$match_friend_current_friend = $this->get_current_friend($match_friend['user_id']);
		if($match_friend_current_friend){
			$data = array(
				'error' => -1,
				'msg' => '未匹配到好友，再试一次看看'
			);
			return $data;
		}
		*/
		$result = $this->get_free_member();
		if($result['error'] != 0){
			return $result;
		} else {
			$match_friend = $result['data'];
		}
		$data = array(
			'user_id' => $userinfo['id'],
			'friend_id' => $match_friend['user_id'],
			'time' => date('Y-m-d H:i:s')
		);
		$result = $this->db->insert('user_friend', $data);
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '匹配失败了, 再试一次看'
			);
			return $data;
		}
		// 修改两个用户 `status` 状态为1('busy')
		$this->load->model('settings_model');
		$result1 = $this->settings_model->set_status_busy($userinfo['id']);
		$result2 = $this->settings_model->set_status_busy($match_friend['user_id']);
		
		$data = array(
			'error' => 0,
			'match_friend' => $this->get_member($match_friend['user_id'], 'id')
		);
		return $data;
	}

	/**
	 * 获取一位空闲的会员
	 */
	public function get_free_member(){
		// 从几位会员中随机选取一个
		$count = 20;
		$this->load->model('user_model');
		// 检查登录
		$userinfo = $this->user_model->check_login();
		if(!$userinfo){
			return False;
		}
		$sql = "SELECT * FROM `profile` WHERE `status`=? AND `user_id`!={$userinfo['id']} ORDER BY `last_online_time` DESC, RAND() LIMIT ?";
		$query = $this->db->query($sql, array(0, $count));
		$free_members = $query->result_array();
		if(!$free_members){
			$data = array(
				'error' => -1,
				'msg' => '当前没有处于空闲状态的会员'
			);
			return $data;
		}
		$random_member = $free_members[mt_rand(0, count($free_members)-1)];
		// 检查随机匹配的好友是否正在与别人交流; 因为有可能出现 `status` 修改不及时的情况
		$random_member_current_friend = $this->get_current_friend($random_member['user_id']);
		if($random_member_current_friend){
			$data = array(
				'error' => -1,
				'msg' => '未匹配到好友，再试一次看看'
			);
			return $data;
		}
		$data = array(
			'error' => 0,
			'data' => $random_member
		);
		return $data;
	}

	/**
	 * 检查是否可以申请重新分配好友
	 * 如果匹配后超过 2 天对方没有发布任何动态，则可以申请重新分配好友
	 */
	public function check_rerandom_permission(){
		$this->load->model('user_model');
		// 检查登录
		$userinfo = $this->user_model->check_login();
		if(!$userinfo){
			$data = array(
				'error' => -1,
				'msg' => '你还没有登录',
				'permission' => False
			);
			return $data;
		}
		$current_friend = $this->get_current_friend($userinfo['id']);
		if(!$current_friend){
			$data = array(
				'error' => -1,
				'msg' => '你还没有好友',
				'permission' => False
			);
			return $data;
		}
		$expire = 2;  // 期限为 2 天
		$match_time = $current_friend['match_info']['time'];
		$diff_time = time() - strtotime($match_time);
		// 如果匹配时间还不到两天，则不允许申请重新分配
		if($diff_time < $expire*60*60*24){
			$data = array(
				'error' => -1,
				'msg' => '你不符合申请条件',
				'permission' => False
			);
			return $data;
		}
		$sql = "SELECT count(*) as `count` FROM `feed` WHERE `user_id`=? AND `user_friend_id`=?";
		$query = $this->db->query($sql, array($current_friend['id'], $current_friend['match_info']['id']));
		$count_array = $query->row_array();
		$count = $count_array['count'];
		if($count < 1){
			$data = array(
				'error' => 0,
				'permission' => True
			);
			return $data;
		} else {
			$data = array(
				'error' => -1,
				'msg' => '你不符合申请条件',
				'permission' => False
			);
			return $data;
		}
	}

	/**
	 * 重置用户状态，允许重新分配好友
	 * @param integer $uid 用户uid
	 */
	public function rerandom($uid){
		$this->load->model('user_model');
		$userinfo = $this->user_model->check_login();
		if(!$userinfo){
			$data = array(
				'error' => -1,
				'msg' => '你还没有登录',
			);
			return $data;
		}
		if($userinfo['id'] != $uid){
			$data = array(
				'error' => -1,
				'msg' => '参数错误',
			);
			return $data;
		}
		$result = $this->check_rerandom_permission();
		if($result['error'] != 0){
			return $result;
		}
		$permission = $result['permission'];
		if(!$permission){
			$data = array(
				'error' => -1,
				'msg' => '你不符合申请条件'
			);
			return $data;
		}
		$current_friend = $this->get_current_friend($uid);
		$result = $this->set_user_friend_status($current_friend['match_info']['id'], 'useless');
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '出错了，再试一次看'
			);
			return $data;
		}
		$this->load->model('settings_model');
		$result1 = $this->settings_model->set_status_free($uid);
		if($result1['error'] != 0){
			$this->set_user_friend_status($current_friend['match_info']['id'], 'not_useless');
			$data = array(
				'error' => -1,
				'msg' => '出错了，再试一次看'
			);
			return $data;
		}
		$result2 = $this->settings_model->set_status_free($current_friend['id']);
		if($result2['error'] != 0){
			$this->set_user_friend_status($current_friend['match_info']['id'], 'not_useless');
			$this->settings_model->set_status_busy($uid);
			$data = array(
				'error' => -1,
				'msg' => '出错了，再试一次看'
			);
			return $data;
		}
		$data = array(
			'error' => 0,
			'msg' => '你的匹配状态已重置，现在你可以重新分配好友了'
		);
		return $data;
	}

	/**
	 * 好友匹配状态切换
	 * @param  integer $user_friend_id 匹配id
	 * @param  enum $status 好友匹配状态; 'useless' | 'not_useless'
	 */
	public function set_user_friend_status($id, $status){
		if($status == 'useless'){
			// 设为无效
			$status = 1;
		} else if($status == 'not_useless'){
			// 设为有效
			$status = 0;
		} else {
			return False;
		}
		$sql = "UPDATE `user_friend` SET `is_useless`=? WHERE `id`=?";
		$query = $this->db->query($sql, array($status, $id));
		return $query;
	}
}