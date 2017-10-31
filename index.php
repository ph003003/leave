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
body{
font-family:'微軟正黑體';
}
button{
margin: 0.5rem 0rem;
}
div{
 text-align:center;line-height:100px,
}
input{
 text-align:center;line-height:100px,
}
input{
 text-align:center;line-height:100px,
}
</style>
<body>
<?php
		if(isset($_SESSION['authority']) ){
			if($_SESSION['authority']=="n"||$_SESSION['ad_license']=="n"){
				unset($_SESSION['mid']);
				unset($_SESSION['authority']);
				//此會員停權中，自動登出
				header("Location:login.php"); 
			}
		}
		else{
			
			//<h1>尚未登入,此功能現會員使用</h1>
				header("Location:login.php"); 
		
		}
		?>
			<div class="container">
			<div class="jumbotron">
				<h2>亞典請假系統</h2>		
			</div>
			</div> 

		<div class="col-md-3 column">
				<button id="about1" type="button"  style='margin: 1.2rem 0rem;' class="btn btn-block btn-primary btn-lg" data-toggle="collapse" data-target="#shrink1">請假相關</button>
				<div id="shrink1" class="collapse">
				<div class="col-md-11 column">
        <?php
                    $atten_new=" ";
                    conn();
                    $sql = "SELECT  COUNT(*)  FROM atten where agree='wait'";
                    $sql .= " and date_save ='date_save'";
                    if($_SESSION['authority']=='boss') 
                    {
                         //可看同公司的人$_session['comp_name']==row[comp_name]
                    $sql .= " and comp_name ='".$_SESSION['comp_name']."'";
                    }
                    else if($_SESSION['authority']=='y') 
                    { 
                         //只可看 自己
                         $sql .= " and msn ='".$_SESSION['msn']."'";
                    }
                    $res = $conn->query($sql);
                    $count = $res->fetchColumn();
                    if ($count > 0 ) $atten_new="  (".$count.")";
                    
                    $member_new=" ";
                    $sql = "SELECT  COUNT(*)  FROM member where ad_license='n' and authority ='y'";		
                    $sql .= " and date_save ='date_save'";
                    if($_SESSION['authority']=='boss') 
                    {
                         //可看同公司的人$_session['comp_name']==row[comp_name]
                    $sql .= " and comp_name ='".$_SESSION['comp_name']."'";
                    }
                    else if($_SESSION['authority']=='y') 
                    { 
                         //只可看 自己
                         $sql .= " and msn ='".$_SESSION['msn']."'";
                    }
                    $res = $conn->query($sql);
                    $count = $res->fetchColumn();
                    if ($count > 0 ) $member_new="  (".$count.")";
                    cls_conn();
                    if($_SESSION['authority']!="ad")
                    {
        ?>
                    <button  style='margin: 1.2rem 0rem;' type="button" class="btn btn-block btn-lg btn-success" onclick="javascript:location.href='index.php?mod=atten_add'">※我要請假</button>
        <?php
                    }
        ?>
                    <button style='margin: 1.2rem 0rem;'type="button" class="btn btn-block btn-lg btn-success" onclick="javascript:location.href='index.php?mod=calendar'">※查看請假日曆</button>						
                    <button style='margin: 1.2rem 0rem;'type="button" class="btn btn-block btn-lg btn-success" onclick="javascript:location.href='index.php?mod=intern_calendarid'">※臨時缺勤日曆</button>
                    <button style='margin: 1.2rem 0rem;'type="button" class="btn btn-block btn-lg btn-success" onclick="javascript:location.href='index.php?mod=atten_get_on'">※申請中的請假<?=$atten_new?></button>
                    <button style='margin: 1.2rem 0rem;'type="button" class="btn btn-block btn-lg btn-success"onclick="javascript:location.href='index.php?mod=atten_record'" >※過去請假紀錄</button>
            </div>			
            </div>		
            <button id="about2" type="button"  style='margin: 1.2rem 0rem;' class="btn btn-block btn-primary btn-lg" data-toggle="collapse" data-target="#shrink2">設定與管理</button>
                <div id="shrink2" class="collapse">
                <div class="col-md-11 column">
        <?php
                    if($_SESSION['authority']!="y")
                    {
        ?>
                        <button style='margin: 1.2rem 0rem;' type="button"  class="btn btn-block btn-lg btn-success" onclick="javascript:location.href='index.php?mod=mem_re'">會員管理<?=$member_new?></button>
        <?php
                    }
                    if($_SESSION['authority']=="ad")
                    {
        ?>
                        <button style='margin: 1.2rem 0rem;' type="button" class="btn btn-block btn-lg btn-success" onclick="javascript:location.href='index.php?mod=comp_add'">+ 新增公司部門</button>
        <?php
                    }
        ?>
                    <button style='margin: 1.2rem 0rem;' type="button" class="btn btn-block btn-lg btn-success" onclick="javascript:location.href='index.php?mod=mem_edit&sn=<?=$_SESSION['msn']?>'">個人資料管理</button>	
                    <br/><br/>
                    <form id="myform2" class="form" name="logout" action="edit.php" ct="logout" method="post"> 
                        <button type="submit" id="submit2" class="btn btn-block btn-lg btn-primary">登出</button>
                    </form>
                </div>	
                </div>
		</div>
		<?php
		
	
switch ($mod)
{		
		case "calendar":
		?>	
			<!-- 以下為日曆 -->
			<div class="col-md-9 column">
				<iframe src="https://calendar.google.com/calendar/embed?height=600&amp;wkst=1&amp;bgcolor=%2366cccc&amp;src=hkrngrnphdl9dtgerjp0hbktv4%40group.calendar.google.com&amp;color=%236B3304&amp;ctz=Asia%2FTaipei" 
				style="border:solid 1px #777" width="100%" height="600" frameborder="0" scrolling="no"></iframe>
			</div>
		<?php
		break;	
	//-------------------------------------------------------------------------------------------------------------	//
		case "intern_calendarid":
		?>	
				<!-- 以下為日曆 -->	
		<div class="col-md-9 column">
			<iframe src="https://calendar.google.com/calendar/embed?height=600&amp;wkst=1&amp;bgcolor=%2366cccc&amp;
			src=g3o5v35dona4ffa1u5atarlpeo%40group.calendar.google.com&amp;color=%236B3304&amp;ctz=Asia%2FTaipei" style="border:solid 1px #777" 
			width="100%" height="600" frameborder="0" scrolling="no"></iframe>
		</div>
		<?php
		break;	
	//-------------------------------------------------------------------------------------------------------------	//
		case "comp_add":
		?>
			<div class="col-md-7 column">
				<div class="status alert alert-danger" style="display: none"></div> 
				<form id="myform" class="form" name="comp_add" action="edit.php" ct="add" method="post">	 
					<h2>公司相關資料</h2>
				
						<input type="text" class="form-control" id='comp_name' name='comp_name' placeholder="公司名*" required/>
						
					<h2>管理員資料</h2>			
                        <div class="form-group">
                        <input type="text" class="form-control" name='mnm' placeholder="姓名*" required/>
                        </div>
                        <div class="form-group">
                        <input type="text" class="form-control" id='mid' name='mid' placeholder="登入帳號*" onblur="chk_mem('mid');" required/>
                        </div>
                        <div class="form-group">
                        <input type="password" class="form-control" id='pwd' name='pwd' placeholder="登入密碼*" required/>
                        </div>
                        <div class="form-group">
                        <input type="password" class="form-control" id='pwd2' name='pwd2' placeholder="確認密碼*" onblur="chk_pwd();" required/>
                        </div>
                        <div class="form-group">
                        <input type="mail" class="form-control" id='mail' name='mail' placeholder="電郵" onblur="chk_mail();" />ps.如填入google信箱會連動google日曆，可加入最高權限。
                        </div>
                        <div class="form-group">
                        <input type="text" class="form-control" name='tel' placeholder="電話*" required/>
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
					<button type="submit" id="submit" class="btn btn-primary">確定</button>
				</form>	
			</div>
		<?php
		break;
		//-------------------------------------------------------------------------------------------------------------	//
		case "atten_add":
			$path="http://".path()."atten_add_index.php";// settings.php當中	
		?>	
			<div class="col-md-9 column">
					<iframe src=<?=$path?> width="100%" height="800" frameborder="0" scrolling="no">
					</iframe>
			</div>	
		<?php
		break;
		//-------------------------------------------------------------------------------------------------------------	//
		case "mem_edit":	
			//-------------------------------------------------------------------------------------------------------------	//
			//	會員修改																									//
			//-------------------------------------------------------------------------------------------------------------	//
			$sn=get('sn');
			member($sn,'','');	//$mid $pwd,$mno,$mnm,$mail,$tel,$fax,$vat,$addr,$mail,$reg,$authority;
		?>
			<div class="col-md-7 column">
				<form id="myform" class="form" name="mem_edit" action="edit.php" ct="mem_edit" method="post">
						<div class="status alert alert-danger" style="display: none"></div>
					<h2>會員修改</h2><br/>	
		<?php
					if($_SESSION['authority']=="ad"){
						echo"所屬部門<br/>";
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
						<option value=<?=$row;?> ><?=$row;?></option>
		<?php
						}
						echo"</select>";
					}
		?>　	
						<div class="form-group">
						姓名<input type="text" class="form-control" name='mnm'  value="<?=$mnm;?>" required/>
						</div>						<div class="form-group">
						帳號<input type="text" class="form-control" name='mid'  value="<?=$mid;?>" required/>
						</div>
						更新密碼
						<div class="form-group">
							<input type="password" class="form-control" id='pwd' name='pwd' placeholder="新密碼*"  value="<?=$pwd;?>" onblur="chk_pwd();"/>
						</div>
						<div class="form-group">
							<input type="password" class="form-control" id='pwd2' name='pwd2' placeholder="確認密碼*"  value="<?=$pwd;?>" onblur="chk_pwd();" />
						</div>
						<div class="form-group">
							信箱<input type="mail" class="form-control" id='mail' name='mail' placeholder="電郵" value="<?=$mail;?>" onblur="chk_mail();" />
						</div>
					<input type="hidden" name="msn" value="<?=$sn?>" />
					<button type="submit" id="submit" class="btn btn-primary">確定更新</button>
				</form>
			</div>
		<?php
		break;
		//-------------------------------------------------------------------------------------------------------------	//
		case "special_edit":
		//-------------------------------------------------------------------------------------------------------------	//
		//	修改																									//
		//-------------------------------------------------------------------------------------------------------------	//
		$sn=get('sn');	
		$special=get('special');
		?>
		<div class="col-md-9 column" style="text-align:center;line-height:100px,">
			<form id="myform" class="form" name="special_edit" action="edit.php" ct="mem_edit" method="post">
						<div class="form-group">
						特休時數<input  style="text-align:center;line-height:100px," type="text" class="form-control" name='special'  value="<?=$special;?>" required/>
				<input type="hidden" name="msn" value="<?=$sn?>" />
				<button type="submit" id="submit" class="btn btn-primary">確定更新</button>
			</form>	
		</div>	
		<?php
		break;
		//-------------------------------------------------------------------------------------------------------------	//
		case "mem_re":
			//-------------------------------------------------------------------------------------------------------------	//
			//	會員管理列表																								//
			//-------------------------------------------------------------------------------------------------------------	//
			$get_page	= get('page');
			$mnm		= post('mnm');	//客戶名稱
			$comp_name		= post('comp_name');								
			$status		= post('status') ? post('status') : 'y';	//狀態
			$status		= get('status') ? get('status'):$status;
			$search		= get('search');
			if($search)
			{
				$array	= explode(",",$search);
				$comp_name=$array[0];
				$mnm	= $array[1];
				$status	= $array[2];
			}
			$search=array($comp_name,$mnm,$status);
			member('',$search,$get_page);	//=>$list_mem,$count_mem
		?>
		<!-- Search -->
			<h2>會員管理</h2>
			<div class="col-md-9 column">
				<form action="index.php?mod=mem_re" method="POST">
					<div class="col-md-4">
					<div class="form-group">
						<input type="text" class="form-control" name="mnm" placeholder="姓名" value="<?=$mnm;?>"/>
					</div>
					</div>	
					<div class="col-md-4">
					<div class="form-group">
						<input type="text" class="form-control" name="comp_name" placeholder="公司名" value="<?=$comp_name;?>"/>
					</div>
					</div>
					<div class="col-md-4">
					<div class="widget search">
					<div class="input-group">
						<div class="form-group">
						<select class="form-control" name="status">
		<?php
							$sel1	= 'selected';
							$sel2	= '';
							$sel3	= '';
							if($status=='n')
							{
							$sel1='';
							$sel2='selected';
							}	
							if($status=='ad_license')
							{
							$sel1='';
							$sel2='';	
							$sel3='selected';
							}
		?>
							<option value="y" <?=$sel1;?>>上線中</option>
							<option value="n" <?=$sel2;?>>已停權</option>
							<option value="ad_license" <?=$sel3;?>>待審核</option>
							</select>
						</div>
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit" style='margin: -0.5rem 0rem;' >搜尋
							</button>
						</span>
					</div>
					</div>
					</div>
				</form>
			<!-- Default -->
			<div class="row">

					<table>
						<thead>
						<tr>				
						<th>姓名</th>
						<th>聯絡方式</th>
						<th>所屬公司</th>	
						<th>權限</th>		
						<th>特休時數</th>				
						<th>請假相關</th>
						<th align="center">管理</th>
						</tr>
						</thead>
						<tbody>
		<?php
						foreach($list_mem as $row)
						{	
							$sql = "SELECT * FROM atten where `msn`='".$row['msn']."' AND `reason_kind`='特休' AND `agree` !='veto'";						
							$res = $conn->query($sql);	
							$list_special = $res->fetchAll();
							cls_conn();
							$surplus_special=$row['special'];
							foreach($list_special as $row_special)	
							{				
							$surplus_special=$surplus_special-$row_special['hours'];
							}
							if($row['authority']=='ad') $authority_txt="管理者";
							if($row['authority']=='boss') $authority_txt="主管";
							if($row['authority']=='y') $authority_txt="員工";
							if($row['authority']=='n') $authority_txt="停權";	
							if($row['ad_license']=='n') $authority_txt="未審核";
							?>
							<tr>
							<td><?=$row['mnm'];?></td>
							<td>
							電話：<?=$row['tel'];?><br/>
							電郵：<?=$row['mail'];?>
							</td>
							<td><?=$row['comp_name'];?></td>	
							<td><?=$authority_txt;?></td>
							<td>
		<?php		
							if($_SESSION['authority']=='ad')
							{  echo "<a href='index.php?mod=special_edit&sn=".$row['msn']."&special=".$row['special']."' >時數設定</a><br/>";
							}
		?>
							共有:<?=$row['special'];?><br/>
							剩餘:<?=$surplus_special;?>
							</td>
							<td>
							<button onclick="location.href='index.php?mod=atten_get_on&msn=<?=$row['msn']?>' " >申請中請假</button><br/>
							<button onclick="location.href='index.php?mod=atten_record&msn=<?=$row['msn']?>' ">已審核紀錄</button>
							</td>
							<td align="center">
		<?php
			
							if($_SESSION['authority']=='ad'&& $row['authority']=="y" && $row['ad_license']!="n")
							{  
								echo "<span class='go' link='edit.php?wk=mem_up&sn=".$row['msn']."' ct='mem_up' bk='index.php?mod=mem_re' style='color:blue;'>設為該公司主管</span><br/>";
							}
							else if($row['authority']=='n'&& $row['ad_license']!="n")
							{   
								echo "<span class='go' link='edit.php?wk=mem_on&sn=".$row['msn']."' ct='on' bk='index.php?mod=mem_re' style='color:blue;'>恢復</span><br/>";
								echo "<span class='go' link='edit.php?wk=mem_del&sn=".$row['msn']."' ct='del' bk='index.php?mod=mem_re'' style='color:red;'>刪除</span><br/>";
							}	
							if($row['ad_license']=="n")
							{   
								echo "<span class='go' link='edit.php?wk=ad_license&sn=".$row['msn']."' ct='on' bk='index.php?mod=mem_re' style='color:blue;'>審核會員</span><br/>";
							}	
							else  if($row['authority']!='n')
							{ 
								echo "<span class='go' link='edit.php?wk=mem_off&sn=".$row['msn']."' ct='off' bk='index.php?mod=mem_re' style='color:red;'>停權</span><br/>";						
							}
							echo "<a href='index.php?mod=mem_edit&sn=".$row['msn']."'>修改</a>";
						}
		?>
						</td>
						</tr>
						</tbody>
					</table>
			</div>	
		</div>
		<?php
		break;
		//-------------------------------------------------------------------------------------------------------------	//
			case "atten_get_on":
			case "atten_record":
			//-------------------------------------------------------------------------------------------------------------	//
			//	請假列表																								//
			//-------------------------------------------------------------------------------------------------------------	//
			$get_page	= get('page');
			$msn	= get('msn');
			$mnm	= post('mnm');
			$start_time 		= post('start_time');	//搜尋條件							
			$status		= post('status') ? post('status') : 'y';	//狀態
			$status		= get('status') ? get('status'):$status;
			$search		= get('search');
			if($search)
			{
				$array	= explode(",",$search);
				$mnm	= $array[0];
				$start_time 	= $array[1];
				$mod	= $array[2];	
				$msn	= $array[3];
				$status	= $array[4];
			}
			$search=array($mnm,$start_time,$mod,$msn,$status,);
			atten_list('',$search,$get_page);	//=>$list_mem,$count_mem
		?>
				<!-- Search -->
		<div class="col-md-9 column">
		<?php
				 if($mod=='atten_get_on')
					{	
					echo'<h2>申請中的請假</h2>';
						}		
				else if($mod=='atten_record')
					{	
					echo'<h2>請假紀錄</h2>';
						}
		?>
				<form action="index.php?mod=atten_get_on" method="POST">
					<div class="col-md-4">
						<div class="form-group">
							<input type="text" class="form-control" name="mnm" placeholder="申請人" value="<?=$mnm;?>"/>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						<input type="text" class="form-control" name="start_time" placeholder="開始時間" value="<?=$start_time;?>"/>
						</div>
					</div>
					<div class="widget search">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit" style='margin: -0.5rem 0rem;'>搜尋
								</button>
							</span>
						</div>
					</div>
				</form>
			<!-- Default -->
			<div class="row">
			<div class="col-md-12">
					<table>
						<thead>
							<tr>
								<th>姓名</th>
								<th>所屬公司</th>
								<th>起迄時間</th>
								<th>總時數</th>
								<th>提出時間</th>	
								<th>事由</th>
								<th>事由詳細</th>
		<?php
						 if($mod=='atten_get_on')
							{	
							echo'<th align="center">管理</th>';
								}		
						else if($mod=='atten_record')
							{	
							echo'<th align="center">審核結果</th>';
								}
		?>
							</tr>
						</thead>
		<?php		
						foreach($list_mem as $row)
						{
						$time=explode("T",$row['start_time']);
						$start_day	= $time[0];
						$start_time = substr($time[1],0,5);		
						$time=explode("T",$row['end_time']);
						$end_day	= $time[0];
						$end_time   = substr($time[1],0,5);					
						$time=explode(" ",$row['insert_time']);
						$insert_day	= $time[0];
						$insert_time   = substr($time[1],0,5);
		?>
						<tr>
						<td><?=$row['mnm'];?><br/></td>
						<td><?=$row['comp_name'];?><br/></td>
						<td>起<?=$start_day;?> <?=$start_time;?><br/><br/>
							迄<?=$end_day;?> <?=$end_time;?> </td>
						<td><?=$row['hours'];?></td>
						<td><?=$insert_day;?>  <?=$insert_time;?></td>
						<td><?=$row['reason_kind'];?><br/></td>
						<td><?=$row['reason'];?><br/></td>
		<?php	
						if($mod=='atten_get_on'){
							echo'<td align="center">';
							if($_SESSION['authority']!="y" && $_SESSION['msn']!=$row['msn'])
							{	
								echo "<button type='submit' class='go' link='atten_edit.php?wk=atten_agree&sn=".$row['atten_sn']."' ct='atten_agree' bk='index.php?mod=atten_get_on'' style='color:blue;'>通過申請 </button><br/>";		
								echo "<button type='submit' class='go' link='atten_edit.php?wk=atten_veto&sn=".$row['atten_sn']."' ct='atten_veto' bk='index.php?mod=atten_get_on'' style='color:red;'>  否決</button>";		
							}
							if($_SESSION['msn']==$row['msn'])
							{
								echo "<span class='go' link='atten_edit.php?wk=atten_del&sn=".$row['atten_sn']."' ct='del' bk='index.php?mod=atten_get_on''>刪除</span>";		
							}
							echo'</td>';
						}						
						else if($mod=='atten_record')
						{
							echo'<td align="center">';
							if($row['agree']=="agree")
							{							
								echo "<text style='color:blue;'>通過</text>"; 
							}
							else if($row['agree']=="veto")
							{
								echo "<text style='color:red;'>不通過</text>";
							}						
							echo'</td>';
						}						
		?>
							</tr>
		<?php
						}
		?>
					</table>
			</div>
			</div>
		</div>
			
		<?php
			break;
		//-------------------------------------------------------------------------------------------------------------	//
		default:
		echo'<meta http-equiv="refresh" content="1;url=login.php?mod=calendar">' ;
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
	<!-- Page JS (Customer) -->			
		<script src="js/Shrink.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="js/check.js"></script>
		<script src="js/form.js"></script>
	</body>