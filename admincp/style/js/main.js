$(document).ready(function() {
	
		$('.fancybox').fancybox();
		
		$(".iframefancy").fancybox({
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'autoScale'     	: false,
		'type'				: 'iframe',
		'width'				: 500,
		'height'			: 300,
		'scrolling'   		: 'no'
	});
	
	
		$( "#dialog-modal" ).dialog({
			height: 140,
			modal: true
		});
		/*******dialog********/
		$("#dialog-box").dialog({
			autoOpen: false,
			modal: true
		});
		$(".intro_ui_del").click(function(e) {
			var currentElem = $(this);
			$("#dialog-box").dialog({
			  buttons : {
				'Delete' : function() {
				  $(this).dialog("close");
				  $.get( currentElem.attr('href') +"&NH=1" , function( data ) { /*alert( data );*/ });
				  currentElem.closest('tr').fadeOut();
				},
				'Cancel' : function() {
				  $(this).dialog("close");
				}
			  }
			});

			$("#dialog-box").dialog("open");
			e.preventDefault();
			return false;
		});
		/*******end dialog********/
		
		/*******lang change********/
		$(window).on('hashchange', function(){
			var doc_hash = window.location.hash;
			if (doc_hash=="#ar"){
				$('body').append('<form method="post"><input type="submit" id="newlang_submit"><input type="hidden" name="newlang" value="ar" /></form>');
				$("#newlang_submit").click();
			}else if (doc_hash=="#en"){
				$('body').append('<form method="post"><input type="submit" id="newlang_submit"><input type="hidden" name="newlang" value="en" /></form>');
				$("#newlang_submit").click();
			}
		});
		
	$(".global_ajax").click(function(e) {
		var currentElem = $(this);			
		$("#"+ currentElem.attr('data-id') ).html("<img src='"+admin_folder+"/images/loading.gif' />");			
		$.get(currentElem.attr('href'), function(data) {
			$("#"+ currentElem.attr('data-id') ).html(data);
		});			
	});
		
		$( ".datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
		
	});
	

function popup(url) {
	newwindow=window.open(url,'name','height=800,width=1000');
	if (window.focus) {newwindow.focus()}
	return true;
}

