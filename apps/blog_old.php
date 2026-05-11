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
		TableOpen( $this->nav(NULL,1) );
		
		$lang = $intro->maa->lang;

		echo "<ul>";
		$result = $intro->db->query("SELECT  * FROM ".PREFIX."_posts order by id desc limit 10");
		while ($row = $intro->db->fetch_assoc($result)) {
			extract($row);
			
			
			$title = $row['title_'.$lang];
			$url = "<a href=\"{$this->base}/View?id=$id\">$title</a>";
			//echo "<div class=big_link></div><hr>";
			
			echo "<li>$url</li>";
		}
		echo "</ul>";
		
		TableClose();
		pfooter();

	}

	function View(){
		global $intro;
		
		$lang = $intro->maa->lang;
		
		$id = intval( $intro->input->get('id') );
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_posts where id=$id;");
		$row = $intro->db->fetch_assoc($sql);
		@extract($row);
		
		$title = $row['title_'.$lang];
		$details = $row['details_'.$lang];

		$this->page_title = $title;
		
		$image = "<img src='$image'>";

		pHeader();
		
		TableOpen( $this->nav(" > $title ") );

		echo "<h3>$title</h3> 
			$dtime 
		<div>$details</div>";

		$update = $intro->db->query("UPDATE ".PREFIX."_posts set hits=hits+1 where id=$id;");

		TableClose();
		pfooter();

	}

	
}//end class: posts
?>