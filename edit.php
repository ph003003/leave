<?php
session_start();
require_once('settings.php');//存入各項初始變數
require_once('google_calendar_api.php');
require_once 'inc/root.php';	
include ('recaptchalib.php');
require_once('PHPMailer/PHPMailerAutoload.php'); //引入phpMailer	
include('PHPMailer_function.php'); //引入phpMailer
$wk		= post("wk")?post("wk"):get("wk");
$sn=get('sn');
	switch ($wk)
	{	
	/**********************************************  會員登出		**********************************************/
		case "logout":
			unset($_SESSION['mid']);
			unset($_SESSION['authority']);
			$status = array(
					'type'	=> 'success',
					'url'	=> 'login',
					'msg'	=> '登出成功!'
				);
			echo json_encode($status);
		break;
	/**********************************************  會員登入		**********************************************/
		case "login":
			$mid	= post("mid"); 	 			
			$password	= post("password"); 	 					
			conn();		
			$sql = "SELECT COUNT(*) FROM member WHERE mid = '".$mid."' and pwd='".$password."'";
			$sql .= " and date_save ='date_save'";
			$res = $conn->query($sql);
			$count = $res->fetchColumn();
			if ($count > 0 )
			{		
				$sql = "SELECT `msn`,`mnm`,`authority`,`comp_name`,`special`,`mail`,`ad_license`
				FROM member WHERE mid = '".$mid."' and pwd='".$password."'";
				$res = $conn->query($sql);
				list($_SESSION['msn'],$_SESSION['mnm'],$_SESSION['authority'],$_SESSION['comp_name'],$_SESSION['special'],$_SESSION['mail'],$_SESSION['ad_license'])=$res->fetch();
				$sql = "SELECT * FROM atten where `msn`='".$_SESSION['msn']."' AND `reason_kind`='特休' AND `agree` !='veto'";						
				$res = $conn->query($sql);	
				$list_mem = $res->fetchAll();
				cls_conn();
				foreach($list_mem as $row)	
				{				
				$_SESSION['special']=$_SESSION['special']-$row['hours'];
				}
					if($_SESSION['authority']=="wait_mail")
					{
						$status = array(
						'type'	=> 'success',
						'url'	=> 'login.php',
						'msg'	=>'權限不足，請先驗證郵箱。'
						);
						unset($_SESSION['mid']);
						unset($_SESSION['authority']);
					}		
					else if($_SESSION['authority']=="n"||$_SESSION['authority']=="delete")
					{
						$status = array(
						'type'	=> 'success',
						'url'	=> 'login.php',
						'msg'	=>'帳號停權中。'
						);
						unset($_SESSION['mid']);
						unset($_SESSION['authority']);
					}
					else if($_SESSION['ad_license']=="n")
					{
						$status = array(
						'type'	=> 'success',
						'url'	=> 'login.php',
						'msg'	=>'帳號仍在審查中，請稍後再試。'
						);
						unset($_SESSION['mid']);
						unset($_SESSION['authority']);
					}
					else{
						$status = array(
							'type'	=> 'success',
							'url'	=> 'index.php?mod=calendar',
							'msg'	=>'會員:'.$_SESSION['mnm'].'已登入，歡迎使用'
						);
					}
			}
			else
				{
					$status = array(
						'type'=>'false',
						'cls'=>'n',
						'btn'=>'success',
						'msg'=>'帳密錯誤!'
					);
				}
					cls_conn();
				echo json_encode($status);
		break;
/**********************************************  公司管理員新增		************************************/
		case "comp_add":
			$today = date("Y-m-d H:i:s"); 		//註冊時間
			$comp_name	= post("comp_name"); 	 			
			$mnm	= post("mnm"); 	 			
			$mid	= post("mid");				
			$pwd	= post("pwd");    	 					
			$mail	= post("mail");    
			$tel	= post("tel");    
		/*	 google 驗證碼
				if(isset($_POST['g-recaptcha-response']))
                {
				// 建立一個驗證碼物件
				$reCaptcha = new ReCaptcha($secret);
				// 將 recaptcha->verify 的值給 resp
				$resp = $reCaptcha->verifyResponse($_SERVER['REMOTE_ADDR'],$_POST['g-recaptcha-response']);
					 // 判斷 resp->isSuccess 是 true 或 false
					 if($resp->success == true)
                     { }
                     else
                     {
						$status = array(
								'type'=>'false',
								'cls'=>'n',
								'btn'=>'success',
								'msg'=>'未填驗證碼!'
							);				
						echo json_encode($status);
						break;
					 }
				}
        */
        /*	 離線用 驗證碼 */
                 
        if(isset($_POST["captcha"])){
        if($_SESSION["dice"]!=$_POST["captcha"])
        {
            $status = array(
                            'type'=>'false',
                            'cls'=>'n',
                            'btn'=>'success',
                            'msg'=>'驗證碼錯誤'
                        );				
            echo json_encode($status);
            break; 
        }
        }

        
			conn();
			$sql = "INSERT INTO member (`comp_name`,`mnm`,`mid`,`pwd`,`mail`,`tel`,`authority`,`ad_license`,`calendar_id`)";	
			$sql.= "VALUES(:comp_name,:mnm,:mid,:pwd,:mail,:tel,:authority,:ad_license,:calendar_id)";
			$res = $conn->prepare($sql);
			$res->bindParam(':comp_name',$comp_name,PDO::PARAM_STR,20);
			$res->bindParam(':mnm',$mnm,PDO::PARAM_STR,50);				
			$res->bindParam(':mid',$mid,PDO::PARAM_STR,10);
			$res->bindParam(':pwd',$pwd,PDO::PARAM_STR,10);
			$res->bindParam(':mail',$mail,PDO::PARAM_STR,18);							
			$res->bindParam(':tel',$tel,PDO::PARAM_STR,15);
			$boss='boss';$ad_license='y';
			$res->bindParam(':authority',$boss,PDO::PARAM_STR,15);	
			$res->bindParam(':ad_license',$ad_license,PDO::PARAM_STR,15);	
			$res->bindParam(':calendar_id',$calendarid,PDO::PARAM_STR,15);	
			$res->execute();
			/*日曆權限*/
			$gapi = new GoogleCalendarApi();
			$data = $gapi->GetRefreshedAccessToken(CLIENT_ID, $_SESSION['refresh_token'], CLIENT_SECRET);
			// The new access token 
			 $_SESSION['access_token']= $data['access_token'];
			$Authorization = $gapi->add_calendarAuthorization($calendarid,"owner",$mail,$_SESSION['access_token']);
			if($res)
			{
				$status = array(
					'type'	=> 'success',
					'url'	=> 'index.php',
					'msg'	=> '已註冊，此帳號有分公司經理權限'
				);
			}
			else
			{
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'系統暫停服務!'
				);					
			}
			cls_conn();	
			echo json_encode($status);
		break;
/**********************************************  會員新增		**********************************************/
		case "mem_add":
			$today = date("Y-m-d H:i:s"); 		//註冊時間
			$comp_name	= post("comp_name"); 	 			
			$mnm	= post("mnm"); 	 			
			$mid	= post("mid");				
			$pwd	= post("pwd");    	 					
			$mail	= post("mail");    
			$tel	= post("tel");    
		/*	 google 驗證碼
				if(isset($_POST['g-recaptcha-response']))
                {
				// 建立一個驗證碼物件
				$reCaptcha = new ReCaptcha($secret);
				// 將 recaptcha->verify 的值給 resp
				$resp = $reCaptcha->verifyResponse($_SERVER['REMOTE_ADDR'],$_POST['g-recaptcha-response']);
					 // 判斷 resp->isSuccess 是 true 或 false
					 if($resp->success == true)
                     { }
                     else
                     {
						$status = array(
								'type'=>'false',
								'cls'=>'n',
								'btn'=>'success',
								'msg'=>'未填驗證碼!'
							);				
						echo json_encode($status);
						break;
					 }
				}
        */
        /*	 離線用 驗證碼 */
                 
        if(isset($_POST["captcha"])){
        if($_SESSION["dice"]!=$_POST["captcha"])
        {
            $status = array(
                            'type'=>'false',
                            'cls'=>'n',
                            'btn'=>'success',
                            'msg'=>'驗證碼錯誤'
                        );				
            echo json_encode($status);
            break; 
        }
        }

			srand((double)microtime()*1000000);
			$no = md5(uniqid(rand()));
			$ed = strlen($no)-8;
			$rat = rand(0,$ed);
			$chkno = strtoupper(substr("$no",$rat,8));
			$mail_pass=$chkno;
			conn();
			$sql = "INSERT INTO member (`comp_name`,`mnm`,`mid`,`pwd`,`mail`,`tel`,`mail_pass`,`calendar_id`)";	
			$sql.= "VALUES(:comp_name,:mnm,:mid,:pwd,:mail,:tel,:mail_pass,:calendar_id)";
			$res = $conn->prepare($sql);
			$res->bindParam(':comp_name',$comp_name,PDO::PARAM_STR,20);
			$res->bindParam(':mnm',$mnm,PDO::PARAM_STR,50);				
			$res->bindParam(':mid',$mid,PDO::PARAM_STR,10);
			$res->bindParam(':pwd',$pwd,PDO::PARAM_STR,10);
			$res->bindParam(':mail',$mail,PDO::PARAM_STR,18);							
			$res->bindParam(':tel',$tel,PDO::PARAM_STR,15);	
			$res->bindParam(':mail_pass',$mail_pass,PDO::PARAM_STR,15);
			$res->bindParam(':calendar_id',$calendarid,PDO::PARAM_STR,15);//$calendarid於setting 中
			$res->execute();
			/**驗證信*****/
			$path="http://".path()."mail_pass.php?mail_pass=";// settings.php當中
			$sql ="SELECT MAX(`msn`) FROM `member` WHERE 1";
			$res = $conn->query($sql);	
			$sn= $res->fetchColumn();
			$mail_body ="請點擊下列連結!進行信箱驗證<br>".$path.$mail_pass."&sn=".$sn; //郵件內容						
			phpmailer($mail,$mail_body);
			if($res)
			{
				$status = array(
					'type'	=> 'success',
					'url'	=> 'index.php',
					'msg'	=> '已註冊，請至信箱收取驗證信'
				);
			}
			else
			{
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'系統暫停服務!'
				);					
			}
			cls_conn();	
			echo json_encode($status);
		break;
/**********************************************  會員檢查重複 **********************************************/
		case "check_mem":
			$vl		= get('vl');
			$chk	= get('chk');
			if(!isset($vl) || $vl == '')
			{
				$result = "";
			}
			else
			{
				conn();
				$sql = "SELECT COUNT(*) FROM member WHERE $chk = '".$vl."'";		
				$sql .= " and date_save ='date_save'";
				$res = $conn->query($sql);
				$count = $res->fetchColumn();
				if ($res)
				{
					if ($count > 0 && $chk=='mid')
					{
						$result = "此帳號已註冊";
					}
					else
					{
						$result = "";
					}
				}
				cls_conn();
				$status = array(
					'result'	=> $result
				);
				echo json_encode($status);
			}
		break;		
/**********************************************  會員修改  **********************************************/
		case "mem_edit":
			$msn	= post("msn"); 	 			//
            $mnm	= post("mnm"); 	 			//
			$mid	= post("mid"); 	 			//
			$pwd	= post("pwd");				//
			$pwd2	= post("pwd2");				//
			$mail	= chk(post("mail"),'g1');	//
			if($mid!=null&&$mnm!=null&&$mail!=null&&$pwd!=null)
			{
				conn();
				$sql = "UPDATE member SET `mnm`=:mnm,`mid`=:mid,`pwd`=:pwd,`mail`=:mail WHERE `msn`='".$msn."'";
				$res = $conn->prepare($sql);				
				$res->bindParam(':mnm',$mnm,PDO::PARAM_STR,18);
                $res->bindParam(':mid',$mid,PDO::PARAM_STR,18);	
				$res->bindParam(':pwd',$pwd,PDO::PARAM_STR,15);							
				$res->bindParam(':mail',$mail,PDO::PARAM_STR,30);		
				$res->execute();
				if($_SESSION['authority']=="ad")
				{
					$comp_name	= post("comp_name");
					$sql = "UPDATE member SET `comp_name`=:comp_name WHERE `msn`='".$msn."'";
					$res = $conn->prepare($sql);				
					$res->bindParam(':comp_name',$comp_name,PDO::PARAM_STR,18);	
					$res->execute();
				}
				if($res)
				{
					$status = array(
						'type'	=> 'success',
						'url'	=> 'index.php?mod=mem_re',
						'msg'	=> '會員資料已更新'
					);
				}
				else
				{
					$status = array(
						'type'=>'false',
						'cls'=>'n',
						'btn'=>'success',
						'msg'=>'系統暫停服務!'
					);					
				}
				cls_conn();
			}
			else
			{
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'必填欄位不得空白!'
				);
			}
			echo json_encode($status);
		break;
/********************************************** 特休時數變更  **********************************************/	
		case "special_edit":
			$special	= post("special"); 	 
			$msn	= post("msn"); 	 			//序號
			conn();
			$sql = "UPDATE member SET `special`='".$special."' WHERE `msn`='".$msn."'";
			$res = $conn->prepare($sql);
			$res->execute();
			if($res)
			{
				$status = array(
					'type'	=> 'success',
					'url'	=> 'index.php?mod=mem_re',
					'msg'	=>'特休時數已變更!'
				);
			}
			else
			{
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'系統暫停服務!'
				);					
			}
			cls_conn();
			echo json_encode($status);
		break;
/**********************************************  審核會員 **********************************************/	
		case "ad_license":
			$msn	= get("sn"); 	 			//序號
			conn();
			$sql = "UPDATE member SET `ad_license`='y' WHERE `msn`='".$msn."'";
			$res = $conn->prepare($sql);
			$res->execute();
			if($res)
			{
				$status = array(
					'type'	=> 'success',
					'url'	=> 'index.php?mod=mem_re',
					'msg'	=> '會員已審核'
				);
			}
			else
			{
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'系統暫停服務!'
				);					
			}
			cls_conn();
			echo json_encode($status);
		break;
		/**********************************************   會員升權(設定公司主管) **********************************************/
		case "mem_up":
			$msn	= get("sn"); 	 			//序號
			conn();
			$sql = "SELECT comp_name FROM member WHERE `msn`='".$msn."'";
			$res = $conn->query($sql);
			$comp_name = $res->fetchColumn();
			$sql = "UPDATE member SET `authority`='y' WHERE `comp_name`='".$comp_name."' and authority ='boss'";
			$res = $conn->prepare($sql);
			$res->execute();
			$sql = "UPDATE member SET `authority`='boss' WHERE `msn`='".$msn."'";
			$res = $conn->prepare($sql);
			$res->execute();
			if($res)
			{
				$status = array(
					'type'	=> 'success',
						'url'	=> 'index.php?mod=mem_re',
						'msg'	=> '會員已設定公司主管'
				);
			}
			else
			{
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'系統暫停服務!'
				);					
			}
			cls_conn();
			echo json_encode($status);
			break;
		/**********************************************  會員停權  **********************************************/
		case "mem_off":
			$msn	= get("sn"); 	 			//序號
			conn();
			$sql = "UPDATE member SET `authority`='n' WHERE `msn`='".$msn."'";
			$res = $conn->prepare($sql);
			$res->execute();
			if($res)
			{
				$status = array(
					'type'	=> 'success',
					'url'	=> 'index.php?mod=mem_re',
					'msg'	=> '會員停權'
				);
			}
			else
			{
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'系統暫停服務!'
				);					
			}
			cls_conn();
			echo json_encode($status);
		break;
		/**********************************************  會員恢復權限  **********************************************/
		case "mem_on":
			$msn	= get("sn"); 	 			//序號
			conn();
			$sql = "UPDATE member SET `authority`='y' WHERE `msn`='".$msn."'";
			$res = $conn->prepare($sql);
			$res->execute();
			if($res)
			{
				$status = array(
				'type'	=> 'success',
				'url'	=> 'index.php?mod=mem_re',
				'msg'	=> '會員恢復權限'
				);
			}
			else
			{
				$status = array(
				'type'=>'false',
				'msg'=>'系統暫停服務!'
				);					
			}
			cls_conn();
			echo json_encode($status);
		break;
		/**********************************************  會員刪除  **********************************************/
		case "mem_del":
			$msn	= get("sn"); 	 			//序號
			conn();
			$sql = "UPDATE member SET `date_save`='date_delete' WHERE `msn`='".$msn."'";
			$res = $conn->prepare($sql);
			$res->execute();		

			$sql = "UPDATE atten SET `date_save`='date_delete' WHERE `msn`='".$msn."'";
			$res = $conn->prepare($sql);
			$res->execute();
			if($res)
			{
					$status = array(
					'type'	=> 'success',
					'url'	=> 'index.php?mod=mem_re',
					'msg'	=> '會員刪除'
				);							
			}
			else
			{
				$status = array(
					'type'=>'false',
					'msg'=>'oops!系統暫停服務'
				);
			}
			cls_conn();	
			echo json_encode($status);
	break;
/**********************************************  表單測試  **********************************************/
		case "test":	
				/*	
				資料處理結果	'type'	=>	成功(會跳轉頁面) 	=>	success 
											失敗(停留原始頁面)	=>	紅 false / 無 dismissable / 藍 info / 黃 warning 
				欄位是否清空	'cls'	=>	y/n
				原始按鈕樣式	'btn'	=>	success/primary...
				顯示訊息		'msg'	=>	'xxxxxxx'
				*/					
				$status = array(
					'type'=>'false',
					'cls'=>'n',
					'btn'=>'success',
					'msg'=>'test successed'
				);				
			echo json_encode($status);
		break;	
	}
?>