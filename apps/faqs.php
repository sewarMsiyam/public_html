<?php

class Faqs_App extends Intro_Apps 
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
		
	}
	function nav($extra="",$step=0){
		global $intro;
		
		return "<a href=\"{$intro->base_url}\">".$intro->lang["home"]."</a> > ".(($step==1) ? $this->app_caption : "<a href=\"{$this->base}/index\">{$this->app_caption}</a> " )." $extra";
				
	}
	
	function index(){
		global $intro,$array,$discount_text,$discount_percent,$counttdiscount;

		pHeader();
		
		TableOpen( $this->nav(NULL,1) );
		$fags='';
		$lang = $intro->maa->lang;
		
		$result = $intro->db->query("SELECT  * FROM ".PREFIX."_faqs order by id desc limit 10");
		$i=1;
		while ($row = $intro->db->fetch_assoc($result)) {

			$question = stripslashes(strip_tags($row['question_'.$lang]));
			$answer=$row['answer_'.$lang];
			$answer=intro_html_decode($answer);
			$answer=intro_html_decode($answer);
			$answer=stripslashes($answer);
			$answer=stripslashes($answer);
			
			$answer = str_replace("{lang}", $lang, $answer);
			
			if($lang=='ar'){$spanicon="icon-leftArrow6";}else{$spanicon='';}
			
			$fags.= "<div class=\"bg-leight py-3\" style='border-top:1px #e2e2e2 dotted'>
							<div class=\"light_gray\">
								<a class=\"text-success font-weight-bold\">  <i class=\"fas fa-arrows-alt\"></i>
									$question
								</a>
							</div>
						<div class=\"collapse_panel off\">
							<p>$answer
							</p>
						</div>
							
						</div>";
		$i++;
		}
		
		
		$featrued ='';
	$sql3 = $intro->db->query("SELECT * from ".PREFIX."_products where place=2 and status=1 order by id desc limit 3");
	while($row2 = $intro->db->fetch_assoc($sql3))
		{
			$name=$row2['name_'.$lang];
			$pid=$row2['id'];
			$photo=$row2['photo'];
			$price=$row2['price'];
			$catids=$row2['catid'];
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
                    <span class="breadcrumb-item active"><?=$intro->lang['qa']?></span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->
	 <div class="container-fluid pb-5">
        <div class="row px-xl-5">
             <div class="col-lg-9 col-md-8 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3 class='text-primary'><?=$intro->lang['qa']?></h3>
					<?=$fags?>
                   
                </div>
            </div>
			<div class="col-lg-3 col-md-4 mb-30">
              <?=$featrued?>
            </div>
			
        </div>
      
    </div>
	

<?php	
		
		
		
		
		pfooter();

	}

	
}//end class: faqs
?>