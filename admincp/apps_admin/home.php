<?php

class Home_AppAdmin extends Intro_AppsAdmin{

	var $appname = null;
	var $base = null;
	var $img_path;
	
	function __construct($appname,$base,$img_path="")
	{
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
	}

	function index(){
		global $admin, $intro, $acct_type,$status_val,$array,$sess_admin,$option;
		
		$teckets=$teckets_user=$orders=$cotacts='';
		$i=1;
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_orders ord "
		." JOIN ".PREFIX."_users u on ord.ord_userid=u.userid order by ord.ord_id desc limit 5");
		while($row =  $intro->db->fetch_assoc($sql)){
			@extract($row);
			$orders.="
						<tr>
						  <th scope=\"row\"><a href='{$this->base}/viewitem?ordid=$ord_id&NH=1' class='vieworder fancybox fancybox.iframe '>$ord_id</a></th>
						  <td>$ord_date</td>
						  <td>$ord_date_pay</td>
						  <td>$ord_amount</td>
						  <td><a href=/admincp/index.php/users/Form?t=edit&userid=$ord_userid>{$fullname}</a></td>
						  <td>".$array['paymentmethods'][$ord_pay_method]."</td>
						  <td>".$array['order_status'][$ord_status]."</td>
						</tr>";
			
			$i++;
		}
	/***************************/


	$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_contactus  order by cid desc limit 10");
		while($row =  $intro->db->fetch_assoc($sql2)){
			@extract($row);
			if($i%2==0){$class='even';}else{$class='odd';}
			$cotacts.="<tr>
						  <th scope=\"row\">$cid</th>
						  <td>$email</td>
						  <td>$subject</td>
						  <td>$datesend</td>
						  
						<td class='$class'><a href='{$intro->base_url}admincp/index.php/contactus/Form?t=edit&cid=$cid'>view</a></td>
						</tr>";
			
			$i++;
		}	
		
		echo "
		<div class=\"card border-warning col-lg-10 col-md-12 col-sm-12\"  >
			  <div class=\"card-header  bg-warning fw-bold\">
				<i class=\"px-2 fa-solid fa-bag-shopping\"></i> Last Orders
			  </div>
			  <div class=\"card-body\">
			  <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
						  <th scope=\"col\">#</th>
						  <th scope=\"col\">{$intro->lang['orddate']}</th>
						  <th scope=\"col\">{$intro->lang['ordertime']}</th>
						  <th scope=\"col\">{$intro->lang['ordamount']}</th>
						  <th scope=\"col\">User</th>
						  <th scope=\"col\">{$intro->lang['ordpaymethod']}</th>
						  <th scope=\"col\">{$intro->lang['ordsstatus']}</th>
						</tr>
					  </thead>
					  <tbody>
						$orders
						
					  </tbody>
				</table>
			  </div>
			  </div>
		</div>
	
		{$this->last_users()}

		<div class=\"card my-3 border-success col-lg-10 col-md-12 col-sm-12 \" >
			  <div class=\"card-header text-white bg-success fw-bold\">
				 <i class=\"px-2 fa-solid fa-envelope\"></i>  Last Contact us Mails
			  </div>
			  <div class=\"card-body\">
			  <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
						  <th scope=\"col\">#</th>
						  <th scope=\"col\">{$intro->lang['contactus_email']}</th>
						  <th scope=\"col\">{$intro->lang['contactus_subject']}</th>
						  <th scope=\"col\">{$intro->lang['contactus_datesend']}</th>
						  <th scope=\"col\">{$intro->lang['options']}</th>
						</tr>
					  </thead>
					  <tbody>
						$cotacts
						
					  </tbody>
				</table></div>
			  </div>
			</div>";
	 
	

	}
	function last_users(){
		global $admin,$db,$intro,$array;
		
		  
		$html = "
		<div class=\"card border-primary col-lg-10 col-md-12 col-sm-12 my-3\"  >
			  <div class=\"card-header  bg-primary fw-bold\">
				<i class=\"px-2 fa-solid fa-users\"></i> Last Clients
			  </div>
			  <div class=\"card-body\">
			  <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
						  <th scope=\"col\">#</th>
						  <th scope=\"col\">{$intro->lang['users_fullname']}</th>
						  <th scope=\"col\">{$intro->lang['users_email']}</th>
						  <th scope=\"col\">{$intro->lang['users_country']}</th>
						  <th scope=\"col\">IP</th>
						</tr>
					  </thead>
					  <tbody>
						";
		$i=0;
		$result = $intro->db->query("SELECT * from ".PREFIX."_users where status=1 order by userid desc limit 5"); 
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$i++;
			
			$country = isset($array['country'][$country])?$array['country'][$country]:'-';
			
			$html .= "<tr>
						  <th scope=\"row\">$userid</th>
						  <td>$fullname</td>
						  <td>$email</td>
						  <td>$country</td>
						  <td><a href='https://www.geolocation.com/?ip=$ip#ipresult' target=_blank>$ip</a></td>
						</tr>";
		}
		$html .= "</tbody>
					</table>
			  </div></div>
		</div>";	
		
		return $html;
			 
	}

	
	function viewitem()
	{
		global $intro,$home,$array;
		$orders=$total='';
		 $lang=$intro->maa->lang;
	
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
							<td class='$class'>$prodName  </td>
							<td class='$class'><img src='{$intro->base_url}uploads/news/$photo' width=60 height=60></td>
							<td class='$class'>$filelang</td>
							<td class='$class'>$net_price</td>
							<td class='$class'>$qty</td>
							<td class='$class'>$sub_total</td>
						</tr>";
			
		$i++;	
		}
		
	?>
	<!DOCTYPE html dir='<?=$intro->lang['dir']?>'> 

	
	  <meta charset="utf-8">
		<style>
			
		.tableorder th{
		background:#3C3C3C;
		color:#fff;
		}
		.tableorder td{

		font-weight:normal;
		}
		.tableorder .even{
		background:#DBDBDB;
		}
		</style>	
	<?
		
	echo "<table class='tableorder' width=100% dir='{$intro->lang['dir']}'>
				<tr>
					<th>{$intro->lang['product']}</th>
					<th>{$intro->lang['prodphoto']}</th>
					<th>Lnaguage</th>
					<th>{$intro->lang['prod_price']}</th>
					<th>{$intro->lang['prod_qnty']}</th>
					<th>{$intro->lang['prod_total']}</th>
				</tr>
				
				
				$orders
			</table>";
		
	

	
	}	


	
	
	
	
	function clear_cache(){
			 Global $admin,$db,$intro;
			 
			 
			$path = $_SERVER['SCRIPT_FILENAME'];
			$path = str_replace( array("img.php","index.php","admincp/","{$intro->admin_folder}"),"",$path);
			$folder = $path ."includes/db/cache/"; 
			
			echo "<div>
			<div dir=ltr style=\"height: 300px; width: 600px; overflow: auto;\">";
			if ($handle = opendir($folder)) 
			{
				while (false !== ($file = readdir($handle))) 
				{
					 if($file != '.' && $file != '..' && $file != 'index.php'){        
						echo "<font color=#fff>$file - Deleted </font><br>";
						@unlink($folder.$file);
					}		
				}
				closedir($handle);
			}
			echo "</div>
			<br/> <div style='text-align: center; font-size:18px;color:#fff;'> تم مسح ملفات التخزين المؤقتة. </div>";
			echo "</div>";
	}
	
	
	

}
?>