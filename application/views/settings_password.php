<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<?php
	include APPPATH.'views/fragment/settings_sidebar.php';
	?>
	<div class="span8 main pull-right">
		<?php
			if(!empty($errorMsg) && $error == 0){
				echo '<div class="white_background">';
				echo "<div class='header'>修改密码</div>";
				echo "<div class='inner'>修改密码成功</div>";
				echo '</div>';
			} else {
		?>
				<div class="white_background">
					<?php if(!empty($error) && $error != 0){?>
					<div class="alert alert-error">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<h4>Warning!</h4>
						<?php echo $errorMsg; ?>
					</div>
					<?php } ?>

					<div class="header">
						修改密码
					</div>
					<div class="inner">
						<form action="/settings/password" method="post">
							<table width="100%" cellspacing="0" cellpadding="5" border="0">
								<tbody>
									<tr>
										<td width="120" align="right">当前密码</td>
										<td width="auto" align="left">
											<input type="password" name="password_current" value="" />
										</td>
									</tr>
									<tr>
										<td width="120" align="right">新密码</td>
										<td width="auto" align="left">
											<input type="password" name="password_new" value="" />
										</td>
									</tr>
									<tr>
										<td width="120" align="right">再次输入密码</td>
										<td width="auto" align="left">
											<input type="password" name="password_confirm" value="" />
										</td>
									</tr>
									<tr>
										<td width="120" align="right"></td>
										<td width="auto" align="left">
											<input class="btn" type="submit" value="更改密码" />
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>
				</div>
			<?php
			}
			?>
	</div>
</div>
<?php include APPPATH.'views/templates/footer.php';?>