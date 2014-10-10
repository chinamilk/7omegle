<?php
include APPPATH.'views/templates/header.php';
?>
<div class="row-fluid">
	<div class="span8">
		<div class="row">
			<div class="span10 offset1">
				<div class="inner">
					<h2>欢迎来到 7omegle。</h2>
					<p>每隔 7 天与一位随机匹配的陌生人进行交流。</p>
					<div class="sep40"></div>
					<p>Usually people only see what happened and said:"why?"</p>
					<p>But I dream of things we've never had before, and ask myself:"why not?"</p>
				</div>
			</div>
		</div>
	</div>
	<div class="span4">
		<div class="row-fluid">
			<div class="span12">
				<div class="white_background inner">
					<div class="front-signin">
						<form action="/login" method="post" class="form-horizontal">
							<div class="username field">
								<input type="text" name="username" id="username" required="required" placeholder="输入用户名" />
							</div>
							<div class="password field">
								<input type="password" name="password" id="password" required="required" placeholder="输入密码" />
							</div>
							<div>
								<input class="btn" type="submit" name="submit" class="btn btn-primary" value="登录" />
							</div>
						</form>
					</div><!--end front-signin-->
				</div><!--end inner-->
				<div class="sep20"></div>
				<div class="white_background inner">
					<div class="front-signup">
						<div class="cell">
							<h4><strong>新来 7omegle?</strong> 注册</h4>
						</div>
						<div class="sep10"></div>
						<!--/////////////////////////////////////-->
						<form action="/register" method="post" class="form-horizontal">
							<div class="field">
								<input type="text" name="username" id="username" required="required" placeholder="用户名" />
							</div>
							<div class="field">
								<input type="text" name="email" id="email" required="required" placeholder="邮箱" />
							</div>
							<div class="field">
								<input type="password" name="password" id="password" required="required" placeholder="密码" />
							</div>
							<div class="field">
								<input type="password" name="repassword" required="required" placeholder="确认密码" /><br/>
							</div>
							<div>
								<input class="btn" type="submit" name="submit" class="btn btn-primary" value="注册" />								
							</div>
						</form>
						<!--/////////////////////////////////////-->
					</div>
				</div>
			</div><!--end span12-->
		</div><!--end row-fluid-->
	</div><!--end span4-->
</div><!--end row-fluid-->
<div><?php include APPPATH.'views/templates/footer.php';?></div>
</body>
</html>