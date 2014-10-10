<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<div class="span8 main pull-left">
		<div class="white_background inner">
			<?php if(!empty($errorMsg)){?>
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4>Warning!</h4>
				<?php echo $errorMsg;?>
			</div>
			<?php } ?>
			<form action="/register" method="post" class="form-horizontal">
				<fieldset>
					<legend>注册</legend>
					<div class="control-group">
						<label for="username" class="control-label">用户名</label>
						<div class="controls">
							<input type="text" name="username" id="username" required="required" placeholder="用户名" />
						</div>
					</div>
					<div class="control-group">
						<label for="email" class="control-label">邮箱</label>
						<div class="controls">
							<input type="text" name="email" id="email" required="required" placeholder="邮箱" />
						</div>
					</div>
					<div class="control-group">
						<label for="password" class="control-label">密码</label>
						<div class="controls">
							<input type="password" name="password" id="password" required="required" placeholder="密码" />							
						</div>
					</div>
					<div class="control-group">
						<label for="repassword" class="control-label">确认密码</label>
						<div class="controls">
							<input type="password" name="repassword" required="required" placeholder="确认密码" /><br/>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="submit" name="submit" class="btn btn-primary" value="注册" />				
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	<?php
	include APPPATH.'views/fragment/login_register_sidebar.php';
	?>
</div>
<?php include APPPATH.'views/templates/footer.php';?>

