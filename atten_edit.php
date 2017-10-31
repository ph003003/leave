<?php
session_start();
require_once('settings.php');//存入各項初始變數
require_once('google_calendar_api.php');
require_once 'inc/root.php';	
require_once('PHPMailer/PHPMailerAutoload.php'); //引入phpMailer	
include('PHPMailer_function.php'); //引入phpMailer
$wk		= post("wk")?post("wk"):get("wk");
switch ($wk)
{				
	case "atten_add":
		$insert_time=date("Y-m-d H:s:i");		
		$hours=$_GET['hours'];
		$reason=urldecode($_GET['reason']);
		$reason=nl2br($reason);
		$reason_kind=urldecode($_GET['reason_kind']);
		$event = json_decode(base64_decode($_GET['state']), true);
		$event_time= $event['event_time'];
		$start_time =  $event_time['start_time'];
		$end_time = $event_time['end_time'];
		conn();
		$sql = "INSERT INTO `atten` (`atten_sn`, `mnm`, `end_time`, `start_time`, `reason`, `msn`, `reason_kind`, `comp_name`, `insert_time`, `hours`, `agree`, `calendar_id`)
		VALUES (NULL, '".$_SESSION['mnm']."', '".$end_time."','".$start_time."', 
		'".$reason."', '".$_SESSION['msn']."', '".$reason_kind."', '".$_SESSION['comp_name'].
		"', '".$insert_time."', '".$hours."', 'wait', '".$calendarid."')";
		$res = $conn->prepare($sql);
		$res->execute();
		/*以下找出請假詳細資料*/
		$title_time=explode("T",$start_time);
		$title_start_day	= $title_time[0];
		$title_start_time = substr($title_time[1],0,5);
		$title_time=explode("T",$end_time);	
		$title_end_day	= $title_time[0];
		$title_end_time	= substr($title_time[1],0,5);		
		$title=$title_start_time."~~".$title_end_time.":".$_SESSION['mnm'];
		$reason =$reason_kind.":".$reason;
		/**通知信*****/		
		$sql = "SELECT `comp_name` FROM member WHERE msn = '".$_SESSION['msn']."' ";
		$res = $conn->query($sql);
		$comp_name = $res->fetchColumn();
		if($_SESSION['authority']=="boss")
		{	
		$sql = "SELECT `mail` FROM member WHERE authority = 'ad'  LIMIT 1";	
		}
		else
		{	
		$sql = "SELECT `mail` FROM member WHERE comp_name = '".$comp_name."' and authority = 'boss'  LIMIT 1";
		}
		$res = $conn->query($sql);
		$boss_mail = $res->fetchColumn();
		$sql = "SELECT `mail` FROM member WHERE msn = '".$_SESSION['msn']."' ";
		$res = $conn->query($sql);
		$mail = $res->fetchColumn();
		$mail_body ="<table border='1' style='width:100%'><tr>
		<td align='center' valign='center'
			style='font-size:1.2rem;
				background-color: lightblue;
				padding: 1.2rem;width:6%
				'>狀態</td>
		<td align='center' valign='center'
			style='font-size:1.2rem;
				background-color: lightblue;
				padding: 1.2rem;width:6%
				'>姓名</td>
		<td align='center' valign='center'
		style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:6%
			'>種類</td>
		<td align='center' valign='center'
		style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:30%'>事由</td>
		<td align='center' valign='center'
		style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:26%
			'>缺勤開始時間</td>
		<td align='center' valign='center'
		style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:26%
			'>結束時間</td>
			</tr>
		<tr>
		<td  align='center' valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>未審核</td>	
		<td  align='center' valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$_SESSION['mnm']."</td>
		<td align='center'  valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$reason_kind."</td>
		<td style='font-size:1.2rem; 
			padding: 1.2rem;'>".$reason."</td>
		<td  align='center'  valign='center' 
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$title_start_day."<br>".$title_start_time."</td>	
		<td  align='center'  valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$title_end_day."<br>".$title_end_time."</td>	
		</tr>
		</table>"; //郵件內容					
		phpmailer($mail,$mail_body);		
		phpmailer($boss_mail,$mail_body);
		cls_conn();
		echo json_encode(array('name11'=>"已寄送出通知信"));
	break;
/*****************************************************************************************************************************************/
	case "atten_del":
		$atten_sn	= get("sn"); 	 			//序號
		conn();
		$sql = "DELETE FROM `atten` WHERE `atten_sn`='".$atten_sn."'";				
		$res = $conn->exec($sql);
		if($res)
		{
			$status = array(
				'type'	=> 'success',
				'msg'	=> '刪除OK'
			);							
		}	
		echo json_encode($status);
	break;
/*****************************************************************************************************************************************/
	case "atten_agree":
		$atten_sn	= get("sn"); 	 			//序號
		conn();
		$sql = "UPDATE atten SET `agree`='agree' WHERE `atten_sn`='".$atten_sn."'";
		$res = $conn->prepare($sql);
		$res->execute();
		/*找出請假詳細資料*/
		$sql = "SELECT `msn`,`mnm`,`start_time`,`end_time`,`reason`,`reason_kind`
		FROM atten WHERE atten_sn = '".$atten_sn."'";
		$res = $conn->query($sql);
		list($msn,$mnm,$start_time,$end_time,$reason,$reason_kind)=$res->fetch();
		$title_time=explode("T",$start_time);
		$title_start_day	= $title_time[0];
		$title_start_time = substr($title_time[1],0,5);
		$title_time=explode("T",$end_time);	
		$title_end_day	= $title_time[0];
		$title_end_time	= substr($title_time[1],0,5);
		$title=$title_start_time."~~".$title_end_time.":".$mnm;
		$reason =$reason_kind.":".$reason;
		/*以下是登記日曆部分*/
		$gapi = new GoogleCalendarApi();
		$data = $gapi->GetRefreshedAccessToken(CLIENT_ID, $_SESSION['refresh_token'], CLIENT_SECRET);
		// The new access token
		$_SESSION['access_token']= $data['access_token'];
		$user_timezone = $gapi->GetUserCalendarTimezone($_SESSION['access_token']);
		$event_id = $gapi->CreateCalendarEvent($calendarid, $title,$start_time,$end_time, $user_timezone, $_SESSION['access_token'],$reason);
		/**通知信*****/		
		$sql = "SELECT `comp_name` FROM member WHERE msn = '".$msn."' ";
		$res = $conn->query($sql);
		$comp_name = $res->fetchColumn();
		$sql = "SELECT `mail` FROM member WHERE msn = '".$msn."' ";
		$res = $conn->query($sql);
		$mail = $res->fetchColumn();
		$mail_body ="<table border='1' style='width:100%'><tr>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:6%
			'>狀態</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:6%
			'>姓名</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:6%
			'>種類</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:30%'>事由</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:26%
			'>缺勤開始時間</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:26%
			'>結束時間</td>
			</tr>
			<tr>
			<td   align='center' valign='center' 
			style='font-size:1.2rem; color:red;
			padding: 1.2rem ;'>通過</td>	
			<td  align='center' valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$mnm."</td>
			<td align='center'  valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$reason_kind."</td>
			<td style='font-size:1.2rem; 
			padding: 1.2rem;'>".$reason."</td>
			<td  align='center'  valign='center' 
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$title_start_day."<br>".$title_start_time."</td>
			<td  align='center'  valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$title_end_day."<br>".$title_end_time."</td>	
			</tr>
			</table>"; //郵件內容	
		phpmailer($mail,$mail_body);		
		phpmailer($_SESSION['mail'],$mail_body);
		if($res)
		{
		$status = array(
		'type'	=> 'success',
		'url'	=> 'index.php?mod=mem_re',
		'msg'	=>  '已【通過】該申請，已寄出通知信給雙方以便備查'
		);
		}
		cls_conn();
		echo json_encode($status);
	break;
/**************************************//**************************************//**************************************//**************************************//**************************************/
	case "atten_veto":
		$atten_sn	= get("sn"); 	 			//序號
		conn();
		$sql = "UPDATE atten SET `agree`='veto' WHERE `atten_sn`='".$atten_sn."'";
		$res = $conn->prepare($sql);
		$res->execute();
		/*以下找出請假詳細資料*/
		$sql = "SELECT `msn`,`mnm`,`start_time`,`end_time`,`reason`,`reason_kind`
		FROM atten WHERE atten_sn = '".$atten_sn."'";
		$res = $conn->query($sql);
		list($msn,$mnm,$start_time,$end_time,$reason,$reason_kind)=$res->fetch();
		$title_time=explode("T",$start_time);
		$title_start_day	= $title_time[0];
		$title_start_time = substr($title_time[1],0,5);
		$title_time=explode("T",$end_time);	
		$title_end_day	= $title_time[0];
		$title_end_time	= substr($title_time[1],0,5);
		$title=$title_start_time."~~".$title_end_time.":".$mnm;
		$reason =$reason_kind.":".$reason;
		cls_conn();	
		/**通知信*****/		
		$sql = "SELECT `comp_name` FROM member WHERE msn = '".$msn."' ";
		$res = $conn->query($sql);
		$comp_name = $res->fetchColumn();
		$sql = "SELECT `mail` FROM member WHERE msn = '".$msn."' ";
		$res = $conn->query($sql);
		$mail = $res->fetchColumn();
		$mail_body ="<table border='1' style='width:100%'><tr>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:6%
			'>狀態</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:6%
			'>姓名</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:6%
			'>種類</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:30%'>事由</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:26%
			'>缺勤開始時間</td>
			<td align='center' valign='center'
			style='font-size:1.2rem;
			background-color: lightblue;
			padding: 1.2rem;width:26%
			'>結束時間</td>
			</tr>
			<tr>
			<td   align='center' valign='center' 
			style='font-size:1.2rem; color:red;
			padding: 1.2rem ;'>否決</td>	
			<td  align='center' valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$mnm."</td>
			<td align='center'  valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$reason_kind."</td>
			<td style='font-size:1.2rem; 
			padding: 1.2rem;'>".$reason."</td>
			<td  align='center'  valign='center' 
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$title_start_day."<br>".$title_start_time."</td>
			<td  align='center'  valign='center'
			style='font-size:1.2rem;
			padding: 1.2rem;'>".$title_end_day."<br>".$title_end_time."</td>	
			</tr>
			</table>"; //郵件內容	
		phpmailer($mail,$mail_body);		
		phpmailer($_SESSION['mail'],$mail_body);
		if($res)
		{
			$status = array(
				'type'	=> 'success',
				'url'	=> 'index.php?mod=mem_re',
				'msg'	=> '已否決該申請，已寄出通知信給雙方以便備查'
			);
		}
		echo json_encode($status);
	break;
			/**************************************/
}
?>