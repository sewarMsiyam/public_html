<?php


class Pages_App extends Intro_Apps 
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
	
	function index(){

		global $id, $intro;
		$lang=$intro->maa->lang;
	
		
		pHeader(array('seo_title'=>$intro->lang['about']));
		TableOpen();
		echo "
					<h2 class=\"blog-post-title\">{$intro->lang['about']}</h2>
					
					 <div class=\"row \">
       <div class=\"col-lg-3\" style='position:relative;'>
					
					<h5><a href='{$intro->uri_links('pages','view',1,'')}'> {$intro->lang['Establishment']} </a> </h5> 
				</div>
				<div class=\"col-lg-3\" style='position:relative;'>
				
					<h5><a href=\"{$intro->uri_links('pages','view',7,'')}\">{$intro->lang['Mission']}</a> </h5> 
				</div>
				
				<div class=\"col-lg-3\" style='position:relative;'>
					
					<h5><a href=\"{$intro->uri_links('pages','view',8,'')}\">{$intro->lang['Vision']}</a></h5> 
				</div>
				<div class=\"col-lg-3\" style='position:relative;'>
				
					<h5><a href=\"{$intro->uri_links('pages','view',9,'')}\">{$intro->lang['Goals']}</a></h5> 
				</div>
      </div>
					
					
			";		

		
		TableClose();
		pFooter();		
	}


	function View(){
		global $id, $intro,$array,$discount_text,$discount_percent,$counttdiscount;
		$lang=$intro->maa->lang;
		$id=intval($intro->input->get('id'));
		if($id==0) $id=$intro->uri->id;

		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_pages  where id=$id ");
		$row =  $intro->db->fetch_assoc($sql);
		@extract($row);
		$title=$row['title_'.$lang];
		$details=$row['bodytext_'.$lang];
		
		$details = stripslashes($details);
		$details = intro_html_decode($details);
		$details = intro_html_decode($details);
		
		pHeader(array('seo_title'=>$title));
		
		
		
	$featrued ='';
	$sql3 = $intro->db->query("SELECT * from ".PREFIX."_products where place=2 and status=1 order by id desc limit 3");
	while($row2 = $intro->db->fetch_assoc($sql3))
		{
			$name=$row2['name_'.$lang];
			$pid=$row2['id'];
			$photo=$row2['photo'];
			$catids=$row2['catid'];
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
		   <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="<?=$intro->base_url?>"><?=$intro->lang['home']?></a>
                    <span class="breadcrumb-item active"><?=$title?></span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Shop Detail Start -->
    <div class="container-fluid pb-5">
        <div class="row px-xl-5">
             <div class="col-lg-9 col-md-8 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3 class='text-primary'><?=$title?></h3>
                 
                    <p class="mb-4"><?=$details?></p>
                   
                </div>
            </div>
			<div class="col-lg-3 col-md-4 mb-30">
              <?=$featrued?>
            </div>
			
        </div>
      
    </div>
    <!-- Shop Detail End -->


 

		<?
		

		pFooter();		
	}

	
	
	
	}

?>