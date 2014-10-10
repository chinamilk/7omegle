<div class="span4 sidebar pull-left">
	<div class="white_background inner">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr>
					<td width="100" valign="top">
						<a href="/member/<?php echo $userinfo['username'];?>">
							<img class="img-rounded" src="<?php echo $userinfo['avatar_medium'];?>" />
						</a>
					</td>
					<td width="10" valign="top"></td>
					<td width="auto" align="left">
						<a href="/member/<?php echo $userinfo['username'];?>"><?php echo $userinfo['username'];?></a>
						<?php
							if($userinfo['status'] == 0){
								echo '(还未匹配好友)';
							} else if($userinfo['status'] == 1){
								echo '(已匹配一位好友)';
							}
						?>
						<div class="sep5"></div>
						<span class="user-tagline"><?php echo $userinfo['tagline'];?></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>