<?php
session_start();
class Order_App extends Intro_Apps 
{
	var $appname = null;
	var $base = null;
	var $header_data = array();
	var $return_url = '';
	var $cancel_url = '';
	
	function __construct($appname,$base)
	{
		global $intro;
		
		$this->appname = $appname;
		$this->base = $base;
		$this->header_data = array();
		$this->sessid = session_id();
		if ($intro->auth->auth_user()== true)
		{
			$sess_user = $intro->auth->sess_user();
			$userid = intval($sess_user['userid']);
			$this->sql_cart = "cart.userid=$userid";
		}
		else{
			$this->sql_cart = "cart.sessid='$this->sessid'";
		}
		
		$this->return_url = $intro->site_url."/index.php/{$intro->uri->lang}/order/success";
		$this->cancel_url = $intro->site_url."/index.php/{$intro->uri->lang}/order/index?fail=true";
		
	/*	if (!preg_match('/www\..*?/', $_SERVER['HTTP_HOST'])) {
			
			$this->return_url = str_replace("www.","",$this->return_url);
			$this->cancel_url = str_replace("www.","",$this->cancel_url);
		}*/

	}
	
	function error($index=""){
		global $error;
		return isset($error[$index])?$error[$index]:"";
	}
	
	function total(){
		global $intro;
		
		$sql = $intro->db->query("SELECT sum(prod.net_price*cart.qty) as total "
		." from ".PREFIX."_products prod"
		." JOIN  ".PREFIX."_cart cart ON prod.id=cart.prodid "
		." where cart.sessid='$this->sessid';");
		$row = $intro->db->fetch_assoc($sql);
		
		return floatval($row['total']);
		
	}	

	function index()
	{
		global $intro,$error;
		
		$lang = $intro->maa->lang;
		if ($intro->auth->auth_user()==false)
		{
			$intro->app_redirect("login", "index","?order=1");
			die();
		}
		$sess_user = $intro->auth->sess_user();
		
		
		pHeader();
		
			
		$total = $this->total();
		
			$sql = $intro->db->query("SELECT * from ".PREFIX."_gateway where gate_status=1 ");
			while($row = $intro->db->fetch_assoc($sql)){
				$gate_name=$row['gate_name'];
				$gate_discount=$row['gate_discount'];
				
				
				$total= round( $total - ( $total * ($gate_discount/100)  ) , 0);
					if ($row['gate_id'] == 6 ) {$dolar="USDT";}else{$dolar= "$";};
				
				if($gate_discount != ''){
					$discount_note .= "<div class='mb-3'>{$intro->lang['discount_gateway']}	 
					[  <span class=\"text-success font-weight-bolder\"> {$row['id']} $gate_name </span>  ] 
						{$intro->lang['discount_gatway2']}	 ( <span class=\"text-success font-weight-bolder\">  $gate_discount %   </span> ) 
						{$intro->lang['discount_gateway3']}	
						[ <span class=\"text-danger font-weight-bolder\"> $total  $dolar   </span>] 
						</div>";
				}
				
			}
		
		
		echo "<div class=\"container-fluid\">
				<h4 class=\"section-title position-relative text-uppercase mx-xl-5 mb-4\"><span class=\"bg-secondary px-4\">{$intro->lang['email_subject_order']}</span></h4>
				<div class=\"container\">
				<div class=\"card bg-light m-auto\" style=' max-width:60rem;'>
					  <div class=\"card-header bg-success \">
						<center><h2 class='text-white' > Total Order :   ". $intro->maa->Currency_amount($this->total()) ." </h2>	</center> 
					  </div>
					  <div class=\"card-body\">
						<center><h3>{$intro->lang['selectpayment']}</h3></center>
						$discount_note
						
						<form action='{$this->base}/paynow' method=post  style='width:30%;margin:auto;'>
							<center class=\"text-danger\"> ".$this->payError()."  {$error['msg']}  </center> 
							<div>".form_select_global('paymentmethod','Choose payment method','gateway','','gate_id','gate_name',"where gate_status=1")."</div>
								
							<center><input type=submit name='' class='buynow btn btn-primary mt-2 btn-lg px-5 '  value='{$intro->lang['nextstep']}' ></center>
						</form>
						
						<div class='checkout_msg' style='text-align:justify;'>".get_msg('checkout_msg')."</div>	
						
						</p>
						
					  </div>
					</div>
					</div>

				
			</div>
			";
		
		pFooter();
	}	

	
	function paynow()
	{
		global $intro,$array,$error;

		$lang = $intro->maa->lang;
		$paymentmethod = intval($intro->input->get_post('paymentmethod'));
	//	$_SESSION['paymentmethod'] = $paymentmethod;
		
		if($paymentmethod !=8){
			if ($intro->auth->auth_user() == false)
			{
				$intro->app_redirect("login", "index");
				die();
			}
			
		}
		
		
		if($paymentmethod ==0){
			$error['msg'] = "<font color=red>Please choose payment method. </font> ";
			$this->index();
			die();
		}
		pHeader();
		TableOpen();
		
			$sql = $intro->db->query("SELECT prod.name_ar,prod.id as prodid,prod.name_en,prod.photo,prod.net_price,cart.id as cartid,cart.qty,cart.file_lang as filelang "
				." from ".PREFIX."_cart cart "
				." JOIN ".PREFIX."_products prod ON cart.prodid=prod.id "
				." where cart.sessid='$this->sessid' order by cart.id asc");	
				$total = 0;
				$products_numbers=$intro->db->returned_rows;	
		
				
			$sess_user = $intro->auth->sess_user();
			
			$data['ord_userid'] = $sess_user['userid'];
			$data['ord_date'] = date('Y-m-d');
			$data['ord_amount'] = $this->total();
			$data['ord_date_pay'] = date('H:i:s');
			$data['ord_pay_method'] = $paymentmethod;
		
			$data['ord_status'] = 1;
			
			
			if( isset($_SESSION['orderid']) && intval($_SESSION['orderid']) != 0){
				
				$orderid = $_SESSION['orderid'];
				$total = $_SESSION['total'];
				$paymentmethods = $_SESSION['paymentmethod'];
				
				
				$intro->db->update(PREFIX."_orders",$data,"ord_id=$orderid");
				$intro->db->query("DELETE FROM ".PREFIX."_orders_items WHERE ord_id=$orderid ");
				$sql2 = $intro->db->query("SELECT prod.name_ar,prod.id as prodid,prod.name_en,prod.photo,prod.net_price,cart.id as cartid,cart.qty,cart.file_lang as filelang "
				." from ".PREFIX."_cart cart "
				." JOIN ".PREFIX."_products prod ON cart.prodid=prod.id "
				." where cart.sessid='$this->sessid' order by cart.id asc");	
				$total = 0;
				$products_numbers=$intro->db->returned_rows;
				
				while($row = $intro->db->fetch_assoc($sql2))
				{
					@extract($row);

					$sub_total = $qty*$net_price;
					$total += $sub_total;
					
					$data2['ord_id'] = $orderid;
					$data2['prodid'] = $prodid;
					$data2['qty'] = $qty;
					$data2['price'] = $sub_total;
					$data2['file_lang'] = $filelang;
					
					$intro->db->query("insert into ".PREFIX."_orders_items(ord_id,prodid,qty,price,file_lang) 
										values('$orderid','$prodid','$qty','$sub_total','$filelang') ");
					$data2="";
				}
					
			}else{
					$intro->db->insert(PREFIX."_orders" , $data );
					$intro->db->insert(PREFIX."_orders" , $data );
				$orderid = $intro->db->insert_id();	
				$_SESSION['orderid'] = $orderid;
				$_SESSION['paymentmethod'] = $array['paymentmethods'][$paymentmethod];
		
				
				$sql = $intro->db->query("SELECT prod.name_ar,prod.id as prodid,prod.name_en,prod.photo,prod.net_price,cart.id as cartid,cart.qty,cart.file_lang as filelang "
				." from ".PREFIX."_cart cart "
				." JOIN ".PREFIX."_products prod ON cart.prodid=prod.id "
				." where cart.sessid='$this->sessid' order by cart.id asc");	
				$total = 0;
				$products_numbers=$intro->db->returned_rows;
				
				while($row = $intro->db->fetch_assoc($sql))
				{
					@extract($row);
					$sub_total = $qty*$net_price;
					$total += $sub_total;
					$data2['ord_id'] = $orderid;
					$data2['prodid'] = $prodid;
					$data2['qty'] = $qty;
					$data2['price'] = $net_price;
					$data2['file_lang'] = $filelang;
					
					$intro->db->query("insert into ".PREFIX."_orders_items(ord_id,prodid,qty,price,file_lang) 
										values('$orderid','$prodid','$qty','$net_price','$filelang') ");
					$data2="";
				}	
				
			
			$_SESSION['total'] = $total;
			$_SESSION['paymentmethod'] = $array['paymentmethods'][$paymentmethod];
						
		}
		
			
		if($paymentmethod == 3){
			$gate = $this->getGateway($paymentmethod);
		
			echo $this->paypal($gate['gate_user'],$total,$orderid);
		}
		elseif($paymentmethod == 2){
			$gate = $this->getGateway($paymentmethod);
			echo $this->skrill($gate['gate_user'],$total,$orderid);
		}
		
		elseif($paymentmethod == 9){
		
			$gate = $this->getGateway($paymentmethod);
			$discount= $gate['gate_discount'];
			echo $this->perfectMoney($gate['gate_user'],$total,$orderid,$discount);
		}
		elseif($paymentmethod == 8){
			$gate = $this->getGateway($paymentmethod);
			$discount= $gate['gate_discount'];
			
			echo $this->visa($gate,$total,$desc,$fname,$orderid);			
		}
		elseif($paymentmethod == 6){
			$gate = $this->getGateway($paymentmethod);
		
			
			$url = $intro->url . $intro->uri->lang;
			$discount= $gate['gate_discount'];
			$dolar = $gate['gate_id'] == 6 ? "USDT":"$";
			
			if($discount != ''){
				$total= round( $total - ( $total * ($discount/100)  ) , 0);
					?>
					<style>
					#elem {animation: blink 1s infinite} @keyframes blink {from {opacity: 0} to { opacity: 1 }}
					</style>
					<?php
					$discount_note .= "<div class='mb-2'> 
					 {$intro->lang['discount_gateway4']}	
						[ <span class=\"text-danger font-weight-bolder\" style='font-size:1.3rem ' id='elem'> $total  $dolar   </span>] 
						</div>";
				}
			echo "<div class=\"container-fluid\">
				<h2 class=\"section-title position-relative text-uppercase mx-xl-5 mb-4\"><span class=\"bg-secondary px-4\">{$intro->lang['email_subject_order']}</span></h2>
				<div class=\"container\">
				<div class=\"card bg-light m-auto\" style=' max-width:60rem;'>
					  <div class=\"card-header bg-primary \">
						<center><h2 class='text-white' > Total Order :  $ ". $this->total() ." </h2>	</center> 
					  </div>
					  <div class=\"card-body\">
						<center><h3>Charg By USDT </h3></center>
							<div class='checkout_msg'>USDT Wallet : <span class='text-success' > ".$gate['gate_path']."</span> </div>	
							<div class='checkout_msg'>USDT Network : <span class='text-success' > ".$gate['gate_privatekey']."</span></div>	
							$discount_note
							<br>
							<center>
							<a href=\"https://buyformula.net/en/account/orders\" class='btn btn-warning mx-3 font-weight-bolder'>{$intro->lang['backtoorder']}</a>
							<a href=\"https://buyformula.net/en/order/success\" class='btn btn-success font-weight-bolder mx-3 '>{$intro->lang['donepay']}</a>
							</center>
						</p>
						
					  </div>
					</div>
					</div>
			</div>
			";
				die();
		}
		else{
			$this->onlyPaypal();
			
		}
		
	///	$this->SendOrderEmail( $sess_user['email'] , $sess_user['userid'],$orderid,$total,$_SESSION['paymentmethod']);

		TableClose();	
		pFooter();
	
	}
	
	function SendOrderEmail($email ,$name,$orderid,$total,$method){
		global $intro,$array;

	
		$emailBody = get_msg('email_account_order');
		$table = $this->order_items($orderid);
		$totalitems =$total;
		
		$emailBody = str_replace(
			array("{method}","{table}","{orderid}","{name}" , "{email}"  , "{logo}" ),
			array($method,$table,$orderid,$name ,  $email  , $intro->logo ),
		$emailBody);

		$intro->send_email($email, $name,$intro->lang['email_subject_order'],$emailBody , "Order from");

	}

	function order_items($order_id=0)
	{
		global $intro,$home,$array;
		
		$style = "border:1px solid #111111;";
		$stylebg = "background-color:#CACAD9;";
		$bold = "font-weight: bold;";
		$c = "text-align:center;";
		
		$orders=$total='';
		
		 $lang=$intro->maa->lang;
		$sess_user = $intro->auth->sess_user();
		$userid=$sess_user['userid'];
		$order_id=$order_id;
		$i=1;
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_orders_items  oi "
		." left join  ".PREFIX."_orders ord on oi.ord_id=ord.ord_id "
		." left join  ".PREFIX."_products p on oi.prodid=p.id "
		." where oi.ord_id=$order_id   order by oi.prodid desc ");
		
		$table_order = $this->css() . "
		<table class=\"gridtable\" width=\"100%\" $style>
		<tr>
			<th style=\"$style $stylebg $bold\">{$intro->lang['product']}</th>
			<th style=\"$style $stylebg $bold\">{$intro->lang['prod_price']}</th>
			<th style=\"$style $stylebg $bold\">{$intro->lang['prod_qnty']}</th>
			<th style=\"$style $stylebg $bold\">{$intro->lang['prod_subtotal']}</th>
		</tr>";
		while($row =  $intro->db->fetch_assoc($sql)){
			@extract($row);

			$name=$row['name_'.$lang];
			$prodName = $lang == "ar"?$name_ar:$name_en;

			$sub_total = $net_price*$qty;
			$total += $sub_total;
			$table_order .="
			<tr>
				<td style=\"$style\">$prodName  </td>
				<td style=\"$style $c\"> $ $net_price </td>
				<td style=\"$style $c\">$qty</td>
				<td style=\"$style $c\">$ $sub_total </td>
			</tr>";
			
		$i++;	
		}
		
		$table_order .="
				<tr>
					<td></td>
					<td></td>
					<td style=\"$bold $c\">{$intro->lang['prod_total']}</td>
					<td style=\"$style $stylebg  $bold $c\">$ $total  </td>
				</tr>
			</table>";
		
		return $table_order;
	}
	
	
	function success(){
		global $intro,$array,$error;
		
		$url = $intro->url . $intro->uri->lang;
		
		
		
		if(isset($_SESSION['orderid']) && isset($_SESSION['paymentmethod']) && isset($_SESSION['total']))
		{
			$sess_user = $intro->auth->sess_user();
			
			$gate = @$intro->url_segments[3];
			
			$orderid = $_SESSION['orderid'];
		
			$total = $_SESSION['total'];
			$email = $sess_user['email'];	
			$name = $sess_user['fullname'];	
			
			//$this->SendOrderEmail($email , $name, $orderid, $total,$_SESSION['paymentmethod']);
			
			unset($_SESSION['total']);
			unset($_SESSION['orderid']);
			unset($_SESSION['paymentmethod']);
			
			$intro->db->query("UPDATE ".PREFIX."_orders set ord_status=1 where ord_id=$orderid; ");
			$intro->db->query("DELETE FROM ".PREFIX."_cart where sessid='$this->sessid'; ");
			

			
		//	header("Location: {$url}/account/index?payment=true");
			echo "<script>window.location.href = \"https://www.buyformula.net/en/account/index?payment=true\";</script>";


			
		}else{
			
			$sess = var_export($_SESSION, true);
			
			$sess_user = $intro->auth->sess_user();
			
			$ref = $_SERVER['HTTP_REFERER'];
			$ip = $_SERVER['REMOTE_ADDR'];
			$msg = "<h1>You are getting this email because something went wrong with Payment Proccess.</h1><br/>"
			."HTTP_REFERER = $ref <br/><br/>"
			."userid={$sess_user['userid']} <br/>"
			."email={$sess_user['email']} <br/>"
			."fullname={$sess_user['fullname']} <br/>"
			."ipaddress=$ip <br/>"
			." <br/> Session Conents= <br/><br><hr> $sess ";
			
			//$intro->send_email("maaking@gmail.com", "Mohammed","Error when pay and return to merchant", $msg, "Order from");
			
			echo "<h3>Oops! Something went errror. Please contact support: {$intro->option['site_email']}</h3>";
			//echo "<script>window.location.href = \"https://www.buyformula.net/en/account/index?payment=true&msg=Oops\";</script>";

			//header("Location: {$url}/account/index?payment=true&amp;msg=Oops!+Something+went+errror.");
		
		}
	}
	function getGateway($gate_id=0){
		global $intro;
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_gateway where gate_id=$gate_id; ");
		$row =  $intro->db->fetch_assoc($sql);
		return $row;
	}
	function getOrder(){
		global $intro;
		
		$orderid = intval($_SESSION['orderid']);
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_orders where ord_id=$orderid; ");
		$row =  $intro->db->fetch_assoc($sql);
		return $row;
	}
	function paypal($username,$amound,$orderid){
		
		return "
	
		<div class=\" m-auto alert alert-warning\" style='max-width:50rem;'>
			 You are being redirected to PayPal payment. Please wait.
			<button class=\"close\"></button>
		
		</div>
		
		
			
			<form style=\"display: none;\" id=\"paypal\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
			<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
			<input type=\"hidden\" name=\"business\" value=\"$username\">
			<input type=\"hidden\" name=\"item_name\" value=\"buyformula.net Order# $orderid\">
			<input type=\"hidden\" name=\"item_number\" value=\"$orderid\">
			<input type=\"hidden\" name=\"amount\" value=\"$amound\">
			<input type=\"hidden\" name=\"no_shipping\" value=\"0\">
			<input type=\"hidden\" name=\"no_note\" value=\"1\">
			<input type=\"hidden\" name=\"currency_code\" value=\"USD\">
			<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF\">
			<input type=\"hidden\" name=\"return\" value=\"{$this->return_url}\">
			<input type=\"hidden\" name=\"cancel_return\" value=\"{$this->cancel_url}\">
			<input type=\"image\" src=\"https://www.paypal.com/en_AU/i/btn/btn_buynow_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online.\">
			
		</form>
		<script>
		$(document).ready(function(){
			$('#paypal').submit();
		});
		</script>";
	}
	function skrill($username,$amound,$orderid){
		
		return "
		<div class=\"checkout_msg\">
		<p></p>
		<h4> You are being redirected to Skrill payment. Please wait. </h4>
		<p></p>
		</div>
		
		<form id=\"paypal\" action=\"https://www.moneybookers.com/app/payment.pl\" method=\"post\">
			<input type=\"hidden\" name=\"pay_to_email\" value=\"$username\">
			
			<input type=\"hidden\" name=\"language\" value=\"EN\">
			<input type=\"hidden\" name=\"amount\" value=\"$amound\">
			<input type=\"hidden\" name=\"currency\" value=\"USD\">
			<input type=\"hidden\" name=\"detail1_description\" value=\"buyformula.net Order# $orderid\">
			<input type=\"hidden\" name=\"detail1_text\" value=\"buyformula.net Order# $orderid\">
			<input type=\"hidden\" name=\"confirmation_note\" value=\"Thank you fir your payment.\">
			<input type=\"submit\" value=\"Pay!\">
			<input type=\"hidden\" name=\"return_url\" value=\"{$this->return_url}\">
			<input type=\"hidden\" name=\"cancel_url\" value=\"{$this->cancel_url}\">
			<input type=\"hidden\" name=\"status_url\" value=\"info@buyformula.net\">
			<!--<input type=\"hidden\" name=\"payment_type\" value=\"VSA\">-->

		</form>

		<script>
		$(document).ready(function(){
			$('#paypal').submit();
		});
		</script>";
	}
	
	function perfectMoney($username,$amound,$orderid,$discount=0){
		global $intro;
			
			
			$url = $intro->url . $intro->uri->lang;
				$total=$amound;
		
				
			if($discount !=  0){
				$total= round( $total - ( $total * ($discount/100)  ) , 0);
					?>
					<style>
					#elem {animation: blink 1s infinite} @keyframes blink {from {opacity: 0} to { opacity: 1 }}
					</style>
					<?php
					$discount_note = "<div class='mb-2'> 
					 {$intro->lang['discount_gateway4']}	
						[ <span class=\"text-danger font-weight-bolder\" style='font-size:1.3rem ' id='elem'> $total $   </span>] 
						</div>";
				}
				$data="<div class=\"container-fluid\">
				<h2 class=\"section-title position-relative text-uppercase mx-xl-5 mb-4\"><span class=\"bg-secondary px-4\">{$intro->lang['email_subject_order']}</span></h2>
				<div class=\"container\">
				<div class=\"card bg-light m-auto\" style=' max-width:60rem;'>
					  <div class=\"card-header bg-primary \">
						<center><h2 class='text-white' > Total Order :  $ ". $this->total() ." </h2>	</center> 
					  </div>
					  <div class=\"card-body\">
						<center><h3>Pay By Perfect Money </h3></center>
							<div class='checkout_msg'>Perfect Money Account  : <span class='text-success' > $username </span> </div>	
							$discount_note
							<br>
							<center>
							<a href=\"$url/account/orders\" class='btn btn-warning mx-2 font-weight-bolder'>{$intro->lang['backtoorder']}</a>
							<a href=\"$url/order/success\" class='btn btn-success font-weight-bolder'>{$intro->lang['donepay']}</a>
							</center>
						</p>
						
					  </div>
					</div>
					</div>
			</div>";
		return $data;	
	}
	
	function visa($gate,$amound,$desc,$fname,$orderid){
		global $intro,$array;
		
	
				$sql = $intro->db->query("SELECT * from ".PREFIX."_currences where id=1  ");
				$rows_numb=$intro->db->returned_rows;
				if($rows_numb == 0 ){
					$amound=$amound;
				}else{
					$row = $intro->db->fetch_assoc($sql);
					$amound=intval($amound * $row['cur_per_dollar'] * 100 );
					
				}
					
		?>
		
    <script src="https://cube.paysky.io:6006/js/LightBox.js?v=1.2"></script> 
   <!-- <script src="https://cube.paysky.io/Portal/Transactions/LightboxNew/CallLightBoxByJS/Lightbox.js"></script>   Staging --> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

   
	<script type="text/javascript">
    var AmountTrxn = "<?=$amound?>";
    var MID = "13351995983";
    var TID = "56609636";
    var MerchantReference = "REF-" + Date.now();
    var Secret = "ccf6f046de5b1178374ff6b4987a5e17";
    var TrxDateTime = new Date().toGMTString();
    var SecureHash = generateSecureHash(TrxDateTime, AmountTrxn, MerchantReference, MID, TID, Secret);

    Lightbox.Checkout.configure = {
        MID: MID,
        TID: TID,
        AmountTrxn: AmountTrxn,
        SecureHash: SecureHash,
        MerchantReference: MerchantReference,
        TrxDateTime: TrxDateTime,
        completeCallback: function (data) {
            console.log('completed');
            console.log(data);
        },
        errorCallback: function (error) {
            alert(error);
        },
        cancelCallback: function () {
            console.log('canceled');
        }
    };

    function showLightBox() {
        Lightbox.Checkout.showLightbox();
    }

    function generateSecureHash(time, amount, merchRef, merchantId, terminalId, secretKey) {
        const hashing = `Amount=${amount}&DateTimeLocalTrxn=${time}&MerchantId=${merchantId}&MerchantReference=${merchRef}&TerminalId=${terminalId}`;
        const secretKeyWordArray = CryptoJS.enc.Hex.parse(secretKey);
        const hmac = CryptoJS.HmacSHA256(hashing, secretKeyWordArray);
        const hmacHex = hmac.toString(CryptoJS.enc.Hex);
        const mac = hmacHex.toUpperCase();
        return mac;
    }
</script>



		
		<?php
		
		echo"<div class=\"container\">
				<div class=\"card bg-light m-auto\" style=' max-width:60rem;'>
					  <div class=\"card-header bg-success \">
						<center><h2 class='text-white' > Total Order :   ". $intro->maa->Currency_amount($this->total()) ." </h2>	</center> 
					  </div>
					  <div class=\"card-body\">
						<center><h3> ".$gate['gate_name']." </h3></center>
						<div class=\"m-auto\" style=\"width:90%\">";
						?>
					<form method="POST" action="<?=$this->base?>/doRegister" name="reg_form" id="reg_form" >
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
							<label  class="col-sm-3 col-form-label"><?=$intro->lang['country']?>
							<font class="star">*</font> 
							</label>
							
							<div class="col-sm-8">
							  <?=form_select_array("country",$array['country'],$intro->input->post('country'))?>
							  <?=$this->error('country')?>
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
					<div id="returndata">xxxx</div>

					<script>
					$(document).ready(function(){
						$("#TransactionButton").hide();
						$("#reg_form").on("submit", function(e){
							e.preventDefault(); // منع الإرسال العادي
							
							$.ajax({
								url: $(this).attr("action"),
								method: $(this).attr("method"),
								data: $(this).serialize(),
								beforeSend: function(){
									$("#returndata").html("<p class='text-info'>جارٍ الإرسال...</p>");
								},
								success: function(response){
									if(response == 'Done' ){
										$("#TransactionButton").show();
										$("#reg_form").hide();
										
										
									}else{
										$("#TransactionButton").hide();
									}
									$("#returndata").html(response);
								},
								error: function(){
									$("#returndata").html("<p class='text-danger'>حدث خطأ أثناء الإرسال.</p>");
								}
							});
						});
					});
					</script>

				
				
				<?php 
                    echo "				
						</div>
							<center> <button onclick=\"showLightBox()\" class=\"btn btn-info py-3 px-5 m-auto mt-5 \" id=\"TransactionButton\" >
							Make Transaction
							</button></center>
							<br>
						<div class='checkout_msg' style='text-align:justify;'>".get_msg('instantpayment')."</div>	
						</p>
					  </div>
					</div>
			</div>";
			
			
			
		
	}
	function doRegister() {
		global $intro, $error;
		
		$star = "<span style=\"color:red\">*</span>";
		$err = "<span class='error' style='color:red'>{$intro->lang['required']}</span>";
	
		if($intro->input->post('address') == '' ){ 
			echo "<div class='error' style='color:red'>{$intro->lang['address']} : {$intro->lang['required']}</div>";die();
		}
		if($intro->input->post('fullname') == '' ){ 
			echo "<div class='error' style='color:red'>{$intro->lang['fullname']} : {$intro->lang['required']}</div>";die();
		}
		if($intro->input->post('email') == '' ){ 
			echo "<div class='error' style='color:red'>{$intro->lang['email']} : {$intro->lang['required']}</div>";die();
		}
		if($intro->input->post('country') == '' ){ 
			echo "<div class='error' style='color:red'>{$intro->lang['country']} : {$intro->lang['required']}</div>";die();
		}
		if($intro->input->post('tel') == '' ){ 
			echo "<div class='error' style='color:red'>{$intro->lang['mobile']} : {$intro->lang['required']}</div>";die();
		}
		
		
		$cap=intval($intro->input->post('cap'));
		$cap_numb=intval($intro->input->post('cap_numb'));
		
		 
		if ( trim(strtolower($cap)) != $cap_numb ) {
			echo  '<div style="color:red"> error capatcha</div>';	
			die();
			} 

		$data["fullname"] = _clean($intro->input->post('fullname'));
		$data["email"] = $intro->input->post('email');
		$data["password"] = $intro->pwd('123654');
		$data["country"] = intval($intro->input->post('country'));
		$data["city"] = 'city';
		$data["address"] = _clean($intro->input->post('address'));
		$data["tel"] = _clean($intro->input->post('tel'));
		$data["status"] = 1;
		$data["reg_code"] = "";
		$data["reg_date"] = date('Y-m-d H:i:s');
		$ip = $intro->input->server('REMOTE_ADDR');
		$data["ip"] = $ip;
		$intro->db->insert(PREFIX."_users",$data);	
		
		echo  "Done";		
	}	
	
	
	
	
	function visa_3($gate,$amound,$desc,$fname,$orderid){
		global $intro;
	
	echo "gate = $gate <br> amound = $amound <br> desc=$desc <br>fname=$fname <br>  orderid=$orderid <br> ";
	?>
	<form method=post action="pay.php">
		<input type=text name='orderid' value="<?=$orderid?>">
		<input type=text name='orderprice' value="<?=$amound?>">
		<input type=text name='name' placeholder='enter your name ' value='<?=$fname?>'><br>
		<input type=email name='email' placeholder='enter your email '><br>
		<input type=text name='phone' placeholder='enter your phone '><br>
		<input type=submit  value='Enter'>
	</form>
	
	
	<?php

	}	
	function visa2($gate,$amound,$desc,$fname,$orderid){
		global $intro;
		
		if($fname == ""){
			$fname = " Client ";
		}
		$fname="BuyFormala website";
		//var_dump($gate);
		$url = trim($gate['gate_sandbox']);

		//$url = "https://icredit.rivhit.co.il/API/PaymentPageRequest.svc/GetUrl";

		//"RedirectURL" => "'.$intro->url.'"order/success",
		//"RedirectURL" => "https://www.alameer.co.il/index.php/order/success",
		$POST = '{
					"GroupPrivateToken":"'.trim($gate['gate_user']).'",
					
					"Items":[{
						"UnitPrice":'.$amound.',
						"Quantity":1,
						"Description":"'.$desc.'"
					}],
					"CustomerFirstName":"'.$fname.'CustomerFirstNameCustomerFirstName",
					"CustomerLastName":"'.$fname.'CustomerLastNameCustomerLastName",
					"DocumentLanguage":"English",
					
					
				}';
		// Prepare new cURL resource
		$curl = curl_init($url);
		
	
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $POST);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			)
		);
		$result = curl_exec($curl);
		if ($result === false) {
			return boot_alert("Error 101: connection failed. Please try again later.");
		}
		curl_close($curl);
		
		$res = json_decode($result , true);
		
		
		if($res == null)
		{
			return boot_alert("Error 102: Request error. Please try again later.", "danger");
			return ;
		}

		$url_to_view = $res['URL'];
		
		
		
		return '<iframe src="'.$url_to_view.'" width="100%" height="800px" frameborder="0"></iframe>';
		/*
		return '<script type="text/javascript">window.location.href="'.$res['URL'].'";</script>'
		. '<noscript><meta http-equiv="refresh" content="0;url='.$res['URL'].'" /></noscript>';
		*/

		
	}
	function css(){
		
		return "
		<style type=\"text/css\">
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:11px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #dedede;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
		</style>";
	}
	function payError(){
		global $intro;
		
		$fail = $intro->input->get_post('fail');
		if($fail == "true")
		{
			//alert_box success
			//alert_box info
			//alert_box error
			//alert_box warning
			
			return "
			<div class=\"alert_box error\">
				".get_msg('payment_faild')."
				<button class=\"close\"></button>
			
			</div>";
		}
	}
	function onlyPaypal(){
		global $intro;
		echo "
		<div class=\"alert alert-danger alert_box warning\">
			We currently support PayPal and Skrill payments. We will add other payment gateways ASAP.
			<button class=\"close\"></button>
		
		</div>";
	}

}

?>