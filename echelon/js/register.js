	// Check Username Function (registration setup user page)
	$("#uname-check").blur(function(){
		
		var loading = $(".loader").fadeIn("normal");
		var key = $("#key").val();
		
		
		$.post("actions/check-username.php",{ username:$(this).val() } ,function(data){
			loading.fadeOut('fast');
			$('div.result').removeClass('r-bad').removeClass('r-good');
			
			if(data=='no') {
				$('div.result').html('Username unavailable').addClass('r-bad').fadeTo(900,1);
			} else if(data=='yes') {
				$('div.result').html('Username available').addClass('r-good').fadeTo(900,1);
			} else {
				$('div.result').html('Name is required').addClass('r-bad').fadeTo(900,1);
			}
		});
		
	});
	
});
