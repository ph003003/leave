//------------------------------------------------------------------------------------------//
//		AJAX-POST																			//
//------------------------------------------------------------------------------------------//

	$('#myform').submit(function () {
		
		$this = $(this);
		
		var ct = $(this).attr('ct');
		
		if (ct === undefined || ct === null)
		{
			var r = true;
		}
		else
		{
			switch(ct){
				
				 case "del":
			      text = "確定要刪除?";
			      break;  
			    case "add":
			      text = "確定要新增?";
			      break;  
			      
			    case "edit":
			      text = "確定要更新?";
			      break;
			    
			    default:
			      text = "確定要送出嗎?";
			}
			var r = confirm(text);
		}
		
		if (r == true)
		{
		    var txt=$("#submit").html();
			var loadimg='處理中，請稍待片刻 <img src="img/loading_s.gif"/>';
			
			$("#submit").removeClass("btn-primary");
			$("#submit").addClass("btn-disabled");
			$("#submit").attr("disabled",true);
			$("#submit").html(loadimg);
			
			var wk=$("#myform").attr('name');
							
			$.ajax({
		        url: $(this).attr('action'),
		        data: $('#myform').serialize() + "&wk=" + wk,
		        type:"POST",
		        dataType:"json",

		        success: function(data){
		        	
					if(data.url)
					{
						var bk=data.url;
						if(bk.match('.php?')==null)
						{
							bk+=".php";
						}
						if(data.msg)
						{
							alert(data.msg);
						}
						location.href=bk;
					}
					else
					{
						switch(data.type){
	    		
						    case "success":
						      atype = '.alert-success';
						      break;
						      
						    case "false":
						      atype = '.alert-danger';
						      break;
						      
						    case "dismissable":
						      atype = '.alert-dismissable';
						      break;
						      
						    case "info":
						      atype = '.alert-info';
						      break;
						      		      		      
						    default:
						      atype = '.alert-warning';
						}
						$(atype).text(data.msg).fadeIn().delay(4000).fadeOut();
						if(data.cls==='y')
						{
							$('#myform')[0].reset();
						}
						$("#submit").removeClass("btn-disabled");
						$("#submit").addClass("btn-"+data.btn);
						$("#submit").removeAttr("disabled")
						$("#submit").html(txt);
					}					
		        },

		         error:function(xhr, ajaxOptions, thrownError){ 
		            alert(xhr.status); 
		            alert(thrownError); 
		         }
		    });
		}
			
		return false;
	});
	

	$('#myform2').submit(function () {
		
		$this = $(this);
		
		var ct = $(this).attr('ct');
		
		if (ct === undefined || ct === null)
		{
			var r = true;
		}
		else
		{
			switch(ct){
				
				 case "del":
			      text = "確定要刪除?";
			      break;  
			    case "add":
			      text = "確定要新增?";
			      break;  
			      
			    case "edit":
			      text = "確定要更新?";
			      break;
			    
			    default:
			      text = "確定要送出嗎?";
			}
			var r = confirm(text);
		}
		
		if (r == true)
		{
		    var txt=$("#submit2").html();
			var loadimg='處理中，請稍待片刻 <img src="img/loading_s.gif"/>';
			
			$("#submit2").removeClass("btn-primary");
			$("#submit2").addClass("btn-disabled");
			$("#submit2").attr("disabled",true);
			$("#submit2").html(loadimg);
			
			var wk=$("#myform2").attr('name');
							
			$.ajax({
		        url: $(this).attr('action'),
		        data: $('#myform2').serialize() + "&wk=" + wk,
		        type:"POST",
		        dataType:"json",

		        success: function(data){
		        	
					if(data.url)
					{
						var bk=data.url;
						if(bk.match('.php?')==null)
						{
							bk+=".php";
						}
						if(data.msg)
						{
							alert(data.msg);
						}
						location.href=bk;
					}
					else
					{
						switch(data.type){
	    		
						    case "success":
						      atype = '.alert-success';
						      break;
						      
						    case "false":
						      atype = '.alert-danger';
						      break;
						      
						    case "dismissable":
						      atype = '.alert-dismissable';
						      break;
						      
						    case "info":
						      atype = '.alert-info';
						      break;
						      		      		      
						    default:
						      atype = '.alert-warning';
						}
						$(atype).text(data.msg).fadeIn().delay(4000).fadeOut();
						if(data.cls==='y')
						{
							$('#myform2')[0].reset();
						}
						$("#submit2").removeClass("btn-disabled");
						$("#submit2").addClass("btn-"+data.btn);
						$("#submit2").removeAttr("disabled")
						$("#submit2").html(txt);
					}					
		        },

		         error:function(xhr, ajaxOptions, thrownError){ 
		            alert(xhr.status); 
		            alert(thrownError); 
		         }
		    });
		}
			
		return false;
	});

//------------------------------------------------------------------------------------------//
//		AJAX-GET																			//
//------------------------------------------------------------------------------------------//
$(".go").click(function()
		{
			var ct = $(this).attr('ct');
			var bk = $(this).attr('bk');
			if(bk.match('.php?')==null)
			{
				bk+=".php";
			}
			if (ct === undefined || ct === null)
			{
				var r = true;
			}
			else
			{
				switch(ct){

				    case "off":
				      text = "確定要停權?";
				      break;
				      
				    case "on":
				      text = "確定要重新上線?";
				      break;
				    
				    case "del":
				      text = "確定要刪除?";
				      break;
					  
					case "atten_agree":
					  text = "確定要 同意 該申請?";
					  break;
								  
					case "atten_veto":
					  text = "確定要 否決 該申請?";
					 break;  

					 case "mem_up":
					  text = "一公司只有一個主管，其他同公司主管會被更換成員工，確定要變更主管?";
					 break;  
				  
				    default:
				      text = "確定要送出嗎?";
				}
				var r = confirm(text);
			}
			
			if (r == true)
			{	
			var loadimg='處理中，請稍待片刻 <img src="img/loading_s.gif"/>';
			$(".go").removeClass("btn-primary");
			$(".go").addClass("btn-disabled");
			$(".go").attr("disabled",true);
			$(".go").html(loadimg);
				
				var url = $(this).attr('link');
				
				$.getJSON(url,function(data){
					if(data.msg)
					{
						alert(data.msg);
					}
					if(data.type=='success')
					{
						location.href=bk;
					}
			  	});
		  	}
		  	return false;
		});