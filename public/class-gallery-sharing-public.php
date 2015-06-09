<?php

/**
 * The public-facing functionality of the plugin.
 */
class Gallery_Sharing_Public {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * renders the shortcode to content
	 * @param [type] $atts    [description]
	 * @param [type] $content [description]
	 * @return  rendered html of gallery
	 */
	public function shortcode($atts, $content){
	    $atts = shortcode_atts(array(
	        'source' => '',
	        'id' => 1,
	    ), $atts);
	    

	    /**
	     * render locally
	     */
	    if($atts['source'] == ''){
	    	ob_start();
	    	$this->render_gallery($atts['id'], false);
	    	$output = ob_get_contents();
	    	ob_end_clean();
	    	return $output;
	    }
	    /**
	     * render from foreign site
	     */
	    $options = new Gallery_Sharing_Options();
	    $connection = new Gallery_Sharing_Builder($atts['source'], $atts['id'], $options->get_htaccess( $atts['source'] ) );
		return $connection->render();
	}

	/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars
	*/
	public function add_query_vars($vars){
		$vars[] = '__api';
		$vars[] = '__ph_content_sharing';
		$vars[] = '__action';
		$vars[] = '__id';
		$vars[] = '__search';
		return $vars;
	}

	/** Add API Endpoint
	*	This is where the magic happens
	*	@return void
	*/
	public function add_endpoint() {
		add_rewrite_rule(
			'^__api/ph_gallery_sharing/search/(.+)$',
			'index.php?__api=1&__ph_content_sharing=1&__action=search&__search=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			'^__api/ph_gallery_sharing/get/([0-9]+)$',
			'index.php?__api=1&__ph_content_sharing=1&__action=get&__id=$matches[1]',
			'top'
		);
	}

	/**	Sniff Requests
	*	This is where we hijack all API requests
	* 	If $_GET['__api'] is set, we kill WP and serve response
	*	@return die if API request
	*/
	public function sniff_requests() {
		global $wp;
		if ( isset($wp->query_vars['__ph_content_sharing']) &&
			$wp->query_vars['__ph_content_sharing'] &&
			isset($wp->query_vars['__action']) ){

			$action = $wp->query_vars['__action'];
			switch ( $wp->query_vars['__action'] ) {
				case 'search':
					$this->search_gallery( $wp->query_vars['__search'] );
					break;
				case 'get':
					$this->render_gallery( $wp->query_vars['__id'] );
					break;
				default:
					print 'No known action';
					break;
			}

			exit;
		}

	}

	public function search_gallery($title){

		/**
		 * Args for all galleries that container searched title
		 */
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 10,
			's' => $title,
			'tax_query' => array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => 'post-format-gallery',
				),
			),
		);
		$query = new WP_Query( $args );

		/**
		 * Cleanup WP_Query results to minimize result size
		 * Add gallery images for preview in backend
		 */
		$result = array();
		$pattern = get_shortcode_regex();
		foreach ( $query->posts as $post ) {

			// $attachments = get_attached_media('image', $post->ID);

			// test if there is a gallery shortcode set
			preg_match_all( '/\[gallery.*ids=.(.*).\]/', $post->post_content, $ids );
			// if there was no gallery shortcode found continue
			// if(count($ids[0]) < 1) continue;

			// get all attachment ids
			$attachment_ids = array();
			foreach ( $ids[1] as $idstring ) {
				$attachment_ids = array_unique( array_merge( $attachment_ids, explode( ',', $idstring ) ) );
			}

			// get all attachment source to attachments
			$attachments = array();
			foreach ( $attachment_ids as $aid ) {
				$attachments[] = wp_get_attachment_image_src( $aid, 'thumbnail' );
			}

			// build result entry
			$result[] = array(
				'post_title' => $post->post_title,
				'ID' => $post->ID,
				'post_content' => $post->post_content,
				'codes' => $ids,
				'attachment_ids' => $attachment_ids,
				'attachments' => $attachments,
			);
		}
		/**
		 * return galleries as json result
		 */
		$json = json_encode( array( 'query' => $title, 'result' => $result ) );
		if ( isset($_GET['callback']) ){
	    	print sanitize_text_field( $_GET['callback'] ) . '('.$json.')';
	    } else {
	    	print $json;
	    }
		exit;
	}

	/**
	 * Endpoint for getting a renderd gallery
	 * @param  $post_id the post_id of the gallery
	 */
	public function render_gallery($post_id, $die = true) {
		// if post_id is not numeric it cannot be a post post_id
		if ( ! is_numeric( $post_id ) ) {
			die('Could not find gallery!'); }
		global $post;
		$post = get_post( $post_id, OBJECT );
		if ( $post != null && 'gallery' == get_post_format( $post_id ) ){
			setup_postdata($post);
			// render the gallery before printing
			print "<!-- gallery sharing start -->\n";
			the_content();
			print "\n<!-- gallery sharing end -->";
		} else {
			print 'not found '.get_post_format( $post_id );
		}

		if($die) die();

	}

}
