//------------------------------------------------------------------------------------------//
//		檢查統編																			//
//------------------------------------------------------------------------------------------//

	//統編規則
	function isValidGUI(taxId) {
	    var invalidList = "00000000,11111111";
	    if (/^\d{8}$/.test(taxId) == false || invalidList.indexOf(taxId) != -1) {
	        return false;
	    }

	    var validateOperator = [1, 2, 1, 2, 1, 2, 4, 1],
	        sum = 0,
	        calculate = function(product) { // 個位數 + 十位數
	            var ones = product % 10,
	                tens = (product - ones) / 10;
	            return ones + tens;
	        };
	    for (var i = 0; i < validateOperator.length; i++) {
	        sum += calculate(taxId[i] * validateOperator[i]);
	    }

	    return sum % 10 == 0 || (taxId[6] == "7" && (sum + 1) % 10 == 0);
	};

	//檢查統編是否正確
	function chk_vat() {
		
		var vat	= $('#vat').val();
		
		if(isValidGUI(vat))
		{
			$('.alert-danger').fadeOut();
		}
		else
		{
			$('.alert-danger').text('請輸入正確的統一編號').fadeIn();
			$("#vat").focus();
		}
	}

//------------------------------------------------------------------------------------------//
//		檢查Email																			//
//------------------------------------------------------------------------------------------//

	function chk_mail() {
		
		var mail	= $('#mail').val();
		
		re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9])+$/; //email正確格式
		if(re.test(mail))
		{
			$('.alert-danger').fadeOut();
		}		
		else if (mail== "")
		{
			$('.alert-danger').fadeOut();			
		}
		else
		{
			$('.alert-danger').text('Email格式錯誤').fadeIn();
			$("#mail").focus();
		}
	}

//------------------------------------------------------------------------------------------//
//		檢查密碼																			//
//------------------------------------------------------------------------------------------//

	function chk_pwd() {
		
		var pwd		= $('#pwd').val();
		var pwd2	= $('#pwd2').val();
		
		if(pwd !== pwd2)
		{
			$('.alert-danger').text('密碼不相符，請再次確認').fadeIn();
		}
		else
		{
			$('.alert-danger').fadeOut();
		}
	}
	
//------------------------------------------------------------------------------------------//
//		檢查會員是否重複(mno/mid)															//
//------------------------------------------------------------------------------------------//

	function chk_mem(chk) {
		
		var id	= '#'+chk;
		var vl	= $(id).val();
				
		if(vl)
		{
			var url='edit.php?wk=check_mem&chk='+chk+'&vl='+vl;
			
			$.getJSON(url,function(data){
		    	if(data.result)
				{			
					$('.alert-danger').text(data.result).fadeIn();
					$(id).focus();
				}
				else
				{
					$('.alert-danger').fadeOut();
				}
		  	});
		}
		else
		{
			$('.alert-danger').fadeOut();
		}		
	}