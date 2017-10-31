<?php
//連線資料庫
function conn()
{
	$host		= "localhost";
	$root		= "root";
	$root_pwd	= "";
	$db			= "test";
	
	$dsn_db='mysql:host='.$host.';dbname='.$db.';charset=utf8';
	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
	
	global $conn;
	try{$conn = new PDO($dsn_db,$root,$root_pwd);$conn -> exec("set names utf8");}
	catch(PDOException $e){die( "oops!DB error!" /*"DB-table ERROR: ". $e->getMessage()*/) ;}	
}

//關閉連線
function cls_conn()
{
	$res	= null;
	$conn	= null;
}

//其他預設模組
require_once('inc/ini.php');
require_once('inc/modle.php');
?>