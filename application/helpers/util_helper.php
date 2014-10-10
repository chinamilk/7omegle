<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 获取指定长度的随机字符串
 * 
 * @param integer $length 长度, 默认为32
 */
function get_random($length = 32){
	$source = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_';
	$random_arr = '';
	for ($i=0; $i < $length; $i++) { 
		$random_arr .= $source[mt_rand(0, strlen($source)-1)];
	}
	return $random_arr;
}

