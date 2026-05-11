<?php


class Account_App extends Intro_Apps 
{
	var $appname = null;
	var $base = null;
  
	function __construct($appname,$base)
	{
		global $intro;
		
		$this->appname = $appname;
		$this->base = $base;
		
		if ($intro->auth->auth_user()==false)
		{
			$intro->app_redirect("login", "index");
			die();
		}
	
	}
		function error($index=""){
		global $error;
		
		return isset($error[$index])?$error[$index]:"";
	}
	
	
	function menu($class='')
	{
		global $intro;
		$sess_user = $intro->auth->sess_user();
		$lang=$intro->maa->lang;
		$class1=$class2=$class3=$class4='';
		if($lang=='ar'){$direct="left";}else{$direct="right";}
		
		if($class ==1){
			$class1='active text-white';
		}elseif($class ==2){
			$class2='active text-white';
		}elseif($class ==3){
			$class3='active text-white';
		}elseif($class ==4){
			$class4='active text-white';
		}else{
			
			$class1='';
		}
		
		$x= "<nav class=\"bg-light nav nav-pills flex-column flex-sm-row\">
			<a href=\"{$intro->uri_links('account','index')}\" class=\"border text-dark flex-sm-fill text-sm-center nav-link $class1\"  > <i class=\"fas fa-user-circle mx-2 \"></i> {$intro->lang['myaccount']} </a>  
			<a href=\"{$intro->uri_links('account','orders')}\" class='border text-dark flex-sm-fill text-sm-center nav-link $class2'> <i class=\"fas fa-shopping-bag mx-2 \"></i> {$intro->lang['userorders']} </a> 
			<a href=\"{$intro->uri_links('account','myinfo')}\" class='border text-dark flex-sm-fill text-sm-center nav-link $class3'> <i class=\"fas fa-user-circle mx-2 \"></i> {$intro->lang['userinfo']} </a> 
			<a href=\"{$intro->uri_links('account','mypassword')}\" class='border text-dark flex-sm-fill text-sm-center nav-link $class4' ><i class=\"fas fa-key mx-2 \"></i> {$intro->lang['changepass']}</a> 
			
			</nav>";
		
			
		return $x;
	}
	
	function index()
	{
		global $intro,$home,$array;
		$orders='';
		$sess_user = $intro->auth->sess_user();
		$userid=$sess_user['userid'];
		
		pHeader();
		
						
	
		
		
		$this->PaymentSuccess();
		
		$i=1;
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_orders  where ord_userid=$userid  order by ord_id desc limit 5");
		if($intro->db->returned_rows>0){
				while($row =  $intro->db->fetch_assoc($sql)){
					@extract($row);
				
					$orders.="
							<tr>
							  <th scope=\"row\">$ord_id</th>
							  <td>$ord_date : $ord_date_pay</td>
							  <td>".$intro->maa->Currency_amount($ord_amount)." </td>
							  <td>".$array['order_status'][$ord_status]."</td>
							  <td class=\"center\">
							  <a href='{$this->base}/viewitem?ordid=$ord_id' class='btn btn-primary fancybox fancybox.iframe '>{$intro->lang['viewitems']}</a></td>
							</tr>";
					$i++;
				}
			
		}else{
		$orders="<tr><td colspan=7> {$intro->lang['noitems']}</td></tr>";	
			
		}
	?>
	 
	 <div class="container-fluid pb-5">
	  <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3"> <i class="fas fa-user-circle mx-2 "></i> <?=$intro->lang['myaccount']?></span></h2>
	  <div class="container px-5"><?=$this->menu(1)?></div>
	  <br>
	  <div class="row px-xl-5">
             <div class="col-lg-12 col-md-12 h-auto my-15">
                <div class="h-100 bg-light p-30">
		  <div class="card">
			  <div class="card-header bg-success text-white">
				<i class="fas fa-shopping-bag mx-2"></i> <?=$intro->lang['lastorders']?>
			  </div>
			  <div class="card-body">
				<p class="card-text">
				<div class="table-responsive">
				<table class="table table-striped ">
				  <thead  class="table-dark">
					<tr>
					<th scope="col" ><?=$intro->lang['ordid']?></th>
					<th scope="col"><?=$intro->lang['orddate']?></th>
					<th scope="col"><?=$intro->lang['ordamount']?></th>
					<th scope="col"><?=$intro->lang['ordsstatus']?></th>
					<th scope="col"><?=$intro->lang['options']?></th>
					
					</tr>
				  </thead>
				  <tbody>
					<?=$orders?>
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
	
	<?php
	pFooter();

	}
	function PaymentSuccess(){
		global $intro;
		
		$payment = $intro->input->get_post('payment');
		if($payment == "true")
		{
			echo "
			<div class=\"alert alert-success\">
				".get_msg('payment_success')."
				<button class=\"close\"></button>
			
			</div>";
		}
	}
	function viewitem()
	{
		global $intro,$home,$array;
		$orders=$total=$filelang='';
		 $lang=$intro->maa->lang;
		$sess_user = $intro->auth->sess_user();
		$userid=$sess_user['userid'];
		$ordid=intval($_GET['ordid']);
		$i=1;
		$sql = $intro->db->query("SELECT *,oi.price as oi_price FROM ".PREFIX."_orders_items  oi "
		." left join  ".PREFIX."_orders ord on oi.ord_id=ord.ord_id "
		." left join  ".PREFIX."_products p on oi.prodid=p.id "
		." where oi.ord_id=$ordid and ord.ord_userid=$userid  order by oi.prodid desc ");
		
			$products_numbers=$intro->db->returned_rows;
			
		while($row =  $intro->db->fetch_assoc($sql)){
			@extract($row);
			$name=$row['name_'.$lang];
			$prodName = $lang == "ar"?$name_ar:$name_en;
			$pid = $row['id'];
			$net_price = $oi_price;
			$sub_total = $net_price*$qty;
			$total += $sub_total;
			if($file_lang=='ar'){$filelang="Arabic";}elseif($file_lang=='en'){$filelang="English";}
			$url = $intro->uri_links('products','View',$pid,$prodName);;
			$orders.="<tr >
							<td class='text-center' ><a href=\"$url\" class='text-dark' target=_blank>$prodName</a></td>
							<td class='text-center' ><a href=\"$url\" target=_blank><img src='{$intro->base_url}uploads/news/$photo' width=60 height=60></a></td>
							<td class='text-center' >$filelang</td>
							<td class=text-center' >".$intro->maa->Currency_amount($net_price)." </td>
							<td class='text-center' > X $qty</td>
							<td class='text-center'>".$intro->maa->Currency_amount($sub_total)." </td>
						</tr>";
			
		$i++;	
		}
		
		custom_header('');
		?>
		
		 <div class="container-fluid pb-5">
	 
	  <div class="row px-xl-1">
             <div class="col-lg-12 col-md-12 h-auto my-15">
                <div class="h-100 bg-light p-30">
				 <h4 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3"> <?=$intro->lang['ordid']?> : <?=$ordid?></span></h4>
		  <div class="card">
			  <div class="card-header bg-success text-white">
				<?=$intro->lang['ordid']?> : <?=$ordid?>
			  </div>
			  <div class="card-body">
			  <div ><?=$intro->lang['ordpaymethod']?> : <font color=green><?=$array['paymentmethods'][$ord_pay_method]?>."</font></div>
			  
				<div class="card-text">
				<div class="table-responsive">
				<table class="table table-sm table-striped ">
				  <thead  class="table-dark">
					<tr>
						<th scope="col"><?=$intro->lang['product']?></th>
						<th scope="col"><?=$intro->lang['prodphoto']?></th>
						<th scope="col">Lnaguage</th>
						<th scope="col"><?=$intro->lang['price']?></th>
						<th scope="col"><?=$intro->lang['prod_qnty']?></th>
						<th scope="col"><?=$intro->lang['prod_subtotal']?></th>	
					</tr>
				  </thead>
				  <tbody>
					<?=$orders?>
					<tr>
					<td colspan=4></td>
					<td class='center font-weight-bold'> <?=$intro->lang['prod_total']?> : </td>
					<td class='center'> <b><?=$intro->maa->Currency_amount($total)?> </b>  </td>
					</tr>
				
				  </tbody>
				</table>
				</div>
				</div>
			  </div>
			</div>
			</div>
			</div>
			</div>
	  </div>
		
		<?
		
	}
	
	function orders()
	{
		global $intro,$home,$array;
		$orders='';
		$sess_user = $intro->auth->sess_user();
		$userid=$sess_user['userid'];
		pHeader();
		TableOpen();
		$i=1;
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_orders  where ord_userid=$userid  order by ord_id desc ");
		if($intro->db->returned_rows>0){
			while($row =  $intro->db->fetch_assoc($sql)){
				@extract($row);
				if($i%2==0){$class='even';}else{$class='odd';}
				$orders.="<tr>
								<td class=\"center\">$ord_id</td>
								<td class=\"center\">$ord_date : $ord_date_pay</td>
								<td class=\"center\">".$intro->maa->Currency_amount($ord_amount)." </td>
								<td class=\"center\">".$array['order_status'][$ord_status]."</td>
								<td class=\"center\"><a href='{$this->base}/viewitem?ordid=$ord_id' class='btn btn-primary fancybox fancybox.iframe '>{$intro->lang['viewitems']}</a></td>
							</tr>";
				$i++;
			}
		}else{
			$orders="<tr><td colspan=7> {$intro->lang['noitems']}</td></tr>";	
			
		}
?>

	 <div class="container-fluid pb-5">
	  <h2 class="section-title position-relative text-uppercase mx-xl-2 mb-4"><span class="bg-secondary pr-3"><i class="fas fa-user-circle mx-2 "></i> <?=$intro->lang['myaccount']?></span></h2>
	  <div class="container px-2"><?=$this->menu(2)?></div>
	
	  <div class="row px-xl-5">
             <div class="col-lg-12 col-md-12 h-auto my-15">
                <div class="h-100 bg-light p-30">
		  <div class="card">
			  <div class="card-header bg-success text-white">
				<i class="fas fa-shopping-bag mx-2"></i> <?=$intro->lang['lastorders']?>
			  </div>
			  <div class="card-body">
				<p class="card-text">
				<div class="table-responsive">
				<table class="table table-striped ">
				  <thead  class="table-dark">
					<tr>
					<th scope="col" ><?=$intro->lang['ordid']?></th>
					<th scope="col"><?=$intro->lang['orddate']?></th>
					<th scope="col"><?=$intro->lang['ordamount']?></th>
					<th scope="col"><?=$intro->lang['ordsstatus']?></th>
					<th scope="col"><?=$intro->lang['options']?></th>
					
					</tr>
				  </thead>
				  <tbody>
					<?=$orders?>
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

<?php	  
		
		pFooter();

	}
	
	
	function myinfo(){
		global $id, $intro,$array,$error;
		
		pHeader();
		TableOpen();
		
		$sess_user = $intro->auth->sess_user();
		$userid=$sess_user['userid'];
		
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_users where userid=$userid ");
		$row =  $intro->db->fetch_assoc($sql);
		$fullname = $row['fullname'];
		$email = $row['email'];
		$tel = $row['tel'];
		$country = $row['country'];
		$address = $row['address'];
		$zip_code = $row['zip_code'];
		$city = $row['city'];
		
		$passEdit=$intro->lang['newpassword'];
		?>
 <div class="container-fluid pb-5">
	  <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3"><i class="fas fa-user-circle mx-2 "></i><?=$intro->lang['myaccount']?></span></h2>
	  <div class="container px-5"><?=$this->menu(3)?></div>
	  <br>
	  <div class="row px-xl-5">
             <div class="col-lg-12 col-md-12 h-auto my-15">
                <div class="h-100 bg-light p-30">
		  <div class="card">
			  <div class="card-header bg-success text-white">
				<i class="fas fa-user-circle mx-2 "></i> <?=$intro->lang['userinfo']?>
			  </div>
			  <div class="card-body">
				<p class="card-text">
				<form method="POST" name="form_add"  action="" class='forms container-sm' id='userinfo' >
					<div class="mb-3 row">
					<label class="col-sm-3 col-form-label"><?=$intro->lang["users_fullname"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="text"  class="form-control" name="fullname" value="<?=$fullname?>"> <?=$this->error('fullname')?>
					</div>
				  </div>
				   <div class="mb-3 row">
					<label  class="col-sm-3 col-form-label"><?=$intro->lang["users_email"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="text" dir="ltr" class="form-control" name="xxem" value="<?=$email?>" readonly disabled>
					</div>
				  </div>
				  <div class="mb-3 row">
					<label class="col-sm-3 col-form-label"><?=$intro->lang["users_address"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="text"  dir="ltr" class="form-control" name="address" value="<?=$address?>"> <?=$this->error('address')?>
					</div>
				  </div>
				   <div class="mb-3 row">
					<label class="col-sm-3 col-form-label"><?=$intro->lang["users_city"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="text"  dir="ltr" class="form-control" name="city" value="<?=$city?>"> <?=$this->error('city')?>
					</div>
				  </div>
				   <div class="mb-3 row">
					<label class="col-sm-3 col-form-label"><?=$intro->lang["users_country"]?></label>
					<div class="col-sm-8">
					  <?=form_select_array("country",$array['country'],$country)?> <?=$this->error('country')?>
					</div>
				  </div>
				<div class="mb-3 row">
				<label class="col-sm-3 col-form-label"><?=$intro->lang["users_tel"]?><span style='color:#ff0000'>*</span></label>
				<div class="col-sm-8">
				  <input type="text"  dir="ltr" class="form-control" name="tel" value="<?=$tel?>"> <?=$this->error('tel')?>
				</div>
			  </div>
			
			  
			   <div class="mb-3 row">
				<label class="col-sm-3 col-form-label"> <input type="hidden" name="oldemail"  value="<?=$email?>"></label>
				<div class="col-sm-8">
				  <button type="submit"   class='btn btn-primary'  id='userinfosubmit' ><i class="fas fa-save px-2"></i><?=$intro->lang["savechanges"]?>  </button>
				<div id='result_form'></div>
				</div>
			  </div>
			  </form>
				</p>
				
			  </div>
			</div>
			</div>
			</div>
			</div>
	  </div>
	  
	  
	  
		<script type='text/javascript'>//<![CDATA[
$(function(){
	$('#userinfo')
		.each(function(){
			$(this).data('serialized', $(this).serialize())
		})
        .on('change input', function(){
		
			
           $(this)				
                .find('#userinfosubmit')
                    .attr('disabled', $(this).serialize() == $(this).data('serialized'));
                    
            ;
		var x= 	$(this).serialize() == $(this).data('serialized');
		
			
			$('#userinfosubmit').addClass('login_submit');
			
         })
		
		.find('#userinfosubmit')
			.attr('disabled', true)
	;

});//]]> 

</script>
		
		<?
		

		pFooter();
	
	}
	
	function doeditinfo(){
	
		global $intro,$error,$email;
		$isedit=0;
		$post_fullname = preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $intro->input->post('fullname'));
		$post_address = preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $intro->input->post('address'));
		$post_city = preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $intro->input->post('city'));
		$post_country = preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $intro->input->post('country'));
		$post_tel = preg_replace('/[^0-9+]/ui', '', $intro->input->post('tel'));
		$userid = intval($intro->input->post('userid'));
		$user_email = $intro->input->post('oldemail');
		$xxem = $intro->input->post('xxem');
		$intro->auth->update_sess_fullname($post_fullname);
		
		$sess_user = $intro->auth->sess_user();
		$userid = intval($sess_user['userid']);
		
	

		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_users  where userid=$userid  ");
		$row =  $intro->db->fetch_assoc($sql);
		@extract($row);
		
		$isfullname = ($post_fullname != $fullname) ? 1 : 0 ;
		$isaddress = ($post_address != $address) ? 1 : 0 ; 
		$iscity = ($post_city != $city) ? 1 : 0 ; 
		$iscountry = ($post_country != $country) ? 1 : 0 ; 
		$istel = ($post_tel != $tel) ? 1 : 0 ; 
		
		if($isfullname==1 || $isaddress==1 || $iscity==1 || $iscountry==1 ||  $istel==1 ){
			
		$data["fullname"] = $post_fullname;
		$data["address"] = $post_address;
		$data["city"] = $post_city;
		$data["country"] = $post_country;
		$data["tel"] = $post_tel;
		
		$intro->db->update(PREFIX."_users",$data,"userid='$userid'");
		
		echo "<div class=\alert alert-success\" role=\"alert\">
				 <font color=graan size=3> {$intro->lang['editsaved']} </font>
				</div>";
		
		//$this->SendEditInfoEmail($email , $fullname, $data);
		}else{
			
				echo "<div class=\alert alert-danger\" role=\"alert\">
				  <font color=red size=3>  Data not Changed</font>
				</div>";
				
		}
			
			
	}
	function SendEditInfoEmail($email , $name, $data){
		global $intro,$array;

		$country = intval($data['country']);

		$emailBody = get_msg('email_change_info');
		$password = '';
		$emailBody = str_replace(
			array("{fullname}","{address}","{city}","{country}","{tel}","{name}" , "{email}"  ,"{site_name}" , "{site_url}" , "{logo}" ),
			array($data["fullname"],$data["address"],$data["city"],$array["country"][$country],$data["tel"],$name ,  $email , $intro->site_name, $intro->site_url , $intro->logo ),
		$emailBody);

		$intro->send_email($email, $name,$intro->lang['email_subject_info_changed'],$emailBody);

	}

	function mypassword(){
		global $id, $intro,$error;
		
		pHeader();
		TableOpen();
		
		$sess_user = $intro->auth->sess_user();
		$userid=$sess_user['userid'];
		
	
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_users where userid=$userid ");
		$row =  $intro->db->fetch_assoc($sql);
		
		$email = $row['email'];
		$tel = $row['tel'];
		$country = $row['country'];
		$address = $row['address'];
		$city = $row['city'];
	?>
	 <div class="container-fluid pb-5">
	  <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3"><i class="fas fa-user-circle mx-2 "></i> <?=$intro->lang['myaccount']?></span></h2>
	  <div class="container px-5"><?=$this->menu(4)?></div>
	  <br>
	  <div class="row px-xl-5">
             <div class="col-lg-12 col-md-12 h-auto my-15">
                <div class="h-100 bg-light p-30">
		  <div class="card">
			  <div class="card-header bg-success text-white">
				<i class="fas fa-key mx-2 "></i> <?=$intro->lang['changepass']?>
			  </div>
			  <div class="card-body">
				<p class="card-text">
				<div class='alert alert-warning text-center' ><?=$intro->lang['passwordcondition']?> </div>
				
				<form method="POST" name="form_add"  action="" class='forms container-sm' id='edit_password' >
					<div class="mb-3 row">
					<label class="col-sm-3 col-form-label"><?=$intro->lang["oldpassword"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="password"  class="form-control" name="oldpassword" value="" required> <?=$error[oldpassword]?>
					</div>
				  </div>
				  <div class="mb-3 row">
					<label class="col-sm-3 col-form-label"><?=$intro->lang["newpassword2"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="password"  class="form-control" name="password" value="" required> <?=$error[password]?>
					</div>
				  </div>
				  <div class="mb-3 row">
					<label class="col-sm-3 col-form-label"><?=$intro->lang["newpassword3"]?><span style='color:#ff0000'>*</span></label>
					<div class="col-sm-8">
					  <input type="password"  class="form-control" name="password2" value="" required> <?=$error[password2]?>
					</div>
				  </div>
				   <div class="mb-3 row">
					<label class="col-sm-3 col-form-label"></label>
					<div class="col-sm-8">
					  <button type="submit" name="maa"  value="" class="btn btn-primary" id='reset_submit'><i class="fas fa-save px-2"></i> <?=$intro->lang['savenewpass']?></button>
					  <div id='result_form'></div></td>
					</div>
				  </div>
				  
				  </form>
				</p>
				
			  </div>
			</div>
			</div>
			</div>
			</div>
	  </div>
	  
	  
	<?php
		
		pFooter();
	}
	
	function doeditpassword(){
		global $intro,$error,$email;
	
		$oldpassword = $intro->input->post('oldpassword');
		$password = $intro->input->post('password');
		$password2 = $intro->input->post('password2');
		
		$sess_user = $intro->auth->sess_user();
		$userid=$sess_user['userid'];
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_users where userid=$userid; ");
		$row =  $intro->db->fetch_assoc($sql);
		$db_password = $row['password'];
		$user_email = $row['email'];
		$address = $row['address'];
		$city = $row['city'];
		$fullname = $row['fullname'];
		$country = $row['country'];
		$tel = $row['tel'];
	
		
		if(strlen($password) <6 ){
			echo "<font color=red>{$intro->lang['passwordlength']}</font>";
			die();
		}
		
		if( $intro->pwd($oldpassword) != $db_password ){
			echo "<font color=red>{$intro->lang['oldpasswrong']}</font>";
			die();
		}
		
		if($password != $password2 ){
			
			echo "<font color=red>{$intro->lang['notmatched']}</font>";
			die();
		}

		if($password =="" ||  $password2 =="" || $oldpassword='' ){
			
			echo "<font color=red>{$intro->lang['allrequird']}</font>";
			die();
		}

		$data["password"] = $intro->pwd($password);
		$intro->db->update(PREFIX."_users",$data,"userid='$userid'");
		$intro->auth->update_sess_pass($data["password"]);
		
		$data2["fullname"] = $fullname;
		$data2["address"] = $address;
		$data2["city"] = $city;
		$data2["country"] = $country;
		$data2["tel"] = $tel;
		$data2["password"] = $password;
		$data2["email"] = $user_email;
		
		$this->SendEditpassword($user_email , $fullname, $data2);
		echo "<font color=green>{$intro->lang['passwordchange']}</div>";
		echo "	
		<script>
		$('#edit_password')[0].reset();
			$(document).ready(function()
				{
			$('#reset_submit').hide('slow');
				});
				</script>";
		
	}

	
	function SendEditpassword($email , $name, $data){
		global $intro,$array;

		$country = intval($data['country']);
	
		$emailBody = get_msg('email_account_pass');
		$today=date('Y-m-d H:i:s');
		$ip = $intro->input->server('REMOTE_ADDR');
		$password = '';
		$password=$data["password"];
		$emailBody = str_replace(
			array("{name}","{password}","{email}","{today}" ,"{site_name}" , "{site_url}" , "{logo}" ),
			array($name,$password,$email,$today , $intro->site_name, $intro->site_url , $intro->logo ),
		$emailBody);

		$intro->send_email($email, $name,$intro->lang['email_subject_pass_changed'],$emailBody);

	}
}

?>