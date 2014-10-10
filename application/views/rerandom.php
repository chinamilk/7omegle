<?php
include APPPATH.'views/templates/header.php';
?>
<div id="content" class="row-fluid">
	<div class="span8 main pull-left">
		<div class="white_background inner">
				<div>
					<p><?php echo $errorMsg;?></p>
				</div>
		</div>
	</div>
	<?php
	if(!$userinfo){
		include APPPATH.'views/fragment/login_register_sidebar.php';		
	} else {
		// include APPPATH.'views/fragment/userinfo_sidebar.php';
	}
	?>
</div>
<?php include APPPATH.'views/templates/footer.php';?>