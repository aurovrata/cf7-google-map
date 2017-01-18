<?php


class Listing{

public function admin_scripts_styles( $hook ) {
	global $typenow;
//die($typenow."not set");
	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

	$plugin_dir = plugins_url('', __FILE__ );
//echo $template_dir; die;
	if ( ! isset( $typenow ) ) return;
	wp_enqueue_style( 'et_admin_styles', $plugin_dir . '/css/admin_settings.css' );
	wp_enqueue_script( 'google-maps-api-admin', 'http://maps.google.com/maps/api/js?sensor=false', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'gmap3-admin', $plugin_dir . '/js/gmap3.min.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'et_admin_map', $plugin_dir . '/js/admin_settings_map.js', array( 'jquery' ), '1.0', true );
	wp_localize_script( 'et_admin_map', 'et_map_admin_settings', array(
		'theme_dir' 			=> $plugin_dir,
		'detect_address'		=> __( 'Detect the marker address', 'Explorable' ),
		'note_address'			=> __( 'It will replace the value, set in Location Address', 'Explorable' ),
		'detect_lat_lng'		=> __( 'Detect Latitude and Longtitude values, using the address', 'Explorable' ),
		'fill_address_notice'	=> __( 'Please, fill in the Location Address field', 'Explorable' ),
	) );

	wp_enqueue_script( 'metadata', $plugin_dir . '/js/jquery.MetaData.js', array('jquery'), '4.11', true );
	wp_enqueue_script( 'et-rating', $plugin_dir . '/js/jquery.rating.pack.js', array('jquery'), '4.11', true );
	wp_enqueue_style( 'et-rating', $plugin_dir . '/css/jquery.rating.css' );


	/*if ( in_array( $typenow, array( 'post', 'page' ) ) ) {
		wp_enqueue_script( 'et_image_upload_custom', $template_dir . '/js/admin_custom_uploader.js', array( 'jquery' ) );
	}*/
}


public function update_listing_fields($post_id)
{

 if ( isset( $_POST['et_listing_lat'] ) )
			update_post_meta( $post_id, '_et_listing_lat', sanitize_text_field( $_POST['et_listing_lat'] ) );
		else
			delete_post_meta( $post_id, '_et_listing_lat' );

		if ( isset( $_POST['et_listing_lng'] ) )
			update_post_meta( $post_id, '_et_listing_lng', sanitize_text_field( $_POST['et_listing_lng'] ) );
		else
			delete_post_meta( $post_id, '_et_listing_lng' );

		if ( isset( $_POST['et_listing_custom_address'] ) )
			update_post_meta( $post_id, '_et_listing_custom_address', sanitize_text_field( $_POST['et_listing_custom_address'] ) );
		else
			delete_post_meta( $post_id, '_et_listing_custom_address' );

		if ( isset( $_POST['et_listing_description'] ) )
			update_post_meta( $post_id, '_et_listing_description', sanitize_text_field( $_POST['et_listing_description'] ) );
		else
			delete_post_meta( $post_id, '_et_listing_description' );

		if ( isset( $_POST['et_star'] ) ) {
			//update_post_meta( $post_id, '_et_author_rating', intval( $_POST['et_star'] ) );
			//$this->et_update_post_user_rating( $post_id );
		} else {
			//delete_post_meta( $post_id, '_et_author_rating' );
			//$this->et_update_post_user_rating( $post_id );
		}




}

public function et_listing_settings_meta_box() {
	$post_id = get_the_ID();
	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>
	<p><?php esc_html_e( 'Drag and drop a marker to detect Location latitude and longtitude automatically.', 'Explorable' ); ?></p>

	<div id="et_admin_map"></div>

	<p>
		<label for="et_listing_lat" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Location Latitude', 'Explorable' ); ?>: </label>
		<input type="text" name="et_listing_lat" id="et_listing_lat" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_listing_lat', true ) ); ?>" />
		<br />
		<small><?php esc_html_e( 'e.g.', 'Explorable' ); ?> <code>37.715342685425995</code></small>
	</p>

	<p>
		<label for="et_listing_lng" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Location Longtitude', 'Explorable' ); ?>: </label>
		<input type="text" name="et_listing_lng" id="et_listing_lng" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_listing_lng', true ) ); ?>" />
		<br />
		<small><?php esc_html_e( 'e.g.', 'Explorable' ); ?> <code>-122.43436531250012</code></small>
	</p>

	<p>
		<label for="et_listing_custom_address" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Location Address', 'Explorable' ); ?>: </label>
		<input type="text" name="et_listing_custom_address" id="et_listing_custom_address" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_listing_custom_address', true ) ); ?>" />
		<br />
		<small>
			<?php esc_html_e( 'e.g.', 'Explorable' ); ?> <code>Unity Pavilion, Auroville</code>
			<br />
		</small>
	</p>

	<p>
		<label for="et_listing_description" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Description', 'Explorable' ); ?>: </label>
		<input type="text" name="et_listing_description" id="et_listing_description" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_listing_description', true ) ); ?>" />
		<br />
	</p>

	<p id="et-rating">
		<label style="min-width: 150px; display: inline-block; margin-bottom: 8px;"><?php esc_html_e( 'Rating', 'Explorable' ); ?>: </label>
		<br />
	<?php for ( $increment = 1; $increment <= 5; $increment = $increment+1  ) { ?>
		<input name="et_star" type="radio" class="star" value="<?php echo esc_attr( $increment ); ?>" <?php checked( get_post_meta( $post_id, '_et_author_rating', true ) >= $increment ); ?> />
	<?php } ?>
	</p>
<?php
}



}



?>
