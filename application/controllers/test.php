<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Test extends CI_Controller{
	public function index(){
		// $this->load->model('image_model');
		// $source = APPPATH . 'upload/avatar/facebook.jpg';
		// $result = $this->image_model->thumbnail_avatar($source, 'facebook');
		echo '<pre>';
		print_r($result);
		echo '</pre>';
	}
}