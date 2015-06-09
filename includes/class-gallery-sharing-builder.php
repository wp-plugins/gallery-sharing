<?php

/**
 * The connection to gallery sources
 */
class Gallery_Sharing_Builder
{
	/**
	 * domain to a compatible source
	 */
	private $source;

	/**
	 * id to content
	 */
	private $id;

	/**
	 * array of [username, password]
	 */
	private $htaccess;

	/**
	 * Define the core functionality of the plugin.
	 * @param  $source			domain to a compatible source
	 * @param  $id        		id of gallery post
	 */
	public function __construct($source, $id, $htaccess = array( '', '' ))
	{
		$this->source = (isset($source) && $source != '')?$source:$_SERVER['HTTP_HOST'];
		$this->id = $id;
		$this->htaccess = $htaccess;
	}

	/**
	 * Fetches the gallery html from plattform
	 * @return string           HTML of gallery
	 */
	public function render()
	{
		$content = $this->get_result();
		ob_start();
		include dirname( __FILE__ ).'/../public/partials/shared-gallery.tpl.php';
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * get string result
	 */
	public function get_result()
	{
		return $this->execute();
	}

	/**
	 * executes the curl request
	 *
	 */
	private function execute()
	{
		$url = 'http://'.rtrim( $this->source ).'/index.php?__api=1&__ph_content_sharing=1&__action=get&__id='.$this->id;
		if ( 2 == count( $this->htaccess )){
			if('' != $this->htaccess[0]){
				$ht = $this->htaccess[0] . ':' . $this->htaccess[1];
			} else {
				$ht = $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW'];
			}
			$args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode( $ht ),
			);
		} 
		$args['timeout'] = 20;
		$response = wp_remote_request( $url, $args );
		if( is_wp_error($response) ){
			return "<p>Could not load gallery</p>".$response->get_error_message().$url;
		}
		return $response["body"];
	}

}
