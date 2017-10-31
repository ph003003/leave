<?php
	session_start();
	require_once('settings.php');//存入各項初始變數
	require_once 'inc/root.php';	
 /********************************************** 驗證信升權限********/
	$sn=get('sn');
	$mail_pass = get('mail_pass');
	if($mail_pass!=null && $sn!=null)
	{
		conn();
		$sql = "SELECT `mail_pass` FROM member WHERE msn = '".$sn."'";				
		$res = $conn->query($sql);
		$_SESSION['mail_pass'] = $res->fetchColumn();
		cls_conn();
		if($_SESSION['mail_pass']==$mail_pass)
		{
			conn();
			$sql = "UPDATE member SET `authority`='y' WHERE `msn`='".$sn."'";
			$res = $conn->prepare($sql);
			$res->execute();
			cls_conn();
				echo'<h4>驗證成功，2秒後回到首頁中</h4>
			<meta http-equiv="refresh" content="2;url=index.php"> ';	//連結可能須修改
		}
	}
?>