<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://syllogic.in
 * @since      1.0.0
 *
 * @package    Cf7_GoogleMap
 * @subpackage Cf7_GoogleMap/admin/partials
 */
 // TODO: add a check box to include or not address fields
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="control-box cf7-googleMap">
  <fieldset>
	<legend><?php esc_html_e( 'Google Map field for contact form 7', 'cf7-google-map' ); ?></legend>
	<table id="googleMap-tag-generator" class="form-table">
	  <tbody>
		<?php
		/**
		 * Check/warn for google api.
		 *
		 * @since 1.2.6
		 */
		$google_map_api_key = get_option( 'cf7_googleMap_api_key', '' );
		if ( empty( $google_map_api_key ) ) {
			?>
		  <tr style="background:#ffc9c9;border: 3px solid red;">
		  <th scope="row" style="padding:3px;"><?php esc_html_e( 'WARNING', 'cf7-google-map' ); ?></th>
		  <td><?php echo wp_kses_post( sprintf( __( 'You need to <a href="%s">set an API key</a> from Google to use and display maps on your site.', 'cf7-google-map' ), admin_url( 'options-general.php?page=cf7-googleMap-settings' ) ) ); ?></td>
		  </tr>
			<?php
		}
		?>
		<tr>
		  <th scope="row"><label for="<?php esc_attr_e( $args['content'] ); ?>-name"><?php esc_html_e( 'Name', 'contact-form-7' ); ?></label></th>
		  <td><input type="text" name="name" class="tg-name oneline" id="<?php esc_attr_e( $args['content'] . '-name' ); ?>" /></td>
		  </tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Field type', 'contact-form-7' ); ?></th>
			<td><input name="required" type="checkbox"><?php esc_html_e( 'Required field', 'contact-form-7' ); ?><br /></td>
		  </tr>
		  <tr>
			<th>
			  <p><?php esc_html_e( 'Map Zoom', 'cf7-google-map' ); ?>: </p>
			  <input type="text" readonly name="cf7_zoom" id="cf7_zoom" class="regular-text cf7-googleMap-values" value="1" /><br/>
			  <label for="cf7_centre_lat" ><?php esc_html_e( 'Map Centre', 'cf7-google-map' ); ?>: <br />
			  <input type="text" readonly name="cf7_centre_lat" id="cf7_centre_lat" class="regular-text cf7-googleMap-values" value="28.156269" /><br />
			  <input type="text" readonly name="cf7_centre_lng" id="cf7_centre_lng" class="regular-text cf7-googleMap-values" value="63.323149" />
			</th>
			<td>
			  <div id="cf7_admin_map"></div>
			</td>
		  </tr>
		  <tr>
			<th>
			  <?php esc_html_e( 'Marker location', 'cf7-google-map' ); ?>:
			</th>
			<td>
			  <div class="listings">
				<input type="text" name="cf7_listing_lat" id="cf7_listing_lat" class="regular-text cf7-googleMap-values" value="12.007089" />,
			  </div>
			  <div class="listings">
				<input type="text" name="cf7_listing_lng" id="cf7_listing_lng" class="regular-text cf7-googleMap-values" value="79.810600" />
			  </div>
			</td>
		  </tr>
		  <tr>
			<th>
			  <label for="tag-generator-panel-number-id"><?php esc_html_e( 'Id attribute', 'contact-form-7' ); ?></label>
			</th>
			<td>
			  <input name="id" class="idvalue oneline option" id="tag-generator-panel-map-id" type="text">
			</td>
		  </tr>
		  <tr>
			<th>
			  <label for="tag-generator-panel-number-class"><?php esc_html_e( 'Class attribute', 'contact-form-7' ); ?></label>
			</th>
			<td>
			  <input name="class" class="classvalue oneline option" id="tag-generator-panel-map-class" type="text">
			</td>
		  </tr>
		  <?php if ( get_option( 'cf7_googleMap_enable_geocode', 0 ) ) : ?>
		  <tr>
			<th scope="row"><?php esc_html_e( 'Address fields', 'cf7-google-map' ); ?></th>
			<td>
			<input name="show_address" id="cf7-google-map-show-address" type="checkbox"> <?php esc_html_e( 'Show address fields (auto populated line / city / state / country fields)', 'cf7-google-map' ); ?>
			<div class="cf7-gmap-address-fields">
			  <div>
				<input type="radio" name="address_fields" value="show" checked="true"/> <span><?php esc_html_e( 'show 4 fields (address line/city/state &amp; pin/country)', 'cf7-google-map' ); ?></span>
			  </div>
			  <div>
				<input type="radio" name="address_fields" value="custom" /> <span><?php echo wp_kses_post( __( 'Custom (populate your own fields with a javascript event, read <a href="https://wordpress.org/plugins/cf7-google-map/#faq" target="_blank">FAQ</a> #5 for more info)', 'cf7-google-map' ) ); ?></span>
			  </div>
			</div>
		  </td>
			</tr>
		<?php endif; ?>
	  </tbody>
	</table>
  </fieldset>
</div>
<div class="insert-box">
  <input type="hidden" name="values" value="" />
  <input type="text" name="map" class="tag code" readonly="readonly" onfocus="this.select()" />

  <div class="submitbox">
	  <input type="button" class="button button-primary insert-tag" value="<?php esc_html_e( 'Insert Tag', 'contact-form-7' ); ?>" />
  </div>

  <br class="clear" />
</div>
