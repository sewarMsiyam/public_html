<?php

class Blog_App extends Intro_Apps 
{
	var $appname = null;
	var $app_caption = null;
	var $base = null;
	var $page_title = null;
	var $img_path;
  
	function __construct($appname,$base,$img_path="")
	{
		global $intro;
		
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
		$this->page_title = $appname;
		$this->app_caption = @$intro->lang[$appname.'_appname'];
		
		$lang = $intro->maa->lang;
		
	}
	function nav($extra="",$step=0){
		global $intro;
		
		$lang = $intro->maa->lang;
		
		return "<a href=\"{$intro->base_url}\">".$intro->lang["home"]."</a> > ".(($step==1) ? $this->app_caption : "<a href=\"{$this->base}/index\">{$this->app_caption}</a> " )." $extra";
				
	}
	
	function index(){
		global $intro;

		pHeader();
	
		$lang = $intro->maa->lang;
		
		$page = intval($intro->input->get('page'));
		if($page==0) $page = $intro->uri->page;
		
		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;
		
	
		$result = $intro->db->query("SELECT  * FROM ".PREFIX."_posts order by id desc limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sqlsum = $intro->db->query("SELECT id FROM ".PREFIX."_posts  ");
		$totalrows = $intro->db->returned_rows;
		
		while ($row = $intro->db->fetch_assoc($result)) {
			extract($row);
			
			$title = $row['title_'.$lang];
			$details = $row['details_'.$lang];
			
			$details = strip_tags($details);
			$details = _substr($details , 400);
			$details .= " ..... <a href=\"{$this->base}/View/$id\" style='font-size:12pt;color:#355efc;text-decoration:underline;' class='font-weight-bold '>more</a>";
			
			$blogs.="<div class=\"col-lg-6 col-md-6 col-sm-6 mb-3\"> 
						<div class='bg-light p-4 py-6'>
							<h5 class='font-weight-bold text-success pb-2' style='border-bottom:1px #eaeaea dotted;'>$title</h5>
							<div style='text-align:justify'>$details</div>
						</div>
						
					</div>";			
		
			
		}
		
		$urlpage = $intro->uri_links("blog","index",$page,'page');
		$paging = "<div class='pagination'><center>".pagination3uri("$urlpage/", $totalrows, $rows_per_page, $page)."</center></div>";
		?>
		
		<div class="container-fluid">
				<div class="row px-xl-5">
					<div class="col-12">
						<nav class="breadcrumb bg-light">
							<a class="breadcrumb-item text-dark" href="<?=$intro->base_url?>"><?=$intro->lang['home']?></a>
							<span class="breadcrumb-item active"><?=$intro->lang['blog']?></span>
						</nav>
					</div>
				</div>
		</div>
		<div class="container-fluid  pb-3">
			<div class="row px-xl-5">
				<?=$blogs?>
				<?=$paging?>
			</div>
						
		</div>	
		<?php	
	
		

		pfooter();

	}

	function View(){
		global $intro,$array,$discount_text,$discount_percent,$counttdiscount;
		
		$lang = $intro->maa->lang;
		
		$id=intval($intro->input->get('id'));
		if($id==0) $id=@intval($intro->uri->id);
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_posts where id=$id;");
		$row = $intro->db->fetch_assoc($sql);
		@extract($row);
		
		$title = $row['title_'.$lang];
		$details = $row['details_'.$lang];
		$details = stripslashes($details);
		$details = stripslashes($details);

		$this->page_title = $title;
		
		$image = "<img src='$image'>";

		pHeader();
		
		$featrued ='';
	$sql3 = $intro->db->query("SELECT * from ".PREFIX."_products where place=2 and status=1 order by id desc limit 3");
	while($row2 = $intro->db->fetch_assoc($sql3))
		{
			$name=$row2['name_'.$lang];
			$pid=$row2['id'];
			$catids=$row2['catid'];
			$photo=$row2['photo'];
			$price=$row2['price'];
			$img_ar=$row2['img_ar'];
			$code_ar=$row2['code_ar'];
			$discount=$row2['discount'];
		
			//$img = "{$intro->base_url}img.php?news=1&img=$photo&w=243&h=243";
			if($lang == "ar"){
			$img=$intro->base_url."uploads/news/$img_ar";
			
				}else{
			$img=$intro->base_url."uploads/news/$photo";
			
			}
			
			if($counttdiscount > 0 ){
				
				$new_price=discountall($discount_text,$discount_percent,$price);
				$price_view=$new_price['price_view'];
				$prev_price="<s class=\" mx-1 text-danger\" style=\"font-size:18px;\" ><del>  $ ".$new_price['prev_price']."</del>  </s>";
				
				
			}else{							
				$price_view= $price - $discount;	
				if( $discount != 0  ){
					
					$prev_price="<h6 class=\"text-danger mx-3\"><del><s>$ $price </s></del></h6>";	
				}			
			}
			
			$url2=$intro->uri_links('products','View',$pid,$name);
		$urlcat=$intro->uri_links('products','Cat',$catids,$array['newscat'][$catids]);
			$featrued.=" <div class=\" col-lg-12 pb-1\">
                <div class=\"product-item bg-light mb-4\">
                    <div class=\"product-img position-relative overflow-hidden pt-3\">
                        <img class=\"img-fluid w-90\" src=\"$img\" alt=\"\">
                        <div class=\"product-action\">
                            <a class=\"btn btn-outline-dark btn-square addToCart\" href=\"{$intro->href}cart/add/$pid\"><i class=\"fa fa-shopping-cart\"></i></a>
                            <a class=\"btn btn-outline-dark btn-square\" href=\"$url2\"><i class=\"fa fa-search\"></i></a>
                        </div>
                    </div>
                    <div class=\"text-center py-4\">
                        <a class=\"h6 text-decoration-none px-1\" style='display:block;min-height:45px;' href=\"$url2\">$name</a>
                        <div class=\"d-flex align-items-center justify-content-center mt-2\">
                            <h4>$$price_view </h4> $prev_price
                        </div>
						<div class=\"d-flex align-items-center justify-content-center mb-1\">
                                    <small class=\"fas fa-th-list text-primary mx-1\"></small> <a href='$urlcat'>  ".$array['newscat'][$catids]."</a>
                          </div>
                       
                    </div>
                </div>
            </div>";		
							
		
			
		}		
		
		?>
		<div class="container-fluid">
				<div class="row px-xl-5">
					<div class="col-12">
						<nav class="breadcrumb bg-light">
							<a class="breadcrumb-item text-dark" href="<?=$intro->base_url?>"><?=$intro->lang['home']?></a>
							<a class="breadcrumb-item text-dark" href="<?=$intro->uri_links('blog','index',0,'')?>"><?=$intro->lang['blog']?></a>
							<span class="breadcrumb-item active"><?=$title?></span>
						</nav>
					</div>
				</div>
		</div>
		 <!-- Shop Detail Start -->
    <div class="container-fluid pb-5">
        <div class="row px-xl-5">
             <div class="col-lg-9 col-md-8 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3 class='text-primary'><?=$title?></h3>
                    <p class='text-dark'><?=$dtime?></p>
					<p class="mb-4"><?=$details?></p>
                   
                </div>
            </div>
			<div class="col-lg-3 col-md-4 mb-30">
              <?=$featrued?>
            </div>
			
        </div>
      
    </div>
		
		<?php
	

		$update = $intro->db->query("UPDATE ".PREFIX."_posts set hits=hits+1 where id=$id;");

		pfooter();

	}

	
}//end class: posts
?>