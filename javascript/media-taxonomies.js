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

	/**
	 * Extended Filters dropdown with taxonomy term selection values
	 */
	if ( media ) {
		jQuery.each(mediaTaxonomies,function(key,label){

			media.view.AttachmentFilters[key] = media.view.AttachmentFilters.extend({
				className: key,

				createFilters: function() {
					var filters = {};

					_.each( mediaTerms[key] || {}, function( term ) {

						var query = {};

						query[key] = {
							taxonomy: key,
							term_id: parseInt( term.id, 10 ),
							term_slug: term.slug
						};

						filters[ term.slug ] = {
							text: term.label,
							props: query
						};
					});

					this.filters = filters;
				}


			});

			/**
			 * Replace the media-toolbar with our own
			 */
			var myDrop = media.view.AttachmentsBrowser;

			media.view.AttachmentsBrowser = media.view.AttachmentsBrowser.extend({
				createToolbar: function() {

					media.model.Query.defaultArgs.filterSource = 'filter-media-taxonomies';

					myDrop.prototype.createToolbar.apply(this,arguments);

					this.toolbar.set( key, new media.view.AttachmentFilters[key]({
						controller: this.controller,
						model:      this.collection.props,
						priority:   -80
						}).render()
					);
				}
			});

		});
	}

	/* Save taxonomy */
	jQuery('html').delegate( '.media-terms input', 'change', function(){

		var obj = jQuery(this),
			container = obj.parents('.media-terms'),
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

	// Add new taxonomy
	jQuery('html').delegate('.toggle-add-media-term', 'click', function(e){
		e.preventDefault();
		jQuery(this).parent().find('.add-new-term').toggle();
	});

	// Save new taxnomy
	jQuery('html').delegate('.save-media-term', 'click', function(e){

		var obj = jQuery(this),
			termField = obj.parent().find('input'),
			termParent = obj.parent().find('select'),
			data = {
				action: 'add-media-term',
				attachment_id: obj.data('id'),
				taxonomy: obj.data('taxonomy'),
				parent: termParent.val(),
				term: termField.val()
			};

		// No val
		if ( '' == data.term ) {
			termField.focus();
			return;
		}

		jQuery.post(ajaxurl, data, function(response){

			obj.parents('.field').find('.media-terms ul:first').html( response.checkboxes );
			obj.parents('.field').find('select').replaceWith( response.selectbox );

			console.log( response );

			termField.val('');

		}, 'json' );

	});

});
