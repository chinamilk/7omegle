<?php
include APPPATH.'views/templates/header.php';
?>
<div id="content" class="row-fluid">
	<div class="span8 main pull-left">
		<div class="white_background inner">
			<?php
			if($error){
			?>
				<div>
					<p><?php echo $errorMsg;?></p>
				</div>
			<?php
			} else {
			?>
				<div>
					<div class="cell">
						为你匹配到的好友
					</div>
					<div>
						<a href="/member/<?php echo $match_friend['username'];?>">
							<img class="img-rounded" src="<?php echo $match_friend['avatar_small'];?>" />
						</a>
						<a href="/member/<?php echo $match_friend['username'];?>">
							<?php echo $match_friend['username'];?>
						</a>
					</div>
				</div>
			<?php
			}
			?>
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