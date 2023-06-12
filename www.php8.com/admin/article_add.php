<?php
	$pdo = new PDO('mysql:host=localhost;dbname=boke', 'root' , 'root' , array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
	if(!empty($_POST)){
		$title = $_POST['title'];
		if(empty($title)){
			echo json_encode(['code'=>1,'msg'=>'请输入标题']);
			exit;
		}
		$content = $_POST['content'];
		if(empty($content)){
			echo json_encode(['code'=>1,'msg'=>'请输入内容']);
			exit;
		}
		$class = $_POST['class'];
		if(empty($class)){
			echo json_encode(['code'=>1,'msg'=>'请选择分类']);
			exit;
		}
		$img = $_POST['img'];
		$date = date('Y-m-d');
		try{
			$sql = "INSERT INTO article (`title`,`img`,`content`,`date`,`class_id`)VALUES('{$title}','{$img}','{$content}','{$date}','{$class}');";
		
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			echo json_encode(['code'=>0,'msg'=>'添加成功']);
		}catch (PDOException $e){
			echo json_encode(['code'=>1,'msg'=>'添加失败']);
		}
		return false;
	}else{
		$stmt = $pdo->prepare('select * from class ORDER BY sort DESC');
	    $stmt->execute();
	    $menu = $stmt->fetchAll();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<title>文章添加</title>
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
			<div class="layui-form component" lay-filter="LAY-site-header-component"></div> 
			<ul class="layui-nav">
				<li class="layui-nav-item fly-layui-user">
					<a class="fly-nav-avatar">
						<cite class="layui-hide-xs">欧阳克</cite>
					</a>
				</li>
				<li class="layui-nav-item fly-layui-user">
					<a class="fly-nav-avatar">
						<cite class="layui-hide-xs">退出</cite>
					</a>
				</li>
			</ul> 
			</div>
		</div>

		<div class="layui-container fly-marginTop fly-user-main"> 
			<ul class="layui-nav layui-nav-tree layui-inline" style="padding:20px 0;">
				<li class="layui-nav-item layui-this">
					<a href="/admin/article.html">
						<i class="layui-icon layui-icon-table"></i>文章列表
					</a>
				</li>
				<li class="layui-nav-item">
					<a href="/admin/article_cat.html">
						<i class="layui-icon layui-icon-tabs"></i>文章分类
					</a>
				</li>
			</ul>
			<div class="site-mobile-shade"></div>
			<div class="fly-panel fly-panel-user" pad20="">
				<div class="layui-tab layui-tab-brief">
					<ul class="layui-tab-title">
						<li data-type="mine-jie">文章列表</li>
						<li data-type="mine-jie" class="layui-this">添加文章</li>
					</ul>
					<div class="layui-tab-content" style="padding:5px 0;margin-top:10px;">
						<div class="layui-tab-item layui-show">

							<form class="layui-form">
							    <div class="layui-form-item">
							        <label class="layui-form-label">文章标题</label>
							        <div class="layui-input-block">
							            <input type="text" class="layui-input" name="title" placeholder="请输入文章标题">
							        </div>
							    </div>
							    <div class="layui-form-item">
								    <label class="layui-form-label">图片</label>
								    <div class="layui-input-block layui-upload">
								        <button type="button" class="layui-btn" id="test1">上传图片</button>
								        <div class="layui-upload-list">
								            <img class="layui-upload-img" id="demo1" style="width:100px;">
								            <!-- 增加隐藏`input`框，存储上传后的图片地址 -->
    										<input type="hidden" class="layui-input" id="img" name="img">
								        </div>
								    </div>
								</div>
							    <div class="layui-form-item">
							        <label class="layui-form-label">详情</label>
							        <div class="layui-input-block">
							            <textarea class="layui-textarea" name="content" placeholder="请输入详情"></textarea>
							        </div>
							    </div>
							    <div class="layui-form-item">
							        <label class="layui-form-label">分类</label>
							        <div class="layui-input-block">
							            <select name="class" lay-verify="required" lay-search="">
							                <option value="">请选择分类</option>
							                <?php
							                	foreach($menu as $menu_v){
							                ?>
							                	<option value="<?php echo $menu_v['id'] ?>"><?php echo $menu_v['name'] ?></option>
							                <?php
							                	}
							                ?>
							                
							            </select>
							        </div>
							    </div>
							</form>
							<div class="layui-input-block">
								<button type="button" class="layui-btn" onclick="save()">保存</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="/static/layui/layui.js"></script>
		<script type="text/javascript">
			layui.use(['layer','form','upload'],function(){
			    form = layui.form;
			    layer = layui.layer;
			    upload = layui.upload;
			    $ = layui.jquery;

			    var uploadInst = upload.render({
			        elem: '#test1'
			        ,url: 'upload.php'
			        ,before: function(obj){
			            obj.preview(function(index, file, result){
			                $('#demo1').attr('src', result);
			            });
			        }
			        ,done: function(res){
			            if(res.code > 0){
			                layer.msg('上传失败',{'icon':2});
			            }else{
			            	$('#img').val(res.data);
			                layer.msg('上传成功',{'icon':1});
			            }
			        }
			    });
			});
			function save(){
				$.post('/admin/article_add.php',$('form').serialize(),function(res){
					if(res.code>0){
						layer.msg(res.msg,{'icon':2});
					}else{
						layer.msg(res.msg,{'icon':1});
						window.location.href='/admin/article.php';
					}
				},'json');
			}
		</script>
	</body>
</html>