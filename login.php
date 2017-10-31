<?php
session_start();
require_once('settings.php');//存入各項初始變數
require_once("inc/root.php");
include ('recaptchalib.php');
$mod	= get('mod');
?>
<script language="javascript">
	function roll(){
		var myDate=new Date();
		document.getElementById("dice").src="dice.php?cache=" + myDate.getTime();
	}
</script>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>亞典請假系統</title>
<link rel="stylesheet"  href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/style.css" >
<style type="text/css">
body
{
font-family:'微軟正黑體';
}
div
{
 text-align:center;line-height:100px,
}
input
{
 text-align:center;line-height:100px,
}
</style>
		<?php
	switch ($mod)
	{		
		case "mem_add":
		
		//-------------------------------------------------------------------------------------------------------------	//
		//	會員新增																										//
		//-------------------------------------------------------------------------------------------------------------	//
		?>
		<div class="container">
			<div class="jumbotron">
				<h2>亞典請假系統</h2>		
			</div>
			<div class="status alert alert-danger" style="display: none"></div> 
			<form id="myform" class="form" name="mem_add" action="edit.php" ct="mem_add" method="post">	 
				<h2>所屬公司部門</h2>
		<?php
				$sql = "SELECT * FROM member where authority!='n' && comp_name!='ad'";
				conn();							
				$res = $conn->query($sql);	
				$list_mem = $res->fetchAll();
				cls_conn();
				foreach($list_mem as $row)	
				{				
				$downlist[]=$row['comp_name'];
				}
				$downlist=array_unique($downlist);
		?>
			<select name="comp_name"  id='comp_name'  style="width:20%;">
		<?php
				foreach($downlist as $row)	
				{				
		?>
				<option value=<?=$row;?>><?=$row;?></option>
		<?php
				}
				?>　
				</select>	
				<div class="status alert alert-danger" style="display: none"></div> 
				<h2>個人資料</h2>			
                    <div class="form-group">
                    <input type="text" class="form-control" name='mnm' placeholder="姓名*" required/>
                    </div>
                    <div class="form-group">
                    <input type="text" class="form-control" id='mid' name='mid' placeholder="登入帳號*" onblur="chk_mem('mid');" required />
                    </div>
                    <div class="form-group">
                    <input type="password" class="form-control" id='pwd' name='pwd' placeholder="登入密碼*" required />
                    </div>
                    <div class="form-group">
                    <input type="password" class="form-control" id='pwd2' name='pwd2' placeholder="確認密碼*" onblur="chk_pwd();" required />
                    </div>
                    <div class="form-group">
                    <input type="mail" class="form-control" id='mail' name='mail' placeholder="電郵" onblur="chk_mail();" />ps.如填入google信箱會連動google日曆，可加入閱讀權限。
                    </div>
                    <div class="form-group">
                    <input type="tel" class="form-control" id='tel' name='tel' placeholder="電話" required />
                    </div>
                                            <!--google 驗證碼
                        <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>" data-theme="dark"></div>
                        <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>" async defer></script>
                        <br/>
                        -->
                        <!--離線用 骰子驗證碼-->
                        <div style="padding:7px">
                            <div class="dice-div">
                            <img  id="dice" alt="請點擊右圖，產生驗證圖" />
                            <img src="dice.php?but=1" border=0 onclick="roll()" style="cursor:pointer" alt="Roll dice again" title="Roll dice again">
                            </div>
                            <div class="text">
                            請輸入骰子值的總和: <input type="text" name="captcha" class="captcha">
                            </div>
                        </div>
				<button type="button" class="btn btn-danger" onclick="history.go(-1);">返回</button>
				<button type="submit" id="submit" class="btn btn-primary">確定</button>
			</form>
		</div> 
		<?php			
	break;
//-------------------------------------------------------------------------------------------------------------	//
	default:
		?>
	<!--//-------------------------------------------------------------------------------------------------------------	//
			//	(預設)		登入與註冊 選擇頁面																						//
			//-------------------------------------------------------------------------------------------------------------	// -->
			<div class="container">
			<div class="row clearfix">
			<div class="col-md-12 column">
			<div class="jumbotron">
				<h1>歡迎使用<br/>亞典請假系統<br/></h1>
		<?php
					if(isset($_SESSION['authority'])&&$_SESSION['authority']!="n")
					{    
						header('Location: index.php?mod=calendar');	
		?>
		<?php
					}					
					else  
					{//請先登入或註冊會員 方有編輯權限
						echo "請先登入或註冊會員";
		?>
		<?php
						$date =new DateTime();
						echo $date->format('Y/m/d');
					}
		?>
						<div class="row clearfix">
							<div class="status alert alert-danger" style="display: none">
							</div> 
						<form id="myform" class="form" name="login" action="edit.php"  method="post">	
						<div class="form-group">
							<label for="inpu" class="col-sm-2 control-label">帳號</label>
							<div class="col-sm-10">
								<input type="mid" class="form-control" id="inpu" name='mid' />
							</div>
							<label for="input" class="col-sm-2 control-label">Password</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" id="input" name='password' />
							</div>
						</div>
						</div>
							<div class="container">
							<div class="row clearfix">
								<div class="col-md-6 column">
									<button type="submit"  class="btn btn-block btn-lg btn-primary" >登入 </button>
								</div>
								<div class="col-md-6 column">
									<button type="button" class="btn btn-warning btn-lg btn-block active" onclick="javascript:location.href='login.php?mod=mem_add'">+ 註冊</button>	
								</div>
							</div>
							</div>
						</form>			
			</div>
			</div>
			</div>
			</div> 
		<?php
	}		
		?>
<!-- Page JS  Scripts -->	
		<script src="https://use.fontawesome.com/09faeb38dd.js"></script>
		<script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
		<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.scrollex.min.js"></script>
		<script src="js/jquery.scrolly.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/util.js"></script>
		<script src="js/main.js"></script>	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<!-- Page JS (Customer) -->	
		<script src="js/check.js"></script>
		<script src="js/form.js"></script>
	</body>