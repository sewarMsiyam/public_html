<?php


class Downloads_App extends Intro_Apps 
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
		global $intro,$search_txt,$page;
			$class="";
		$lang=$intro->maa->lang;
		
		$bodytext = $title=$downloads="";		
		pHeader(array('seo_title'=>' Downloads'));
		TableOpen("<a href='{$intro->base_url}'>Home</a>   <span> » </span> Contact Us » ");

		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_downloads  where status=1 and lang='$lang'   order by id desc ");
		$i=1;
		if($lang=='ar'){$align="right";}else{$align="left";}
		if($intro->db->returned_rows>0){
		while($row =  $intro->db->fetch_assoc($sql)){
			@extract($row);
			$title = $row['title'];
			$file = $row['the_file'];
			if($i%2==0){$class='even';}else{$class="";}
			$downloads.="<tr class='$class'>
						<td class='$class'>$i</td>
						<td class='$class'>$title</td>
						<td class='$class'>$notes</td>
						<td class='$class'><a href='{$intro->base_url}$file' download=\"{$intro->base_url}$file\" ><i class=\"fas fa-download mr-1 text-danger\" style=\"font-size:20px;\"></i><a></td>
					  </tr>";
		$i++;	
		}
		}else{
			$downloads.="<tr>
						<td class='$class' colspan=4><center>{$intro->lang['nofiles']}</center></td>
					  </tr>";
			
		}
	?>
<div class="container-fluid pb-5">
	  <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3"><?=$intro->lang['downlods']?></span></h2>
	  <div class="row px-xl-5">
             <div class="col-lg-12 col-md-12 h-auto my-15">
                <div class="h-100 bg-light p-30">
				  <div class="card">
			  <div class="card-header bg-success text-white">
				<small class="fas fa-download mr-1"></small> <?=$intro->lang['downlods']?>
			  </div>
			  <div class="card-body">
				<p class="card-text">
				<div class="table-responsive">
				<table class="table table-striped ">
				  <thead  class="table-dark">
					<tr>
					<th scope="col" >#</th>
					<th scope="col"><?=$intro->lang['downloa_name']?></th>
					<th scope="col"><?=$intro->lang['downloads_notes']?></th>
					<th scope="col"></th>
					
					
					</tr>
				  </thead>
				  <tbody>
					<?=$downloads?>
				  </tbody>
				</table>
				</div>
				</p>
				
			  </div>
			</div>
				
				
				
				</div>
			</div>
	  </div>
	  </div>
<?	
	
		pFooter();
	}
	
	function add()
	{	
		global $intro,$page;

		$cname = $intro->input->post('cname');		
		$email = $intro->input->post('email');
		$subject = $intro->input->post('subject');
		$msg = $intro->input->post('msg');

		if($cname =="" || $email=="" || $subject=="" || $msg=="" ){
			if($cname==""){echo "<font color=red>{$intro->lang['error_name']} </font><br>"; }
			if($email==""){echo "<font color=red>{$intro->lang['error_email']}  </font><br/>";	}
			if($subject==""){echo "<font color=red>{$intro->lang['error_subject']}  </font><br/>";	}
			if($msg==""){echo "<font color=red>{$intro->lang['error_msg']}  </font>";	}
			die();
		}

		$data["cname"] =  preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '',$cname);
		$data["email"] =  preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '',$email);
		$data["subject"] = preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $subject);
		$data["msg"] =preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $msg);
		$data["datesend"] = date('Y-m-d H:i:s');
		$data["ip"] = $intro->input->server('REMOTE_ADDR');
		
		$intro->db->insert(PREFIX."_contactus",$data);
		
		$this->SendReplyEmail($email , $cname, $subject,$msg);
		
		echo "<br/><font color=green> {$intro->lang['contact_success_msg']}</font>";
		echo "<script>$('#contact')[0].reset();</script>";
	
	}
	function SendReplyEmail($email , $name, $subject,$msg){
		global $intro;
		
		$msg = nl2br($msg);
		#link to send by email
		
		$emailBody = get_msg('email_contact_us');
		$password = '';
		$emailBody = str_replace(
			array("{name}" , "{email}" , "{pass}" ,"{site_name}" , "{site_url}" , "{logo}" , "{subject}" , "{msg}"),
			array($name ,  $email , $password , $intro->site_name, $intro->site_url , $intro->logo , $subject , $msg),
		$emailBody);
	
		$intro->send_email($email, $name,$intro->lang['email_subject_contact'],$emailBody , "Contact from");
		

	}
	

}

?>