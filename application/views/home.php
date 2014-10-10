<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<?php include APPPATH.'views/fragment/userinfo_sidebar.php';?>
	<div class="span8 main">
		<div class="white_background inner">
			<?php if(!empty($current_friend)){ ?>
				<div class="clearfix">
					<div class="pull-left">
						当前好友：
						<img class="img-rounded" src="<?php echo $current_friend['avatar_small'];?>" />
						<a href="/member/<?php echo $current_friend['username'];?>"><?php echo $current_friend['username'];?></a>
						<span class="gray smaller">(将于 
							<?php 
							$expire_time = strtotime($current_friend['match_info']['time']) + 7*60*60*24;
							$expire_date = date('Y-m-d H:i:s', $expire_time);
							echo $expire_date;
							?>
							 过期)
						</span>
					</div>
					<p class="pull-right"><button class="btn disabled" disabled>随机匹配一位好友</button></a></p>
					<?php if($rerandom_permission){ ?>
					<p class="pull-right">
						<a href="/rerandom">
							<button class="btn" title="如果对方在最初 2 天未发布任何动态则可以申请重新分配好友">
								申请重新分配好友
							</button>
						</a>
					</p>
					<?php } else { ?>
					<p class="pull-right">
						<button class="btn disabled" disabled  title="如果对方在最初 2 天未发布任何动态则可以申请重新分配好友">
							申请重新分配好友
						</button>
					</p>
					<?php } ?>
				</div>
			<?php } else {?>
				<div class="clearfix">
					<div class="pull-left">
						还没有匹配好友，点击为你随机匹配一位好友，并且相互交流。
					</div>
					<p class="pull-right"><a href="/random"><button class="btn">随机匹配一位好友</button></a></p>
				</div>
			<?php } ?>
		</div>
		<div class="sep20"></div>
		<!-- 动态 -->
		<div class="white_background inner">
			<div class="cell">动态</div>
			<div class="sep20"></div>
			<?php
			if(!$current_friend){
				echo '<p>还没有匹配好友。</p><p>匹配好友后就可以互相发布动态，并相互交流。</p>';
			} else {
			?>
				<!-- 动态 -->
				<div class="feeds">
					<div class="clearfix">
						<form action="/i/feed/create" method="post" enctype="multipart/form-data">
							<textarea name="feed-content" id="feed-content" rows="3" style="width:98%;resize:none;"></textarea><br/>
							<input type="file" name="userfile" style="display:none;" id="feed-image" />
							<div class="pull-right">
								<span id="add-image"><i class="icon-plus"></i> 添加图片</span>
								<span id="remove-image" style="display:none;"><i class="icon-remove"></i> 取消图片</span>
								<button class="btn btn-primary" id="feed-create-submit">发布</button>
							</div>
						</form>
					</div>
					<div class="sep20"></div>
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
														<a href="/member/<?php echo $feed['user']['username'];?>">
															<?php echo $feed['user']['username'];?>
														</a>
														<div class="sep5"></div>
														<span class="gray"><?php echo $feed['time'];?></span>
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
				</div>
				<div class="sep20"></div>
				<!-- 页码 -->
				<div class="page">
					<ul class="pager">
					<?php
					if($previous_page){
					?>
					<li class="previous"><a href="/?p=<?php echo $previous_page;?>">前一页</a></li>
					<?php
					} else {
					?>
					<li class="previous disabled"><span>前一页</span></li>
					<?php } ?>
					<li><?php echo $current_page,'/',$total_page;?></li>
					<?php
					if($next_page){
					?>
					<li class="next"><a href="/?p=<?php echo $next_page;?>">后一页</a></li>
					<?php
					} else {
					?>
					<li class="next disabled"><span>后一页</span></li>
					<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php include APPPATH.'views/templates/footer.php';?>
