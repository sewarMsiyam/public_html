<?PHP
class Sitemap_App extends Intro_Apps 
{
	var $appname = null;
	var $base = null;
  
	function __construct($appname,$base)
	{
		$this->appname = $appname;
		$this->base = $base;
	}

	function index()
	{
		$this->xml($this->links('en'));
	}
	function en()
	{
		 $this->xml($this->links('en'));
	}
	function ar() 
	{
		$this->xml($this->links('ar'));
	}
	function links($lang='ar'){
		global $intro;
		
		$allWords = array();
		
		$data = '';
		$sql = $intro->db->query_fast("SELECT * FROM ".PREFIX."_products order by id desc limit 1000");
		while($row =  $intro->db->fetch_assoc($sql))
		{
			@extract($row);
			
			$name = $row['name_'.$lang];
			
			$name = preg_replace('/\s+/', ' ',$name);
			$name = str_replace(' ', '-',$name);
			
			if($lang == "ar"){
				$photo = $img_ar;
			}else{
				$photo = $photo;
			}

			$link = "https://buyformula.net/$lang/products/View/$id/$name";
			$img = $intro->option['site_url'].$intro->base_url."uploads/news/".$photo;

			$data .= "
			<url> 
				<loc>$link</loc> 
				<priority>0</priority>
				<changefreq>always</changefreq>
				<image:image>
				<image:loc>$img</image:loc> 		   
				</image:image>
			</url>";
			/*if( isset($row['extra_titles_'.$lang]) )
			{
				$extra_titles = $row['extra_titles_'.$lang];
				$lines = explode("\n" , $extra_titles);
				if( is_array($lines) && count($lines) > 1 ) 
				{
					foreach($lines AS $line)
					{
						if(!in_array($line , $allWords))
						{
							$link = "https://buyformula.net/$lang/products/View/$id/".str_replace(" ","-",$line);
							$data .= "
							<url> 
								<loc>$link</loc> 
								<priority>0</priority>
								<changefreq>always</changefreq>
							</url>";
						}
						$allWords[] = $line;
					}
				}
			}*/
		}
		######################################
		##
		## blog
		##
		#######################################
		$result = $intro->db->query("SELECT  * FROM ".PREFIX."_posts order by id desc");
		while ($row = $intro->db->fetch_assoc($result)) {
			extract($row);
			
			
			$title = $row['title_'.$lang];
			$titles = $row['titles_'.$lang];
			
			$link = "https://buyformula.net/$lang/blog/View/$id/$name";
			$img = $intro->option['site_url'].$intro->base_url."uploads/news/".$image;

			$data .= "
			<url> 
				<loc>$link</loc> 
				<priority>0</priority>
				<changefreq>always</changefreq>
				<image:image>
				<image:loc>$img</image:loc> 		   
				</image:image>
			</url>";
			/*
			if( isset($row['titles_'.$lang]) )
			{
				$extra_titles = $row['titles_'.$lang];
				$lines = explode("\n" , $extra_titles);
				if( is_array($lines) && count($lines) > 1 ) 
				{
					foreach($lines AS $line)
					{
						if(!in_array($line , $allWords))
						{
							$link = "https://buyformula.net/$lang/blog/View/$id/".str_replace(" ","-",$line);
							$data .= "
							<url> 
								<loc>$link</loc> 
								<priority>0</priority>
								<changefreq>always</changefreq>
							</url>";
						}
						$allWords[] = $line;
					}
				}
			}*/
			
		}
		
		
		return $data;

	}
	function xml($data){
		
		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" 
		  xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\" 
		  xmlns:video=\"http://www.google.com/schemas/sitemap-video/1.1\">
		  $data
		</urlset>";
	}
	

}
?>