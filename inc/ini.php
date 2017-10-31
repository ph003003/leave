<?php
ini_set('default_charset','utf-8');

//----------------------------------------------------------------------------------------------------------//
//		hash salt																							//
//----------------------------------------------------------------------------------------------------------//

$salt	= "aden";	//member

//----------------------------------------------------------------------------------------------------------//
//		Initial var																							//
//----------------------------------------------------------------------------------------------------------//

//get
function get($str)
{
    $val = isset($_GET[$str]) && !empty($_GET[$str]) ? $_GET[$str] : null;		// 定義初始化(判斷是否空值)
    $val = @trim(stripslashes($val));											// 清除前後空白及斜線
	return $val;
}

//post
function post($str)
{
	$val = isset($_POST[$str]) && !empty($_POST[$str]) ? $_POST[$str] : null;
	if(is_array($val))
	{
		$val = array_filter($val);												//去除陣列中的空值
		//$val = array_values(val);												//重整序列
		$val = !empty($val) ? $val : null;
	}
	else
	{
    	$val = @trim(stripslashes($val));
	}
    return $val;
}

//session
function session($str)
{
    $val = isset($_SESSION[$str]) && !empty($_SESSION[$str]) ? $_SESSION[$str] : null;
    $val = @trim(stripslashes($val));
    return $val;
}

//array
function ary($ary,$str)
{
	$val = isset($ary[$str]) && !empty($ary[$str]) ? $ary[$str] : null;
	$val = @trim(stripslashes($val));
    return $val;
}

//----------------------------------------------------------------------------------------------------------//
//		heck standard Var																					//
//----------------------------------------------------------------------------------------------------------//

function chk($val,$chk)
{
	$chk_a	= "/^([0-9]+)$/"; 								//A. 檢查是不是數字
	$chk_b	= "/^([a-z]+)$/"; 								//B. 檢查是不是小寫英文	
	$chk_c	= "/^([A-Z]+)$/"; 								//C. 檢查是不是大寫英文
	$chk_d	= "/^([A-Za-z]+)$/"; 							//D. 檢查是不是全為英文字串
	$chk_e	= "/^([0-9A-Za-z]+)$/";							//E. 檢查是不是英數混和字串	
	$chk_f	= "/^([\x7f-\xff]+)$/"; 						//F. 檢查是不是中文
	$chk_g1	= "/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/"; 	//G1.檢查是不是電子信箱格式(允許"david.777-ok@aden.com")
	$chk_g2	= "/^[\w]*@[\w-]+(\.[\w-]+)+$/" ; 				//G2.檢查是不是電子信箱格式(僅允許 "david777ok@aden.com")
	$chk_h	= "/^[0][1-9]{1,3}[-][0-9]{6,8}$/"; 			//H. 檢查是不是市話格式(06-2261519)
	$chk_h2	= "/^[09]{2}[0-9]{8}$/";						//H2.檢查是不是手機格式(0911014253)
		
	$chk ="chk_".$chk;
	if(preg_match($$chk, $val, $resultArray)){return $resultArray[0];/*是*/ }else{return null; /*否*/ }
}

//----------------------------------------------------------------------------------------------------------//
//		Number FORMAT																						//
//----------------------------------------------------------------------------------------------------------//

//take ceiling	=> ex: ceil_dec(100.121,2)=100.13
function ceil_dec($v, $precision)
{
    $c = pow(10, $precision);
    return ceil($v*$c)/$c;
}

//chop off	=> ex: floor_dec(100.121,2)=100.12
function floor_dec($v, $precision)
{
    $c = pow(10, $precision);
    return floor($v*$c)/$c;
}

//format
function num($v,$t)
{
	$c	= $t=='y' ? 'NT$ ' : '';
    $c .= number_format($v, 0, '.' ,',');
    return $c;
}
?>