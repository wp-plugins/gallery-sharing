<?php

/**
 * HTML for gallery sharing modal
 */


$options = new Gallery_Sharing_Options();
$sources = $options->get_sources();
array_unshift($sources, "-intern-");
?>

<div class="ph-gallery-sharing-modal">
	<div class="ph-gallery-sharing-modal-controls">
		<select id="ph-gallery-sharing-modal-source">
			<?php
			foreach ($sources as $key => $source) {
				$value = "";
				if($key > 0){
					$value = $source;
				}
				echo "<option value='$value'>$source</option>";
			}
			?>
		</select>
		<input type="text" id="ph-gallery-sharing-modal-search" name="ph-gallery-sharing-search" />
	</div>
	<ul id="ph-gallery-sharing-list"></ul>
	<?php
	// submit_button( "Einfügen", "primary", "ph-gallery-sharing-submit");
	?>
	
</div class="ph-gallery-sharing-modal">

