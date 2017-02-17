<?php
//TODO add a button to allow user to fill address based on location
$validation_error = wpcf7_get_validation_error( $tag->name );
$class = wpcf7_form_controls_class( $tag->type, 'cf7-googleMap' );
if ( $validation_error ) {
    $class .= ' wpcf7-not-valid';
}
if ( 'map*' === $tag->type ) {
    $class .= ' wpcf7-validates-as-required';
}
$value = (string) reset( $tag->values );
$map_values = explode( ';',$value );

//error_log("GoogleMap: ".$value."\n".print_r($slide_values,true));
$zoom = explode(':',$map_values[0]); //zoom:
$clat = explode(':',$map_values[1]); //lat:
$clng = explode(':',$map_values[2]); //lng:
$lat = explode(':',$map_values[3]); //lat:
$lng = explode(':',$map_values[4]); //lng:


?>
<div id="cf7-googlemap-<?php echo $tag->name?>" class="cf7-googlemap"></div>
<input name="lat-<?php echo $tag->name?>" id="lat-<?php echo $tag->name?>" value="" class="<?php echo $class?> cf7marker-lat" type="hidden">
<input name="lng-<?php echo $tag->name?>" id="lng-<?php echo $tag->name?>" value="" class="<?php echo $class?> cf7marker-lng" type="hidden">
<input name="<?php echo $tag->name?>" id="<?php echo $tag->name?>" value="" class="<?php echo $class?> cf7marker-lng" type="hidden">
<?php echo $validation_error;?>
<script type="text/javascript">
(function($){
	$(document).ready( function(){
		var $et_map = $( '#cf7-googlemap-<?php echo $tag->name?>' ),
			googleMap, googleMarker;
		$et_map.gmap3({
      center : [<?php echo $clat[1]?>, <?php echo $clng[1]?>],
	    zoom: <?php echo $zoom[1] ?>,
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: true,
      navigationControl: true,
      scrollwheel: true,
      streetViewControl: true
    }).marker({
			position : [<?php echo $lat[1]?>, <?php echo $lng[1]?>],
			icon : "<?php echo $plugin_url ?>assets/red-marker.png",
      draggable : true
    }).on('dragend', function(marker, e){
      $('input#lat-<?php echo $tag->name?>', $('form') ).val(marker.getPosition().lat());
      $('input#lat-<?php echo $tag->name?>', $('form') ).val( marker.getPosition().lng());
      $('input#<?php echo $tag->name?>', $('form') ).val( marker.getPosition().lat() + "," + marker.getPosition().lng() );
		}).then(function(result){
      googleMap = this.get(0);
      googleMarker = this.get(1);
    });
	} );
})(jQuery)
</script>
