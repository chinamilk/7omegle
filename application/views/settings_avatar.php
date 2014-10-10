<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<?php
	include APPPATH.'views/fragment/settings_sidebar.php';
	?>
	<div class="span8 main pull-right">
		<div class="white_background">
			<?php if(!empty($errorMsg)){?>
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Warning!</h4>
				<?php echo $errorMsg;?>
			</div>
			<div class="sep10"></div>
			<?php } ?>
			<div class="header">
				头像上传
			</div>
			<form action="/settings/avatar" method="post" enctype="multipart/form-data">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
				<tbody>
					<tr>
						<td width="120" align="right">当前头像</td>
						<td width="auto" align="left">
							<img class="img-rounded" align="default" border="0" src="<?php echo $userinfo['avatar_medium'];?>"  />
							<img class="img-rounded" align="default" border="0" src="<?php echo $userinfo['avatar_small'];?>"  />
						</td>
					</tr>
					<tr>
						<td width="120" align="right">选择一个图片文件</td>
						<td width="auto" align="left">
							<input type="file" name="userfile" />
						</td>
					</tr>
					<tr>
						<td width="120" align="right"></td>
						<td width="auto" align="left">
							支持 2MB 以内的 PNG / JPG / GIF 文件
						</td>
					</tr>
					<tr>
						<td width="120" align="right"></td>
						<td width="auto" align="left">
							<input class="btn" type="submit" value="开始上传" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
		</div>
	</div>
</div>
<?php include APPPATH.'views/templates/footer.php';?>