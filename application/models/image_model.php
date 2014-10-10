<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require VENDORPATH . 'autoload.php';
use Imagine\Image\Box;
class Image_model extends CI_Model{

	/**
	 * 调整用户头像大小, 保存不同大小的头像文件
	 * 共保存两张: 一张在个人主页显示(medium), 一张在动态中显示(small)
	 * 
	 * @param string $filepath 图片路径, 绝对路径
	 * @param integer $uid 用户id
	 */
	public function thumbnail_avatar($filepath, $uid){
		// 个人主页显示的头像大小
		$medium['width'] = 100;
		$medium['height'] = 100;
		// 个人动态中显示的头像大小
		$small['width'] = 50;
		$small['height'] = 50;

		$imgfile = pathinfo($filepath);
		// 头像文件名
		$avatar_medium = $uid . '_medium.' . $imgfile['extension'];
		$avatar_small = $uid . '_small.' . $imgfile['extension'];
		// 保存后的头像完整路径
		$final_avatar_medium = APPPATH . 'upload/avatar/' . $avatar_medium;
		$final_avatar_small = APPPATH.'upload/avatar/'.$avatar_small;

		try{
			$imagine = new Imagine\Gd\Imagine();
			$image = $imagine->open($filepath);
			// $image->resize(new Imagine\Image\Box(100, 300))
			// medium 个人主页头像
			$image->thumbnail(new Imagine\Image\Box($medium['width'], $medium['height']), Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND)
				// ->rotate(45)
			// ->crop(new Imagine\Image\Point(0, 0), new Imagine\Image\Box(50, 150))
			// 保存的图片名按用户 uid 区分
				->save($final_avatar_medium);

			// small 动态中的头像
			$image->thumbnail(new Imagine\Image\Box($small['width'], $small['height']), Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND)
				->save($final_avatar_small);
		} catch(exception $e){
			echo 'ERROR: ', $e->getMessage();
			die();
		}

		if(file_exists($final_avatar_medium) && file_exists($final_avatar_small)){
			return array('avatar_medium' => $avatar_medium, 'avatar_small' => $avatar_small);
		}
		else{
			return False;
		}
	}

	/**
	 * 用户与好友交流时的图片
	 * 或者不调整大小，直接使用原图，用 css 调整显示大小
	 */
	public function media(){}

	public function test(){
		return 'test';
	}
}