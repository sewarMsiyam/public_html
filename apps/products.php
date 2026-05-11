<?php

class Products_App extends Intro_Apps 
{
	var $appname = null;
	var $base = null;
	var $header_data = array();
	var $img_path;
  
	function __construct($appname,$base,$img_path="")
	{
	global $intro;
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
		
		$this->header_data = array();
	}
	
		
	function index()
	{
	
		global $intro,$id,$page,$array;
		$catnews ="";
		$lang=$intro->maa->lang;
		$id = intval($intro->input->get('id'));
		if($id==0) $id = @intval($intro->uri->id);
		$catid=$id;
	
		$sql8 = $intro->db->query("SELECT * FROM ".PREFIX."_products_cat  order by catid desc ");
		$totrows = $intro->db->returned_rows;

		$i=1;
		while($row2 = $intro->db->fetch_assoc($sql8))
		{
			$catname=$row2['catname_'.$lang];
			$catid=$row2['catid'];
			$catimage=$row2['catimage'];
			
			$url2=$intro->uri_links('products','Cat',$catid,$catname);
			##############
			$catnews .="
						<div class=\" col-lg-3 col-md-4 col-sm-6 pb-1\">
                <div class=\"product-item bg-light mb-4\">
                    <div class=\"product-img position-relative overflow-hidden pt-3\">
                        <img class=\"img-fluid w-90\" src=\"{$intro->base_url}uploads/news/$catimage\" style='max-height:250px;' alt=\"\">
                       
                    </div>
                    <div class=\"text-center py-4\">
                        <a class=\"h6 text-decoration-none px-1\" style='display:block;' href=\"$url2\">$catname</a>
                         
                    </div>
                </div>
            </div>";
		
			$i++;	
		}
		$catnews .="<div class='clear'></div>";
		
	
		$this->header_data = array('title'=>'Products');
		
		pHeader(array('seo_title'=>"Products" ));
		?>
		  <div class="container pt-3 pb-3">
				<h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary px-3"><?=$intro->lang['categories']?></span></h2>
				<div class="row px-xl-5">
					<?=$catnews?>
		   
				</div>
			</div>
   
		<?php
		
		pFooter();
	}
	
	
	
	function Cat()
	{
	
		global $intro,$id,$page,$array,$prod_cat,$stylecatid,$discount_text,$discount_percent,$counttdiscount;
		
		//bug fix
		$qry = '';
		
		$catnews ="";
		$lang=$intro->maa->lang;
		$p = trim($intro->input->get('p'));
		$p = '';
		$sh_page = intval($intro->input->get('sh_page'));
		$id = intval($intro->input->get('id'));
		if($id==0) $id = @intval($intro->uri->id);
		$catid=$id;
		$stylecatid=$catid;
		
		$page = intval($intro->input->get('page'));
		if($page==0) $page = $intro->uri->page;
		
		
		$rows_per_page = 16;
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$datnow=date("Y-m-d H:i:s");
		if($p=='all' || $p=='' ){
			$qry.="";
		}else{
			$px=explode('-',$p);
			$pxmin=$px[0];
			$pxmax=$px[1];
			$qry.=" and (price-discount) >= '$pxmin' and (price-discount) <= '$pxmax'  ";
		}
		
		$sql8 = $intro->db->query("SELECT * FROM ".PREFIX."_products where  $lang=1 and catid=$catid and status=1 $qry order by id desc limit $nexlimit,$rows_per_page ");
		$totrows = $intro->db->returned_rows;
		
		$sqlsum = $intro->db->query("SELECT * FROM ".PREFIX."_products  where $lang=1 and catid=$catid  and status=1 $qry ");
		$totalrows = $intro->db->returned_rows;
		
		$i=1;
		while($row2 = $intro->db->fetch_assoc($sql8))
		{
			
			$prev_price=$price_view='';
			
			$name=$row2['name_'.$lang];
			$pid=$row2['id'];
			$photo=$row2['photo'];
			$catids=$row2['catid'];
			$price=$row2['price'];
			$img_ar=$row2['img_ar'];
			$code=$row2['code'];
			$code_ar=$row2['code_ar'];
			$net_price=$row2['net_price'];
			$discount=$row2['discount'];
		//	$discount_percent=$row2['discount_percent'];
			
			
			$url2=$intro->uri_links('products','View',$pid,$name);
			##############
				$img = $intro->base_url."uploads/news/$photo";
			if($lang == "ar"){
				$img=$intro->base_url."uploads/news/$img_ar";
				$code = $code_ar;
			}else{
				$img=$intro->base_url."uploads/news/$photo";
				//$code = $code;
			}
			$urlcat=$intro->uri_links('products','Cat',$catids,$array['newscat'][$catids]);
			
		
			
			if($counttdiscount > 0 ){
				
				$new_price=discountall($discount_text,$discount_percent,$price);
				$price_view=$intro->maa->Currency_amount($new_price['price_view']);
				
				$converted=$intro->maa->Currency_amount($new_price['prev_price']);
				$prev_price="<h6 class=\"text-danger mx-3\"><del>$converted</del></h6>";
				
				
			}else{							
				$price_view= $intro->maa->Currency_amount( $price - $discount );	
				if( $discount != 0  ){
					$converted=$intro->maa->Currency_amount($price);
					$prev_price="<h6 class=\"text-danger mx-3\"><del>$converted</del></h6>";	
				}			
			}
			
			
			
			
	
			$catnews.=" <div class=\"col-lg-3 col-md-4 col-sm-6 pb-1\">
			
			
                        <div class=\"product-item bg-light mb-4\">
                            <div class=\"product-img position-relative overflow-hidden pt-3\">
					
                                <img class=\"img-fluid w-90\" src=\"$img\" alt=\"$name\">
                                <div class=\"product-action\">
                                    <a class=\"btn btn-outline-dark btn-square addToCart\" data-toggle=\"modal\" data-target=\"#staticBackdrop\"  href=\"{$intro->href}cart/add/$pid\"><i class=\"fa fa-shopping-cart\"></i></a>
                                   <a class=\"btn btn-outline-dark btn-square\" href=\"$url2\"><i class=\"fa fa-search\"></i></a>
                                </div>
                            </div>
                            <div class=\"text-center py-4\">
                                <a class=\"h6 text-decoration-none px-2 \" style='display:block;min-height:45px;' href=\"$url2\">$name</a>
                            
								
								<div class=\"d-flex align-items-center justify-content-center mt-2\">
										<h4>$price_view </h4>$prev_price
									</div>
						
                                <div class=\"d-flex align-items-center justify-content-center mb-1\">
                                    <small class=\"fas fa-th-list text-primary mx-1\"></small> <a href='$urlcat'>  ".$array['newscat'][$catid]."</a>
                                </div>
                            </div>
                        </div>
                    </div>";			
		
			$i++;	
		}
		$catnews .="<div class='clear'></div>";
		
		$urlpage = $intro->uri_links("products","Cat",$catid,$array['newscat'][$catid]);
		
		$paging = "<div class='pagination justify-content-center m-auto' ><center>".pagination3uri("$urlpage/", $totalrows, $rows_per_page, $page)."</center></div>";
	
		$this->base = "{$intro->base_url}index.php/$urlpage";
		
		$this->header_data = array('title'=>'news');
		
		pHeader(array('seo_title'=>$array['newscat'][$catid] ));
	
	
echo "<!-- Breadcrumb Start -->
    <div class=\"container-fluid\">
        <div class=\"row px-xl-5\">
            <div class=\"col-12\">
                <nav class=\"breadcrumb bg-light mb-30\">
                    <a class=\"breadcrumb-item text-dark\" href=\"{$intro->url}\">{$intro->lang['home']}</a>
                    <a class=\"breadcrumb-item text-dark\" href=\"{$intro->uri_links("products","index",0,'')}\">{$intro->lang['products']}</a>
                    <span class=\"breadcrumb-item active\">".$array['newscat'][$catid]." </span>
                </nav>
            </div>
        </div>
    </div>";
	
	echo " 
	<div class=\"container\">
        <div class=\"row px-xl-5\">
          <div class=\"col-lg-12 col-md-8\">
			<div class=\"row pb-3\">
			$catnews 
			$paging
			 </div>
	 </div>
            <!-- Shop Product End -->
        </div>
    </div>
    <!-- Shop End -->";
		
		TableClose();
		
		pFooter();
	}
	
	


	function View()
	{
		global $intro,$home,$array,$discount_text,$discount_percent,$counttdiscount;
       $lang=$intro->maa->lang;
		$id=intval($intro->input->get('id'));
		if($id==0) $id=@intval($intro->uri->id);
		$white =$date=$comment="";
		
		$dis_perc=$discount_percent;
		
		$sqlv = $intro->db->query("update ".PREFIX."_products set views=views+1 where id=$id ");

		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_products where $lang=1 and status=1  and id=$id ");
		if($intro->db->returned_rows == 0)
		header("Location: {$intro->base_url}");
		
		$row =  $intro->db->fetch_assoc($sql);
		@extract($row);	
		
		
		$intro->db->query("update ".PREFIX."_products set hits = hits+1 where id=$id");
		$urlpage = $intro->uri_links("products","View",$id,'');
		############################ SEO ##############
		#links for SEO and Social Networks
		$news_url = $intro->option['site_url'].$urlpage;
		$news_img = $intro->option['site_url']."{$intro->base_url}uploads/news/$photo";
		
		$details=$row['details_'.$lang];
		$name=$row['name_'.$lang];
		$title2=$row['title2_'.$lang];
		
		
		
			
		
		if($counttdiscount > 0 ){
				
				$new_price=discountall($discount_text,$dis_perc,$price);
				$price_view=$intro->maa->Currency_amount($new_price['price_view']);
				$prev_price="<s class=\" mx-1 text-danger\" style=\"font-size:18px;\" ><del>   ".$intro->maa->Currency_amount($new_price['prev_price'])."</del>  </s>";
				
				
			}else{							
				$price_view= $intro->maa->Currency_amount($price - $discount);	
				if( $discount != 0  ){
					
					$prev_price="<s class=\" mx-1 text-danger\" style=\"font-size:18px;\" ><del> ".$intro->maa->Currency_amount($price)." </del>  </s>";	
				}			
			}
			
		
		
		$header_data = array(
			'seo_title' => htmlspecialchars($name), 
			'seo_image' => $news_img , 
			'seo_link' => $news_url,
			'seo_desc' => _substr($details  ,500 ) 
		);
		
		############################ SEO ##############
		
		$intro->auth->flag = 'admin';
		$style='display:none';
		if ($intro->auth->auth_admin() == true) {
			$style='';
		}
		
		pHeader($header_data);
		$yousave='';
		$dicount_price=dicount($price,$discount, $discount_percent);
		
		$urlpage = $intro->option['site_url'].$intro->base_url."news/View/$id";
		$catname = $array['newscat'][$catid];
		$yousave = $price - $net_price;
		$percent = round((($yousave*100)/$price),0);
		if($lang == "ar"){
			$img="{$intro->base_url}uploads/news/$img_ar";
			$code=$code_ar;
		}else{
			$img="{$intro->base_url}uploads/news/$photo";
			
			}
		$details=intro_html_decode($details);	
		?>
		<div class="container-fluid">
				<div class="row px-xl-5">
					<div class="col-12">
						<nav class="breadcrumb bg-light mb-30">
							<a class="breadcrumb-item text-dark" href="<?=$intro->base_url?>"><?=$intro->lang['home']?></a>
							<a class="breadcrumb-item text-dark" href="<?=$intro->base_url?>"><?=$array['newscat'][$catid]?></a>
							<span class="breadcrumb-item active"><?=$name?></span>
						</nav>
					</div>
				</div>
			</div>

<!-- Shop Detail Start -->
    <div class="container-fluid pb-5">
        <div class="row px-xl-5">
            <div class="col-lg-5 mb-30">
                <div class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner bg-light">
                        <div class="carousel-item active">
                            <img class="w-100 h-100" src="<?=$img?>" alt="Image">
                        </div>  
                    </div>
                    
                </div>		
            </div>
					<style>
					#elem {
						  animation: blink 1s infinite
						}

						@keyframes blink {
						  from {
							opacity:0.0;
						
						  }

						  to {
							opacity: 1;
							color:black;
						  }
						}
					</style>
            <div class="col-lg-7 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3><?=$name?></h3>
                   
                    <div class="d-flex h2">
                    <div class="my-4">
					 <strong class="text-success "><?=$intro->lang['products_price']?> :</strong> 
					<span id='elem'> <?=$price_view?>   </span> 
					<?=$prev_price?>

					</div>
					
					 <div class="mx-5 my-4">
                        <strong class="text-danger mx-2"><?=$intro->lang['productcod']?>:</strong> 
                        <label  for="size-2"> <?=$code?></label>
                    </div>
					</div>
					
					
                    <div class="mb-4 " style='color:#000;font-size:1rem;'><?=$title2?></div>
                   
				
					 <div class="d-flex align-items-center mb-4 pt-2">
                        
                        <a class="btn btn-primary px-3 addToCart" data-toggle="modal" data-target="#staticBackdrop"  href="<?=$intro->href?>cart/add/<?=$id?>">
						<i class="fa fa-shopping-cart mr-1"></i> <?=$intro->lang['add2cart']?></a>
                    </div>
					
					<div class=" py-2">
						<div class="nav nav-tabs mb-4">
							<a class="nav-item nav-link text-dark active" data-toggle="tab" href="#tab-pane-1"><?=$intro->lang['description']?></a>
						</div>
						<div class="tab-content">
							<div class="tab-pane fade show active" id="tab-pane-1">
								<?=$details?>
							 
							</div>
					   </div>
					</div>
				
				
                 
                </div>
            </div>
        </div>
        
    </div>
    <!-- Shop Detail End -->




<?php		
			
		pFooter();
	}
	function getTags($row){
		global $intro;
		
		$lang=$intro->maa->lang;
		
		$allWords = array();
		$foundTags = '';
		
		if( isset($row['extra_titles_'.$lang]) )
		{
			$extra_titles = $row['extra_titles_'.$lang];
			$lines = explode("\n" , $extra_titles);
			if( is_array($lines) && count($lines) > 1 ) 
			{
				foreach($lines AS $line)
				{
					if(!in_array($line , $allWords))
					{
						$tag = str_replace(" ","+",$line);
						$url = $intro->uri_links('search','index',0,'')."?query=$tag";
						$foundTags .= "<a href=\"$url\">$line</a>, ";
						//echo "<li>$line</li>";
					}
					$allWords[] = $line;
				}
			}
		}
		return $foundTags!=""?"Tags: $foundTags":'';
		
	}
		

}

?>