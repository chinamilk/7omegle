<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Logs_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}

	/**
	 * 记录登录日志
	 * @param integer $uid 用户uid
	 */
	public function set_login_log($uid){
		$data = array(
			'user_id' => $uid,
			'ip' => $this->input->ip_address(),
			'time' => date('Y-m-d H:i:s')
		);
		$result = $this->db->insert('login_log', $data);
		return $result;
	}
}