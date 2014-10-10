<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title><?php echo $title?:'7omegle';?></title>
<script src="/application/static/js/jquery/jquery-1.8.3.js"></script>
<link href="/application/static/css/bootstrap.min.css" rel="stylesheet" media="screen" />
<link rel="stylesheet" href="/application/static/css/shared.css" />
<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<!-- 响应式 -->
<meta name="viewpoint" content="width=device-width, initial-scale=1.0" />
<link href="/application/static/css/bootstrap-responsive.min.css" real="stylesheet" />
<!--  -->
<script src="/application/static/js/bootstrap.min.js"></script>
<script src="/application/static/js/main.js" type="text/javascript"></script>
<body>
<div class="sep40"></div>
<div class="wrapper">
	<div class="sep20"></div>
	<div class="container">
		<!-- <div class="navbar navbar-inverse navbar-fixed-top"> -->
		<div class="navbar navbar-fixed-top">
			<div class="nav-collapse navbar-inner" style="height: auto;">
				<!-- <div class="container-fluid"> -->
					<!-- <a href="#" class="brand">wall series</a> -->
					<ul class="nav pull-left nav-pills" style="left:15%;">
						<li><a href="/">首页</a></li>
						<!-- <li><a href="/explore">发现</a></li> -->
						<li><a href="/members">用户</a></li>
					</ul>
					<ul class="nav pull-right nav-pills" style="left:-15%;">
						<?php
							if(!empty($userinfo)){
								if($userinfo['is_admin']){
									echo '<li><a href="/admin" target="_blank">后台管理</a></li>';
								}
								echo '<li><a href="/member/'.$userinfo['username'].'">', $userinfo['username'], '</a></li>';
								echo '<li><a href="/settings">设置</a></li>';
								echo '<li><a href="/logout">退出</a></li>';
							} else {
								echo '<li><a href="/register">注册</a></li>';
								echo '<li><a href="/login">登录</a></li>';
							}
						?>
					</ul>
				<!-- </div> -->
			</div>
		</div>