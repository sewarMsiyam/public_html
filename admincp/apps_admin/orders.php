<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-07-11 Time: 14:48:56
#	AppName: orders
##############################################

class Orders_AppAdmin extends Intro_AppsAdmin{

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
	
	function nav(){
		global $intro,$sess_admin;
		
		echo policy($sess_admin['adminid'],$this->appname.".php");
		
		echo "<ul class=\"nav justify-content-center mb-2\">
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("index")."  px-3\" href=\"{$this->base}/index\">
					<i class=\"px-2 fa-solid fa-bag-shopping\"></i> ".$intro->lang["orders_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["orders_add"]."</a>
				  </li>
			</ul>";
				
	}
	function viewitem()
	{
		global $intro,$home,$array;
		$orders=$total='';
		 $lang=$intro->maa->lang;

	
		include('style/header_custom.html');
		$ordid=intval($_GET['ordid']);
		$i=1;
		$sql = $intro->db->query("SELECT *,oi.price as order_items_price FROM ".PREFIX."_orders_items  oi "
		." left join  ".PREFIX."_orders ord on oi.ord_id=ord.ord_id "
		." left join  ".PREFIX."_products p on oi.prodid=p.id "
		." where oi.ord_id=$ordid   order by oi.prodid desc ");
		while($row =  $intro->db->fetch_assoc($sql)){
			@extract($row);
			$net_price = $order_items_price;
			//var_dump($row );
			$name=$row['name_'.$lang];
			$prodName = $lang == "ar"?$name_ar:$name_en;
			$sub_total = $qty*$net_price;
			$total += $sub_total;
			if($i%2==0){$class='even';}else{$class='odd';}
			if($file_lang=='ar'){$filelang="Arabic";}elseif($file_lang=='en'){$filelang="English";}

			$orders.="<tr >
							<td >$prodName  </td>
							<td ><img src='{$intro->base_url}uploads/news/$photo' width=60 height=60></td>
							<td >$filelang</td>
							<td >$net_price</td>
							<td >$qty</td>
							<td >$sub_total</td>
						</tr>";
			
		$i++;	
		}
		
	
		
	echo "<div class='container my-3' style='height:500px;'>
	 <div class=\"table-responsive\">
			<table class='table table-striped table-sm table-hover '  dir='{$intro->lang['dir']}'>
			 <thead  class=\" table-dark\">
				<tr>
					<th>{$intro->lang['product']}</th>
					<th>{$intro->lang['prodphoto']}</th>
					<th>Lnaguage</th>
					<th>{$intro->lang['prod_price']}</th>
					<th>{$intro->lang['prod_qnty']}</th>
					<th>{$intro->lang['prod_total']}</th>
				</tr>
				 <thead>
				 <tbody>
				 $orders
				 </tbody>
				
			</table>
			</div>
			</div>
		
	";
	}	

	function auto()
	{	
		global $intro,$page;
		
		$lang=$intro->maa->lang;	
		
		$query = trim($intro->input->get_post('query'));
		$term = trim($intro->input->get_post('term'));

		$name = 'name_'.$lang;
		$x=$intro->uri->segments[3];
			
			/*$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_products where status=1 and $name LIKE '%$term%' 
			or details_ar  LIKE '%$term%' or details_en  LIKE '%$term%'
			or code  LIKE '%$term%' or code_ar  LIKE '%$term%'
			order by $name asc;");
			*/
			$sql2 = $intro->db->query("SELECT *  from ".PREFIX."_orders ord"
				." JOIN ".PREFIX."_users u on ord.ord_userid=u.userid where u.fullname  LIKE '%$term%'  
				or email LIKE '%$term%'  
				or tel LIKE '%$term%'  
				or city LIKE '%$term%'  
				or mob LIKE '%$term%'  
				GROUP BY u.fullname
				order by fullname asc;");
		$totrows = $intro->db->returned_rows;

			while($row2 =  $intro->db->fetch_assoc($sql2))
			{
				@extract($row2);
				
				
				$json[] = array("value" => $userid  , "label" => $fullname);
			}
		//	$data = array("suggestions" => $json);
		echo json_encode($json);
	
	
	}
	
	
	function index(){
		global $intro,$array;

		$qry = "";
		$page = intval( $intro->input->get_post("page") );
		$order = trim( $intro->input->get_post("order") );
		$search_txt = trim( $intro->input->get_post("search_txt") );

		$this->nav();

		if($search_txt !=""){
			$qry = " where ord.ord_id='$search_txt' or u.userid='$search_txt' ";
		}
		
		if ($order=="") $order="ord_id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		
		$result = $intro->db->query("SELECT * from ".PREFIX."_orders ord"
		." left JOIN ".PREFIX."_users u on ord.ord_userid=u.userid $qry  order by ord.ord_id desc  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		
		$sql_all_rows = $intro->db->query("SELECT ord_id from ".PREFIX."_orders ord"
		." left JOIN ".PREFIX."_users u on ord.ord_userid=u.userid $qry  order by ord.ord_id desc ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$data.="<tr  >
						<td ><a href='{$this->base}/viewitem?ordid=$ord_id&NH=1' class='fancybox fancybox.iframe'>$ord_id</a></td>
						<td ><a href='{$this->base}/viewitem?ordid=$ord_id&NH=1' class='fancybox fancybox.iframe'>$ord_date</a></td>
						<td >$ord_date_pay</td>
						<td >$ord_amount</td>
						<td ><a href=/admincp/index.php/users/Form?t=edit&userid=$ord_userid>$fullname</a></td>
						<td >".$array['paymentmethods'][$ord_pay_method]."   </td>
						<td >".$array['order_status'][$ord_status]."</td>
						<td class=\"center\"> 
							<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&amp;ord_id=$ord_id\" title=\"".$intro->lang["edit"]."\"><i class=\" fa-solid fa-pen-to-square\"></i></a>
							<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?ord_id=$ord_id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\" fa-solid fa-trash\"></i></a>
						</td>
					</tr>";
		}
	
          echo "<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"px-2 fa-solid fa-search\"></i> Search Form
				  </div>
				  <div class=\"card-body nopadding\">
					<form class=\"row nopadding\"  action=\"\" method=\"post\">
					 
					  <div class=\"col-md-8\">
						<input type=\"text\" class=\"form-control\" name=\"search_txt\" id='autocomplete'  value=\"$search_txt\" placeholder=\"Type Text\">
					  </div>
					  <div class=\"col-md-4\">
					  <input type=\"hidden\" name=\"maa\" value=\"Main\">
						<button type=\"submit\" class=\"btn btn-primary mb-3\"> <i class=\"fa-solid fa-search fa-sm\"></i> Search</button>
					  </div>
					</form>
				  </div>
				</div>";

		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-bag-shopping\"></i> ".$intro->lang["orders_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th style='width:9%;'>{$intro->lang['ordid']}</th>
							<th>{$intro->lang['orddate']}</th>
							<th>{$intro->lang['ordertime']}</th>
							<th>{$intro->lang['ordamount']}</th>
							<th>User</th>
							<th>{$intro->lang['ordpaymethod']}</th>
							<th>{$intro->lang['ordsstatus']}</th>
							<th>{$intro->lang['options']}</th>	
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>
			  </div>
		</div>";		
		
		$order = str_replace(" ", ":" , $order);
		
		echo "<center class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
	?>
    <script>
			  $( function() {
				  $( "#autocomplete" ).autocomplete({
				
				   source: function( request, response ) {
					$.ajax( {
					  url: "https://buyformula.net/admincp/index.php/orders/auto?NH=1",
					  dataType: "json",
					  data: {
						term: request.term
					  },
					  success: function( data ) {
						response( data );
						
					  }
					} ).fail(function() {
							alert(data );
						  });
				  },
				  minLength: 1,
				  select: function( event, ui ) {
				  }
				} );

			  } );
			  
			</script>

<?php			
	}
	
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $ord_userid,$ord_date,$ord_date_pay,$ord_amount,$ord_pay_method,$ord_status;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$ord_id = intval( $intro->input->get_post("ord_id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
			$sess_user = $intro->auth->sess_user();
		
			
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_orders where ord_id='$ord_id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["orders_edit"]." <b>$ord_id</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
			$btn['copy'] = "<button type=\"submit\" name=\"app_action\" value=\"doAdd\" class=\"icon-floppy\">حفظ كسجل جديد</button>";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["orders_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$btn['copy'] = "";
		}		
			
	echo "
	<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']} 
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
				<table class=\"table table-sm \">
				 <tbody>
				 <tr>
					<td>".$intro->lang["orders_ord_userid"]." :  <span style='color:#ff0000'>*</span></td>
					<td>
					".form_select_global("ord_userid","Client","users",$ord_userid,'userid','fullname')." {$this->error('ord_userid')}</td>
				</tr>
				 
				 
			<tr>
				<td>".$intro->lang["orders_ord_date"]." : </td>
				<td><input dir=ltr type=\"text\" name=\"ord_date\" value=\"$ord_date\" class='form-control datepicker'
				> {$this->error('ord_date')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["orders_ord_date_pay"]." : </td>
				<td><input dir=ltr type=\"time\" name=\"ord_date_pay\" value=\"$ord_date_pay\" class='form-control' style='font: 1rem 'Fira Sans', sans-serif;'> {$this->error('ord_date_pay')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["orders_ord_amount"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"ord_amount\" value=\"$ord_amount\" class='form-control'> {$this->error('ord_amount')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["orders_ord_pay_method"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select_global('ord_pay_method','method Payment ','gateway',$ord_pay_method,'gate_id','gate_name',$where='',$order='')." {$this->error('ord_pay_method')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["orders_ord_status"]." : </td>
				<td>".sel_array("ord_status",$array['order_status'],$ord_status)." {$this->error('ord_status')}</td>
			</tr>
			<tr>
				<td class=\"center\" ></td>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"ord_id\"  value=\"$ord_id\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" name=\"app_action\" class='btn btn-primary' value=\"{$btn['action']}\"><i class=\"{$btn['img_icon']}\"></i> {$btn['name']} </button>
					
				</td>
			</tr>
			</tbody>
			</table>
			</form>
			</div>
		</div>";
	}

	############################################################################

	function doAdd(){
		global $intro,$error;
		$ord_userid = intval( $intro->input->post('ord_userid') );
		$ord_date = trim( $intro->input->post('ord_date') );
		$ord_amount = floatval( $intro->input->post('ord_amount') );
		$ord_pay_method = intval( $intro->input->post('ord_pay_method') );
		$ord_status = intval( $intro->input->post('ord_status') );
		
		if( $ord_userid == 0 || $ord_pay_method == 0){

			
			if($ord_userid == 0){ $error['ord_userid'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($ord_pay_method == 0){ $error['ord_pay_method'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			$this->Form("add");
			die();
		}		
		
		$data["ord_userid"] = $intro->input->post('ord_userid');
		$data["ord_date"] = $intro->input->post('ord_date');
		$data["ord_date_pay"] = $intro->input->post('ord_date_pay');
		$data["ord_amount"] = $intro->input->post('ord_amount');
		$data["ord_pay_method"] = $intro->input->post('ord_pay_method');
		$data["ord_status"] = $intro->input->post('ord_status');
				 
		$intro->db->insert(PREFIX."_orders",$data);
		
		//if($intro->input->post('IF') == 1) die("<script>parent.location.reload(true);parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		$ord_id = intval( $intro->input->post('ord_id') );
		$data["ord_userid"] = $intro->input->post('ord_userid');
		$data["ord_date"] = $intro->input->post('ord_date');
		$data["ord_date_pay"] = $intro->input->post('ord_date_pay');
		$data["ord_amount"] = $intro->input->post('ord_amount');
		$data["ord_pay_method"] = $intro->input->post('ord_pay_method');
		$data["ord_status"] = $intro->input->post('ord_status');
		
		
		
		$orderuserid=$intro->input->post('ord_userid');
		
		if($intro->input->post('ord_status') == 2 ){
			$sess_user = $intro->auth->sess_user();
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_users where userid ='$orderuserid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);
			
			$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_orders where ord_id ='$ord_id'");
			$row2 = $intro->db->fetch_assoc($sql2);
			@extract($row2);
			
			$ord_pay_method=  $intro->input->post('ord_pay_method');
			
			$this->SendOrderEmail($email , $fullname ,$ord_id ,$ord_amount,$array['paymentmethods'][$ord_pay_method] );
		}
		
		
		

		$intro->db->update(PREFIX."_orders",$data,"ord_id=$ord_id");
		
		//if($intro->input->post('IF') == 1) die("<script>parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$ord_id = intval( $intro->input->get_post('ord_id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_orders WHERE ord_id=$ord_id ");

		$intro->redirect($this->appname);
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
	

	
	############################################################################

	function Active(){
		global $intro,$ord_id;

		$sql = $intro->db->query("UPDATE ".PREFIX."_orders SET status='1' WHERE ord_id='$ord_id' ");

		$intro->redirect($this->appname);

	}

}//end class Orders
?>