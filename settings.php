<?php
//重要!! 用於刷新令牌的不要常用 50次後會被鎖/* Google App Client Secret */
/* Google App Redirect Url *///
//define('CLIENT_REDIRECT_URL', $path);
//$login_url = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/calendar') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=offline&prompt=consent';

define('CLIENT_ID', '360725408001-s24fpg9ova68e1mrnphnvnfmjont01vl.apps.googleusercontent.com');
/* Google App Client Secret */
define('CLIENT_SECRET', 'H1ZtAyLoaNpS_xoLzRXGt7lK');function path()
{
	$path1	= $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	$path2	= strrpos($path1,'/',0)+1;
	$path	= substr($path1,0,$path2);
	return $path;
}
$path=path();
$intern_calendarid ='g3o5v35dona4ffa1u5atarlpeo@group.calendar.google.com';
$calendarid ='hkrngrnphdl9dtgerjp0hbktv4@group.calendar.google.com';
$_SESSION['refresh_token']='1/3uH6kmXSm1UKvHznm4ehsM31tOh20vi7jsOfg9SDQ9I';//重要!! 用於日曆權限，更新後要改
/*寄信用的初始變數(還沒寫)
	$phpmailer->AddAddress($boss_mail, "我是收件人"); //收件人郵件和名稱
		$phpmailer->Username = "ph001001001@gmail.com"; //您的 gamil 帳號
		$phpmailer->Password = "b93thcqn"; //您的 gmail 密碼
		$phpmailer->SMTPSecure = "ssl"; // SSL連線 (要使用gmail stmp需要設定ssl模式) 
		$phpmailer->Port = 465; //Gamil的SMTP主機的port(Gmail為465)。
		$phpmailer->CharSet = "utf-8"; //郵件編碼  
		$phpmailer->From = "ph001001001@gmail.com"; //寄件者信箱
*/
?>