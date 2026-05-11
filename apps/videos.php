<?php

class Videos_App extends Intro_Apps 
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
	
		global $intro,$id,$page,$array,$prod_cat,$stylecatid;
		
		
		$catnews ="";
		$lang=$intro->maa->lang;
		
		$id = intval($intro->input->get('id'));
		if($id==0) $id = @intval($intro->uri->id);
		
		
		$page = intval($intro->input->get('page'));
		if($page==0) $page = $intro->uri->page;
		
		
		$rows_per_page = 9;
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

	
		$sql8 = $intro->db->query("SELECT * FROM ".PREFIX."_videos where status=1 order by vid desc limit $nexlimit,$rows_per_page ");
		$totrows = $intro->db->returned_rows;
		
		$sqlsum = $intro->db->query("SELECT * FROM ".PREFIX."_videos where   status=1  ");
		$totalrows = $intro->db->returned_rows;
		
		$i=1;
		while($row2 = $intro->db->fetch_assoc($sql8))
		{
			@extract($row2);
			$vtitle=$row2['vtitle_'.$lang];
			$url=$row2['url_'.$lang];
			$vfile=$row2['vfile_'.$lang];
			
			$url2=$intro->uri_links('videos','View',$vid,$vtitle);
		
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
				$flv_player="<video style='width:100%;height:250px;'  controls >
					  <source src=\"{$intro->base_url}$vfile\" type=\"video/mp4\">
					  <source src=\"mov_bbb.ogg\" type=\"video/ogg\">
					  Your browser does not support HTML video.
					</video>";
				}	
			$videos.=" <div class=\" col-lg-4 col-md-4 col-sm-4 col-6 pb-1\">
							<div class=\" position-relative bg-light mb-4\">
								<div style=\"border:1px #333 solid;padding:10px;\"> $flv_player </div>
								<div class='py-3 px-2 h6 text-center'> <a href='$url2' class='text-dark '> $vtitle </a></div>
							</div>
						</div>";
						
		
				
		}
		
		
		$urlpage = $intro->uri_links("videos","index",0,'videos');
		
		$paging = "<div class='pagination justify-content-center'><center>".pagination3uri("$urlpage/", $totalrows, $rows_per_page, $page)."</center></div>";
	
		$this->base = "{$intro->base_url}index.php/$urlpage";
		
		$this->header_data = array('title'=>'videos');
		
		pHeader(array('seo_title'=>'videos' ));
	
	
echo "<!-- Breadcrumb Start -->
    <div class=\"container-fluid\">
        <div class=\"row px-xl-5\">
            <div class=\"col-12\">
                <nav class=\"breadcrumb bg-light mb-30\">
                    <a class=\"breadcrumb-item text-dark\" href=\"{$intro->url}\">{$intro->lang['home']}</a>
                   <span class=\"breadcrumb-item active\"> {$intro->lang['videos']}</span>
                </nav>
            </div>
        </div>
    </div>";
	?>
	 <div class="container-fluid">
        <div class="row px-xl-5">
           <?=$videos?>
		   <div class='clear'></div>
           <?=$paging?>
          </div>
    </div>
			
	
	<?php
	
		pFooter();
	}
	
	


	function View()
	{
		global $intro,$home,$array;
       $lang=$intro->maa->lang;
		$id=intval($intro->input->get('id'));
		if($id==0) $id=@intval($intro->uri->id);
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_videos where status=1  and vid=$id ");
		if($intro->db->returned_rows == 0)
		header("Location: {$intro->base_url}");
		
		$row =  $intro->db->fetch_assoc($sql);
		@extract($row);	
		
		
		$url=$row['url_'.$lang];
		$photo=$row['photo_'.$lang];
		$vtitle=$row['vtitle_'.$lang];
		$vfile=$row['vfile_'.$lang];
		
		$body=intro_html_decode($row['body_'.$lang]);
		
		$urlpage = $intro->uri_links("videos","View",$id,'');
		############################ SEO ##############
		#links for SEO and Social Networks
		$news_url = $intro->option['site_url'].$urlpage;
		$news_img = $intro->option['site_url']."{$intro->base_url}uploads/news/$photo";
		
		$header_data = array(
			'seo_title' => htmlspecialchars($vtitle), 
			'seo_image' => $news_img , 
			'seo_link' => $news_url,
			'seo_desc' => _substr($body  ,500 ) 
		);
		
		############################ SEO ##############
		pHeader($header_data);
		
		if($url != ''){
			
			$vurl = get_youtube_name($url);
			$flv_player = "
			<object style='width:100%;height:550px;'>\n
				<param name=\"movie\" value=\"http://www.youtube.com/v/$vurl&hl=en_US&fs=1&\"></param>\n
				<param name=\"allowFullScreen\" value=\"true\"></param>\n
				<param name=\"allowscriptaccess\" value=\"always\"></param>\n
				<embed  style='width:100%;height:550px;' src=\"http://www.youtube.com/v/$vurl&hl=en_US&fs=1&\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>
			</object>";
		}else{
		  
		$flv_player="<video style='width:80%;height:450px;padding:10px;border:1px #333 solid; '  controls >
			  <source src=\"{$intro->base_url}$vfile\" type=\"video/mp4\">
			  <source src=\"mov_bbb.ogg\" type=\"video/ogg\">
			  Your browser does not support HTML video.
			</video>";
		}
		
		
		
		
		
		
		
		
		
		
			$featrued ='';
	$sql3 = $intro->db->query("SELECT * from ".PREFIX."_products where $lang=1 and  place=2 and status=1 order by id desc limit 3");
	while($row2 = $intro->db->fetch_assoc($sql3))
		{
			$name=$row2['name_'.$lang];
			$pid=$row2['id'];
			$photo=$row2['photo'];
			$catids=$row2['catid'];
			$price=$row2['price'];
			$img_ar=$row2['img_ar'];
			$code_ar=$row2['code_ar'];
			$discount_percent=$row2['discount_percent'];
			$discount=$row2['discount'];
			$net_price=$row2['net_price'];
		
			//$img = "{$intro->base_url}img.php?news=1&img=$photo&w=243&h=243";
			if($lang == "ar"){
			$img=$intro->base_url."uploads/news/$img_ar";
			
				}else{
			$img=$intro->base_url."uploads/news/$photo";
			
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
                            <h4>$$net_price </h4><h6 class=\"text-danger mx-3\"><del>".($price != $net_price?"<s>$$price</s> ":"")."</del></h6>
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
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="<?=$intro->base_url?>"><?=$intro->lang['home']?></a>
                    <a class="breadcrumb-item text-dark" href="<?=$intro->uri_links('videos','index',0,'videos')?>"><?=$intro->lang['videos']?></a>
                    <span class="breadcrumb-item active"><?=$vtitle?></span>
                </nav>
            </div>
        </div>
    </div>

<div class="container-fluid pb-5">
        <div class="row px-xl-5">
             <div class="col-lg-9 col-md-8 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3 class='text-primary'><?=$vtitle?></h3>
					<div class="my-4 col-lg-12 col-md-12 col-sm-12 text-center"  ><?=$flv_player?></div>
					<p class="mb-4"><?=$body?></p>
                   
                </div>
            </div>
			<div class="col-lg-3 col-md-4 mb-30" >
              <?=$featrued?>
            </div>
			
        </div>
      
    </div>




<?php		
			
		pFooter();
	}
	

}

?>