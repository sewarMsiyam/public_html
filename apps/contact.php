<?php


class Contact_App extends Intro_Apps 
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
	
		$new_footbal=$adv_opntion=$left_vedio="";
		$lang=$intro->maa->lang;
		$bodytext = $title=$fields2="";		
		pHeader(array('seo_title'=>' Contact Us '));
		TableOpen("<a href='{$intro->base_url}'>Home</a>   <span> » </span> Contact Us » ");

		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_pages  where id=4 ");
		$row =  $intro->db->fetch_assoc($sql);
		@extract($row);
		$bodytext= stripslashes($row['bodytext_'.$lang]);
		$bodytext= stripslashes($bodytext);
		$title = $row['title_'.$lang];
		

		$star = "<span style='color:red'>*</span>";
		?>
		
	
	  <div class="container-fluid">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">Contact Us</span></h2>
        <div class="row px-xl-5">
            <div class="col-lg-7 mb-5">
                <div class="contact-form bg-light p-30">
                    <div id="success"></div>
                    <form name="sentMessage" id="contact" method=post >
					
					<div class="mb-3 row">
					<label class="col-sm-2 col-form-label"><?=$intro->lang["contactus_cname"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="text" class="form-control" id="name" name='cname' placeholder="<?=$intro->lang['contactus_cname']?>" required />
					</div>
				  </div>
				  <div class="mb-3 row">
					<label class="col-sm-2 col-form-label"><?=$intro->lang["email"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					 <input type="email" class="form-control" name='email'  placeholder="<?=$intro->lang['email']?>" required  />
					</div>
				  </div>
				   <div class="mb-3 row">
					<label class="col-sm-2 col-form-label"><?=$intro->lang["subject"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					<input type="text" class="form-control" id="subject"  name='subject' placeholder="<?=$intro->lang['subject']?>" required /> 
					</div>
				  </div>
				  <div class="mb-3 row">
					<label class="col-sm-2 col-form-label"><?=$intro->lang["msg"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					<textarea class="form-control" rows="8" name='msg' placeholder="<?=$intro->lang['msg']?>" required></textarea>
					</div>
				  </div>
				  <div class="mb-3 row">
					<label class="col-sm-2 col-form-label">Enter Number<span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
							<img src='<?=$intro->base_url?>intro/captcha.php' width=150> 
							<input type=text name='capatcah' />
					</div>
				  </div>
				   <div class="mb-3 row">
					<label class="col-sm-2 col-form-label"></label>
					<div class="col-sm-8">
							 <button class="btn btn-primary py-2 px-4" type="submit" ><?=$intro->lang['send']?>   </button>
					</div>
				  </div>
				  
                    </form>
					<div id="contact_result" ></div>
                </div>
            </div>
            <div class="col-lg-5 mb-5">
                <div class="bg-light p-30">
				
                  <?=$bodytext?>
				  <div><img src='<?=$intro->base_url?>style/img/contact.png' style='max-width:150px;'></div>
				</div>
                
            </div>
        </div>
    </div>
    <!-- Contact End -->
<?php
	
	pFooter();
	}
	
	function add()
	{	
		global $intro,$page;

		$capatcah = $intro->input->post('capatcah');		
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
		$data["email"] =  strip_tags($email);
		$data["subject"] = preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $subject);
		$data["msg"] =preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $msg);
		$data["datesend"] = date('Y-m-d H:i:s');
		$data["ip"] = $intro->input->server('REMOTE_ADDR');
		
		if (empty($_SESSION['intro_uic']) || trim(strtolower($capatcah)) != $_SESSION['intro_uic']) {
            echo "<br/><font color=green>error capatch</font>";
        
			die();
			
			}else{
				$intro->db->insert(PREFIX."_contactus",$data);
				$this->SendReplyEmail($email , $cname, $subject,$msg);
			}

		echo "<br/><font color=green> {$intro->lang['contact_success_msg']}</font>";
		echo "<script>
			$('#contact')[0].reset();
			$('.login_submit').attr('disabled','disabled')
			 $('input[type=\"submit\"]').removeClass('login_submit');
		
		</script>";
	
	}
	function SendReplyEmail($email , $name, $subject,$msg){
		global $intro;
		
		$msg = nl2br($msg);
		#link to send by email
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date("Y-m-d H:i:s");
		
		$emailBody = get_msg('email_contact_us');
		$password = '';
		$emailBody = str_replace(
			array("{name}" , "{email}" , "{pass}" ,"{site_name}" , "{site_url}" , "{logo}" , "{subject}" , "{msg}" , "{date}" , "{ip}"),
			array($name ,  $email , $password , $intro->site_name, $intro->site_url , $intro->logo , $subject , $msg , $date , $ip),
		$emailBody);
	
		$intro->send_email($email, $name,$intro->lang['email_subject_contact'],$emailBody , "Contact from");
		

	}
	

}

?>