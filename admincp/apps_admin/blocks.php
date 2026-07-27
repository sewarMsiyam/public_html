<?PHP

class Blocks_AppAdmin extends Intro_AppsAdmin{

	var $appname = null;
	var $base = null;
	var $img_path;
	
	function __construct($appname,$base,$img_path="")
	{
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
	}

	function nav(){
			 global $intro;

			 echo "<div class=\"app_nav\">
			 <a class=\"btn btn-default\" href=\"$this->base/index\">".$intro->lang["blocks_appname"]."</a>
			 <a class=\"btn btn-default\" href=\"$this->base/Form?t=add\">".$intro->lang['blocks_add']."</a>		 		 
			 </div>";
	}

	function Index(){
		global $intro,$active,$page,$CONF,$order,$search_txt,$blocks_type;

		
		$this->nav();
		$html_blocks = array();
		$html_blocks['r'] = $html_blocks['c'] = $html_blocks['l'] = "";

		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_blocks order by weight asc");

		while($row = $intro->db->fetch_assoc($sql)){
			@extract($row);
			
			$blockfile=$row['blockfile'];
			$btype=$blocks_type[$type];

			if($type==1){
				$block_type = "<span class='icon-folder' title='$blockfile'> </span>";
			}elseif($type==1 ){
				$block_type = "<span class='icon-code' title='HTML Code'> </span>";
			}elseif($type==3 ){
				$block_type ="<span class='icon-edit' title='من تقسيمات الاخبار'> </span>";
			}


			$links ="<div style='float:left;'><a href='$this->base/Form?t=edit&amp;bid=$bid' class=icon-edit> </a>
						<a class=\"intro_ui_del icon-cancel\" href=\"$this->base/Del?bid=$bid\" onclick=\"return false;\"> </a>
						</div>";
						
			$html_blocks[$bposition] .= "
			<li id=\"$bid\">
				<div class='wrap_blocks'>					
					<div class='blocktitle'> $title $links</div>
					<div class='block_contents'>
					".(($active == 1) ? "<a class='icon-ok' title='مفعلة'></a>" : "<a class='icon-off' title='معطلة'></a>")."
					".(($file_theme != "") ? "<a class='icon-cloud' title='$file_theme'></a>" : "")."
					$block_type 
					".(($inhome == 1) ? "<a class='icon-home' title='على الرئيسية'></a>" : "")."
					".(($inpage == 1) ? "<a class='icon-docs' title='في الداخل'></a>" : "")."
					</div>
				</div>
			</li>";				
		}
	?>	  

	<style type="text/css">
	.placeHolder div { background-color:white !important; border:dashed 1px gray !important; }
	.blocktitle{
		background:#333333;
		display:block;
		color:white;padding:5px;
		font-weight:bold;
		height:25px ;
		line-height:25px;
		font-size: 10px;
	}
	.blocktitle a:link,.blocktitle a:visited{
		color: #D9D900;
	}
	.blocktitle a:hover{
		color: #D96C00;
	}
	.blocktitle img{
		vertical-align:middle; margin-right:5px; 
	}
	.blocks_container{width:800px;margin:auto;border:1px solid #000;}
	.block_cols{
		
		margin-left: 20px;
		list-style-type:none; 
		width:90%;		
		padding:5px;
	}
	.block_cols li{border:solid 1px black;  position:relative; margin-top:10px;}
	.block_cols a{ color:#000; line-height:20px; }
	.block_contents img{vertical-align:middle; width:16px; height:16px; margin:2px 5px 2px; }
	.blocks_table { width:90%; }
	.blocks_table td{ vertical-align: top; width: 30%; border: 2px dotted #c0c0c0;  }
	</style>
	
	<script type="text/javascript" src="<?=$intro->base_url?>admincp/style/js/jquery.dragsort-0.5.1.js"></script>
	
	<table class="blocks_table" align="center">
		<tr>
			<th>مجموعات يمين</th>
			<th>مجموعات الوسط</th>
			<th>مجموعات اليسار</th>
		</tr>
		<tr>
			<td><ul id="listBlockRight" class="block_cols"><?=$html_blocks['r']?></ul></td>
			<td><ul id="listBlockCenter" class="block_cols"><?=$html_blocks['c']?></ul></td>
			<td><ul id="listBlockLeft" class="block_cols"><?=$html_blocks['l']?></ul>	</td>
		</tr>
	</table>
	<script type="text/javascript">			
		$("#listBlockRight, #listBlockLeft, #listBlockCenter").dragsort({ 
			dragSelector: "div", 
			dragBetween: true, 
			dragEnd: saveOrder, 
			placeHolderTemplate: "<li class='placeHolder'><div></div></li>" 
		});
		function saveOrder() {

			var data_right = $("#listBlockRight li").map(function() { return $(this).attr('id'); }).get();
			var data_left = $("#listBlockLeft li").map(function() { return $(this).attr('id'); }).get();
			var data_center = $("#listBlockCenter li").map(function() { return $(this).attr('id'); }).get();

			$('#OrderResult').load("<?=$this->base?>/save_blocks_list?NH=1&block_right="+data_right.join("|")+"&block_center="+data_center.join("|")+"&block_left="+data_left.join("|"));
		};		
	</script>
	<div style="clear:both;"></div>
	<div id="OrderResult"></div>
	<?
	}

	function save_blocks_list() {
		global  $intro, $CONF;

		
		$block_right = explode("|",$_GET['block_right']);
		$block_left = explode("|",$_GET['block_left']);
		$block_center = explode("|",$_GET['block_center']);

		for($i=0;$i<count($block_right);$i++){
		
			
			$sql = $intro->db->query("UPDATE ".PREFIX."_blocks SET weight='$i',bposition='r' WHERE bid='$block_right[$i]' ")or die(mysql_error());
		}
		for($i=0;$i<count($block_left);$i++){
		
			//echo "( L = $block_left[$i] )";
			$sql = $intro->db->query("UPDATE ".PREFIX."_blocks SET weight='$i',bposition='l' WHERE bid='$block_left[$i]'  ");
		}
		for($i=0;$i<count($block_center);$i++){
		
			//echo "( L = $block_left[$i] )";
			$sql = $intro->db->query("UPDATE ".PREFIX."_blocks SET weight='$i',bposition='c' WHERE bid='$block_center[$i]'  ");
		}
		
		echo "<center ><font color=red> OK : ".date('H:i:s')." </font></center>";
		//save_blocks_list
		
		
	}

	function get_blocks_theme($file_theme='') {
		global  $intro;
		
		$sel='';
		
		$html = "<select name=\"file_theme\">\n"
			 ."<option value=\"\" selected>{$intro->lang['choose_theme']} </option>\n";
			$result_s = $intro->db->query("SELECT style_id FROM " . PREFIX . "_style where style_default='1' limit 1");
			$row_s = $intro->db->fetch_assoc($result_s);
			
			$result = $intro->db->query("SELECT name,name_arab FROM " . PREFIX . "_style_files where style='$row_s[style_id]' and style_type='2'");
			while ($row = $intro->db->fetch_assoc($result))
			{         
				if($file_theme == $row['name']){ $sel='selected'; }
					$html .= "<option value=\"$row[name]\" $sel>$row[name] - $row[name_arab]</option>\n";
					$sel='';
				
			}
			
			
		$html .= "</select>\n";
		
		return $html;
		
	}
	function display_blocks($type){
		echo "
		<script>			 
		$(document).ready(function () {
			/* var typeid =$(\"hidden[name='type']\").val();*/
			var typeid =$type;

			if(typeid == 1){
				$(\"#topics\").hide();
				$(\"#html_code\").hide();
				$(\"#blocks_files\").show();
			}else if(typeid == 2){
				$(\"#topics\").hide();
				$(\"#blocks_files\").hide();
				$(\"#html_code\").show();
			}else if(typeid == 3){
				
				$(\"#topics\").show();
				$(\"#blocks_files\").hide();
				$(\"#html_code\").hide();
			}
		});			 
		</script>";
	}
	function Form(){
			global $intro,$t,$error,$blocks_type,$array,$blocks_places,$inhome,$inpage;
			global $bid,$title,$bposition,$active,$blockfile,$file_theme,$type,$content,$expire;

			
			$info_icon = $info_text = $action = '';
			$topic=0;
			
			$t = $intro->input->get_post('t');
			$bid = $intro->input->get_post('bid');
			$bid = intval($bid);
			$this->nav();
			if($t == "edit"){
			   $sql = $intro->db->query("SELECT * FROM ".PREFIX."_blocks where bid='$bid'") or die(mysql_error());
			   $row = $intro->db->fetch_assoc($sql);
			   @extract($row);
			   
			   $info_text = $intro->lang['blocks_edit']."<b>$bid</b>";
			   $info_icon = "{$this->img_path}/icons/edit_24.png";
			   $action = "doEdit";
			   $btn_submit = $intro->lang['save'];
			}
			elseif($t == "add"){
				 $info_text = $intro->lang['blocks_add'];
				 $info_icon = "{$this->img_path}/icons/add_24.png";
				 $action = "doAdd";
				 $btn_submit = $intro->lang['blocks_add'];
				 
			}else{
				die("No t=add or edit");
			}
			
			if($type !=""){
			$this->display_blocks($type);			
			}
			
	echo "<fieldset><legend><img src=\"$info_icon\" align=\"absmiddle\">$info_text</legend>";

	echo "<form method=\"POST\" name=\"form_add\"  action=\"$this->base/$action\" enctype=\"multipart/form-data\">
		 <table align=\"center\" border=\"1\" width=\"100%\" id=\"table1\" cellpadding=\"2\" bordercolor=\"#C0C0C0\">	 
				<tr>
					<td>{$intro->lang['blocks_type']}  : <font color=red>*</font></td>
					<td>".sel_array("type",$array['blocks'],$type)." $error[type]</td>
				</tr>
				<tr>
					 <td>{$intro->lang['blocks_title']}  : <font color=red>*</font></td>
					 <td><input type=\"text\" name=\"title\" value=\"$title\" size=\"30\"> $error[title]</td>
				 </tr>
				<tr>
					 <td>{$intro->lang['blocks_inhome']}  : <font color=red>*</font></td>
					 <td>".form_option("inhome",$inhome)." $error[inhome]</td>
				 </tr>
				 
				 <tr>
					 <td>{$intro->lang['blocks_inpage']}  : <font color=red>*</font></td>
					 <td>".form_option("inpage",$inpage)." $error[inpage]</td>
				 </tr>
				 
				 <tr>
					 <td>{$intro->lang['blocks_plcae']} : <font color=red>*</font></td>
					 <td>".sel_array("bposition",$array['blocks_places'],$bposition)." $error[bposition]</td>
				 </tr>			 
				 <tr id='blocks_files' >
					 <td>{$intro->lang['blocks_file']}  : </td>
					 <td>".$this->block_file($blockfile)." $error[blockfile]</td>
				 </tr>
				   <tr id='html_code' >
					 <td>{$intro->lang['blocks_content']} : </td>
					 <td>";
					 
					 fck_editor("content",stripslashes($content)/*,array('tools'=>'mini','dir'=>'rtl') */);
					 echo "
					 $error[content]</td>
				 </tr>

				 <tr bgcolor=#DFDFDF  ID=\"topics\" >
					 <td>{$intro->lang['take_from']}:</td>
					 <td>".$this->news_select($topic)."
						{$intro->lang['num_subjects']}: <input type=\"text\" name=\"topic_no\" size=\"2\" value=5 maxlength=\"2\">
						{$intro->lang['moving_up']}<input type='radio' name='move' value='1'> {$intro->lang['yes']} &nbsp;&nbsp;
						<input type='radio' name='move' value='0' checked> {$intro->lang['no']} &nbsp;&nbsp;&nbsp;&nbsp;
						| {$intro->lang['pic_width']}: <input type=\"text\" name=\"imgw\" size=\"3\" value=100 maxlength=\"3\">
						{$intro->lang['pic_height']}: <input type=\"text\" name=\"imgh\" size=\"3\" value=100 maxlength=\"3\">
					</td>
				</tr>
				 <tr>
					 <td>{$intro->lang['blocks_expire']}: </td>					
					 <td><input type=\"text\" name=\"expire\" value=\"$expire\" size=\"4\" maxlength=\"3\"> {$intro->lang['day_s']} $error[expire]</td>
				 </tr>
				 
				 <tr>
				  <td >{$intro->lang['after_end_date']} :</td>
				  <td>
					  <select name=\"actionb\">"
					  ."<option name=\"actionb\" value=\"d\">تعطيل </option>"
					  ."<option name=\"actionb\" value=\"r\">{$intro->lang['del']}</option>
					  </select>
				  </td>
			 </tr>
				 <tr>
					 <td>{$intro->lang['blocks_theme']}: </td>
					 <td>".$this->get_blocks_theme($file_theme)." $error[file_theme]</td>
				 </tr>

				 <tr>
					 <td>{$intro->lang['blocks_active']} : </td>
					 <td>".form_option("active",$active)." $error[active]</td>
				 </tr>    
		<tr>
			<td align=\"center\" colspan=\"2\">

					<input type=\"hidden\" name=\"maa\"  value=\"$action\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
				<input type=\"hidden\" name=\"bid\"  value=\"$bid\">
				<input type=\"submit\" value=\" $btn_submit \" name=\"B1\">
				
			</td></form>
		</tr>
		</table></fieldset><br><br><br>";


	}

	############################################################################
	function news_select($topic){
		global $intro;
		
		$html_data = "<select name='topic'>
		<option value='0' selected> {$intro->lang['none']} </option>";
		$toplist = $intro->db->query("select * from ".PREFIX."_news_cat where father='0' order by catname asc");	
		while($topic_row = $intro->db->fetch_assoc($toplist))
		{
			
			
			$html_data .= "<option ".($topic_row['catid']==$topic?"selected=\"selected\"":"")." value={$topic_row['catid']}>{$topic_row['catname']}</option>";
		}
		$html_data .= "</select>";
		return $html_data;
	}	
	function block_file($blockfile){
		global $intro;
		
		$html_data = "<select name=\"blockfile\" dir=ltr>"
		."<option name=\"blockfile\" value=\"\" selected>{$intro->lang['choose_block_file']}</option>";

		$root = scandir("../blocks");
		foreach($root as $file)
		{
			if(substr($file, -3, 3) == "php") { $blockslist[] = $file; }
			
			
		}
		@sort($blockslist);
		foreach($blockslist as $thefile)
		{
			if($thefile!="") 
			{
				if($blockfile==$thefile){$sel="selected=\"selected\"";}else{$sel="";}						

				$html_data .= "<option value=\"$thefile\"  $sel>".str_replace('.php','',$thefile)." </option>\n";
			}
		}
		$html_data .= "</select>";
		return $html_data;
	}
	function doAdd(){
		global $intro,$t,$error,$topic,$topic_no,$move,$imgw,$imgh,$actionb,$inhome,$inpage;
		global $title,$bposition,$active,$blockfile,$file_theme,$type,$content,$expire ;
		
		extract($_POST);
		
		$title = trim($title);
		$bposition = $bposition;
		$active = intval($active);
		$blockfile = trim($blockfile);
		$file_theme = trim($file_theme);
		$type = intval($type);
		$content = trim($content);
		$expire = intval($expire);

		if($title == "" ||  $bposition == "" || $type == 0 ){
			if($title == ""){ $error['title'] = "<font class=error>"._REQUIRED."</font>"; }
			if($bposition == ""){ $error['bposition'] = "<font class=error>"._REQUIRED."</font>"; }
			if($type == 0){ $error['type'] = "<font class=error>"._REQUIRED."</font>"; }
			$t = "add";
			$this->Form();
			die();
		}
			 
		$row2 = $intro->db->fetch_assoc($intro->db->query("SELECT weight FROM ".PREFIX."_blocks WHERE bposition='$bposition' ORDER BY weight DESC"));
		$weight = intval($row2['weight']);
		$weight++;
		
		$title = stripslashes($title);
		$content = stripslashes($content);
		$bkey = "";
		$btime = "";
		if ($expire == "") {
			$expire = 0;
		}
		if ($expire != 0) {
			$expire = time() + ($expire * 86400);
		}
		$refresh = intval($refresh);
		$view = intval($view);
		$subscription = intval($subscription);
		$showtitle = intval($showtitle);
		 
			
		$data["title"] = $title;
		$data["bposition"] = $bposition;
		$data["active"] = $active;
		$data["blockfile"] = $blockfile;
		$data["file_theme"] = $file_theme;
		$data["type"] = $type;
		$data["content"] = $content;
		$data["expire"] = $expire;
		$data["bkey"] = $bkey;
		//$data["url"] = $url;
		$data["refresh"] = $refresh;
	
		$data["time"] = $btime;
		//$data["blanguage"] = $blanguage;
		$data["view"] = $view;
		$data["action"] = $actionb;
		$data["subscription"] = $subscription;
		$data["showtitle"] = $showtitle;
		$data["topic"] = $topic;
		$data["topic_no"] = $topic_no;
		$data["move"] = $move;
		
		$data["imgw"] = $imgw;
		$data["imgh"] = $imgh;
		$data["inhome"] = $inhome;
		$data["inpage"] = $inpage;

		$intro->db->insert(PREFIX."_blocks",$data);

		revalidateNext('blocks');
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$bid,$inhome,$inpage;
		global $title,$bposition,$active,$blockfile,$file_theme,$type,$content,$expire ;
		global $bkey, $url, $refresh, $btime, $blanguage , $view , $actionb , $subscription , $showtitle ;
		global $topic, $topic_no, $move , $image , $imgw ,$imgh , $lang  ;

		extract($_POST);
		$data = array();
		$data["title"] = $title;
		$data["bposition"] = $bposition;
		$data["active"] = $active;
		$data["blockfile"] = $blockfile;
		$data["file_theme"] = $file_theme;
		$data["type"] = $type;
		$data["content"] = $content;
		$data["expire"] = $expire;
		$data["bkey"] = $bkey;
		$data["url"] = $url;
		$data["refresh"] = $refresh;
		$data["time"] = $btime;
		$data["blanguage"] = $blanguage;
		$data["view"] = $view;
		$data["action"] = $actionb;
		$data["subscription"] = $subscription;
		$data["showtitle"] = $showtitle;
		$data["topic"] = $topic;
		$data["topic_no"] = $topic_no;
		$data["move"] = $move;
		$data["image"] = $image;
		$data["imgw"] = $imgw;
		$data["imgh"] = $imgh;
		$data["lang"] = $lang;
		$data["inhome"] = $inhome;
		$data["inpage"] = $inpage;

		$intro->db->update(PREFIX."_blocks",$data,"bid='$bid'");
			
		revalidateNext('blocks');
		$intro->redirect($this->appname);	
	}

	############################################################################
	function Del(){
		 global $intro,$bid;

		 $sql = $intro->db->query("DELETE FROM ".PREFIX."_blocks WHERE bid='$bid' ");

		 revalidateNext('blocks');
		 $intro->redirect($this->appname);
	}

	############################################################################
	function Active(){
		global $intro,$bid;

		$sql = $intro->db->query("UPDATE ".PREFIX."_blocks SET active='1' WHERE bid='$bid' ");

		revalidateNext('blocks');
		$intro->redirect($this->appname);
	}

}


?>