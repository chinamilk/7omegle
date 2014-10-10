<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require VENDORPATH . 'autoload.php';
class Admin_user extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('admin/admin_user_model');
		$is_admin = $this->admin_user_model->check_admin();
		if(!$is_admin){
			header('Location: /');
		}
	}

	public function index(){
		$this->load->model('admin/admin_user_model');
		$result = $this->admin_user_model->check_admin();
	}

	/**
	 * 锁定用户
	 * @param  mixed $user_data 锁定的用户信息, 'username' 或 'user id'
	 */
	public function block($user_data){
		echo "this is block function, user_data: {$user_data}";
	}

}