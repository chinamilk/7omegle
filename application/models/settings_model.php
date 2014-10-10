<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Settings_model extends CI_Model{

	public function __construct(){
		$this->load->database();
	}

	/**
	 * 默认设置入口
	 * 设置个人 profile 信息
	 * 
	 * @param  integer $uid 用户uid
	 */
	public function index($uid){
		$gender = $this->input->post('gender');
		$location = $this->input->post('location');
		$tagline = $this->input->post('tagline');
		$bio = $this->input->post('bio');
		$gender = empty($gender) ? '' : $gender;
		$location = empty($location) ? '' : $location;
		$tagline = empty($tagline) ? '' : $tagline;
		$bio = empty($bio) ? '' : $bio;
		// 检查是否有不合法数据
		$tagline_max_length = 64;
		if(mb_strlen($tagline) > $tagline_max_length){
			$data = array(
				'error' => -1,
				'msg' => '签名不能超过 64 个字符'
			);
			return $data;
		}
		$data = array(
			'gender' => $gender,
			'location' => $location,
			'tagline' => $tagline,
			'bio' => $bio
		);
		$this->db->where('user_id', $uid);
		$result = $this->db->update('profile', $data);
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '个人资料设置失败'
			);
			return $data;
		}
		$data = array(
			'error' => 0,
			'msg' => '个人资料设置成功'
		);
		return $data;
	}

	/**
	 * 更新最后在线时间
	 * @param  integer $uid 用户uid
	 */
	public function update_last_online_time($uid){
		$uid = intval($uid);
		$time = date('Y-m-d H:i:s');
		$sql = "UPDATE `profile` SET `last_online_time`=? WHERE `user_id`=?";
		$query = $this->db->query($sql, array($time, $uid));
		return $query;
	}

	/**
	 * 设置头像
	 * 
	 * @param integer $uid 用户uid
	 * @return 返回结果
	 */
	public function set_avatar($uid){
		$userinfo = $this->db->get_where('profile', array('user_id' => $uid));
		$userinfo = $userinfo->result_array();
		if($userinfo){
			$is_profile_exists = TRUE;
		} else {
			$is_profile_exists = FALSE;
		}
		// 设置为默认头像
		if(empty($_FILES)){
			// 注册时设置默认头像
			// 如果不存在记录, 则执行插入操作
			if(!$is_profile_exists){
				$data = array(
					'user_id' => $uid,
					'avatar_medium' => 'default_medium.jpg',
					'avatar_small' => 'default_small.jpg'
				);
				$result = $this->db->insert('profile', $data);
			} else {
				// 如果已存在记录, 则执行更新操作
				$data = array(
					'avatar_medium' => 'default_medium.jpg',
					'avatar_small' => 'default_small.jpg'
				);
				$this->db->where('user_id', $uid);
				$result = $this->db->update('profile', $data);
			}
			// 插入数据库失败
			if(!$result){
				$data = array(
					'error' => -1,
					'msg' => '设置头像失败'
				);
				return $data;
			}
			$data = array(
				'error' => 0,
				'msg' => '头像设置成功'
			);
			return $data;
		}
		// 如果上传图片, 则设置上传的图片为头像
		$config['upload_path'] = APPPATH . '/upload/avatar';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = '2048';
		$config['overwrite'] = TRUE;
		// $this->load->helper('util');
		// $config['file_name'] = $uid . '_' . md5(get_random());
		$config['file_name'] = $uid;
		$this->load->library('upload', $config);
		if(!$this->upload->do_upload()){
			// 上传失败: False
			$error = $this->upload->display_errors();
			$data = array(
				'error' => -1,
				'msg' => $error
			);
			return $data;
		} else {
			$data = $this->upload->data();
			$this->load->model('image_model');
			$thumbnail_avatar = $this->image_model->thumbnail_avatar($data['full_path'], $uid);
			// 删除上传文件
			@unlink($data['full_path']);
			// 更新数据库记录
			$data = array(
				'avatar_medium' => $thumbnail_avatar['avatar_medium'],
				'avatar_small' => $thumbnail_avatar['avatar_small']
			);
			$this->db->where('user_id', $uid);
			$result = $this->db->update('profile', $data);
			// 插入数据库失败
			if(!$result){
				$data = array(
					'error' => -1,
					'msg' => '设置头像失败'
				);
				return $data;
			}
			$data = array(
				'error' => 0,
				'msg' => '头像设置成功'
			);
			return $data;
		}
	}

	/**
	 * 设置密码
	 * @param  integer $uid              用户uid
	 * @param  string  $password_current 当前密码
	 * @param  string  $password_new     新密码
	 * @param  string  $password_confirm 确认密码
	 */
	public function set_password($uid, $password_current, $password_new, $password_confirm){
		$this->load->model('user_model');
		$userinfo = $this->user_model->get_userinfo($uid, 'id', True);
		if(!$userinfo){
			$data = array(
				'error' => -1,
				'msg' => '用户不存在'
			);
			return $data;
		}
		if($password_new != $password_confirm){
			$data = array(
				'error' => -1,
				'msg' => '两次密码不一致'
			);
			return $data;
		}
		// 检查当前密码是否正确
		$password_current_encrypt = $this->user_model->encrypt_password($password_current);
		$password_new_encrypt = $this->user_model->encrypt_password($password_new);
		if($userinfo['password'] != $password_current_encrypt){
			$data = array(
				'error' => -1,
				'msg' => '密码不正确'
			);
			return $data;
		}
		// 开始设置密码
		$this->db->where('id', $uid);
		$result = $this->db->update('user', array('password'=>$password_new_encrypt));
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '密码修改失败了'
			);
		} else {
			$data = array(
				'error' => 0,
				'msg' => '修改密码成功'
			);
		}
		return $data;
	}

	/**
	 * 切换用户状态为空闲
	 * @param  integer $uid 用户uid
	 */
	public function set_status_free($uid){
		$result = $this->set_status($uid, 0);
		if($result['error'] == 0){
			return $result;
		} else {
			$result['msg'] = '切换为空闲状态失败了，再试一次看';
			return $result;
		}
	}

	/**
	 * 切换用户状态为忙碌
	 * @param  integer $uid 用户uid
	 */
	public function set_status_busy($uid){
		$result = $this->set_status($uid, 1);
		if($result['error'] == 0){
			return $result;
		} else {
			$result['msg'] = '切换为忙碌状态失败了，再试一次看';
			return $result;
		}
	}

	/**
	 * 切换用户状态为拒绝匹配任何好友
	 * @param  integer $uid 用户uid
	 */
	public function set_status_refuse($uid){
		$userinfo = $this->user_model->check_login();
		if(!$userinfo['id'] || $userinfo['id'] != $uid){
			$data = array(
				'error' => -1,
				'msg' => '非法操作'
			);
			return $data;
		}
		$this->load->model('member_model');
		$current_friend = $this->member_model->get_current_friend($uid);
		if($current_friend){
			$data = array(
				'error' => -1,
				'msg' => '你当前正在与一位好友交流'
			);
			return $data;
		}
		if($userinfo['status'] == 2){
			$data = array(
				'error' => -1,
			);
			return $data;
		}
		$result = $this->set_status($uid, 2);
		if($result['error'] == 0){
			return $result;
		} else {
			$result['msg'] = '切换为拒绝匹配好友状态失败了，再试一次看';
			return $result;
		}
	}

	/**
	 * 取消'拒绝任何人匹配'状态
	 * @param  integer $uid 用户uid
	 */
	public function cancel_status_refuse($uid){
		$userinfo = $this->user_model->check_login();
		if(!$userinfo['id'] || $userinfo['id'] != $uid){
			$data = array(
				'error' => -1,
				'msg' => '非法操作'
			);
			return $data;
		}
		// 只有用户状态为 `status`=2 时，才允许切换为空闲状态
		if($userinfo['status'] != 2){
			$data = array(
				'error' => -1,
				'msg' => '非法操作'
			);
			return $data;
		}
		$result = $this->set_status_free($uid);
		return $result;
	}

	/**
	 * 设置用户状态
	 * @param  integer $uid 用户uid
	 * @param  integer $status_value 设置的状态值
	 */
	public function set_status($uid, $status_value){
		// $userinfo = $this->user_model->check_login();
		// if(!$userinfo['id']){
		// 	$data = array();
		// 	return $data;
		// }
		$sql = "UPDATE `profile` SET `status`=? WHERE `user_id`=?";
		$result = $this->db->query($sql, array($status_value, $uid));
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '修改状态失败'
			);
			return $data;
		}
		$data = array(
			'error' => 0,
			'msg' => 'ok'
		);
		return $data;
	}
}
