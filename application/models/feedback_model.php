<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feedback_model extends CI_Model{
	public function __construct(){
		$this->load->database();		
	}

	/**
	 * 提交用户反馈
	 */
	public function create_feedback(){
		// 同一 ip 在某一段时间内限制提交次数
		$ip = $this->input->ip_address();
		$time_limit = 1; // 分钟, 时间限制间隔
		$check_min_time = date('Y-m-d H:i:s', time() - $time_limit*60);
		$count_limit = 3; // 限制次数
		$sql = "SELECT count(*) as `count` FROM `feedback` WHERE `ip`=? AND `time`>?";
		$query = $this->db->query($sql, array($ip, $check_min_time));
		$count_array = $query->row_array();
		$count = $count_array['count'];
		if($count >= $count_limit){
			$data = array(
				'error' => -1,
				'msg' => '你的提交次数过于频繁，请稍后再试'
			);
			return $data;
		}
		
		$contact = $this->input->post('feedback-contact');
		$content = $this->input->post('feedback-content');
		$contact = empty($contact) ? '' : $contact;
		$content = empty($content) ? '' : $content;
		$time = date('Y-m-d H:i:s');
		$data = array(
			'contact' => $contact,
			'content' => $content,
			'ip' => $ip,
			'time' => $time,
		);
		$result = $this->db->insert('feedback', $data);
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '反馈提交失败，再试一次看'
			);
			return $data;
		}
		$data = array(
			'error' => 0,
			'msg' => '反馈提交成功'
		);
		return $data;
	}

	/**
	 * 获取用户反馈
	 */
	public function get_feedback(){

	}
}
