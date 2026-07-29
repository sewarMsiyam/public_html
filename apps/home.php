<?php

class Home_App extends Intro_Apps 
{
  
	function __construct()
	{
		//Default class construtor
	}
	
	function index()
	{
		global $intro,$lang,$array,$discount_text,$discount_percent,$counttdiscount;
		
		$intro->home = 1;
	
		$lang=$intro->maa->lang;
	    $currency =$intro->maa->Currency();
		
	//	echo $intro->maa->Currency_amount(500);
		
		pHeader(array());
	
	$cataloge = '';
	$product =$menu1=$menu2=$menu3= '';
	$sql_cat = $intro->db->query("SELECT * from ".PREFIX."_products_cat where father=0 order by catid asc");
	while($cap = $intro->db->fetch_assoc($sql_cat))
	{
		@extract($cap);
		$catname=$cap['catname_'.$lang];
		$url=$intro->uri_links('products','Cat',$catid,$catname);
		$catid=$cap['catid'];
		$menu1=$menu2=$menu3= '';
		$sqlp = $intro->db->query("SELECT id from ".PREFIX."_products where $lang=1 and catid=$catid");
		 $totalproducts = mysqli_num_rows($sqlp);
	
	
			
			$cataloge.=" <div class=\"col-lg-3 col-md-3  col-sm-6 mb-2 \">
                <a class=\"text-decoration-none\" href=\"$url\">
                      <div class=\"bg-white p-3 d-flex  align-items-center justify-content-between shadow-sm border hover-border-primary \">
                            <h9 class=\"text-black\" >$catname</h9>
                            <small class=\"text-body\">$totalproducts {$intro->lang['products']}	</small>
                        </div>
                </a>
            </div>";
			
					
	}
	/**************/
	$newproduct ='';
	$sql2 = $intro->db->query("SELECT * from ".PREFIX."_products where  $lang=1  and place=3 and status=1 order by id desc limit 2");
	while($row2 = $intro->db->fetch_assoc($sql2))
		{
			$photo=$row2['photo'];
			$pid=$row2['id'];
			$name=$row2['name'];
			$img_ar=$row2['img_ar'];
			$url2=next_product_url($row2['name_en'], $intro->uri_links('products','View',$pid,$name));
			
			if($lang == "ar"){
			$img=$intro->base_url."uploads/news/$img_ar";
			
				}else{
			$img=$intro->base_url."uploads/news/$photo";
			
			}
			$besideslider.="
			<div class=\" col-6 col-md-12 text-center mb-3 \">
				<a href=\"$url2\" ><img class=\"img-fluid border hover-border-primary \" src=\"$img\" alt=\"$name\" style=\"max-height:200px;padding:5px\"> </a>
             </div>";			
			
		}
		
		if( $intro->option['showvisa'] ==1){
			$besideslider="<div class=\"d-flex  items-center\"> 
			<img class=\"img-fluid border hover-border-primary \" 
			src=\"https://buyformula.net/style/img/visaview.jpeg\" alt=\"Visa\" style=\"height:300px;padding:5px\"> </div>";
		}else{
			$besideslider = "<div class=\"row \">". $besideslider ."</div>";
		}
		
		/**************************************/
	
					
					
	$newproduct ='';
	$sql2 = $intro->db->query("SELECT * from ".PREFIX."_products where $lang=1 and place=1 and status=1 order by id desc limit 8");
	while($row2 = $intro->db->fetch_assoc($sql2))
		{
			$prev_price=$price_view='';
		
			$name = $row2['name_'.$lang];
			$pid = $row2['id'];
			$catids = $row2['catid'];
			$photo = $row2['photo'];
			$price = $row2['price'];
			$discount = $row2['discount'];
			$img_ar = $row2['img_ar'];
			
			$net_price = $row2['net_price'];
			$url2=next_product_url($row2['name_en'], $intro->uri_links('products','View',$pid,$name));
			$urlcat=$intro->uri_links('products','Cat',$catids,$array['newscat'][$catids]);
			//$img = "{$intro->base_url}img.php?news=1&img=$photo&w=243&h=243";
			if($lang == "ar"){$img=$intro->base_url."uploads/news/$img_ar";}else{$img=$intro->base_url."uploads/news/$photo";}
			
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

				$rating_stars = $this->getProductRatingStars($pid);

					$newproduct.=" <div class=\"col-lg-3 col-md-4 col-sm-6 \">
						<div class=\"product-item bg-light mb-4 shadow-sm border hover-border-primary\">
							<div class=\"product-img position-relative overflow-hidden pt-3\">
								<img class=\"img-fluid w-90\" src=\"$img\" alt=\"\">
								<div class=\"product-action\">
									<a class=\"btn btn-outline-dark btn-square addToCart\" data-toggle=\"modal\" data-target=\"#staticBackdrop\" href=\"{$intro->href}cart/add/$pid\"><i class=\"fa fa-shopping-cart\"></i></a>
									<a class=\"btn btn-outline-dark btn-square\" href=\"$url2\"><i class=\"fa fa-search\"></i></a>
								</div>
							</div>
							<div class=\"text-center py-4\">
								<a class=\"text-black text-decoration-none px-1\" style='display:block;min-height:45px;' href=\"$url2\">$name</a>
								<div class=\"d-flex align-items-center justify-content-center mt-2\">
									<h6>$price_view </h6>$prev_price
								</div>
							
								
								<div class=\"d-flex align-items-center justify-content-center mb-1\">
									<small class=\"fas fa-th-list text-primary mx-1\"></small> <a href='$urlcat' class=\"\">".$array['newscat'][$catids]."</a>
								</div>
								
							</div>
						</div>
					</div>";


			
			
		}
	
	
	
	/****************/
	
	$featrued ='';
	$sql3 = $intro->db->query("SELECT * from ".PREFIX."_products where $lang=1  and place=2 and status=1 order by id desc limit 8");
	while($row2 = $intro->db->fetch_assoc($sql3))
		{
			$prev_price=$price_view='';
			$name=$row2['name_'.$lang];
			$pid=$row2['id'];
			$photo=$row2['photo'];
			$catids=$row2['catid'];
			$price=$row2['price'];
			$img_ar=$row2['img_ar'];
			$code_ar=$row2['code_ar'];
			$discount=$row2['discount'];
			$net_price=$row2['net_price'];
		
			//$img = "{$intro->base_url}img.php?news=1&img=$photo&w=243&h=243";
			if($lang == "ar"){
			$img=$intro->base_url."uploads/news/$img_ar";
			
				}else{
			$img=$intro->base_url."uploads/news/$photo";
			
			}
			

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
			
			
			
			$url2=next_product_url($row2['name_en'], $intro->uri_links('products','View',$pid,$name));
			$urlcat=$intro->uri_links('products','Cat',$catids,$array['newscat'][$catids]);
			$rating_stars = $this->getProductRatingStars($pid);
			$featrued.=" <div class=\" col-lg-3 col-md-4 col-sm-6 \">
                <div class=\"product-item bg-light mb-4 shadow-sm border hover-border-primary\">
                    <div class=\"product-img position-relative overflow-hidden pt-3\">
                        <img class=\"img-fluid w-90\" src=\"$img\" alt=\"\">
                        <div class=\"product-action\">
                            <a class=\"btn btn-outline-dark btn-square addToCart\"  data-toggle=\"modal\" data-target=\"#staticBackdrop\"  href=\"{$intro->href}cart/add/$pid\"><i class=\"fa fa-shopping-cart\"></i></a>
                            <a class=\"btn btn-outline-dark btn-square\" href=\"$url2\"><i class=\"fa fa-search\"></i></a>
                        </div>
                    </div>
                    <div class=\"text-center py-4\">
                        <a class=\"text-black text-decoration-none px-1\" style='display:block;min-height:45px;' href=\"$url2\">$name</a>
                        <div class=\"d-flex align-items-center justify-content-center mt-2\">
                            <h6>$price_view </h6> $prev_price
                        </div>
					
								
                        <div class=\"d-flex align-items-center justify-content-center mb-1\">
                            <small class=\"fas fa-th-list text-primary mx-1\"></small> <a href='$urlcat'>  ".$array['newscat'][$catids]."</a>
						 </div>
                    </div>
                </div>
            </div>";		
		}		
	
/*****************sliders********************/

   $sliders ='';
	$sql2 = $intro->db->query("SELECT * from ".PREFIX."_slides order by sid desc limit 7");
	$o=0;
	while($row2 = $intro->db->fetch_assoc($sql2))
		{
			$stitle=$row2['stitle_'.$lang];
			$sid=$row2['sid'];
			if($lang =="ar"){
				$image=$row2['image'];
			}else{
				$image=$row2['image_en'];	
			}	
			$url = $row2['url_'.$lang];
			
			$active=($o==0)?"active":"";
			$slidersol.=" <li data-target=\"#header-carousel\" data-slide-to=\"$o\" class=\"$active\"></li>";
			$sliders.=" <div class=\"carousel-item position-relative $active\" style=\"height: 430px;\" >
                            <a href=\"$url\"><img class=\"position-absolute w-100 h-100\" src=\"{$intro->base_url}uploads/news/$image\" ></a>
                            
                        </div>";
		$o++;	
		}
		/**************************/
	
	$vedios=$this->Vedios();
	
	
	?>
	<script>
$(document).ready(function() {
    // تفويض الحدث باستخدام on() للنجوم
    $('body').on('click', '.star', function(e) {
        e.preventDefault();

        var rating = $(this).data('value');
        var productId = $(this).closest('.rating-stars').data('product');

        $.ajax({
            url: 'rate_product.php',
            method: 'POST',
            data: {
                product_id: productId,
                rating: rating
            },
            success: function(response) {
                alert(response); 
            },
            error: function() {
                alert('حدث خطأ أثناء إرسال التقييم.');
            }
        });
    });
});
</script>


	
	<?php
	eval("\$index_main = \" " . $intro->style['index_main'] . "\";");
		echo stripslashes($index_main);
		
		pFooter();		
	}

function getProductRatingStars($product_id, $clickable = true) {
	global $intro;

	$sql = "SELECT AVG(rating) as avg_rating FROM maa_product_ratings WHERE product_id = '$product_id' AND is_approved = 1";
	$intro->db->query($sql);
	$avg = 0;

	if ($intro->db->returned_rows > 0) {
		$row = $intro->db->fetch_assoc();
		$avg = round($row['avg_rating'], 1);
	}

	$fullStars = floor($avg);
	$halfStar = ($avg - $fullStars) >= 0.5;
	$emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

	$starsHtml = "<div class='rating-stars' data-product='$product_id'>";
	for ($i = 1; $i <= 5; $i++) {
		if ($i <= $fullStars) {
			$class = 'fas fa-star';
		} elseif ($halfStar && $i == $fullStars + 1) {
			$class = 'fas fa-star-half-alt';
		} else {
			$class = 'far fa-star';
		}
		$starsHtml .= "<small class='$class text-warning mr-1 star' data-value='$i' style='".($clickable ? "cursor:pointer;" : "")."'></small>";

	}
	$starsHtml .= "<small>($avg)</small>";
	$starsHtml .= "</div>";
	return $starsHtml;
	

}

	function Vedios(){
		global $intro;
	$lang=$intro->maa->lang;
		
	$sql2 = $intro->db->query("SELECT * from ".PREFIX."_videos where status=1 order by vid ");
	$totrows = $intro->db->returned_rows;
	
	if($totrows == 0){
		return null;
	}else{
	while($row2 = $intro->db->fetch_assoc($sql2))
		{
			@extract($row2);
			$vtitle = $row2['vtitle_'.$lang];
			$url = $row2['url_'.$lang];
			$vfile = $row2['vfile_'.$lang];
		
			$url2=$intro->uri_links('videos','View',$vid,$vtitle);
			
			if($lang == "ar"){
			$img=$intro->base_url."uploads/news/$img_ar";
				}else{
			$img=$intro->base_url."uploads/news/$photo";
			}
			if($url != ''){
			
			$vurl = get_youtube_name($url);
			$flv_player = "
			<object style='width:100%;height:250px;'>\n
				<param name=\"movie\" value=\"http://www.youtube.com/v/$vurl&hl=en_US&fs=1&\"></param>\n
				<param name=\"allowFullScreen\" value=\"true\"></param>\n
				<param name=\"allowscriptaccess\" value=\"always\"></param>\n
				<embed  style='width:100%;height:250px;' src=\"http://www.youtube.com/v/$vurl&hl=en_US&fs=1&\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>
			</object>";
		}else{
		$flv_player="<video style='width:100%;height:250px;padding:5px;border:1px #333 solid;'  controls >
            			  <source src=\"{$intro->base_url}$vfile\" type=\"video/mp4\">
            			  <source src=\"mov_bbb.ogg\" type=\"video/ogg\">
            			  Your browser does not support HTML video.
            			</video>";
		}
		
			
	
			$videos.=" <div class=\" col-lg-4 col-md-4 col-sm-4 pb-1\">
							<div class=\" position-relative bg-light mb-4\">
								<div class=''> $flv_player </div>
								<div class='py-3 px-2 h6 text-center'> <a href='$url2' class='text-dark '> $vtitle </a></div>
							</div>
						</div>";			
			
		}
	
	$data=" <!-- vedio Start -->
    <div class=\"container pt-3 pb-3\">
        <h2 class=\"section-title position-relative text-uppercase mx-xl-5 mb-4\"><span class=\"bg-secondary px-3\">{$intro->lang['videos']}</span></h2>
        <div class=\"row px-xl-5\">
         $videos
   
        </div>
    </div>
    <!-- vedio End -->";
	return $data;
	}
	
	}	
	function ClickAdd(){
		global $intro;
		
		$id=intval($intro->input->get('id'));
		if($id==0) $id=intval($intro->url_segments[3]);
		
		$result = $intro->db->query_fast("SELECT * from ".PREFIX."_adv where status='1' AND advid='$id'");

		if(mysqli_num_rows($result) != 0){
		
			$myrow = $intro->db->fetch_assoc($result);
			$advid = $myrow['advid'];
			$url = $myrow['url'];
			$intro->db->query("UPDATE  ".PREFIX."_adv SET  hits=hits+1 where advid='$advid'");
			
			if(filter_var($url, FILTER_VALIDATE_URL) === FALSE) $url =  $intro->base_url;
			
			if (!headers_sent())
			{
				header('Location: '.$url);
			}else {
				echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
				echo '<noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>';
			}

		}else{
			$url = $intro->base_url;
			if (!headers_sent())
			{
				header('Location: '.$url);
			}else {
				echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
				echo '<noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>';
			}
		}
	}	
}
?>