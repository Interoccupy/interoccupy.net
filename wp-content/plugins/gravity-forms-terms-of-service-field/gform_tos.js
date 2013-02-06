jQuery(document).ready(function($) {
	$(".gform_footer input[type='submit']").prop("disabled",true);

	$(".gform_body textarea.gform_tos").scroll(function(){
		if($(this).scrollTop()+$(this).height() >= $(this)[0].scrollHeight-10){
			$(".gform_footer input[type='submit']").prop("disabled",false);
		}
	});
	
});