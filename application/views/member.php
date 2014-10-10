<?php
include APPPATH.'views/templates/header.php';
?>
<div id="content" class="row-fluid">
	<div class="span8 main pull-left">
		<div class="white_background inner">
			<?php 
			$m = $member_info;
			if(!$m){
			?>
				<p>该用户不存在</p>
			<?php } else {?>
			<div class="cell">
			<!-- <div> -->
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="100" valign="top" align="center">
							<img class="img-rounded" src="<?php echo $m['avatar_medium'];?>" />
						</td>
						<td width="10"></td>
						<td width="auto" valign="top" align="left">
							<div class="member-username"><?php echo $m['username'];?></div>
							<span class="bigger"><?php echo $m['tagline'];?></span>
							<div class="sep10"></div>
							<div class="gray">
								第 <?php echo $m['id'];?> 号会员，加入于 <?php echo $m['time'];?>
								<div class="sep10"></div>
								上次登录：<?php echo $m['last_login']['time'];?>
								<div class="sep10"></div>
								匹配状态：
								<?php
								if($m['status'] == 0){
									echo '空闲';
								} else if($m['status'] == 1){
									echo '正在与一位随机匹配的好友交流';
								} else if($m['status'] == 2){
									echo '暂时不允许任何人匹配到自己';
								}
								?>
								<div class="sep10"></div>
								性别：<?php echo $m['gender']?:'未设定';?>
								<div class="sep10"></div>
								所在地：<?php echo $m['location']?:'未设定';?>
							</div>
						</td>
					</tr>
				</table>
				<div class="sep5"></div>
			</div>
				<?php if($m['bio']){?>
			<div class="cell">
				<div class="sep5"></div>
				<?php echo nl2br(nl2br($m['bio']));?>
				<div class="sep5"></div>
			</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div><!---end span8-->
</div><!--end row-fluid-->
</div><!--end container-->
</div><!--end wrapper-->
<div class="sep20 e9e9e9-background"></div>
<div class="wrapper-no-background-image">
	<div class="container">
		<div class="row-fluid">
			<div class="span8 main pull-left">
				<div class="white_background inner">
					<div class="cell">
						<span class="gray">
							最近匹配的好友
						</span>
					</div>
					<?php
						if($m['friend_history']){ 
							foreach ($m['friend_history'] as $k => $match_friend) {
					?>
					<div class="cell">
						<a href="/member/<?php echo $match_friend['username'];?>">
							<img class="img-rounded" src="<?php echo $match_friend['avatar_small'];?>" />
						</a>
						<a href="/member/<?php echo $match_friend['username'];?>">
							<?php echo $match_friend['username'];?>
						</a>
						&nbsp;匹配时间：<?php echo $match_friend['match_info']['time'];?>
						<?php if($match_friend['match_info']['is_useless']){ ?>
						&nbsp;(已由一方申请解除好友关系)
						<?php }?>
					</div>
					<?php
							}
						} else { ?>
					<div class="cell">
						暂无
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	<!-- </div> --><!--end container-->
<!-- </div> --><!--end wrapper-->
<?php include APPPATH.'views/templates/footer.php';?>