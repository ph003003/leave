<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>請假申請</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" href="css/style.css" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.css"/>
<style type="text/css">
body{
font-family:'微軟正黑體';
}
</style>
</head>
<body>

<div class="container">
<div class="row clearfix">
<div class="col-md-12 column">
	<h2>
	請假申請<br>
	</h2>
</div> 
</div> 
</div> 

<div class="container">
<div class="row clearfix">
<div class="col-md-12 column">
<div class="jumbotron well">
	<form>
		<div>
		<div>
			<h4>缺勤時間</h4>
			<input type="hidden" id="event-type" value="FIXED-TIME">
			<h4>從<input type="text"   class="event-hours" id="event-start-time" placeholder="Event Start Time" autocomplete="off" required="required" /></br>
			到<input type="text" class="event-hours" id="event-end-time" placeholder="Event End Time" autocomplete="off" required="required" />
			<br/>時數<input type="number" min="1" id="hours" placeholder="時數" autocomplete="off" required="required" />	
			<br/>特休剩餘時數<input type="number" id="special" value=<?=$_SESSION['special']?>  disabled />
			</h4>
			ps,時數自動計算不一定正確，請多加確認。
		</div>
		</div>
		<div>
			<h4>事由類別</h4>
			<select  id="reason_kind">
			<option value="事假">事假</option>
			<option value="病假">病假</option>
			<option value="婚假">婚假</option>
			<option value="喪假">喪假</option>
			<option value="產假">產假</option>
			<option value="特休">特休</option>
			<option value="公假">公假</option>
			<option value="其他">其他</option>
			</select>
		</div>
		<div>
			<h4>事由詳細(非必填)</h4>
			<textarea id="reason" class="control" rows="2" name='reason' style="width:80%; height:150px;"></textarea>
		</div>
		<input type="button" value="送出申請" class="btn btn-primary btn-large" id="create-event" >
	</form>
</div>
</div>
</div>
</div>
</body>
<!-- Page JS  Scripts -->
<script src="https://use.fontawesome.com/09faeb38dd.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.js"></script>
<!-- Page JS (Customer) -->
<script src="js/event_start_end_time.js"></script>
</html>