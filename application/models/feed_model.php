<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feed_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}

	/**
	 * 获取某一用户的动态
	 * @param  integer $uid 用户uid
	 * @param  integer $page 获取的页数
	 * @param  integer $per_page 每页数量, 默认 30
	 */
	public function get_feeds($uid, $page, $per_page = 30){
		$page = intval($page);
		$per_page = intval($per_page);
		if($page <= 0){
			$page = 1;
		}
		if($per_page <= 0){
			$per_page = 30;
		}
		$this->load->model('user_model');
		$this->load->model('member_model');
		$current_friend = $this->member_model->get_current_friend($uid);
		if(!$current_friend){
			return False;
		}
		$user_friend_id = $current_friend['match_info']['id'];

		$start = ($page-1)*$per_page;
		$sql = "SELECT * FROM `feed` WHERE `user_friend_id`=? ORDER BY `time` DESC LIMIT ?,?";
		$feeds_object = $this->db->query($sql, array($user_friend_id, $start, $per_page));
		$feeds = $feeds_object->result_array();
		if(!$feeds){
			return False;
		}
		foreach ($feeds as $k => $v) {
			$feeds[$k]['user'] = $this->user_model->get_userinfo($v['user_id'], 'id');
			$feeds[$k]['content'] = htmlspecialchars($feeds[$k]['content']);
			if($feeds[$k]['image']){
				$feeds[$k]['image'] = "/application/upload/media/" . $feeds[$k]['image'];
			}
		}
		return $feeds;
	}

	/**
	 * 获取某一用户的动态总数
	 * @param  integer $uid 用户uid
	 */
	public function get_feed_count($uid){
		$this->load->model('member_model');
		$current_friend = $this->member_model->get_current_friend($uid);
		if(!$current_friend){
			return 0;
		}
		$sql = "SELECT count(*) as `count` FROM `feed` WHERE `user_friend_id`=?";
		$count_object = $this->db->query($sql, array($current_friend['match_info']['id']));
		$count_array = $count_object->row_array();
		$count = $count_array['count'];
		return $count;
	}

	/**
	 * 发布动态
	 */
	public function create_feed(){
		$this->load->model('user_model');
		$this->load->model('member_model');
		$userinfo = $this->user_model->check_login();

		if(!$userinfo){
			$data = array(
				'error' => -1,
				'msg' => '你还没有登录'
			);
			return $data;
		}
		$current_friend = $this->member_model->get_current_friend($userinfo['id']);
		if(!$current_friend){
			$data = array(
				'error' => -1,
				'msg' => '你还没有匹配好友'
			);
			return $data;
		}

		$user_friend_id = $current_friend['match_info']['id'];
		$feed_content = $this->input->post('feed-content');
		$feed_content = empty($feed_content) ? '' : $feed_content;
		// 如果既没有文字内容也没有图片, 则返回错误
		if(!$feed_content && !$_FILES['userfile']['name']){
			$data = array(
				'error' => -1,
				'msg' => '没有任何内容'
			);
			return $data;
		}
		$database = array(
			'user_id' => $userinfo['id'],
			'user_friend_id' => $user_friend_id,
			'content' => $feed_content,
			'image' => '',
			'time' => date('Y-m-d H:i:s')
		);
		// 如果有图片
		if(!empty($_FILES['userfile']['name'])){
			$config['upload_path'] = APPPATH . '/upload/media';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size'] = '2048';
			$config['encrypt_name'] = True;
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
				$upload_data = $this->upload->data();
				$database['image'] = $upload_data['file_name'];
			}
		}
		$result = $this->db->insert('feed', $database);
		$database['id'] = $this->db->insert_id();
		if(!$result){
			$data = array(
				'error' => -1,
				'msg' => '发布失败了，过一会再试试看'
			);
			return $data;
		}
		if($database['image']){
			$database['image'] = '/application/upload/media/' . $database['image'];
		}
		$return_data = array(
			'user' => $userinfo,
			'feed' => $database
		);
		$data = array(
			'error' => 0,
			'msg' => 'ok',
			'data' => $return_data
		);
		return $data;
	}

	/**
	 * 检查是否有动态更新
	 * @param  integer $min_feed_id 作为查找条件的最小 feed_id
	 */
	public function check_update($min_feed_id){
		$min_feed_id = intval($min_feed_id);
		$this->load->model('user_model');
		// $this->load->model('member_model');
		$userinfo = $this->user_model->check_login();
		if(!$userinfo){
			// $data = array('error' => -1);
			$data = array();
			return $data;
		}

		// 更新最后在线时间
		$this->load->model('settings_model');
		$this->settings_model->update_last_online_time($userinfo['id']);

		// 检查 `status` 状态
		// $this->user_model->check_status($userinfo['id']);

		// 如果 $min_feed_id == -1, 结束执行
		if($min_feed_id == -1){
			$data = array();
			return $data;
		}

		// 检查是否有动态更新
		$this->load->model('member_model');
		$current_friend = $this->member_model->get_current_friend($userinfo['id']);
		if(!$current_friend){
			$data = array('error' => -1);
			return $data;
		}
		// sql 语句中 `user_id`!=$userinfo['id'] 为防止用户自己发表内容后与请求更新的ajax冲突
		$sql = "SELECT * FROM `feed` WHERE `user_friend_id`=? AND `id`>? AND `user_id`!=? ORDER BY `time` DESC";
		$bind_param = array(
			$current_friend['match_info']['id'],
			$min_feed_id,
			$userinfo['id']
		);
		$feed_object = $this->db->query($sql, $bind_param);
		$feeds = $feed_object->result_array();
		$count = count($feeds);
		$data = array(
			'error' => 0,
			'feed_count' => $count,
			'feed_html' => '',
			'min_feed_id' => 0,
		);
		$html = '';
		if($count > 0){
			$data['min_feed_id'] = $feeds[0]['id'];
			foreach ($feeds as $k => $feed) {
				$feed_user = $this->user_model->get_userinfo($feed['user_id'], 'id');
				$feed['content'] = htmlspecialchars($feed['content']);
				if($feed['image']){
					$feed['image'] = "/application/upload/media/" . $feed['image'];
				}
				$html .= '<div class="well well-large" feed-id="' . $feed['id'] . '">';
				$html .= '<div class="cell">';
				$html .= '<table width="100%" cellspacing="0" cellpadding="0" border="0"';
				$html .= '<tbody><tr>';
				$html .= '<td width="50" valign="top" align="center">';
				$html .= '<img src="' . $feed_user['avatar_small'] . '" />';
				$html .= '</td>';
				$html .= '<td width="10" valign="top"></td>';
				$html .= '<td width="auto" valign="top" align="left">';
				$html .= '<a href="/member/' . $feed_user['username'] . '">' . $feed_user['username'] . '</a>';
				$html .= '<div class="sep5"></div>';
				$html .= '<span class="gray">' . $feed['time'] . '</span>';
				$html .= '</td></tr></tbody></table></div>';
				$html .= '<h5>' . $feed['content'] . '</h5>';
				if($feed['image']){
					$html .= '<a href="' . $feed['image'] . '" target="_blank">';
					$html .= '<img src="' . $feed['image'] . '" />';
					$html .= '</a>';
				}
				$html .= '</div>';
			}
		}
		$data['feed_html'] = $html;
		return $data;
	}
}