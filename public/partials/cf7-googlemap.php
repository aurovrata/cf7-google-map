<?php
/**
 * HTML file to output the google map field in the form.
 * 
 * @since 1.0.0
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/public/partials
 */

// TODO add a button to allow user to fill address based on location.
$validation_error = wpcf7_get_validation_error( $tag->name );
$classes          = array();
$html_id               = '';
$show_address     = 'false';
$set_address      = false;
if ( ! empty( $tag->options ) ) {
	foreach ( $tag->options as $option ) {
		// $class = str_replace('class:', '', $option);
		switch ( true ) {
			case ( 'show_address' == $option ):
				$show_address = 'true';
				$set_address  = true;
				break;
			case ( 'custom_address' == $option ):
				$show_address = 'true';
				break;
			case strpos( $option, 'id:' ) !== false:
				$html_id = str_replace( 'id:', '', $option );
				break;
			case strpos( $option, 'class:' ) !== false:
				$classes[] = str_replace( 'class:', '', $option );
				break;
		}
	}
}

$class = wpcf7_form_controls_class( $tag->type, 'cf7-googleMap' );
if ( ! empty( $classes ) ) {
	$class .= ' ' . implode( ' ', $classes );
}
if ( $validation_error ) {
	$class .= ' wpcf7-not-valid';
}
if ( 'map*' === $tag->type ) {
	$class .= ' wpcf7-validates-as-required';
}
// debug_msg($tag->options, $show_address);
$value      = (string) reset( $tag->values );
$map_values = explode( ';', $value );

$zoom = explode( ':', $map_values[0] ); // zoom.
$clat = explode( ':', $map_values[1] ); // lat.
$clng = explode( ':', $map_values[2] ); // lng.
$lat  = explode( ':', $map_values[3] ); // lat.
$lng  = explode( ':', $map_values[4] ); // lng.
// check that the file actually exists.
$exists   = false;
$response = wp_remote_get( $marker_settings['icon'] );
$exists   = isset( $response['headers']['status'] ) && 200 == $response['headers']['status'];
// if (!$exists && function_exists('get_headers')) {
// $headers = @get_headers($marker_settings['icon']);
// if ($headers) {
// if (strpos($headers[0], '404') !== false) {
// $exists = true;
// }
// }
// }

// HTML cf7 form.
?>
<div id="<?php echo esc_attr( $html_id ); ?>" class="wpcf7-form-control-wrap cf7-google-map-container <?php echo esc_attr( $tag->name ); ?>" 
  data-name="<?php echo esc_attr( $tag->name ); ?>" 
  data-show-address="<?php echo esc_attr( $show_address ); ?>" 
  data-zoom="<?php echo esc_attr( $zoom[1] ); ?>" 
  data-clat="<?php echo esc_attr( $clat[1] ); ?>" 
  data-clng="<?php echo esc_attr( $clng[1] ); ?>" 
  data-lat="<?php echo esc_attr( $lat[1] ); ?>" 
  data-lng="<?php echo esc_attr( $lng[1] ); ?>">
  <div id="cf7-googlemap-<?php echo esc_attr( $tag->name ); ?>" class="cf7-googlemap <?php echo esc_attr( $class ); ?>"></div>
  <div class="cf7-google-map-search">
	<?php if ( get_option( 'cf7_googleMap_enable_places', 0 ) ) : ?>
	  <span class="dashicons dashicons-search"></span><span class="dashicons dashicons-no-alt"></span>
	<?php endif; ?>
	<input name="search-<?php echo esc_attr( $tag->name ); ?>" id="search-<?php echo esc_attr( $tag->name ); ?>" value="" class="cf7marker-address" type="text">
	<input name="zoom-<?php echo esc_attr( $tag->name ); ?>" id="zoom-<?php echo esc_attr( $tag->name ); ?>" value="" type="hidden">
	<input name="clat-<?php echo esc_attr( $tag->name ); ?>" id="clat-<?php echo esc_attr( $tag->name ); ?>" value="" type="hidden">
	<input name="clng-<?php echo esc_attr( $tag->name ); ?>" id="clng-<?php echo esc_attr( $tag->name ); ?>" value="" type="hidden">
	<input name="lat-<?php echo esc_attr( $tag->name ); ?>" id="lat-<?php echo esc_attr( $tag->name ); ?>" value="" type="hidden">
	<input name="lng-<?php echo esc_attr( $tag->name ); ?>" id="lng-<?php echo esc_attr( $tag->name ); ?>" value="" type="hidden">
	<input name="address-<?php echo esc_attr( $tag->name ); ?>" id="address-<?php echo esc_attr( $tag->name ); ?>" value="" type="hidden">
	<input name="<?php echo esc_attr( $tag->name ); ?>" id="<?php echo esc_attr( $tag->name ); ?>"  class="cf7-googlemap-field" value="" type="hidden">
	<input name="manual-address-<?php echo esc_attr( $tag->name ); ?>" id="manual-address-<?php echo esc_attr( $tag->name ); ?>" value="false" type="hidden">
  </div>
  <span class="wpcf7-form-control-wrap <?php echo esc_attr( $tag->name ); ?>"></span>
<?php if ( $set_address ) : ?>
  <div class="cf7-googlemap-address-fields">
	<label for="line-<?php echo esc_attr( $tag->name ); ?>">
	  <span class="cf7-googlemap-address-field cf7-googlemap-address">
		<?php
		$label = 'Address';
		$label = apply_filters( 'cf7_google_map_address_label', $label, $tag->name );
		$label = apply_filters( 'cf7_google_map_address_field_label', $label, 'adresse', $tag->name );
		echo wp_kses_post( $label );
		?>
	  </span><br />
	  <input name="line-<?php echo esc_attr( $tag->name ); ?>" id="line-<?php echo esc_attr( $tag->name ); ?>" value="" class="cf7-googlemap-address cf7-googlemap-address-line" type="text">
	</label>
	<label for="city-<?php echo esc_attr( $tag->name ); ?>">
	  <span class="cf7-googlemap-address-field cf7-googlemap-city">
		<?php
		$label = 'City';
		$label = apply_filters( 'cf7_google_map_city_label', $label, $tag->name );
		$label = apply_filters( 'cf7_google_map_address_field_label', $label, 'ville', $tag->name );
		echo wp_kses_post( $label );
		?>
	  </span><br />
	  <input name="city-<?php echo esc_attr( $tag->name ); ?>" id="city-<?php echo esc_attr( $tag->name ); ?>" value="" class="cf7-googlemap-address cf7-googlemap-address-city" type="text">
	</label>
	<label for="state-<?php echo esc_attr( $tag->name ); ?>">
	  <span class="cf7-googlemap-address-field cf7-googlemap-pin">
		<?php
		$label = 'State &amp; Pincode';
		$label = apply_filters( 'cf7_google_map_pincode_label', $label, $tag->name );
		$label = apply_filters( 'cf7_google_map_address_field_label', $label, 'code', $tag->name );
		echo wp_kses_post( $label );
		?>
	  </span><br />
	  <input name="state-<?php echo esc_attr( $tag->name ); ?>" id="state-<?php echo esc_attr( $tag->name ); ?>" value="" class="cf7-googlemap-address cf7-googlemap-address-state" type="text">
	</label>
	<label for="country-<?php echo esc_attr( $tag->name ); ?>">
	  <span class="cf7-googlemap-address-field cf7-googlemap-country">
		<?php
		$label = 'Country';
		$label = apply_filters( 'cf7_google_map_country_label', $label, $tag->name );
		$label = apply_filters( 'cf7_google_map_address_field_label', $label, 'pays', $tag->name );
		echo wp_kses_post( $label );
		?>
	  </span><br />
	  <input name="country-<?php echo esc_attr( $tag->name ); ?>" id="country-<?php echo esc_attr( $tag->name ); ?>" value="" class="cf7-googlemap-address cf7-googlemap-address-country" type="text">
	</label>
  </div>
<?php endif; ?>
<?php echo wp_kses_post( $validation_error ); ?>
  <script type="text/javascript"></script>
</div>
