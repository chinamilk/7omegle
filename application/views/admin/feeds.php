<?php
include APPPATH.'views/admin/header.php';
?>
<div class="row-fluid">
	<?php
	include APPPATH.'views/admin/fragment/side_menu.php';
	?>
	<div class="span8 main pull-right">
		<div class="feeds">
			<div class="well well-small new-feeds-bar-container">
			</div>
			<div id="feed-container">
				<?php 
				if($feeds){
					foreach ($feeds as $k => $feed) {
				?>
						<div class="well well-large" feed-id="<?php echo $feed['id']?>">
							<div class="cell">
								<table width="100%" cellspacing="0" cellpadding="0" border="0">
									<tbody>
										<tr>
											<td width="50" valign="top" align="center">
												<img src="<?php echo $feed['user']['avatar_small'];?>" />
											</td>
											<td width="10" valign="top"></td>
											<td width="auto" valign="top" align="left">
												<a href="/admin/feeds/user/<?php echo $feed['user']['username'];?>">
													<?php echo $feed['user']['username'];?>
												</a>
												<div class="sep5"></div>
												<span class="gray"><?php echo $feed['time'];?></span>
												<div class="sep5"></div>
												<span class="gray"><a href="/admin/feeds/match/<?php echo $feed['user_friend_id'];?>">匹配id: <?php echo $feed['user_friend_id'];?></a></span>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<h5><?php echo $feed['content'];?></h5>
							<?php if($feed['image']){ ?>
								<a href="<?php echo $feed['image'];?>" target="_blank">
									<img src="<?php echo $feed['image'];?>" />
								</a>
							<?php } ?>
						</div>
				<?php 
					} 
				}?>
			</div>
			<div class="sep20"></div>
				<!-- 页码 -->
				<div class="page">
					<ul class="pager">
					<?php
					if($previous_page){
					?>
					<li class="previous"><a href="/admin/feeds?p=<?php echo $previous_page;?>">前一页</a></li>
					<?php
					} else {
					?>
					<li class="previous disabled"><span>前一页</span></li>
					<?php } ?>
					<li><?php echo $current_page,'/',$total_page;?></li>
					<?php
					if($next_page){
					?>
					<li class="next"><a href="/admin/feeds?p=<?php echo $next_page;?>">后一页</a></li>
					<?php
					} else {
					?>
					<li class="next disabled"><span>后一页</span></li>
					<?php } ?>
					</ul>
				</div>
		</div>
	</div>
</div>
<?php include APPPATH.'views/templates/footer.php';?>