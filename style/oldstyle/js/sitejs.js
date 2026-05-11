(function( $ ){

  $.fn.mailcrypt = function( options ) {
    return this.each(function() {

		at = '@';
		// insert visivle @ sign
		$(':first-child',this).replaceWith(at);
		
		// get complete mail address
		email = $(this).html();
		
		// place mailto link in the href
		$(this).attr('href', 'mailto:'+email);
    });

  };
})( jQuery );

$(document).ready(function()
{

	$('#autocomplete').autocomplete({
		serviceUrl: site_href + 'search/auto',
		onSelect: function (suggestion) {
			/*alert('You selected: ' + suggestion.value + ', ' + suggestion.label);*/
			
		}
	});
	$('a.addToCart').click(function () {
		var  prodLink = $(this).attr('href');
		
		$.get( prodLink , function( data ) {
			/*alert(data);*/
		});
		
		getFullCart();		
		return false;
	});
	
   $('div.light_gray').click(function () {
		$(this).siblings(0).slideToggle();
		/*if ($(this).find('span').first().attr('class') == 'icon-leftArrow5') {
			$(this).find('span').first().attr('class', 'icon-downArrow');
		} else {
			$(this).find('span').first().attr('class', 'icon-leftArrow5');
		}*/
		return false;
	});
	$('.fancybox').fancybox();
	$('a.introProCon').mailcrypt();
	
	window.onhashchange = function(){
		var doc_hash = document.location.hash;    
		if (doc_hash=="#ar"){
			$('body').append('<form method="post"><input type="submit" id="newlang_submit"><input type="hidden" name="newlang" value="ar" /></form>');
			$("#newlang_submit").click();
		}else if (doc_hash=="#en"){
			$('body').append('<form method="post"><input type="submit" id="newlang_submit"><input type="hidden" name="newlang" value="en" /></form>');
			$("#newlang_submit").click();
		}
	}


	$("#loginform").submit( function save_data_style() {
	
		$('#login_result').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		$.post(site_href+'login/doLogin',$(this).serialize(),
			function(data){
				$("#login_result").html(data);
			}				
		);
		return false;   
	});
	$("#contact").submit( function save_data_style() {
	
		$('#contact_result').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		$.post(site_href+'contact/add',$(this).serialize(),
			function(data){
				$("#contact_result").html(data);
			}				
		);
		return false;   
	});

	$("#userinfo").submit( function save_data_style() {
	
		$('#result_form').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		$.post(site_href+'account/doeditinfo',$(this).serialize(),
			function(data){
				$("#result_form").html(data);
			}				
		);
		return false;   
	});

	$("#edit_password").submit( function save_data_style() {
	
		$('#result_form').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		$.post(site_href+'account/doeditpassword',$(this).serialize(),
			function(data){
				$("#result_form").html(data);
			}				
		);
		return false;   
	});

	$("#update_cart").submit( function save_data_style() {

		$('#BigCartResult').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		$.post(site_href+'cart/updatecart',$(this).serialize(),
			function(data){
				$('#BigCartResult').html('<span style="color:#006600">'+lang_updated+'</span>');
			}
		);
		/*after update load the cart data*/
		getFullCart();
		return false;   
	});
	
	$("select[name='file_lang[]']").change(function () {
		
		var  cart_id = $(this).attr('dataid');
		var  cart_lang = $(this).val();
		var  cartlink = site_href+'cart/updatecartlang?cart_id='+cart_id+' &cart_lang='+cart_lang+'';
		$('#BigCartResult').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		
		$.get( cartlink , function( data ) {
			$('#BigCartResult').html('<span style="color:#006600">'+lang_updated+'</span>');
			//alert(data);
		});
		
		//getFullCart();
		return false;
	});
	
	
});	

function view_faq(divid){

	$("#"+divid).show("slow");
	
}

function getFullCart(){
	
	$.get( site_href+"cart/ajaxCart", function( data ) {
		$("#BigCart").html($('#dataBigCart' , data).html());
		$("#BigCartBuyButton").html($('#dataBigCartBuyButton' , data).html());
		$("#BigCartTotal").html($('#dataBigCartTotal' , data).html());
		$("#header_totalprice").html($('#total' , data).html());
		$('#open_shopping_cart').attr('data-amount',$('#num_prods' , data).html());
	});
}
function del_product(cartid){

	var x = confirm("Are you sure you want to delete?");
	if (x){
		$.ajax({
			type: "POST",url: site_href+"cart/del/"+cartid,data: "d",
			success: function(result){
				$("#prod_tr_"+cartid).hide("slow");
			}
		});
		/*after update load the cart data*/
		getFullCart();

	}else
	return false;

}