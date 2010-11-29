/*global AROS:true */
/*jslint browser:true */

/**
* ArchReactorOS script
*/
var AROS = AROS || {};


// To load on document ready
$(function() {
	var dumy = '';
	$(".view_detail").tooltip({
		delay: 200,
		position: 'mouse',
		top: -15,
		left: 5,
		bodyHandler: function() {
			$.ajax({
				url: $(this).attr('ajaxurl'),
				type:'post',
				success:function(msg){
					dumy = msg; 
				},
				async: false
			});
			return dumy;
		},
		showCFG_SITE_URL: false
	});
});
