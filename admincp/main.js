
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
/////////////////////////

$(document).ready(function () {
	
	$('#autocomplete').autocomplete({
		serviceUrl: site_href + 'search/auto',
		onSelect: function (suggestion) {
			/*alert('You selected: ' + suggestion.value + ', ' + suggestion.label);*/
			
		}
	});
	
	
	
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
	
	
	
$('.fancybox').fancybox();

$("#contact").submit( function save_data_style() {
	
		$('#contact_result').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		$.post(site_href+'contact/add?NH=1',$(this).serialize(),
			function(data){
				$("#contact_result").html(data);
			}				
		);
		return false;   
	});
	
	$("#update_cart").submit( function save_data_style() {

		$('#BigCartResult').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		$.post(site_href+'cart/updatecart',$(this).serialize(),
			function(data){
				$('#BigCartResult').html('<div class="alert alert-success" style="color:#006600">'+lang_updated+'</div>');
			}
		);
		/*after update load the cart data*/
		getFullCart();
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
	
	
	
	$("select[name='zfile_lang[]']").change(function () {
	
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
	
  // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });
  function toggleNavbarMethod() {
            if ($(window).width() > 992) {
                $('.navbar .dropdown').on('mouseover', function () {
                    $('.dropdown-toggle', this).trigger('click');
                }).on('mouseout', function () {
                    $('.dropdown-toggle', this).trigger('click').blur();
                });
            } else {
                $('.navbar .dropdown').off('mouseover').off('mouseout');
            }
        }
        toggleNavbarMethod();
        $(window).resize(toggleNavbarMethod);      
       
  
     
}); /// end query
  
