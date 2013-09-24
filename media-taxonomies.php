<?php
/*
Plugin Name: Media Taxonomies
Plugin URI: http://horttcore.de
Description: Taxononmies for media files
Version: 1.2
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/


/**
 * Media_Taxonomies
 */
class Media_Taxonomies
{



	/** Refers to a single instance of this class. */
	private static $instance = null;



	/**
	 * Constructor
	 *
	 * @access public
	 * @since v0.9
	 * @author Ralf Hortt
	 */
	public function __construct()
	{

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_filter( 'manage_edit-attachment_sortable_columns', array( $this, 'manage_edit_attachment_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 0, 1 );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
		add_action( 'wp_ajax_save-media-terms', array( $this, 'save_media_terms' ), 0, 1 );
		load_plugin_textdomain( 'media-taxonomies', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	} // end __construct



	/**
	 * Enqueue admin scripts and styles
	 *
	 * @access public
	 * @since v0.9
	 * @author Ralf Hortt
	 */
	public function admin_enqueue_assets()
	{

		wp_enqueue_script( 'media-taxonomies', plugins_url( 'javascript/media-taxonomies.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'media-taxonomies', plugins_url( 'css/media-taxonomies.css', __FILE__ ) );

	} // end admin_enqueue_assets



	/**
	 * Add taxonomy information
	 *
	 * @access public
	 * @since v0.9
	 * @author Ralf Hortt
	 */
	public function admin_head()
	{

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects' ) );

		if ( !$taxonomies )
			return;

		$attachment_taxonomies = $attachment_terms = array();

		foreach ( $taxonomies as $taxonomyname => $taxonomy ) :
			$attachment_taxonomies[$taxonomy->name] = $taxonomy->labels->name;

			$terms = get_terms( $taxonomy->name, array(
				'orderby'       => 'name',
				'order'         => 'ASC',
				'hide_empty'    => true,
			) );

			if ( !$terms )
				break;

			$attachment_terms[ $taxonomy->name ][] = array( 'id' => 0, 'label' => $taxonomy->labels->name, 'slug' => '' );

			foreach ( $terms as $term )
				$attachment_terms[ $taxonomy->name ][] = array( 'id' => $term->term_id, 'label' => $term->name, 'slug' => $term->slug );

		endforeach;


		?>
		<script type="text/javascript">
			var mediaTaxonomies = <?php echo json_encode( $attachment_taxonomies ) ?>,
				mediaTerms = <?php echo json_encode( $attachment_terms ) ?>;
		</script>
		<?php

	} // end admin_head



	/**
	 * Add taxonomy checkboxes
	 *
	 * @access public
	 * @param array $fields Fields
	 * @param obj $post Post obj
	 * @return array Fields
	 * @since v0.9
	 * @author Ralf Hortt
	 */
	public function attachment_fields_to_edit( $fields, $post )
	{

		$screen = get_current_screen();

		if ( isset( $screen->id ) && 'attachment' == $screen->id )
			return $fields;

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects' ) );

		if ( $taxonomies ) :

			foreach ( $taxonomies as $taxonomyname => $taxonomy ) :

				$fields[$taxonomyname] = array(
					'label' => $taxonomy->labels->singular_name,
					'input' => 'html',
					'html' => $this->terms_checkboxes( $taxonomy, $post->ID ),
					// 'value' => '',
					// 'helps' => '',
					'show_in_edit' => true,
				);

			endforeach;

		endif;

		return $fields;

	} // end attachment_fields_to_edit



	/**
	* Creates or returns an instance of this class.
	*
	* @access public
	* @return A single instance of this class.
	* @since v0.9
	*/
	public static function get_instance() {

		if ( null == self::$instance )
			self::$instance = new self;

		return self::$instance;

	} // end get_instance;



	/**
	 *
	 * Filter attachments in modal box
	 *
	 * @access public
	 * @since v0.9.1
	 * @author Ralf Hortt
	 */
	public function pre_get_posts( $query )
	{
		if ( 'attachment' != $query->query_vars['post_type'] )
			return;

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects' ) );

		if ( !$taxonomies )
			return;

		foreach ( $taxonomies as $taxonomyname => $taxonomy ) :

			if ( isset( $_REQUEST['query'][$taxonomyname] ) && $_REQUEST['query'][$taxonomyname]['term_slug'] )	 :
				$query->set( $taxonomyname, $_REQUEST['query'][$taxonomyname]['term_slug'] );
			elseif ( isset( $_REQUEST[$taxonomyname] ) && is_numeric( $_REQUEST[$taxonomyname] ) && 0 != intval( $_REQUEST[$taxonomyname] ) ) :
				$term = get_term_by( 'id', $_REQUEST[$taxonomyname], $taxonomyname );
				set_query_var( $taxonomyname, $term->slug );
			endif;

		endforeach;

	} // end pre_get_posts



	/**
	 *
	 * Register taxonomy
	 *
	 * @access public
	 * @since v0.9
	 * @author Ralf Hortt
	 */
	public function register_taxonomy()
	{

		register_taxonomy( 'media-category', array( 'attachment' ), array(
			'hierarchical' => TRUE,
			'labels' => array(
				'name' => _x( 'Categories', 'taxonomy general name' ),
				'singular_name' => _x( 'Category', 'taxonomy singular name' ),
				'search_items' =>  __( 'Search Categories' ),
				'all_items' => __( 'All Categories' ),
				'parent_item' => __( 'Parent Category' ),
				'parent_item_colon' => __( 'Parent Category:' ),
				'edit_item' => __( 'Edit Category' ),
				'update_item' => __( 'Update Category' ),
				'add_new_item' => __( 'Add New Category' ),
				'new_item_name' => __( 'New Category Name' ),
				'menu_name' => __( 'Categories' ),
			),
			'show_ui' => TRUE,
			'query_var' => TRUE,
			'rewrite' => array( 'slug' => _x( 'media-category', 'Category Slug', 'media-taxonomies' ) ),
			'show_admin_column' => TRUE,
			'update_count_callback' => '_update_generic_term_count',
		));

		register_taxonomy( 'media-tag', array( 'attachment' ), array(
			'hierarchical' => FALSE,
			'labels' => array(
				'name' => _x( 'Tags', 'taxonomy general name' ),
				'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
				'search_items' =>  __( 'Search Tags' ),
				'all_items' => __( 'All Tags' ),
				'parent_item' => __( 'Parent Tag' ),
				'parent_item_colon' => __( 'Parent Tag:' ),
				'edit_item' => __( 'Edit Tag' ),
				'update_item' => __( 'Update Tag' ),
				'add_new_item' => __( 'Add New Tag' ),
				'new_item_name' => __( 'New Tag Name' ),
				'menu_name' => __( 'Tags' ),
			),
			'show_ui' => TRUE,
			'query_var' => TRUE,
			'rewrite' => array( 'slug' => _x( 'media-tag', 'Tag Slug', 'media-taxonomies' ) ),
			'show_admin_column' => TRUE,
			'update_count_callback' => '_update_generic_term_count',
		));

	} // end register_taxonomy;



	/**
	 * Add custom filters in attachment listing
	 *
	 * @access public
	 * @since v0.9
	 * @author Ralf Hortt
	 **/
	public function restrict_manage_posts()
	{

		global $wp_query;

		$taxonomies = apply_filters( 'media-taxonomies', get_object_taxonomies( 'attachment', 'objects' ) );

		if ( $taxonomies ) :

			foreach ( $taxonomies as $taxonomyname => $taxonomy ) :

				wp_dropdown_categories( array(
					'show_option_all' => sprintf( _x( 'View all %s', '%1$s = plural, %2$s = singular', 'media-taxonomies' ), $taxonomy->labels->name, $taxonomy->labels->singular_name ),
					'taxonomy' => $taxonomyname,
					'name' => $taxonomyname,
					'orderby' => 'name',
					'selected' => ( isset( $wp_query->query[$taxonomyname] ) ? $wp_query->query[$taxonomyname] : '' ),
					'hierarchical' => TRUE,
					'hide_empty' => TRUE,
				) );

			endforeach;

		endif;

	} // end restrict_manage_posts



	/**
	 * Save media terms
	 *
	 * @todo security nonce
	 * @since v0.9
	 * @author Ralf Hortt
	 */
	public function save_media_terms()
	{

		$post_id = intval( $_REQUEST['attachment_id'] );

		if ( !current_user_can( 'edit_post', $post_id ) )
			die();

		$term_ids = array_map( 'intval', $_REQUEST['term_ids'] );

		$response = wp_set_post_terms( $post_id, $term_ids, sanitize_text_field( $_REQUEST['taxonomy'] ) );
		wp_update_term_count_now( $term_ids, sanitize_text_field( $_REQUEST['taxonomy'] ) );

	} // end save_media_terms



	/**
	 * Create a terms box
	 *
	 * @access protected
	 * @param obj $taxonomy Taxonomy
	 * @return str HTML output
	 * @since v0.9
	 * @author Ralf Hortt
	 */
	protected function terms_checkboxes( $taxonomy, $post_id )
	{

		$terms = get_terms( $taxonomy->name, array(
			'hide_empty' => FALSE,
		));

		if ( !$terms )
			return apply_filters( 'media-checkboxes', '', $taxonomy, $terms );

		$attachment_terms = wp_get_object_terms( $post_id, $taxonomy->name, array(
			'fields' => 'ids'
		));

		$output = '<div class="media-terms" data-id="' . $post_id . '" data-taxonomy="' . $taxonomy->name . '">';
		$output .= '<ul>';

		ob_start();
		wp_terms_checklist( 0, array(
			'selected_cats'         => $attachment_terms,
			'taxonomy'              => $taxonomy->name,
			'checked_ontop'         => FALSE
		));
		$output .= ob_get_contents();
		ob_end_clean();

		$output .= '</ul>';
		$output .= '</div>';

		return apply_filters( 'media-checkboxes', $output, $taxonomy, $terms );

	} // end terms_checkboxes



}



Media_Taxonomies::get_instance();
