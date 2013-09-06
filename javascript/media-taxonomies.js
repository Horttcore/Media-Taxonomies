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
		media.view.AttachmentsBrowser = media.view.AttachmentsBrowser.extend({
			createToolbar: function() {

				media.model.Query.defaultArgs.filterSource = 'filter-media-taxonomies';

				this.toolbar = new media.view.Toolbar({
					controller: this.controller
				});

				this.views.add( this.toolbar );

				this.toolbar.set( key, new media.view.AttachmentFilters[key]({
					controller: this.controller,
					model:      this.collection.props,
					priority:   -80
				}).render() );
			}
		});

	});



	/* Save taxonomy */
	jQuery('html').delegate( '.media-terms input', 'change', function(){

		var obj = jQuery(this),
			container = jQuery('.media-terms'),
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