<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use Mailgun\Mailgun;

class User_model extends CI_Model{
	private $uid; // 用户uid
	private $username; // 用户名

	private $table_user = 'user'; // 用户表

	public function __construct($uid = NULL){
		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('util');
		if($uid){
			$this->uid = $uid;
		}
	}

	/**
	 * 登录
	 * @param  $username 用户名
	 * @param  $password 密码
	 */
	public function login($username, $password){
		$is_login = $this->check_login();
		if($is_login){            // 如果已登录, 跳转至首页
			redirect('/');
		}
		$password = $this->encrypt_password($password);
		// 判断用户是否存在，密码是否正确
		$by = 'username';
		if(preg_match('/.*?@.*?/i', $username)){
			$by = 'email';
		}
		$userinfo = $this->get_userinfo($username, $by, True);
		if(!$userinfo || !$userinfo['username'] || $password !== $userinfo['password']){
			$data = array(
				'error' => -1,
				'msg' => "用户名或密码错误"
			);
			return $data;
		}
		$new_cookie_hash = md5(get_random());
		$data = array(
			'cookie_hash' => $new_cookie_hash
		);
		$this->db->where(array('id'=>$userinfo['id']))->update($this->table_user, $data);
		// 更新最后在线时间
		$this->load->model('settings_model');
		$this->settings_model->update_last_online_time($userinfo['id']);
		// 登录, 记录日志, 设置cookie
		$this->load->model('logs_model');
		$this->logs_model->set_login_log($userinfo['id']);
		$this->load->model('cookie_model');
		$this->cookie_model->set_login_cookie($userinfo['id'], $userinfo['username'], $new_cookie_hash);
		// 检查 `status` 状态是否正确
		$this->check_status($userinfo['id']);
		redirect('/');
	}

	/**
	 * 每次登录时检查 `status` 状态
	 * 如果用户存在过期的好友，并且`status`状态仍然为1(busy), 则设置为0(空闲)
	 * @param  integer $uid 用户uid
	 */
	public function check_status($uid){
		$this->load->model('member_model');
		$member_info = $this->member_model->get_member($uid);
		$current_friend = $this->member_model->get_current_friend($member_info['id']);
		// 如果所有好友已过期, 则检查`status`状态是否为 1, 如果为 1则设置为 0
		if(!$current_friend){
			if($member_info['status'] == 1){
				// 用户状态切换为空闲
				$this->load->model('settings_model');
				$this->settings_model->set_status_free($member_info['id']);
			}
		}
		// 检查最近匹配的一个好友状态是否正确
		if($member_info['friend_history'] && $member_info['friend_history'][0]){
			$last_friend = $member_info['friend_history'][0];
			$last_friend_current_friend = $this->member_model->get_current_friend($last_friend['id']);
			// 如果最近匹配的好友状态不正确则修改
			if(!$last_friend_current_friend){
				if($last_friend['status'] == 1){
					// 用户状态切换为空闲
					$this->load->model('settings_model');
					$this->settings_model->set_status_free($last_friend['id']);
				}
			}
		}
	}

	/**
	 * 退出
	 */
	public function logout(){
		$this->load->model('cookie_model');
		$this->cookie_model->logout();
		redirect('/login');
	}

	/**
	 * 注册
	 */
	public function register(){
		$is_login = $this->check_login();
		if($is_login){            // 如果已登录, 跳转至首页
			$this->load->helper('url');
			redirect('/');
		}
		$username = $this->input->post('username');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$repassword = $this->input->post('repassword');

		// 验证是否有空字段
		$data = array();
		if(empty($username))
			$data['msg'] = '用户名为必填项';
		else if(empty($email))
			$data['msg'] = '邮箱为必填项';
		else if(empty($password))
			$data['msg'] = '未填写密码';
		else if(empty($repassword))
			$data['msg'] = '未填写确认密码';
		else if($password != $repassword)
			$data['msg'] = '两次填写的密码不一致';
		if(!empty($data['msg'])){
			$data['error'] = -1;
			return $data;
		}

		// 验证用户名
		// 验证用户名,邮箱是否已存在
		$userinfo = $this->get_userinfo($username, 'username');
		if($userinfo){
			$data = array(
				'error' => -1,
				'msg' => '此用户名已存在'
			);
			return $data;
		}
		$userinfo = $this->get_userinfo($email, 'email');
		if($userinfo){
			$data = array(
				'error' => -1,
				'msg' => '此邮箱已注册'
			);
			return $data;
		}
		// 验证用户名称是否符合规则
		$validate_user = $this->validate_user_rule($username, 'username');
		if($validate_user['error'] != 0)
			return $validate_user;

		$validate_user = $this->validate_user_rule($password, 'password');
		if($validate_user['error'] != 0)
			return $validate_user;

		// 开始注册
		$userinfo = array(
			'username' => $username,
			'password' => $this->encrypt_password($password),
			'email' => $email,
			// 'halt' => $this->get_halt(),
			// 'halt' => $this->halt,
			// 'ip' => '',
			'cookie_hash' => md5(get_random()),
			'time' => date('Y-m-d H:i:s')
		);
		$result = $this->db->insert($this->table_user, $userinfo);
		// 插入数据库失败
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '用户创建失败'
			);
			return $data;
		}
		// 创建成功
		// 发送注册邮件
		// -*-
		$this->send_activate_mail($email, $username);
		$id = $this->db->insert_id();
		// 设置默认头像
		$this->load->model('settings_model');
		$this->settings_model->set_avatar($id);
		// 更新最后在线时间
		$this->settings_model->update_last_online_time($id);

		$userinfo['id'] = $id;
		// 登录, 记录日志, 设置cookie
		$this->load->model('logs_model');
		$this->logs_model->set_login_log($userinfo['id']);
		// 设置cookie
		$this->load->model('cookie_model');
		$this->cookie_model->set_login_cookie($userinfo['id'], $userinfo['username'], $userinfo['cookie_hash']);
		redirect('/');
	}

	/**
	 * 检查是否已登录
	 */
	public function check_login(){
		$this->load->model('cookie_model');
		$cookie = $this->cookie_model->get_login_cookie();
		// print_r($cookie);
		if(!$cookie)
			return False;
		$userinfo = $this->get_userinfo_by_id($cookie[0], True);
		if(empty($userinfo)){
			return False;
		}
		$hash = md5(md5($userinfo['id'].$userinfo['username']).$userinfo['cookie_hash']);
		if($userinfo['username'] != $cookie[1] || $hash != $cookie[2]){
			return False;
		}
		unset($userinfo['password']);
		unset($userinfo['cookie_hash']);
		// 检查个人状态是否正确
		$this->check_status($userinfo['id']);
		return $userinfo;
	}

	/**
	 * 根据用户id, username 或 email 获取用户信息
	 * 待定: 区分是否返回密码字段
	 * 
	 * @param integer or string $username_or_email 用户id 或 用户名 或 email
	 * @param enum $by 根据什么检查，默认根据用户名检查
	 * @param boolean $get_secret_info 是否获取私密信息; 密码, cookie_hash等
	 * @return 用户信息 or FALSE
	 */
	public function get_userinfo($username_or_email, $by = 'username', $get_secret_info = False){
		$user_object = $this->db->get_where($this->table_user, array($by=>$username_or_email));
		$userinfo = $user_object->row_array();
		if($userinfo){
			$profile_object = $this->db->get_where('profile', array('user_id' => $userinfo['id']));
			$profile = $profile_object->result_array();
			if(empty($profile)){
				return False;
			}
			$userinfo = array_merge($userinfo, $profile[0]);
			// 是否获取 password,cookie_hash 字段
			if(!$get_secret_info){
				unset($userinfo['password']);
				unset($userinfo['cookie_hash']);
			}
			// 过滤特殊字符
			$userinfo['gender'] = htmlspecialchars($userinfo['gender']);
			$userinfo['location'] = htmlspecialchars($userinfo['location']);
			$userinfo['tagline'] = htmlspecialchars($userinfo['tagline']);
			$userinfo['bio'] = htmlspecialchars($userinfo['bio']);

			// 检查是否是管理员
			if($userinfo['user_type'] != 1){
				$userinfo['is_admin'] = False;
			} else if($userinfo['user_type'] == 1){
				$userinfo['is_admin'] = True;
			}

			// 头像地址完整路径设置
			$time = time();
			$userinfo['avatar_medium'] = "/application/upload/avatar/" . $userinfo['avatar_medium'] . "?m={$time}";
			$userinfo['avatar_small'] = "/application/upload/avatar/" . $userinfo['avatar_small'] . "?m={$time}";
			// 获取最近一次登录时间
			$sql = "SELECT * FROM `login_log` WHERE `user_id`=? ORDER BY `time` DESC LIMIT 1";
			$last_login_object = $this->db->query($sql, array($userinfo['id']));
			$last_login = $last_login_object->row_array();
			if(!$last_login){
				$userinfo['last_login']['time'] = $userinfo['time'];
			} else {
				$userinfo['last_login'] = $last_login;
			}
			return $userinfo;
		}
		return False;
	}

	/**
	 * 根据用户 id 获取用户信息
	 * 
	 * @param integer $id 用户id
	 * @param boolean $get_secret_info 是否获取 password,cookie_hash 字段
	 */
	public function get_userinfo_by_id($id, $get_secret_info = False){
		$user = $this->db->get_where($this->table_user, array('id' => $id), 1);
		$userinfo = $user->row_array();
		if(!$userinfo)
			return False;
		$profile_object = $this->db->get_where('profile', array('user_id' => $userinfo['id']));
		$profile = $profile_object->result_array();
		if(empty($profile)){
			return False;
		}
		$userinfo = array_merge($userinfo, $profile[0]);
		// 是否获取 password,cookie_hash 字段
		if(!$get_secret_info){
			unset($userinfo['password']);
			unset($userinfo['cookie_hash']);
		}
		// 过滤特殊字符
		$userinfo['gender'] = htmlspecialchars($userinfo['gender']);
		$userinfo['location'] = htmlspecialchars($userinfo['location']);
		$userinfo['tagline'] = htmlspecialchars($userinfo['tagline']);
		$userinfo['bio'] = htmlspecialchars($userinfo['bio']);

		// 检查是否是管理员
		if($userinfo['user_type'] != 1){
			$userinfo['is_admin'] = False;
		} else if($userinfo['user_type'] == 1){
			$userinfo['is_admin'] = True;
		}
		
		// 头像地址完整路径设置
		$time = time();
		$userinfo['avatar_medium'] = "/application/upload/avatar/" . $userinfo['avatar_medium'] . "?m={$time}";
		$userinfo['avatar_small'] = "/application/upload/avatar/" . $userinfo['avatar_small'] . "?m={$time}";
		// 获取最近一次登录时间
		$sql = "SELECT * FROM `login_log` WHERE `user_id`=? ORDER BY `time` DESC LIMIT 1";
		$last_login_object = $this->db->query($sql, array($userinfo['id']));
		$last_login = $last_login_object->row_array();
		if(!$last_login){
			$userinfo['last_login']['time'] = $userinfo['time'];
		} else {
			$userinfo['last_login'] = $last_login;
		}
		return $userinfo;
	}

	/**
	 * 加密密码
	 * @param $password 密码
	 * @return 加密后的密码
	 */
	// public function encrypt_password($password, $encrypt_algo){
	public function encrypt_password($password){
		for($i=0; $i<3; $i++){
			$password = md5($password . md5($password));
		}
		return $password;
	}

	/**
	 * 验证用户名, 密码是否合法
	 *
	 * @param string $validate_data 需要验证的数据
	 * @param string $type 验证类型
	 */
	public function validate_user_rule($validate_data, $type = 'username'){
		switch ($type) {
			case 'username':
				$minlength = 3;
				$pattern = '/^[a-zA-Z][a-zA-Z0-9_]{' . ($minlength-1) . ',}$/i';
				if(!preg_match($pattern, $validate_data)){
					$data = array(
						'error' => -1,
						'msg' => '用户名必须为字母、数字、下划线并以字母开头, 且最小长度为' . $minlength
					);
					return $data;
				}
				$this->config->load('forbidden_username', True);
				$forbidden_username = $this->config->item('username', 'forbidden_username');
				if(in_array($validate_data, $forbidden_username)){
					$data = array(
						'error' => -1,
						'msg' => '用户名已存在'
					);
					return $data;
				}
				break;
			
			case 'password':
				$minlength = 4;
				if(mb_strlen($validate_data) < $minlength){
					$data = array(
						'error' => -1,
						'msg' => '密码最小长度为' . $minlength
					);
					return $data;
				}
				break;

			default:
				$data = array(
					'error' => -1,
					'msg' => '类型参数错误'
				);
				return $data;
				break;
		}
		$data = array(
			'error' => 0
		);
		return $data;
	}

	/**
	 * 发送或验证注册激活邮件
	 * @param string $to 接收邮件的用户邮箱
	 */
	public function send_activate_mail($to, $username){
		// 暂时只发送欢迎邮件, 不包含激活链接
		require APPPATH.'../vendor/autoload.php';
		$this->load->config('api_keys', True);
		$api_key = $this->config->item('mailgun_api_key', 'api_keys');
		$domain = $this->config->item('mailgun_domain', 'api_keys');
		$mgClient = new Mailgun($api_key);
		$from = "7omegle <hello@{$domain}>";
		$subject = "欢迎来到 7omegle";
		$html = "<p>你好, {$username}</p>";
		$html .= "<p>欢迎来到 7omegle。</p>";
		$html .= "<p>7omegle 是一个与陌生人相互交流的地方, 注册并登录后, 
					每7天你可以与一位随机匹配到的会员相互交流, 
					7天之后你将可以再次匹配另一位会员。</p>";
		$html .= "<p>如果你有任何疑问, 可以回复 shispt18@gmail.com 进行交流。</p>";
		$html .= "<p>-7omegle 敬上</p>";
		$result = $mgClient->sendMessage($domain, array(
			'from' => $from,
			'to' => $to,
			'subject' => $subject,
			'html' => $html
		));
		return $result;
	}

	/**
	 * 发送或验证忘记密码邮件
	 */
	public function forgot_password(){

	}

	/**
	 * test
	 */
	public function test(){
		return 'User_model.test method';
	}
	
}


