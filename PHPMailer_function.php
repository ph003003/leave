<?php
	function phpmailer($mail,$mail_body) {	
		$phpmailer= new PHPMailer(); //初始化一個PHPMailer物件
		$phpmailer->Host = "smtp.gmail.com"; //SMTP主機 (這邊以gmail為例，所以填寫gmail stmp)
		$phpmailer->IsSMTP(); //設定使用SMTP方式寄信
		$phpmailer->SMTPAuth = true; //啟用SMTP驗證模式
		$phpmailer->Username = "ph001001001@gmail.com"; //您的 gamil 帳號
		$phpmailer->Password = "b93thcqn"; //您的 gmail 密碼
		$phpmailer->SMTPSecure = "ssl"; // SSL連線 (要使用gmail stmp需要設定ssl模式) 
		$phpmailer->Port = 465; //Gamil的SMTP主機的port(Gmail為465)。
		$phpmailer->CharSet = "utf-8"; //郵件編碼
		$phpmailer->From = "ph001001001@gmail.com"; //寄件者信箱
		$phpmailer->FromName = "請假系統"; //寄件者姓名
		$phpmailer->AddAddress($mail, "我是收件人"); //收件人郵件和名稱
		$phpmailer->AddBCC('ph001001001@gmail.com'); //設定 密件副本收件人 
		$phpmailer->IsHTML(true); //郵件內容為html 
		//$phpmailer->addAttachment('/tmp/image.jpg', 'new.jpg'); //添加附件(若不需要則註解掉就好)
		$phpmailer->Subject = "請假系統通知信系統。"; //郵件標題
		$phpmailer->Body =$mail_body; //郵件內容		$phpmailer->AltBody = '當收件人的電子信箱不支援html時，會顯示這串~~';	
		$phpmailer->send();						
	}
	?>