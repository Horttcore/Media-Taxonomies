jQuery(document).ready(function(){

	var media = wp.media;

	/*
	// for debug : trace every event triggered in the Region controller
	var originalTrigger = wp.media.view.MediaFrame.prototype.trigger;
	wp.media.view.MediaFrame.prototype.trigger = function(){
		console.log('MediaFrame Event: ', arguments[0]);
		originalTrigger.apply(this, Array.prototype.slice.call(arguments));
	}; //

	// for Network debug
	var originalAjax = media.ajax;
	media.ajax = function( action ) {
		console.log( 'media.ajax: action = ' + JSON.stringify( action ) );
		return originalAjax.apply(this, Array.prototype.slice.call(arguments));
	};
	*/


	/* Save taxonomy */
	jQuery('html').delegate( '.media-term input', 'change', function(){

		var obj = jQuery(this),
			container = obj.parent().parent(),
			row = container.parent(),
			data = {
				action: 'save-media-terms',
				term_ids: [],
				attachment_id: container.data('id'),
				taxonomy: container.data('taxonomy')
			};

		container.find('input:checked').each(function(){
			data.term_ids.push( jQuery(this).val() );
		});

		row.addClass('media-save-terms');
		container.find('input').prop('disabled', 'disabled');

		jQuery.post( ajaxurl, data, function( response ){
			row.removeClass('media-save-terms');
			container.find('input').removeProp('disabled');
		});

	});
});