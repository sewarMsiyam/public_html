<?PHP
class Register_App extends Intro_Apps 
{
	var $appname = null;
	var $base = null;
	var $img_path;
  
	function __construct($appname,$base,$img_path="")
	{
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
	}
	function error($index=""){
		global $error;
		
		return isset($error[$index])?$error[$index]:"";
	}
	function index() {
		global $intro, $error, $array;
		$lang=$intro->maa->lang;
		
		$star = "<span style=\"color:red\">*</span>";
		$email='info@buyformula.net';
		$name='suhair';
		$msg='msg here ';
		//$intro->send_email($email, $name,'subjext',$msg);
		
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
		
			$featrued.=" <div class=\" col-lg-12 \">
                <div class=\"product-item bg-light mb-4 shadow-sm border hover-border-primary\">
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
                            <h4>".$intro->maa->Currency_amount($net_price)." </h4><h6 class=\"text-danger mx-3\"><del>".($price != $net_price?"<s>".$intro->maa->Currency_amount($price)."</s> ":"")."</del></h6>
                        </div>
						 <div class=\"d-flex align-items-center justify-content-center mb-1\">
                            <small class=\"fas fa-th-list text-primary mx-1\"></small> <a href='$urlcat'>  ".$array['newscat'][$catids]."</a>
                        </div>                  
                    </div>
                </div>
            </div>";				
		}		
		pHeader();
		if($intro->maa->lang=='en'){$position="right";}else{$position="left";}
		?>
		
		<!-- Shop Detail Start -->
    <div class="container-fluid pb-5">
	  <h4 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3"><?=$intro->lang['newaccount']?></span></h4>
	  
        <div class="row px-xl-5">
             <div class="col-lg-9 col-md-8 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                   <form method="POST" action="<?=$this->base?>/doRegister" name="reg_form" id="reg_form">
						<p class="text-danger text-center font-weight-bold" > * <?=$intro->lang['RequiredFields']?></p>
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['fullname']?>
							<font class="star">*</font>
							</label>
							
							<div class="col-sm-8">
							  <input type="text" class="form-control" name="fullname"  value="<?=$intro->input->post('fullname')?>"  required /> 
							   <?=$this->error('fullname')?>
							</div>
						</div>
						
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['email']?>
							<font class="star">*</font> 
							</label>
							
							<div class="col-sm-8">
							  <input type="email" class="form-control" name="email"  value="<?=$intro->input->post('email')?>"  required placeholder='example@example.com' /> 
							  <?=$this->error('email')?>
							</div>
						</div>
						
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['passowrd']?>
							<font class="star">*</font> 
							</label>
							
							<div class="col-sm-8 position-relative">
							  <input type="password" class="form-control" name="password" min=6  id="password"   placeholder="<?=$intro->lang['pass_minimum6']?>" required />
							  <i class="fa fa-eye toggle-password" id="togglePassword" 
							  style="position: absolute; top: 50%; <?=$position?>: 25px; transform: translateY(-50%); cursor: pointer;"></i>

							 <?=$this->error('password')?>
							</div>
				
			
			
						</div>
						
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['returnpass']?>
							<font class="star">*</font> 
							</label>
							
							<div class="col-sm-8">
							  <input type="password" class="form-control" name="password2"  required  /> 
							  <?=$this->error('password')?>
							</div>
						</div>
						
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['country']?>
							<font class="star">*</font> 
							</label>
							
							<div class="col-sm-8">
							  <?=form_select_array("country",$array['country'],$intro->input->post('country'))?>
							  <?=$this->error('country')?>
							</div>
						</div>
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['city']?>
							<font class="star">*</font> 
							</label>
							<div class="col-sm-8">
							  <input type="text" class="form-control" name='city' value="<?=$intro->input->post('city')?>"   required  /> 
							  <?=$this->error('city')?>
							</div>
						</div>
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['address']?>
							<font class="star">*</font> 
							</label>
							<div class="col-sm-8">
							  <input type="text" class="form-control" name='address' value="<?=$intro->input->post('address')?>"   required  /> 
							  <?=$this->error('address')?>
							</div>
						</div>
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['mobile']?>
							<font class="star">*</font> 
							</label>
							<div class="col-sm-8">
							  <input type="text" class="form-control" name='tel'  placeholder="eg. +442071234567" value="<?=$intro->input->post('tel')?>"   required  /> 
							  <?=$this->error('tel')?>
							</div>
						</div>
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label">
							</label>
							<div class="col-sm-8">
							  <input type="checkbox" name="accept" value="1" > 
							  <label ><a href='<?=$intro->uri_links('pages','View',3,'')?>' target="_blank"><?=$intro->lang['agreed']?> </a> <?=$this->error('accept')?> </label>
							 
							</div>
						</div>
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label"> 
							
							</label>
							<div class="col-sm-8">
							
							<input type=text name="cap_numb" value="<?=rand(0,99)?>" style="border:0px;width:70px;font-size:50px;font-weight:bold;color:red">
							<input type="text" name="cap" class='ml-2' value="<?=$intro->input->post('cap')?>" placeholder='type the number'> <?=$this->error('cap')?>
							</div>
						</div>
						
						<div class="mb-3 row">
							<label  class="col-sm-3 col-form-label">
							</label>
							<div class="col-sm-8">
							  <input type="submit" name="maa" value="<?=$intro->lang['registernew']?>" class="btn btn-primary"  />
							</div>
						</div>
						
						
						
						
						
					</form>
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
	function is_taken($field,$value) {
		global $intro, $error;
		
		$sql = $intro->db->query(" select $field from ".PREFIX."_users where $field='$value'");
				
		return (($intro->db->returned_rows == 1) ? true : false);
		
	}	
	function doRegister() {
		global $intro, $error;
		
		$star = "<span style=\"color:red\">*</span>";
		$err = "<span class='error' style='color:red'>{$intro->lang['required']}</span>";
				
		if( preg_match("/http/i" , $intro->input->post('fullname') ) ){
			$this->RegSuccess();
			die();
		}
		if( preg_match("/crypto/i" , $intro->input->post('fullname') ) ){
			$this->RegSuccess();
			die();
		}
	
		$v = new validation();
		
		if($v->required($intro->input->post('address')) == false) $error['address'] = $err;		
		if($v->required($intro->input->post('accept')) == false) $error['accept'] = "<span style=\"color:red\">{$intro->lang['must_accept']}</span>";		
		if($v->required($intro->input->post('fullname')) == false) $error['fullname'] = $err;		
		if($v->required($intro->input->post('city')) == false) $error['city'] = $err;		
		if($v->required($intro->input->post('tel')) == false) $error['tel'] = $err;		
		//if($v->min_length($intro->input->post('username'), 6) == false) $error['username'] = " Min=6";
		//if($v->max_length($intro->input->post('username'), 30) == false) $error['username'] = " Max=30";
		
		if($v->required($intro->input->post('email')) == false) $error['email'] = $err;	
		if($v->email($intro->input->post('email')) == false) $error['email'] = "<span style=\"color:red\">Invalid Email</span>";
		
		if($v->required($intro->input->post('password')) == false) $error['password'] = $err;
		if($v->min_length($intro->input->post('password'), 6) == false) $error['password'] = "<span style=\"color:red\">{$intro->lang['pass_min6']}</span>";
		
		if($v->required_with($intro->input->post('password'),$intro->input->post('password2')) == false) $error['password'] = "<span style=\"color:red\">{$intro->lang['pass_req']}</span>";
		if($v->match_field($intro->input->post('password'),$intro->input->post('password2')) == false) $error['password'] = "<span style=\"color:red\">{$intro->lang['pass_no_match']}</span>";
		
		//if($this->is_taken('username',$_POST['username']) == true) $error['username'] = "Username Taken.";
		if($this->is_taken('email',$intro->input->post('email')) == true) $error['email'] = "<span style=\"color:red\">{$intro->lang['email_taken']}</span>";
		
		if(intval($intro->input->post('country')) == 0) $error['country'] = $err;	
		//if($v->captcha($intro->input->post('captcha')) == false) $error['captcha'] = "<span style=\"color:red\">Incorect code</span>";	
		if( strlen($intro->input->post('fullname')) > 50 ){$error['fullname'] = '<span style="color:red">Full Name must be less than 50 charactures</span>';	}
		
		$cap=intval($intro->input->post('cap'));
		$cap_numb=intval($intro->input->post('cap_numb'));
		
		 
		if ( trim(strtolower($cap)) != $cap_numb ) {
			$error['cap'] = '<span style="color:red"> error capatcha</span>';	
			} 

		if($error){
			$this->index();
			//var_dump($error);
			die();
		}

		$password = $intro->input->post('password');
		$data["fullname"] = _clean($intro->input->post('fullname'));
		$data["email"] = $intro->input->post('email');
		$data["password"] = $intro->pwd($password);
		$data["country"] = intval($intro->input->post('country'));
		$data["city"] = _clean($intro->input->post('city'));
		$data["address"] = _clean($intro->input->post('address'));
		$data["tel"] = _clean($intro->input->post('tel'));
		$data["status"] = 1;
		$data["reg_code"] = "";
		$data["reg_date"] = date('Y-m-d H:i:s');
		$ip = $intro->input->server('REMOTE_ADDR');
		$data["ip"] = $ip;
	

		$intro->db->insert(PREFIX."_users",$data);	
			$userid = $intro->db->insert_id();
			$m_fullname=$data["fullname"];
			$m_email=$data["email"];
			$password=$password;
			$this->SendWelcomeEmail($m_email,$m_fullname,$password);
			$this->RegSuccess();
		
		//$intro->app_redirect("home", 'index');		
	}	
	
	function RegSuccess(){
		global $intro;
		
				
		//$url=$intro->app_url('account','index');	
		$url=$intro->uri_links('login','index',0,'');
		
		pHeader();
		$intro->SuccessMsg("{$intro->lang['reg_success']}", get_msg('reg_success') ,$url, 15000);
		pFooter();
		
	}
	function SendWelcomeEmail($email, $name , $password){
		global $intro;
		
		$url = $intro->option['site_url'] . $intro->base_url;
		
		$link = "{$url}index.php/login/index";
		
		$msg = get_msg('email_register_welcome');
		$msg = str_replace(
			array("{name}" , "{email}" , "{pass}" ,"{site_name}" , "{site_url}" , "{logo}"),
			array($name ,  $email , $password , $intro->site_name, $intro->site_url , $intro->logo),
		$msg);

		$intro->send_email($email, $name,$intro->lang['reg_msg_title'],$msg);
		
	


	}
	/*
	function SendActivateEmail($to , $name, $code,$userid){
		global $intro;
		
		#link to send by email
		$link = "{$intro->site_url}{$intro->base_url}index.php/register/Activate?code=$code&amp;id=$userid&amp;validate=".uniqid()."&amp;run=".uniqid()."";
				
		$msg_body = "Hello $name, <br/><br/> 
		Thanks for Registring with US. <br/><br/> 
		<a href=\"$link\">Click Here to activate your account.</a> <br/><br/> 
		Or this link: $link  <br/><br/> 
		<br/> Thanks<br/><br/>
		{$intro->option['site_name']} <br/> {$intro->option['site_url']}";
				
		$intro->send_email($to, $name,"Link to activate your account.",$msg_body);

	}
	function Activate(){
		global $intro;
		
		$code = $intro->input->get('code');
		$userid = intval($intro->input->get('id'));
		
		$sql = $intro->db->query(" select reg_code,userid from ".PREFIX."_users where reg_code='$code' and userid=$userid and status=0");
		if(@mysql_num_rows($sql) == 1)
		{
			#validate and update user
			$intro->db->update(PREFIX."_users",array('status' => 1) , "userid=$userid");

			$intro->SuccessMsg("Success", get_msg('user_activiate_sucess'), null, 5000);			
		}
		else{
			$intro->SuccessMsg("Validation Faild", get_msg('user_activiate_faild'), null, 15000);
		}		
	}*/
}
?>