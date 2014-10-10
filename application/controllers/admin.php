<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// require VENDORPATH . 'autoload.php';
class Admin extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('admin/admin_user_model');
		$is_admin = $this->admin_user_model->check_admin();
		if(!$is_admin){
			header('Location: /');
		}
	}

	public function index(){
		echo 'admin controller -> index()';
	}
}
