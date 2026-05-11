<?php

class Cart_App extends Intro_Apps 
{
	var $sessid = '';
	var $userid = 0;
	var $base = '';
	
	function __construct()
	{
		global $intro;
		//php session id of visitor
		$this->sessid = session_id();
		$_SESSION['session_id']=$this->sessid;
		
		
		$this->base = $intro->base_url."cart";
		$this->shipping= "By Your Email Login Account";
		
		if ($intro->auth->auth_user()== true)
		{
			$sess_user = $intro->auth->sess_user();
			
			$this->shipping = "By Your Email : {$sess_user['email']}";
			$this->userid = intval($sess_user['userid']);
			$this->sql_cart = "cart.userid={$this->userid}";
		}
		else{
			$this->sql_cart = "cart.sessid='$this->sessid'";
			$this->userid = 0;
		}
	}
	
	function index()
	{
		global $intro,$shipping;
		
		$viewbtn=$instantship='';
		$lang = $intro->maa->lang;
	
	
			
		
		$sql = $intro->db->query("SELECT *,cart.id as cartid "
		." from ".PREFIX."_cart cart "
		." JOIN ".PREFIX."_products prod ON cart.prodid=prod.id "
		." where cart.sessid='$this->sessid' order by cart.id asc");	
		
		$num_prods = $intro->db->returned_rows;
		
		
		
		
		if($num_prods > 0 ){
			 if($intro->option['showvisabtn'] == 1 ){
				 $instantship="<a href=\"https://buyformula.net/$lang/order/paynow?paymentmethod=8\" 
			class='center btn btn-success py-3 border w-100 border-success font-weight-bold '>
			{$intro->lang['directshiping']}</a>";
			 }
			 
			 
			
		}
		
		pHeader();
		?>
		<div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="<?=$intro->base_url?>"><?=$intro->lang['home']?></a>
                    
                    <span class="breadcrumb-item active"><?=$intro->lang['shopnigcart']?></span>
                </nav>
            </div>
        </div>
    </div>
	
	 <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
				
					<form method='post' action='' id='update_cart'> 
						<div id='BigCart'> 
						</div> <!--/BigCart-->
					</form>	
			</div>
            <div class="col-lg-4">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary px-4"><?=$intro->lang['cartsamary']?></span></h5>
                <div class="bg-light p-30 mb-5">
				<div id='BigCartResult' class='center'></div>
				
					<div class='totals_cart'>
						
					<div class="border-bottom pb-2">
                        
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium"><?=$intro->lang['shiping']?></h6>
                            <h6 class="font-weight-medium text-danger"><?=$this->shipping?></h6>
                        </div>
                    </div>
                    <div class="pt-2">
                        
						<div id='BigCartTotal' class='center'></div>
                        <div id='BigCartBuyButton' class='center'></div>
						<?=$instantship?>  
						
                    </div>
					
					</div>
					
                </div>
            </div>
        </div>
    </div>
		
	<?php
	echo "<script>
		getFullCart();	
		$(document).ready(function(){
		
		/*load cart from js*/
			
		});
		
		</script>";

		pFooter();
	}
	
	function viewcart()
	{
		global $intro;
		minicart();
	}

	function updatecart()
	{
		global $intro;
		
		$qnty = $intro->input->get_post('qnty');
		$prodids = $intro->input->get_post('prodids');
		$file_lang = $intro->input->get_post('file_lang');
		$lang = $intro->maa->lang;
		$sess = $this->sessid;
		
		for($i=0;$i<count($qnty);$i++)
		{
			if(intval($qnty[$i]==0))
			{
				$intro->db->query("delete from ".PREFIX."_cart where id='$prodids[$i]' and sessid='$sess'; ");
			}else{
				$intro->db->query("update ".PREFIX."_cart set qty='$qnty[$i]',file_lang='$file_lang[$i]' where id='$prodids[$i]' and sessid='$sess'; ");
			}
		}
		
	}
	function updatecartlang()
	{
		global $intro;
		
		$cart_id = $intro->input->get_post('cart_id');
		$cart_lang = $intro->input->get_post('cart_lang');
		
		$lang = $intro->maa->lang;
		$sess = $this->sessid;
		$intro->db->query("update ".PREFIX."_cart set file_lang='$cart_lang' where id='$cart_id' and sessid='$sess'; ");

		
	}
	function ajaxCart()
	{
		global $intro;
		
		$lang = $intro->maa->lang;

		$sql = $intro->db->query("SELECT *,cart.id as cartid "
		." from ".PREFIX."_cart cart "
		." JOIN ".PREFIX."_products prod ON cart.prodid=prod.id "
		." where cart.sessid='$this->sessid' order by cart.id asc");	
		$total = 0;
		$num_prods = $intro->db->returned_rows;
		
		$bigCarto = "<table class='table_cart'><tbody>
			<tr>
				<th></th>
				<th colspan=2><center>{$intro->lang['item']}</center></th>
				<th><center>{$intro->lang['formula_lang']}</center></th>
				<th><center>{$intro->lang['products_price']}</center></th>
				<th colspan=2><center>{$intro->lang['prod_total']}</center></th>
				
			</tr>
		";
		$bigCart="
		
		<table class=\"table table-light table-borderless table-hover text-center mb-0\">
                    <thead class=\"thead-dark\">
                        <tr>
                            <th>{$intro->lang['formula_lang']}</th>
                            <th>image</th>
                            <th>{$intro->lang['item']}</th>
                            <th>{$intro->lang['products_price']}</th>
                           <th>{$intro->lang['Remove']}	</th>
                        </tr>
                    </thead>
                    <tbody class=\"align-middle\">";
		$productsd = '';
		$cartid = 0;
		while($row = $intro->db->fetch_assoc($sql))
		{
			@extract($row);
			$selecten=$selectar="";
			$prodName = $lang == "ar"?$name_ar:$name_en;
			
			$sub_total = $net_price*$qty;
			$total += $sub_total;
			
			$url = $intro->uri_links('products','View',$prodid,$prodName);
			$productsd .="
			<div class=\"animated_item\">
				<div class=\"clearfix sc_product\">
					<a href=\"$url\" class=\"product_thumb\"><img src=\"{$intro->base_url}uploads/news/$photo\" width=60 height=60 alt=\"\"></a>
					<a href=\"$url\" class=\"product_name\">$prodName ... </a>
					<p> ".$intro->maa->Currency_amount($sub_total)." -- $sub_total </p>
				</div>
			</div>";
		if($file_lang == 'en'){
			$selecten="selected";
			
			
		}elseif($file_lang == 'ar'){
			
			$selectar="selected";
		}else{
			if($lang == 'en'){
				$selecten="selected";
				
			}elseif($lang == 'ar'){
				
				$selectar="selected";
			}				
		}	
	
				
		$bigCart.="<tr id='prod_tr_$cartid'>
				<td  class=\"align-middle\"> 
					<select name='file_lang[]' class='form-control file_lang'  dataid=$cartid onchange=\"myFunction('$cartid',this.value)\" >
						<option value='en' $selecten >English</option>
						<option value='ar' $selectar>Arabic</option>
					
					</select>
				</td>
				<td class=\"align-middle\"><img src=\"{$intro->base_url}uploads/news/$photo\" alt=\"\" style=\"width: 50px;\"> 
				
				</td>
				<td class=\"align-middle\"> <a href=\"$url\" class='carttitle'>$prodName</a>
				</td>
				<td class=\"align-middle\">".$intro->maa->Currency_amount($net_price)."  </td>
				
				<td class=\"align-middle\"><a href=\"javascript:()\" onclick=\"del_product('$cartid')\"  class=\"btn btn-sm btn-danger\"><i class=\"fa fa-times\"></i></a></td>
			</tr>";		
		}
		
		$BigCartBuyButton = $total==0?"":"<a href='{$intro->uri_links('order','index',0,'')}?total=$total&order=1'  class='btn btn-block btn-primary font-weight-bold my-3 py-3'>{$intro->lang['buynow']}</a>";
		$update_button = $total==0?"":"<input type=submit name='' value=' {$intro->lang['update']} ' class='btn btn-success'>";
		
		$bigCart .="
				</tbody>
				<tfooter>
				<tr>
						<td><div id='BigCartResultlang'></div> <div id='langupdated'></div> </td>
						<td></td>
						<td> <div id='update_btncart'> </div></td>
						<td class='subtotal'> 
							<div id='total_cart'>   </div>
						</td>
				<tr>
				</tfooter>
				</table>";
		
		//$bigCartTotal = "{$intro->lang['prod_total']} :  $ $total";
		$bigCartTotal = "<div class=\"d-flex justify-content-between mt-2\">
                            <h5>Total</h5>
                            <h5> ".$intro->maa->Currency_amount($total)."</h5>
                        </div>";
		if($num_prods == 0){
			$bigCart = "<div><h3>{$intro->lang['noitems']}</h3></div>";
			$bigCartTotal = '';
		}
		
		$_SESSION['direct_total']=$intro->maa->Currency_amount($total) ;
			
			echo "<div>
				<div id=dataBigCart>$bigCart  </div>
				<div id=dataBigCartTotal> <br><font size=5> $bigCartTotal </font></div>
				<div id=dataBigCartBuyButton > $BigCartBuyButton  </div>
				<div id=total> ".$intro->maa->Currency_amount($total)."  </div>
				<div id=num_prods>$num_prods</div>
				<div id=cartitems>$productsd </div>
				<div class='newtotal'>".$intro->maa->Currency_amount($total)."   </div>
			</div>";
			
			
	}
	
	function add()
	{
		global $intro;
		
		$prodid = intval($intro->input->get_post('id'));
		
		$prodid = $prodid==0?$intro->uri->id:$prodid;

		if($prodid != 0){
			$data['sessid'] = $this->sessid;
			$data['userid'] = $this->userid;
			$data['prodid'] = $prodid;
			$data['file_lang'] = $intro->uri->lang;
			$data['qty'] = 1;
			
			$intro->db->insert(PREFIX."_cart" , $data , true);//true = ignor duplicats
		}	
		
		$url=$intro->uri_links('cart','index',0,'');
		//header("Location: $url");
	}
	
	function del()
	{
		global $intro;
		
		$id = intval($intro->input->get_post('id'));
		$id = $id==0?$intro->uri->id:$id;
		$intro->db->query("delete from ".PREFIX."_cart where id=$id;");
		//echo "OK $id";
	}
	
}
?>