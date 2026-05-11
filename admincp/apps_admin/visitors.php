<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-06-24 Time: 10:44:58
#	AppName: products
##############################################

class Visitors_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-users\"></i> Visitors </a>
				  </li>
				   <li class=\"nav-item mx-2\">
					<a class=\"btn text-danger\"  px-3\" href=\"{$this->base}/delall\">
					<i class=\"px-2 fa-solid fa-trash\"></i> Clear all </a>
				  </li>
				
				</ul>";
	}

	
	function index(){
		global $intro,$array;

		$qry = "";
		$page = intval( $intro->input->get_post("page") );
		$order = trim( $intro->input->get_post("order") );
		$search_txt = trim( $intro->input->get_post("search_txt") );
		$scatid = intval( $intro->input->get_post("scatid") );

		$this->nav();


		$rows_per_page = 100;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

			
			$result = $intro->db->query("
				SELECT DATE(date_visited) AS visit_date, COUNT(DISTINCT ip) AS daily_visitors
				FROM ".PREFIX."_visitors
				GROUP BY DATE(date_visited)
				ORDER BY visit_date DESC
				LIMIT $nexlimit, $rows_per_page
			");
			$totrows = $intro->db->returned_rows;
			$sql_all_rows = $intro->db->query("
				SELECT DATE(date_visited) AS visit_date, COUNT(DISTINCT ip) AS daily_visitors
				FROM ".PREFIX."_visitors
				GROUP BY DATE(date_visited)
				ORDER BY visit_date DESC
			");
			$totalrows = $intro->db->returned_rows;
				

			$data = "";
			$i = 0;
			while($myrow = $intro->db->fetch_assoc($result)){
				$visit_date = $myrow['visit_date'];
				$daily_visitors = $myrow['daily_visitors']; // هذا عدد الزوار لذلك اليوم
				$i++;

				$data .= "
					<tr>
						<td>$visit_date</td>
						<td>$daily_visitors</td>
					</tr>";
			}

			 
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-box-open\"></i> All visitors ($totalrows)
			  </div>
			  <div class=\"card-body\">
		
			 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\">Date</th>
							<th scope=\"col\">Number</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>";
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"  </div></div>";
	}
	
	function delall(){
		global $intro,$array;
		
	$result = $intro->db->query("delete from ".PREFIX."_visitors ");
		$result2 = $intro->db->query("update ".PREFIX."_options set option_value=0 where option_name ='site_views' ");
	
$intro->redirect($this->appname);
		
	}

	
}//end class Products
?>