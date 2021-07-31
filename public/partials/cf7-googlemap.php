<?php
//TODO add a button to allow user to fill address based on location
$validation_error = wpcf7_get_validation_error( $tag->name );
$classes=array();
$id='';
$show_address = 'false';
$set_address = false;
if(!empty($tag->options)){
  foreach($tag->options as $option){
    // $class = str_replace('class:', '', $option);
    switch(true){
      case ('show_address'==$option):
        $show_address = 'true';
        $set_address = true;
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
<div <?=$id?>class="wpcf7-form-control-wrap cf7-google-map-container <?= $tag->name?>" data-show-address="<?= $show_address ?>" data-zoom="<?= $zoom[1]?>" data-clat="<?= $clat[1]?>" data-clng="<?= $clng[1]?>" data-lat="<?= $lat[1]?>" data-lng="<?= $lng[1]?>">
  <div id="cf7-googlemap-<?= $tag->name?>" class="cf7-googlemap <?= $class?>"></div>
  <div class="cf7-google-map-search">
    <?php if( get_option('cf7_googleMap_enable_places',0)):?>
      <span class="dashicons dashicons-search"></span><span class="dashicons dashicons-no-alt"></span>
    <?php endif;?>
    <input name="search-<?= $tag->name?>" id="search-<?= $tag->name?>" value="" class="cf7marker-address" type="text">
    <input name="zoom-<?= $tag->name?>" id="zoom-<?= $tag->name?>" value="" type="hidden">
    <input name="clat-<?= $tag->name?>" id="clat-<?= $tag->name?>" value="" type="hidden">
    <input name="clng-<?= $tag->name?>" id="clng-<?= $tag->name?>" value="" type="hidden">
    <input name="lat-<?= $tag->name?>" id="lat-<?= $tag->name?>" value="" type="hidden">
    <input name="lng-<?= $tag->name?>" id="lng-<?= $tag->name?>" value="" type="hidden">
    <input name="address-<?= $tag->name?>" id="address-<?= $tag->name?>" value="" type="hidden">
    <input name="<?= $tag->name?>" id="<?= $tag->name?>" value="" type="hidden">
    <input name="manual-address-<?= $tag->name?>" id="manual-address-<?= $tag->name?>" value="false" type="hidden">
  </div>
  <span class="wpcf7-form-control-wrap <?= $tag->name?>"></span>
<?php if($set_address):?>
  <div class="cf7-googlemap-address-fields">
    <label for="line-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-address">
        <?php
        $label = 'Address';
        $label = apply_filters('cf7_google_map_address_label', $label, $tag->name);
        $label = apply_filters('cf7_google_map_address_field_label', $label, 'adresse', $tag->name);
        echo $label;
        ?>
      </span><br />
      <input name="line-<?= $tag->name?>" id="line-<?= $tag->name?>" value="" class="cf7-googlemap-address cf7-googlemap-address-line" type="text">
    </label>
    <label for="city-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-city">
        <?php
        $label = 'City';
        $label =  apply_filters('cf7_google_map_city_label',$label,$tag->name);
        $label = apply_filters('cf7_google_map_address_field_label', $label, 'ville', $tag->name);
        echo $label;
         ?>
      </span><br />
      <input name="city-<?= $tag->name?>" id="city-<?= $tag->name?>" value="" class="cf7-googlemap-address cf7-googlemap-address-city" type="text">
    </label>
    <label for="state-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-pin">
        <?php
        $label= 'State &amp; Pincode';
        $label= apply_filters('cf7_google_map_pincode_label',$label,$tag->name);
        $label = apply_filters('cf7_google_map_address_field_label', $label, 'code', $tag->name);
        echo $label;
        ?>
      </span><br />
      <input name="state-<?= $tag->name?>" id="state-<?= $tag->name?>" value="" class="cf7-googlemap-address cf7-googlemap-address-state" type="text">
    </label>
    <label for="country-<?= $tag->name?>">
      <span class="cf7-googlemap-address-field cf7-googlemap-country">
        <?php
        $label= 'Country';
        $label= apply_filters('cf7_google_map_country_label',$label ,$tag->name);
        $label = apply_filters('cf7_google_map_address_field_label', $label, 'pays', $tag->name);
        echo $label;
        ?>
      </span><br />
      <input name="country-<?= $tag->name?>" id="country-<?= $tag->name?>" value="" class="cf7-googlemap-address cf7-googlemap-address-country" type="text">
    </label>
  </div>
<?php endif;?>
<?= $validation_error;?>
  <script type="text/javascript">

  </script>
</div>
