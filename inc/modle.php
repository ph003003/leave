<?php
//請假資料表
function atten_list($atten_sn,$search,$get_page)
{
	global $conn;
	if($atten_sn)
	{
		global $atten_sn,$mnm,$comp_name,$start_time,$end_time,$hours,$insert_time,$reason_kind,$reason,$agree,$calendar_id;
		$sql = "SELECT atten_sn,mnm,comp_name,start_time,end_time,hours,insert_time,reason_kind,reason,agree,calendar_id FROM atten where atten_sn='".$atten_sn."' limit 1";
		conn();
		$res = $conn->query($sql);									
		if($res)
		{
			list($atten_sn,$mnm,$comp_name,$start_time,$end_time,$hours,$insert_time,$reason_kind,$reason,$agree,$calendar_id)=$res->fetch();
		}
		cls_conn();
	}
	else
	{
		global $list_mem,$count_mem,$start,$limit;
		
		$mnm			= ary($search,0);
		$start_time 	= ary($search,1);
		$mod 			= ary($search,2);
		$msn 			= ary($search,3);
		
		if($mod=='atten_get_on')$sql = "SELECT * FROM atten where agree='wait' ";
		else if($mod=='atten_record')$sql = "SELECT * FROM atten where agree!='wait' ";
		
		$sql .= " and date_save ='date_save'";
		if(isset($_SESSION['authority'])){
			if($_SESSION['authority']=='boss') {
				 //可看同公司的人$_session['comp_name']==row[comp_name]
			$sql .= " and comp_name ='".$_SESSION['comp_name']."'";
			}
			else if($_SESSION['authority']=='y') { 
				 //只可看 自己
				 $sql .= " and msn ='".$_SESSION['msn']."'";
				}
		}
		if($msn>0)
		{
			$sql .= " and msn ='".$msn."'";
		}	
		if($mnm)
		{
			$sql .= " and mnm  like '%".$mnm."%'";
		}
		if($start_time)
		{
			$sql .= " and start_time like '%".$start_time ."%'";
		}
		$sql .= " order by insert_time desc";
		

		
		conn();							
		$res = $conn->query($sql);	
		$list_mem = $res->fetchAll();
		$count_mem = count($list_mem);
		cls_conn();
	}
}

//會員資料表
function member($sn,$search,$get_page)
{
	global $conn;
	
 if($sn)
	{
		global $comp_name,$mid,$pwd,$mnm,$mail,$tel,$reg,$authority;
		$sql = "SELECT comp_name,mid,pwd,mnm,mail,tel,reg,authority FROM member where msn='".$sn."' limit 1";
		conn();
		$res = $conn->query($sql);									
		if($res)
		{
			list($comp_name,$mid,$pwd,$mnm,$mail,$tel,$reg,$authority)=$res->fetch();
		}
		cls_conn();
		}
	else
	{
		global $list_mem,$count_mem,$start,$limit;
		
		$comp_name	= ary($search,0);
		$mnm	= ary($search,1);
		$authority	= ary($search,2);
		if($authority!="n")$sql = "SELECT * FROM member where authority!='n'";
		if($authority=="n")$sql = "SELECT * FROM member where authority='n'";
		if($authority=="ad_license")$sql = "SELECT * FROM member where ad_license='n'";
		$sql .= " and date_save ='date_save'";
		$sql .= " and authority != 'wait_mail' and authority != 'ad' ";
		$sql .= " and msn != '".$_SESSION['msn']."' ";
		if(isset($_SESSION['authority'])){
			if($_SESSION['authority']=='boss') {
			//可看同公司的人$_session['comp_name']==row[comp_name]
			$sql .= " and comp_name ='".$_SESSION['comp_name']."'";
			}
		}
		if($comp_name)
		{
			$sql .= " and comp_name like '%".$comp_name."%'";
			
		}
		if($mnm)
		{
			$sql .= " and mnm like '%".$mnm."%'";
		}
		
		$sql .= "  ORDER BY `member`.`ad_license` DESC";
		
		conn();							
		$res = $conn->query($sql);	
		$list_mem = $res->fetchAll();
		$count_mem = count($list_mem);
		cls_conn();
	}
}


//分頁管理
function pagination($target,$count,$get_page,$search)
{
	global $pagination,$start,$limit;
		
	/* Setup vars for query */
	$adjacents=3;
	$page = 0;
	$status = 0;
	$targetpage = $target;					//your file name  (the name of this file)
	$limit = 10;								//how many items to show per page
	if((isset($get_page)) and ($get_page<>0) )
	{
		$page = $get_page;
		$start = ($page - 1) * $limit;		//first item to display on this page
	}
	else
	{
		$page = 0;
		$start = 0;							//if no page var is given, set start to 0
	}
	
	/* Setup page vars for display. */
	if ($page == 0) $page = 1;				//if no page var is given, default to 1.
	$prev = $page - 1;						//previous page is page - 1
	$next = $page + 1;						//next page is page + 1
	$lastpage = ceil($count/$limit);  		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;					//last page minus 1

	/* 
	Now we apply our rules and draw the pagination object. 
	We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	
	$pagination = "";
	if($lastpage > 1)
	{ 
		$pagination .= "<ul class='pagination pagination-lg'>";
		$pagination .= "<li><a href='#'>共".$count."筆</a></li>";
		
		//previous button
		if ($page > 1) 
		{
			$pagination.= "<li><a href='$targetpage?page=$prev&search=$search'><i class='fa fa-angle-left'></i></a></li>";
		}		
		else
		{
			$pagination.= "<li><a href='#'><i class='fa fa-angle-left'></i></a></li>";
		}
		
		//pages 
		if ($lastpage < 7 + ($adjacents * 2))
		{ 
			//not enough pages to bother breaking it up
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
				{
					$pagination.= "<li class='active'><a href='#'>$counter</a></li>";
				}				
				else
				{
					$pagination.= "<li><a href='$targetpage?page=$counter&search=$search'>$counter</a></li>";
				}				      
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2)) 
		{
			//enough pages to hide some			
			if($page < 1 + ($adjacents * 2))    
			{
				//close to beginning; only hide later pages
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
					{
						$pagination.= "<li class='active'><a href='#'>$counter</a></li>";
					}					
					else
					{
						$pagination.= "<li><a href='$targetpage?page=$counter&search=$search'>$counter</a></li>";  
					}
				}
				$pagination.= "<li><a href=\"#\">...</a></li>";
				$pagination.= "<li><a href='$targetpage?page=$lpm1&search=$search'>$lpm1</a></li>"; 
				$pagination.= "<li><a href='$targetpage?page=$lastpage&search=$search'>$lastpage</a></li>";
			}			
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				//in middle; hide some front and some back
				$pagination.= "<li><a href=\"$targetpage?page=1\">1</a></li>";
				$pagination.= "<li><a href=\"$targetpage?page=2\">2</a></li>";
				$pagination.= "<li><a href=\"#\">...</a>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
					{
						$pagination.= "<li><a class=\"active\" href=\"#\">$counter</a></li>";
					}					
					else
					{
						$pagination.= "<li><a href=\"$targetpage?page=$counter&search=$search\">$counter</a></li>";  
					}
				}
				$pagination.= "<li><a href=\"#\">...</a>";
				$pagination.= "<li><a href=\"$targetpage?page=$lpm1&search=$search\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetpage?page=$lastpage&search=$search\">$lastpage</a></li>";   
			}			
			else
			{
				//close to end; only hide early pages
				$pagination.= "<li><a href=\"$targetpage?page=1&search=$search\">1</a></li>";
				$pagination.= "<li><a href=\"$targetpage?page=2&search=$search\">2</a></li>";
				$pagination.= "<li><a href=\"#\">...</a></li>";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
					{
						$pagination.= "<li><a class=\"active\" href=\"#\">$counter</a></li>";
					}
					else
					{
						$pagination.= "<li><a href=\"$targetpage?page=$counter&search=$search\">$counter</a></li>";    
					}
				}
			}
		}

		//next button
		if ($page < $counter - 1) 
		{
			$pagination.= "<li><a href='$targetpage?page=$next&search=$search'><i class='fa fa-angle-right'></i></a></li>";
		}		
		else
		{
			$pagination.= "<li><a href='#'><i class='fa fa-angle-right'></i></a></li>";
		}		  
		$pagination.= "</ul>\n";
	}
	elseif($lastpage==1)
	{
		$pagination .= "<ul class='pagination pagination-lg'>";
		$pagination .= "<li><a href='#'>共".$count."筆</a></li>";
		$pagination .= "</ul>\n"; 
	}
}
?>