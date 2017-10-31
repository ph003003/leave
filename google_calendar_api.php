<?php
require_once('settings.php');//存入各項初始變數
class GoogleCalendarApi
{
	
	public function GetAccessToken($client_id, $redirect_uri, $client_secret, $code) 
	{	
		$url = 'https://accounts.google.com/o/oauth2/token';			
		$curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to receieve access token');
		return $data;
	}
	public function GetRefreshedAccessToken($client_id, $refresh_token, $client_secret) 
	{	
		$url_token = 'https://accounts.google.com/o/oauth2/token';			
		$curlPost = 'client_id=' . $client_id . '&client_secret=' . $client_secret . '&refresh_token='. $refresh_token . '&grant_type=refresh_token';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_token);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);	
		$data = json_decode(curl_exec($ch), true);	//print_r($data);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to refresh access token');
		return $data;
	}
	public function GetUserCalendarTimezone($access_token) 
	{
		$url_settings = 'https://www.googleapis.com/calendar/v3/users/me/settings/timezone';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_settings);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token));	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	
		$data = json_decode(curl_exec($ch), true); //echo '<pre>';print_r($data);echo '</pre>';
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to get timezone');
		return $data['value'];
	}
public function CreateCalendarEvent($calendar_id, $summary, $start, $end, $event_timezone, $access_token,$reason) 
	{
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';
		$curlPost = array('summary' => $summary,'description'=>$reason);
		$curlPost['start'] = array('dateTime' => $start, 'timeZone' => $event_timezone);
		$curlPost['end'] = array('dateTime' => $end, 'timeZone' => $event_timezone);
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to create event');
		return $data;
	}

	public function GetCalendarsList($access_token) 
	{
		$url_parameters = array();
		$url_parameters['fields'] = 'items(id,summary,timeZone)';
		$url_parameters['minAccessRole'] = 'owner';
		$url_calendars = 'https://www.googleapis.com/calendar/v3/users/me/calendarList?'. http_build_query($url_parameters);
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_calendars);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token));	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	
		$data = json_decode(curl_exec($ch), true); //echo '<pre>';print_r($data);echo '</pre>';
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to get calendars list');
		return $data['items'];
	}
	
	public function  try_move_calendarEvent($calendarId, $eventId, $access_token) 
	{
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendarId . '/events/' . $eventId . '/move?destination=soh5vonklb81rkdfbuqugl60ag@group.calendar.google.com';
		//POST https://www.googleapis.com/calendar/v3/calendars/' . $calendarId . '/events/' . $eventId . '/move
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to create event');
		return $data;
	}
	public function try_list_calendarEvent($access_token)
	{
		//https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list#auth
		$calendarId= 'tpefovbf4ge5vas7dlftu8ucfo@group.calendar.google.com';
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/'.$calendarId.'/events';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error :Error :Error :');
		return $data;
	}
	
	public function try_delete_calendarEvent($access_token) 
	{
		$calendarId	='tpefovbf4ge5vas7dlftu8ucfo@group.calendar.google.com';
		$eventId ='1496906780539';
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/'.$calendarId.'/events/'.$eventId;
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code = 204) throw new Exception('NO MORE');
		return $data;
	}
	public function create_calendar($summary,$access_token) 
	{
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars';
		$curlPost = array('summary' => $summary);
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to create event');
		return $data['id'];
	}
	public function try_list_calendarList($access_token)
	{
		//https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list#auth
		$url_events = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error :Error :Error :');
		return $data;
	}
	public function try_delete_calendarList($access_token) 
	{
		//手冊 https://developers.google.com/google-apps/calendar/v3/reference/calendarList/delete
		$url_events = 'https://www.googleapis.com/calendar/v3/users/me/calendarList/'.$calendarid;
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		 curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code = 204) throw new Exception('NO MORE');
		return $data;
	}
	//authorization
	public function try_list_calendarAuthorization($access_token) 
	{
		//https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list#auth
		$calendarId= 'tpefovbf4ge5vas7dlftu8ucfo@group.calendar.google.com';
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/'.$calendarId.'/acl';
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error :Error :Error :');
		return $data;
	}
	public function add_calendarAuthorization($calendar_id,$role,$mail,$access_token) 
	{
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/'.$calendar_id.'/acl';
		$curlPost = array('role' => $role);
		$curlPost['scope'] = array('type' => "user" , 'value' => $mail);
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to create event');
		return $data;
	}
	public function try_patch_calendarAuthorization($access_token)
	{
		/*
		PUT 替換(新增或完整更新)，此例中如果image參數沒有傳，會被更新成空：
		PATCH 部分更新，此例中如果image參數沒有傳，就不會被更新：
		*/
		$calendarId ='tpefovbf4ge5vas7dlftu8ucfo@group.calendar.google.com';
		$ruleId = 'user:ph001001001@gmail.com';
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/'.$calendarId.'/acl/'.$ruleId;
		$curlPost = array('role' => "owner");
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PATCH");	
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) throw new Exception('Error : Failed to create event');
		return $data;
	}
}
?>