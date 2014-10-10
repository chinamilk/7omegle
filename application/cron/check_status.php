<?php
#!/usr/bin/php -q
if(PHP_SAPI !== 'cli'){
	exit();
}
define('BASEPATH', 1);
date_default_timezone_set('Asia/Shanghai');
include '/home/shispt/public/7omegle.com/public/application/config/database.php';

$running = get_running_status();
if($running){
	exit;
}
// 设置运行状态为正在运行
set_running_status(True);

$con = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
if(!$con){
	file_put_contents('/home/shispt/public/7omegle.com/public/application/cron/error.txt', 'connect error');
	exit;
}
$sql = "SELECT * FROM `profile` ORDER BY RAND() LIMIT 100";
$query = mysqli_query($con, $sql);
file_put_contents('/home/shispt/public/7omegle.com/public/application/cron/log.txt', '');
while ($row = mysqli_fetch_assoc($query)) {
	$log_content = file_get_contents('/home/shispt/public/7omegle.com/public/application/cron/log.txt');
	$log_content .= ' | ' . $row['user_id'];
	file_put_contents('/home/shispt/public/7omegle.com/public/application/cron/log.txt', $log_content);
	// 获取好友
	$sql = "SELECT * FROM `user_friend` WHERE `user_id`={$row['user_id']} OR `friend_id`={$row['user_id']} ORDER BY `time` DESC LIMIT 1";
	$match_query = mysqli_query($con, $sql);
	$match_info = mysqli_fetch_assoc($match_query);
	if(!$match_info){
		continue;
	}
	// 检查状态是否正确
	$diff_time = time() - strtotime($match_info['time']);
	if($match_info['is_useless'] || $diff_time > 7*60*60*24){
		// 如果好友已失效
		// 修复状态
		if($row['status'] == 1){
			$sql = "UPDATE `profile` SET `status`=0 WHERE `user_id`={$row['user_id']}";
			$update_query = mysqli_query($con, $sql);
		}
	} else {
		// 如果好友未失效
		// 修复状态
		if($row['status'] == 0 || $row['status'] == 2){
			$sql = "UPDATE `profile` SET `status`=1 WHERE `user_id`={$row['user_id']}";
			$update_query = mysqli_query($con, $sql);
		}
	}
}

// 设置运行状态为没有运行
set_running_status(False);

/**
 * 获取运行状态
 * @return True or False
 */
function get_running_status(){
	$lock_file = '/home/shispt/public/7omegle.com/public/application/cron/running.txt';
	if(!file_exists($lock_file)){
		file_put_contents($lock_file, 0);
	}
	$running = file_get_contents($lock_file);
	if($running == 1){
		return True;
	} else if($running == 0){
		return False;
	} else{
		return False;
	}
}

/**
 * 设置运行状态
 * @param  boolean $status True or False
 */
function set_running_status($status){
	$lock_file = '/home/shispt/public/7omegle.com/public/application/cron/running.txt';
	if(!file_exists($lock_file)){
		file_put_contents($lock_file, 0);
	}
	if($status){
		file_put_contents($lock_file, 1);
	} else {
		file_put_contents($lock_file, 0);
	}
	return file_get_contents($lock_file);
}
