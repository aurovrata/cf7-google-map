<?php
//TODO add a button to allow user to fill address based on location
$validation_error = wpcf7_get_validation_error( $tag->name );
$classes=array();
$id='';
$show_address = 'false';
$set_address = 'false';
if(!empty($tag->options)){
  foreach($tag->options as $option){
    // $class = str_replace('class:', '', $option);
    switch(true){
      case ('show_address'==$option):
        $show_address = 'true';
        $set_address = 'true';
        break;
      case ('custom_address'==$option):
        $show_address = 'true';
        break;
      case strpos($option, 'id:')!==false:
        $id = 'id="'.str_replace('id:', '', $option).'" ';
        break;
      case strpos($option, 'class:')!==false:
        $classes[]= str_replace('class:', '', $option);
        break;
    }
  }
}
$class = wpcf7_form_controls_class( $tag->type, 'cf7-googleMap' );
if( !empty( $classes ) ) $class .= ' '.implode(' ', $classes);
if ( $validation_error ) {
    $class .= ' wpcf7-not-valid';
}
if ( 'map*' === $tag->type ) {
    $class .= ' wpcf7-validates-as-required';
}
//debug_msg($tag->options, $show_address);
$value = (string) reset( $tag->values );
$map_values = explode( ';',$value );

//error_log("GoogleMap: ".$value."\n".print_r($slide_values,true));
$zoom = explode(':',$map_values[0]); //zoom:
$clat = explode(':',$map_values[1]); //lat:
$clng = explode(':',$map_values[2]); //lng:
$lat = explode(':',$map_values[3]); //lat:
$lng = explode(':',$map_values[4]); //lng:
//check that the file actually exists
$exists = false;
if ( in_array('curl', get_loaded_extensions()) ) {
	$ch = curl_init($marker_settings['icon']);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	$response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($response === 200) $exists = true;
	curl_close($ch);
}
if (!$exists && function_exists('get_headers')) {
	$headers = @get_headers($robots);
	if ($headers) {
		if (strpos($headers[0], '404') !== false) {
			$exists = true;
		}
	}
}

//HTML cf7 form
?>
<div <?=$id?>class="wpcf7-form-control-wrap cf7-google-map-container <?= $tag->name?>" data-show-address="<?= $show_address ?>">
  <div id="cf7-googlemap-<?= $tag->name?>" class="cf7-googlemap <?= $class?>"></div>
  <div class="cf7-google-map-search">
    <?php if( get_option('cf7_googleMap_enable_places',0)):?>
      <span class="dashicons dashicons-search"></span><span class="dashicons dashicons-no-alt"></span>
    <?php endif;?>
    <input name="search-<?= $tag->name?>" id="search-<?= $tag->name?>" value="" class="cf7marker-address" type="text">
    <input name="zoom-<?= $tag->name?>" id="zoom-<?= $tag->name?>" value="<?= $zoom[1]?>" type="hidden">
    <input name="clat-<?= $tag->name?>" id="clat-<?= $tag->name?>" value="<?= $clat[1]?>" type="hidden">
    <input name="clng-<?= $tag->name?>" id="clng-<?= $tag->name?>" value="<?= $clng[1]?>" type="hidden">
    <input name="lat-<?= $tag->name?>" id="lat-<?= $tag->name?>" value="<?= $lat[1]?>" type="hidden">
    <input name="lng-<?= $tag->name?>" id="lng-<?= $tag->name?>" value="<?= $lng[1]?>" type="hidden">
    <input name="address-<?= $tag->name?>" id="address-<?= $tag->name?>" value="" type="hidden">
    <input name="<?= $tag->name?>" id="<?= $tag->name?>" value="" type="hidden">
    <input name="manual-address-<?= $tag->name?>" id="manual-address-<?= $tag->name?>" value="false" type="hidden">
  </div>
<?php if($set_address):?>
  <div class="cf7-googlemap-address-fields">
    <label for="line-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-address">
        <?= apply_filters('cf7_google_map_address_label','Address', $tag->name);?>
      </span><br />
      <input name="line-<?= $tag->name?>" id="line-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text">
    </label>
    <label for="city-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-city">
        <?= apply_filters('cf7_google_map_city_label','City',$tag->name);?>
      </span><br />
      <input name="city-<?= $tag->name?>" id="city-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text">
    </label>
    <label for="state-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-pin">
        <?= apply_filters('cf7_google_map_pincode_label','State &amp; Pincode',$tag->name);?>
      </span><br />
      <input name="state-<?= $tag->name?>" id="state-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text">
    </label>
    <label for="country-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-country">
        <?= apply_filters('cf7_google_map_country_label','Country',$tag->name);?>
      </span><br />
      <input name="country-<?= $tag->name?>" id="country-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text">
    </label>
  </div>
<?php endif;?>
<?= $validation_error;?>
  <script type="text/javascript">

  </script>
</div>
