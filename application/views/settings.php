<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<?php
	include APPPATH.'views/fragment/settings_sidebar.php';
	?>
	<div class="span8 main pull-right">
		<div class="white_background">
			<?php if(!empty($error) && $error != 0){?>
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Warning!</h4>
				<?php echo $errorMsg; ?>
			</div>
			<?php } ?>
			
			<div class="header">
				个人资料设置
			</div>
			<div class="inner">
				<form action="/settings" method="post">
					<table width="100%" cellspacing="0" cellpadding="5" border="0">
						<tbody>
							<tr>
								<td width="120" align="right">用户名</td>
								<td width="auto" align="left">
									<?php echo $userinfo['username'];?>
								</td>
							</tr>
							<tr>
								<td width="120" align="right">当前状态</td>
								<td width="auto" align="left">
									<?php
									if($userinfo['status'] == 0){
										echo "空闲";
									} else if($userinfo['status'] == 1){
										echo "忙碌(正在与一位好友交流)";
									} else if($userinfo['status'] == 2){
										echo "拒绝任何人匹配";
									}
									?>
								</td>
							</tr>
							<tr>
								<td width="120" align="right">切换状态</td>
								<td width="auto" align="left">
									<?php
									if($userinfo['status'] != 2){
										// 当前不是'拒绝任何人匹配'状态，可以切换为'拒绝任何人匹配'状态
										if(!$current_friend){
											// 如果当前没有匹配的好友，则可以切换到拒绝任何人匹配状态
											echo '<a class="btn btn-small" href="/settings/status_refuse">';
											echo '拒绝任何人匹配到自己';
											echo '</a>';
										} else {
											echo '<button class="btn btn-small disabled" disabled>拒绝任何人匹配到自己</button>';
										}
									} else {
										// 当前为'拒绝任何人匹配'状态，可以切换为空闲状态
										echo '<a class="btn btn-small" href="/settings/status_free">切换为空闲状态</a>';
									}
									?>
								</td>
							</tr>
							<tr>
								<td width="120" align="right">电子邮件</td>
								<td width="auto" align="left">
									<?php echo $userinfo['email'];?>
								</td>
							</tr>
							<tr>
								<td width="120" align="right">性别</td>
								<td width="auto" align="left">
									<input type="text" name="gender" value="<?php echo $userinfo['gender'];?>" />
								</td>
							</tr>
							<tr>
								<td width="120" align="right">所在地</td>
								<td width="auto" align="left">
									<input type="text" name="locatoin" value="<?php echo $userinfo['location'];?>" />
								</td>
							</tr>
							<tr>
								<td width="120" align="right">签名</td>
								<td width="auto" align="left">
									<input type="text" name="tagline" value="<?php echo $userinfo['tagline'];?>" />
								</td>
							</tr>
							<tr>
								<td width="120" align="right">个人简介</td>
								<td width="auto" align="left">
									<textarea name="bio" cols="30" rows="10"><?php echo $userinfo['bio'];?></textarea>
								</td>
							</tr>
							<tr>
								<td width="120" align="right"></td>
								<td width="auto" align="left">
									<input class="btn" type="submit" value="保存设置" />
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include APPPATH.'views/templates/footer.php';?>
