// Selected time should not be less than current time
function AdjustMinTime(ct) {
	var dtob = new Date(),
  		current_date = dtob.getDate(),
  		current_month = dtob.getMonth() + 1,
  		current_year = dtob.getFullYear();
  			
	var full_date = current_year + '-' +
					( current_month < 10 ? '0' + current_month : current_month ) + '-' + 
		  			( current_date < 10 ? '0' + current_date : current_date );

	if(ct.dateFormat('Y-m-d') == full_date)
		this.setOptions({ minTime: 0 });
	else 
		this.setOptions({ minTime: true });
}
// DateTimePicker plugin : http://xdsoft.net/jqplugins/datetimepicker/
$("#event-start-time, #event-end-time").datetimepicker({ step: 30});
$("#event-date").datetimepicker({ timepicker: true});

$(" #event-date").hide()
$("#event-type").on('change', function(e) {
	if($(this).val() == 'ALL-DAY') {
		$("#event-date").show();
		$("#event-start-time, #event-end-time").hide();
	}
	else {
		$("#event-date").hide(); 
		$("#event-start-time, #event-end-time").show();
	}
});
 
// Since we are settings event details before user authorization, we need to pass event details to the login url with the "state" parameter
// Google will pass this "state" parameter in the redirect url script
$("#create-event").on('click', function(e) {

   if ($("#event-title").val() == "")
   {
      alert("請填入姓名");
      return false;
   }
   if ($("#event-start-time").val() == "")
   {
      alert("請填入起始時間！");
      return false;
   }
 if ($("#event-end-time").val() == "")
 {
      alert("請填入結束時間");
      return false;
   }
	if(Date.parse($("#event-start-time").val()).valueOf() > Date.parse($("#event-end-time").val()).valueOf())
	{
	alert("注意開始時間不能晚於結束時間！");
		return false;
	}
			
	if ($("#reason_kind").val()=="特休" && parseInt($("#hours").val()) > parseInt($("#special").val()))
	{
	  alert("特休時數不足，可以嘗試刪除未通過之特休申請，或請其他假種。");
      return false;
   }	

	hours=$("#hours").val();
	 if ($("#reason_kind").val()=="病假" && hours <2 ||$("#reason_kind").val()=="病假" && $("#hours").val()%2 != 0 )
	 {
		 confirm("確定要提出申請，病假的請假時數最低為 2 小時，且須為 2 小時的倍數"); 
   }

	 if ($("#reason_kind").val()=="特休" && hours <4 ||$("#reason_kind").val()=="特休" && $("#hours").val()%4 != 0 )
	 {
      confirm("確定要提出申請，特休的請假時數最低為 4 小時，且須為 4 小時的倍數");
   }	   
			
	txt=$("#create-event").val();
	var loadimg='loadimg.....';
	$("#create-event").removeClass("btn btn-primary btn-large");
	$("#create-event").addClass("btn btn-disabled btn-large");
	$("#create-event").attr("disabled",true);
	$("#create-event").val(loadimg);
	
	var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/,
		error = 0,
		parameters,
		state_parameter;

	$(".input-error").removeClass('input-error');

	if(!blank_reg_exp.test($("#event-title").val())) 
	{
		$("#event-title").addClass('input-error');
		error = 1;
	}

	if($("#event-type").val() == 'FIXED-TIME') 
	{
		if(!blank_reg_exp.test($("#event-start-time").val())) 
		{
			$("#event-start-time").addClass('input-error');
			error = 1;
		}		

		if(!blank_reg_exp.test($("#event-end-time").val())) {
			$("#event-end-time").addClass('input-error');
			error = 1;
		}
	}
	else if($("#event-type").val() == 'ALL-DAY') 
	{
		if(!blank_reg_exp.test($("#event-date").val()))
			{
			$("#event-date").addClass('input-error');
			error = 1;
		}	
	}

	if(error == 1)
		return false;

	if($("#event-type").val() == 'FIXED-TIME') 
	{
		// If end time is earlier than start time, then interchange them
		if($("#event-end-time").datetimepicker('getValue') < $("#event-start-time").datetimepicker('getValue'))
			{
			var temp = $("#event-end-time").val();
			$("#event-end-time").val($("#event-start-time").val());
			$("#event-start-time").val(temp);
		}
	}

	// Event details
	parameters = { 	title: encodeURI($("#event-title").val()), 
					event_time: {
						start_time: $("#event-type").val() == 'FIXED-TIME' ? $("#event-start-time").val().replace(' ', 'T') + ':00' : null,
						end_time: $("#event-type").val() == 'FIXED-TIME' ? $("#event-end-time").val().replace(' ', 'T') + ':00' : null,
						event_date: $("#event-type").val() == 'ALL-DAY' ? $("#event-date").val() : null
					},
					all_day: $("#event-type").val() == 'ALL-DAY' ? 1 : 0,
				};
	 
	// To pass the "state" parameter, JSON encode and base64 encode the event details
	state_parameter = btoa(JSON.stringify(parameters));
	
     reason = encodeURI($("#reason").val()); 
	 reason_kind = encodeURI($("#reason_kind").val());
		 $.ajax({
            type: "GET",
            url: "atten_edit.php",
            dataType: "json",
            data: {
				wk: 'atten_add', 
                reason: reason, 
				reason_kind: reason_kind,
				hours:$("#hours").val(),
                state:state_parameter   
  
            },
         success: function(data) {//success 表示成功後 data回送數據
                if (data.name11) {
                      alert(data.name11);
					 	window.location.reload();
                }                   
            },
            error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
            }
        })

});
/*
下列為時數計算
*/
$(".event-hours").on('blur', function(e) {
	if ($("#event-start-time").val() != "" && $("#event-end-time").val() !="")
	{
		var starttime = new Date($("#event-start-time").val());
		var endtime = new Date($("#event-end-time").val());
		var s3 = endtime.getTime() - starttime.getTime();

		var tianshu = s3%(24*60*60*1000)/(60*60*1000);
		
		var am_time =new Date( $("#event-start-time").val().split(" ")[0]+" 12:00" );
		var pm_time =new Date( $("#event-end-time").val().split(" ")[0]+" 13:30" );
		
		if ( (s3/(24*60*60*1000)) >1 )
		{
			tianshu=tianshu+Math.ceil(s3/(24*60*60*1000)*8);
		}

		if(am_time.getTime() > starttime.getTime() && pm_time.getTime() < endtime.getTime() )
		{
			tianshu=tianshu-1.5;

		}
			
		$("#hours").val(tianshu);
		return false;
   }
});


