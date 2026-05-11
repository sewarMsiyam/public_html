
        
  function getFullCart(){
    
		$.get( site_href+"cart/ajaxCart", function( data ) {
				$("#BigCart").html($('#dataBigCart' , data).html());
				$("#BigCartBuyButton").html($('#dataBigCartBuyButton' , data).html());
				$("#BigCartTotal").html($('#dataBigCartTotal' , data).html());
				$(".header_totalprice").html($('#total' , data).html());
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


function myFunction(cartid,filelang){
  
    
    	var  cart_id = cartid;
		var  cart_lang = filelang;
		 
		var  cartlink = site_href+'cart/updatecartlang?cart_id='+cart_id+' &cart_lang='+cart_lang+'';
		$('#langupdated').html('<img src="'+site_url+'style/img/loading.gif" align="absmiddle" />');
		
		$.get( cartlink , function( data ) {
			$('#langupdated').html('<span style="color:#006600">Lang Updated</span>');
			alert('Lang Updated');
		});
		
		
}


/////////////////////////

$(document).ready(function () {
    
     $('.currency-option').on('click', function (e) {
      e.preventDefault();
      var currency = $(this).data('currency');
      $('#selectedCurrency').html(currency);
      $('#selectedCurrencyInput').val(currency);
      $('#currencyForm').submit();
    });
    
    /********/

		getFullCart();
$('a.addToCart').click(function () {
		var  prodLink = $(this).attr('href');
		
		$.get( prodLink , function( data ) {
		     
		});
		
		getFullCart();		
	
	});


	
$(".fancybox").fancybox({
    type: 'iframe',
    width: 1000,      // العرض المطلوب بالبكسل
    height: 600,      // الارتفاع (اختياري، غيره حسب حاجتك)
    autoSize: false   // ضروري حتى يستخدم القيم التي وضعتها
});

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
	
	
	
	$("select[name='file_lang[]']").change(function () {
		alert('s');
	
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
    
    	var myCarousel = document.querySelector('#header-carousel')
var carousel = new bootstrap.Carousel(myCarousel, {
  interval: 500
  
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
     
}); /// end query

$(document).ready(function () {
    /*********تغير العملة *******/
       $('.currency-option').on('click', function (e) {
      e.preventDefault();

      var currency = $(this).data('currency');

      // تحديث الزر والنص
      $('#selectedCurrency').html(currency);
      $('#selectedCurrencyInput').val(currency);
      $('#currencyForm').submit();
    });
    
    /******زر اخفاء وظاهر  كلمة السر******/
    $('#togglePassword').on('click', function () {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });
});



