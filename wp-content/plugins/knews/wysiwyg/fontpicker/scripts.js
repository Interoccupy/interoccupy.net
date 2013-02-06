
parent.jQuery(window).load(function() {
	parent.jQuery('div.container select', document).change(function () {
		change();
	});
	parent.jQuery('div.container input', document).keyup(function () {
		change();
	});
});

function change() {
	fsi=parseInt(parent.jQuery('#fontsize', document).val(),10);
	lh=parseInt(parent.jQuery('#lineheight', document).val(),10);

	if (lh==0) lh=Math.round(font_size[fsi][2] * 1.2);
	
	parent.jQuery('div.preview_text', document).html('<p style="font-size:' + font_size[fsi][2] + 'px; line-height:' + lh + 'px; font-family:' + parent.jQuery('#fontfamily', document).val() + '">' + demo_text + '</p>');

	parent.jQuery('div.preview_text_no_css', document).html('<font size="' + font_size[fsi][1] + '" face="' + parent.jQuery('#fontfamily', document).val() + '">' + demo_text + '</font>');

}

function select_font() {
	ff = parent.jQuery('#fontfamily', document).val();
	
	fsi=parseInt(parent.jQuery('#fontsize', document).val(),10);
	fs=font_size[fsi][1];
	ss=font_size[fsi][2];

	lh=parseInt(parent.jQuery('#lineheight', document).val(),10);

	parent.CallBackFont(ff, fs, ss, lh);
}
