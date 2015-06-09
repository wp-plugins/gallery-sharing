<?php


/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 */
class Gallery_Sharing_Admin
{

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
	public function __construct( $plugin_name, $version )
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the menu page for gallery sharing
	 *
	 */
	public function menu_page()
	{
		add_submenu_page( 'options-general.php', 'Gallery Sharing', 'Gallery Sharing', 'manage_options', 'settings-'.$this->plugin_name, array( $this, 'render_menu' ) );
	}

	/**
	 *  renders settings page
	 */
	public function render_menu()
	{
		$options = new Gallery_Sharing_Options();

		if ( isset( $_POST['delete_btn'] ) ){
			$delete_source = sanitize_text_field( $_POST['delete_btn'] );
			$sources = $options->get_sources();
			foreach ( $sources as $key => $source ) {
				if ( $source == $delete_source ){
					unset( $sources[ $key ] );
				}
			}
			$options->set_sources( $sources );

		} else if ( isset( $_POST['sources'] ) && is_array( $_POST['sources'] ) ){
			$sources = array_filter( $_POST['sources'] );

			
			for ( $i = 0; $i < count( $sources ); $i++ ){
				$source = $sources[ $i ];
				if ( $source == '' ) { continue; }
				$sources[$i] = $this->sanitize($sources[$i]);
				$user = $this->sanitize(sanitize_text_field( $_POST['users'][ $i ] ));
				$pw = $this->sanitize(sanitize_text_field( $_POST['pws'][ $i ] ));
				if ( $user != '' && $pw != '' ){
					$options->set_htaccess( $user, $pw, $source );
				}
			}
			$options->set_sources( $sources );
		}

		$page = 'settings-'.$this->plugin_name;

		?>
		<div class="wrap">
			<h2>Gallery Sharing Settings</h2>
			<form method="post" action="<?php echo sanitize_text_field( $_SERVER['PHP_SELF'] ).'?page='.sanitize_text_field( $page ); ?>">

				<table class="form-table">
					<tr>
						<th>Source Domain</th><th>Username</th><th>Password</th><th></th>
					</tr>
					<?php
					foreach ( $options->get_sources() as $source ){
						$htaccess = $options->get_htaccess( $source );
						?>
					<tr>
						<td><input type="text" name="sources[]" value="<?php echo $this->sanitize(sanitize_text_field( $source )); ?>" class="regular-text" /></td>
						<td><input type="text" name="users[]" value="<?php echo $this->sanitize(sanitize_text_field( $htaccess[0] )); ?>" class="regular-text" /></td>
						<td><input type="text" name="pws[]" value="<?php echo $this->sanitize(sanitize_text_field( $htaccess[1] )); ?>" class="regular-text" /></td>
						<td>
							<button name="delete_btn" value="<?php echo $this->sanitize(sanitize_text_field( $source )); ?>" type="submit" class="button delete">Löschen</button>
						</td>
					</tr>
						<?php
					}
					?>
					<tr>
						<td><input type="text" name="sources[]" value="" class="regular-text" /></td>
						<td><input type="text" name="users[]" value="" class="regular-text" /></td>
						<td><input type="text" name="pws[]" value="" class="regular-text" /></td>
						<td></td>
					</tr>
				</table>
				<?php submit_button( 'Speichern' ,'primary', 'save_'.$this->plugin_name ); ?>
			</form>
		</div>
		<?php
	}

	private function sanitize($value){
		return strip_tags(str_replace(array('"',"'",'='), array('','',''),$value));
	}

	/**
	 * renders content for gallery sharing editor modal content
	 */
	public function render_modal(){
		require plugin_dir_path( __FILE__ ) .'partials/gallery-sharing-modal.tpl.php';
		exit;
	}

	/**
	 * adds a button to plaintext editor
	 */
	public function add_text_editor_button(){
		?>
		    <script type="text/javascript">
		    if(typeof QTags != "undefined")
		    {
		    	QTags.addButton( 'gallery_sharing', 'Gallery Sharing', '[ph-gallery-sharing id="" source=""]');
		    }
		    </script>
		<?php
	}

	/**
	 * add button to tinymce
	 */
	public function add_tinymce_button($buttons){
		array_push( $buttons, 'gallery_sharing_button' );
		return $buttons;
	}

	/**
	 * add tinymce plugin js
	 */
	public function add_tinymce_plugin($plugins_array){
		/**
		 * needed for dialog
		 */
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		/**
		 * Autocomplete for content search
		 */
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_style( 'wp-jquery-ui-autocomplete' );
		/**
		 * style for dialog
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tinymce-plugin.css', array(), $this->version, 'all' );
		/**
		 * add plugin js
		 */
		$plugins_array['gallery_sharing_button'] = plugin_dir_url( __FILE__ ) .'js/tinymce-plugin.js';
		return $plugins_array;
	}

}
