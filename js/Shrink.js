var $ofweidht=innerWidth;   //取得螢幕寬度
if($ofweidht>800)
{   //小於等於800
$(".collapse").removeClass("collapse in");
$(".collapse").addClass("collapse in");
$("#about1").attr('disabled',true); 
$("#about2").attr('disabled',true);  
}

 