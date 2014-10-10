<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<div class="span8 main pull-left">
		<div class="white_background inner">
			<div class="members">
				<table>
					<tbody>
					<tr>
						<?php
						for ($i=0; $i < 28; $i++) { 
							if(!empty($members[$i])){
								$m = $members[$i];
								?>
								<td class="span3 user-info">
										<div class="user-avatar">
											<a href="/member/<?php echo $m['username'];?>">
												<img class="img-rounded" src="<?php echo $m['avatar_medium'];?>" />
											</a>
										</div>
										<div class="user-detail">
											<a href="/member/<?php echo $m['username'];?>"><?php echo $m['username'];?></a>
											<br/>
											<!-- <div class="sep5"></div> -->
											<span class="user-tagline"><?php echo $m['tagline'];?></span>
										</div>
								</td>
								<?php
								if(($i+1)%4 == 0){
									echo '</tr><tr>';
								}
							}
						}
						?>
						<td class="span3"></td>
						<td class="span3"></td>
						<td class="span3"></td>
						<td class="span3"></td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="sep20"></div>
			<div class="page">
				<ul class="pager">
					<?php
					if($previous_page){
					?>
					<li class="previous"><a href="/members?p=<?php echo $previous_page;?>">前一页</a></li>
					<?php
					} else {
					?>
					<li class="previous disabled"><span>前一页</span></li>
					<?php 
					}
					if($next_page){
					?>
					<li class="next"><a href="/members?p=<?php echo $next_page;?>">后一页</a></li>
					<?php
					} else {
					?>
					<li class="next disabled"><span>后一页</span></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<?php
	if(!$userinfo){
		include APPPATH.'views/fragment/login_register_sidebar.php';		
	} else {
		include APPPATH.'views/fragment/userinfo_sidebar.php';
	}
	?>
</div>
<?php include APPPATH.'views/templates/footer.php';?>