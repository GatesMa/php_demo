<?php
	if(!empty($_POST)){
		if(empty($_POST['account'])){
			echo '<script>window.alert("请输入账号");history.back();</script>';
	    	return false;
		}
		if(empty($_POST['password'])){
			echo '<script>window.alert("请输入密码");history.back();</script>';
	    	return false;
		}
		$pdo = new PDO('mysql:host=localhost;dbname=boke', 'root' , 'root' , array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
		$stmt = $pdo->prepare('select * from admin WHERE `account`="'.$_POST['account'].'"');
	    $stmt->execute();
	    $admin = $stmt->fetchAll();
	    if(empty($admin)){
	    	echo '<script>window.alert("账号不存在");history.back();</script>';
	    	return false;
	    }
	    $find = $admin[0];
	    if($find['password'] != md5($_POST['password'])){
	    	echo '<script>window.alert("密码不正确");history.back();</script>';
	    	return false;
	    }

	    setcookie('id',$find['id']);
		setcookie('account',$find['account']);
	    echo '<script>window.alert("登录成功");window.location.href="article.php";</script>';
	    return false;
    }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<title>登陆</title>
		<link rel="stylesheet" href="../static/layui/css/layui.css" />
		<link rel="stylesheet" href="../static/css/globals.css" charset="utf-8" />
		<link rel="stylesheet" href="../static/css/global.css" charset="utf-8" />
	</head>
	<body>
		<div class="layui-header header header-user" style="background-color:#24262F;"> 
			<div class="layui-container">
			<a class="logo" href="/">
				<img src="https://www.php.cn/static/images/logo.png" alt="layui" />
			</a>
			<div class="layui-form component"></div>
				
			</div>
		</div>
		<div class="layui-container fly-marginTop">
			<div class="fly-panel fly-panel-user" pad20="">
				<div class="layui-tab layui-tab-brief" lay-filter="user">
					<ul class="layui-tab-title">
						<li class="layui-this">登入</li>
					</ul>
					<div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
						<div class="layui-tab-item layui-show">
							<div class="layui-form layui-form-pane">
								<form method="post" action="">
								  <div class="layui-form-item">
								    <label for="L_loginName" class="layui-form-label">账号</label>
								    <div class="layui-input-inline">
								      <input type="text" id="L_loginName" name="account"  autocomplete="off" class="layui-input">
								    </div>
								    <div class="layui-form-mid layui-word-aux">请输入账号</div>
								  </div>
								  <div class="layui-form-item">
								    <label for="L_pass" class="layui-form-label">密码</label>
								    <div class="layui-input-inline">
								      <input type="password" id="L_pass" name="password"  autocomplete="off" class="layui-input">
								    </div>
								    <div class="layui-form-mid layui-word-aux">请输入密码</div>
								  </div>
								  <div class="layui-form-item">
								    <button class="layui-btn" lay-filter="*" lay-submit="">立即登录</button>
								  </div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="/static/layui/layui.js"></script>
		<script type="text/javascript">
			layui.use(['laypage','layer'],function(){
				layer = layui.layer;
			})
		</script>
	</body>
</html>