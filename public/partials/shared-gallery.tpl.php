<?php
/**
 * HTML of shared gallery
 *
 * 	@var $context 	Gallery_Sharing_Connection Object
 *  @var $content 	rendered Gallery from source
 *
 */
$classes = array();
$classes[] = "gallery-sharing";
$classes[] = "gallery-sharing-from-".str_replace(".", "", $this->source);
$classes[] = "gallery-sharing-id-".$this->id;
?>
<!-- Gallery Sharing START -->
<div class="<?php echo implode(" ", $classes); ?>">
	<?php echo $content; ?>
</div>
<!-- Gallery Sharing END -->
