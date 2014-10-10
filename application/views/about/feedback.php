<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<div class="span8 main pull-left">
		<div class="white_background inner">
			<?php if(!empty($result['msg'])){?>
				<?php if($result['error'] != 0){ ?>
					<div class="alert alert-error">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<h4>Warning!</h4>
						<?php echo $result['msg'];?>
					</div>
				<?php } else { ?>
					<div class="alert alert-success">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<!-- <h4>Success!</h4> -->
						<?php echo $result['msg']; ?>
					</div>
				<?php } ?>
			<?php } ?>
			<h4>反馈</h4>
			<hr/>
			<div>
				<form action="/feedback" method="post">
					<table>
						<tbody>
							<tr>
								<td align="left">
									联系方式：
								</td>
								<td width="auto">
									<input type="text" name="feedback-contact" required="required" placeholder="QQ 或 Email"><br/>
								</td>
							</tr>
							<tr>
								<td align="left">
									反馈内容：									
								</td>
								<td width="auto">
									<textarea name="feedback-content" cols="30" rows="10" required="required"></textarea>		
								</td>
							</tr>
							<tr>
								<td align="left"></td>
								<td width="auto">
									<input type="submit" class="btn" value="提交" />									
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include APPPATH.'views/templates/footer.php';?>