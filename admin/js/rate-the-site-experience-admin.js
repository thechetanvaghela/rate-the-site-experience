jQuery(document).ready(function(){
	if(document.getElementById('rtse-download-rating-sheet'))
	{
		document.getElementById('rtse-download-rating-sheet').click();
	}
	jQuery(".rtse-remove-logo-img").click(function() {
		jQuery('.rtse-logo-img-preview-wrap').remove();
		jQuery('.rtse-logo-img-select-wrap').show();
	});

});