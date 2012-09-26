$(document).ready(function(){

	$('#contact-form').validate({
		rules: {
			name: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			member: {
				required: true
			},
			unit: {
				required: true
			},
			confirmed: {
				required: true
			},
			number: {
				required: true
			},
			accept: {
				required: true
			}
		},
		highlight: function(label) {
			$(label).closest('.control-group').addClass('error');
		},
		success: function(label) {
			label
			.text('OK!').addClass('valid')
			.closest('.control-group').addClass('success');
		}
	});
	  
});