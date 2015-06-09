<?php

/**
 * Handles gallery sharing options
 */
class Gallery_Sharing_Options {
	/**
	 * option prefix
	 */
	private $prefix;

	public function __construct() {
		$this->prefix = 'ph-gallery-sharing-';
	}

	public function get_sources(){
		return $this->get_option( 'sources', array() );
	}
	/**
	 * Saves sources in wp options
	 * @param array $sources array of source domains
	 */
	public function set_sources($sources){
		return $this->set_option( 'sources', $sources );
	}

	/**
	 * user and password for htaccess of source
	 * @return array 	[userame, password]
	 */
	public function get_htaccess($source = ''){
		return $this->get_option( $source.'-htaccess', array("","") );
	}
	public function set_htaccess($user, $password, $source = ''){
		return $this->set_option( $source.'-htaccess', array( $user, $password ) );
	}

	/**
	 * returns a gallery sharing wp option
	 * @param  $optionname 		name of the option
	 * @return  mixed           mixed option value
	 */
	private function get_option($optionname, $default = ''){
		// last param is for cache and is true by default
		return get_site_option( $this->prefix.$optionname, $default , true );
	}

	/**
	 * sets a gallery_sharing option
	 * @param [type] $optionname [description]
	 * @param [type] $value      [description]
	 */
	private function set_option($optionname, $value){
		return update_site_option( $this->prefix.$optionname, $value );
	}

	/**
	 * delete all gallery sharing options
	 */
	public function clear_options(){
		/**
		 * delete all htacess options of sources
		 */
		foreach ( $this->get_sources() as $source ) {
			delete_site_option( $this->prefix.$source.'-htaccess' );
		}
		delete_site_option( $this->prefix.'-htaccess' );
		/**
		 * delete sources itselfe
		 */
		delete_site_option( $this->prefix.'sources' );
	}

}
