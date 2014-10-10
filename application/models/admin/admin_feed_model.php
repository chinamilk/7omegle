<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_feed_model extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->model('admin/admin_user_model');
	}

	/**
	 * 获取所有动态
	 * @param  integer $page 获取的页数
	 * @param  integer $per_page 每页显示数量
	 */
	public function get_all_feeds($page, $per_page = 30){
		// 检查是否是管理员
		if(!$this->admin_user_model->check_admin()){
			header('Location: /');
		}

		$page = intval($page);
		$per_page = intval($per_page);
		if($page <= 0){
			$page = 1;
		}
		if($per_page <= 0){
			$per_page = 30;
		}
		$start = ($page-1)*$per_page;
		$sql = "SELECT * FROM `feed` ORDER BY `time` DESC LIMIT ?,?";
		$query = $this->db->query($sql, array($start, $per_page));
		$feeds = $query->result_array();
		if(!$feeds){
			$data = array(
				'feeds' => False,
				'total_count' => 0
			);
			return $data;
		}
		$this->load->model('user_model');
		foreach ($feeds as $k => $v) {
			$feeds[$k]['user'] = $this->user_model->get_userinfo($v['user_id'], 'id');
			$feeds[$k]['content'] = htmlspecialchars($feeds[$k]['content']);
			if($feeds[$k]['image']){
				$feeds[$k]['image'] = "/application/upload/media/" . $feeds[$k]['image'];
			}
		}
		$data = array(
			'feeds' => $feeds,
			'total_count' => $this->get_all_feeds_count()
		);
		return $data;
	}

	/**
	 * 获取动态总数
	 */
	public function get_all_feeds_count(){
		$sql = "SELECT count(*) as `count` FROM `feed`";
		$query = $this->db->query($sql);
		$count_array = $query->row_array();
		if(!$count_array){
			return 0;
		}
		$count = $count_array['count'];
		return $count;
	}

	/**
	 * 获取某一个用户的所有动态
	 * @param  integer $uid 用户uid
	 * @param  integer $page 获取的页数
	 * @param  integer $per_page 每页显示数量
	 */
	public function get_user_feeds($uid, $page, $per_page = 30){
		// 检查是否是管理员
		if(!$this->admin_user_model->check_admin()){
			header('Location: /');
		}
		$uid = intval($uid);
		$page = intval($page);
		$per_page = intval($per_page);
		if($page <= 0){
			$page = 1;
		}
		if($per_page <= 0){
			$per_page = 30;
		}
		$start = ($page-1)*$per_page;
		$sql = "SELECT * FROM `feed` WHERE `user_id`=? ORDER BY `time` DESC LIMIT ?,?";
		$query = $this->db->query($sql, array($uid, $start, $per_page));
		$feeds = $query->result_array();
		if(!$feeds){
			$data = array(
				'feeds' => False,
				'total_count' => 0
			);
			return $data;
		}
		$this->load->model('user_model');
		foreach ($feeds as $k => $v) {
			$feeds[$k]['user'] = $this->user_model->get_userinfo($v['user_id'], 'id');
			$feeds[$k]['content'] = htmlspecialchars($feeds[$k]['content']);
			if($feeds[$k]['image']){
				$feeds[$k]['image'] = "/application/upload/media/" . $feeds[$k]['image'];
			}
		}
		$sql = "SELECT count(*) as `count` from `feed` WHERE `user_id`=?";
		$query = $this->db->query($sql, array($uid));
		$count_array = $query->row_array();
		$data = array(
			'feeds' => $feeds,
			'total_count' => $count_array['count']
		);
		return $data;
	}

	/**
	 * 获取某一个匹配关系的所有动态
	 * @param  integer $user_friend_id 用户与好友匹配的id
	 * @param  integer $page 获取的页数
	 * @param  integer $per_page 每页显示数量
	 */
	public function get_match_feeds($user_friend_id, $page, $per_page = 30){
		// 检查是否是管理员
		if(!$this->admin_user_model->check_admin()){
			header('Location: /');
		}
		$user_friend_id = intval($user_friend_id);
		$page = intval($page);
		$per_page = intval($per_page);
		if($page <= 0){
			$page = 1;
		}
		if($per_page <= 0){
			$per_page = 30;
		}
		$start = ($page-1)*$per_page;
		$sql = "SELECT * FROM `feed` WHERE `user_friend_id`=? ORDER BY `time` DESC LIMIT ?,?";
		$query = $this->db->query($sql, array($user_friend_id, $start, $per_page));
		$feeds = $query->result_array();
		if(!$feeds){
			$data = array(
				'feeds' => False,
				'total_count' => 0
			);
			return $data;
		}
		$this->load->model('user_model');
		foreach ($feeds as $k => $v) {
			$feeds[$k]['user'] = $this->user_model->get_userinfo($v['user_id'], 'id');
			$feeds[$k]['content'] = htmlspecialchars($feeds[$k]['content']);
			if($feeds[$k]['image']){
				$feeds[$k]['image'] = "/application/upload/media/" . $feeds[$k]['image'];
			}
		}
		$sql = "SELECT count(*) as `count` from `feed` WHERE `user_friend_id`=?";
		$query = $this->db->query($sql, array($user_friend_id));
		$count_array = $query->row_array();
		$data = array(
			'feeds' => $feeds,
			'total_count' => $count_array['count']
		);
		return $data;
	}
}