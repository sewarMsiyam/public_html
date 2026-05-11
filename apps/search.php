<?php


class Search_App extends Intro_Apps 
{
	var $appname = null;
	var $base = null;
	var $img_path;
  
	function __construct($appname,$base,$img_path="")
	{
	global $intro;
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
		
	}
	

	function index()
	{	
		global $intro,$page,$array;
		$qry=$catnews='';
		$query = $intro->input->get_post('query');
	//	$query = preg_replace('/[^اأإ-يA-Za-z0-9 ]/ui', '',$query);
		 
		$lang=$intro->maa->lang;	
		$datnow=date('Y-m-d H:i:s');
		if($query=="" ) 
		{
			pHeader("");
			
			echo "<div class=\"container\">
					<div class=\"\"> 
					<div class='alert alert-warning'> {$intro->lang['resultsearch']} [ $query ] </div>
					<div class='alert alert-danger'> {$intro->lang['emptyseach']} </div>
					</div></div>";
			
			pFooter();			
			die();
		}
		else{		
			$rows_per_page = "70";
			if (!isset($page) or $page=="") $page=1;
			$nexlimit = $page * $rows_per_page - $rows_per_page;
			$title='name_'.$lang;
			$details='details_'.$lang;
			$extra_titles='extra_titles_'.$lang;
			
			if($lang=='en'){
				$code = 'code';
			}else{
				$code = 'code_ar';
			}
			
			if($query !=""){			
				$qry.=" and ($title  LIKE '%$query%' "
				." or $details  LIKE '%$query%' "
				." or $extra_titles  LIKE '%$query%' "
				." or $code  LIKE '%$query%') ";			
			}
			
			$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_products where status=1  $qry order by id desc limit $nexlimit,$rows_per_page  ")or die(mysql_error());
			$totrows = $intro->db->returned_rows;

			$sqlsum = $intro->db->query("SELECT * FROM ".PREFIX."_products where  status=1 $qry  order by id desc  ")or die(mysql_error());
			$totalrows = $intro->db->returned_rows;
			pHeader("");
				
			while($row2 =  $intro->db->fetch_assoc($sql2))
			{
				@extract($row2);
				$catids=$row2['catid'];
				$pid=$row2['id'];
				$name=$row2['name_'.$lang];
				$details=$row2['details_'.$lang];
				$dateadded=explode(" ",$date_add);
				$link=$intro->uri_links("products","View",$row2['id'],'');
				$img = $intro->base_url."uploads/news/$photo";
				if($lang == "ar"){
					$img=$intro->base_url."uploads/news/$img_ar";
					$code=$code;
						}else{
					$img=$intro->base_url."uploads/news/$photo";
					$code=$code_ar;
					}
					$urlcat=$intro->uri_links('products','Cat',$catids,$array['newscat'][$catids]);
			$catnews.=" <div class=\"col-lg-4 col-md-4 col-sm-6 pb-1\">
                        <div class=\"product-item bg-light mb-4\">
                            <div class=\"product-img position-relative overflow-hidden pt-3\">
                                <img class=\"img-fluid w-90\" src=\"$img\" alt=\"$name\">
                                <div class=\"product-action\">
                                    <a class=\"btn btn-outline-dark btn-square addToCart\" href=\"{$intro->href}cart/add/$pid\"><i class=\"fa fa-shopping-cart\"></i></a>
                                   <a class=\"btn btn-outline-dark btn-square\" href=\"$link\"><i class=\"fa fa-search\"></i></a>
                                </div>
                            </div>
                            <div class=\"text-center py-4\">
                                <a class=\"h6 text-decoration-none px-2 \" style='display:block;min-height:45px;' href=\"$link\">$name</a>
                                <div class=\"d-flex align-items-center justify-content-center mt-2\">
                                    <h4>$ $net_price</h4><h6 class=\"text-danger mx-3\"><del>".($price != $net_price?"<s>$$price</s> ":"")."</del></h6>
                                </div>
                                <div class=\"d-flex align-items-center justify-content-center mb-1\">
                                    <small class=\"fas fa-th-list text-primary mx-1\"></small> <a href='$urlcat'>  ".$array['newscat'][$catid]."</a>
                                </div>
                            </div>
                        </div>
                    </div>";			
			
			}
			$urlpage = $intro->uri_links("search","index",0,'');
			$paging= "<div class='pagination'><center  >".pagination3uri("$urlpage", $totalrows, $rows_per_page, $page)."</center></div>";
			
			echo"
			<div class=\"container\">
			<div class='alert alert-success'> {$intro->lang['resultsearch']} [ $query ] </div>
					<div class=\"row px-xl-5\"> 
					
					
					$catnews
					<div class='clear'></div>
					$paging
					</div>
				</div>
				
				";
			pFooter();
		}
	}
	function auto()
	{	
		global $intro,$page;
		
		$lang=$intro->maa->lang;	
		
		$query = trim($intro->input->get_post('query'));
		$term = trim($intro->input->get_post('term'));

		$name = 'name_'.$lang;
		$x=$intro->uri->segments[3];
			
			$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_products where status=1 and $name LIKE '%$term%' order by $name asc;");
		//	$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_products where status=1 ");
			$totrows = $intro->db->returned_rows;

			while($row2 =  $intro->db->fetch_assoc($sql2))
			{
				@extract($row2);
				$name = $row2['name_'.$lang];
				$link = $intro->uri_links("products","View",$row2['id'],'');
				
				$json[] = array("value" => $name , "label" => $name);
			}
		
		
	//	$data = array("suggestions" => $json);
		echo json_encode($json);
	
	}
}

?>