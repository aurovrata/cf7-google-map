<?php
/**
* HTML for settings page.
* @since 1.4.3.
*/
?>
<style>
  ul.default-style{list-style:disc;}
</style>
<div class="wrap">
  <form method="post" action="options.php">
    <?php settings_fields( 'cf7-google-map-settings-group' ); ?>
    <?php do_settings_sections( 'cf7-google-map-settings-group' ); ?>
    <h2>Contact form 7 Google Map Extension Settings</h2>
    <table class="form-table">
      <tbody>
          <tr>
            <th scope="row">
              <label for="cf7_googleMap_api_key"><?=__('Google Maps API Key','cf7-google-map')?></label>
            </th>
            <td>
              <input type="text" name="cf7_googleMap_api_key" value="<?php echo esc_attr( get_option('cf7_googleMap_api_key') ); ?>" />
              <p class="description"><?=__('Get an API Key from <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank">Google</a>','cf7-google-map')?>. <?=__('The Google Maps API usage policy has changed and you now need to,','cf7-google-map')?>
                <ul class="default-style">
                  <li><?=__('create a <a href="https://developers.google.com/maps/gmp-get-started">Google Map billing account</a> (register a payment method), even for testing/free usage, but a <a href="https://developers.google.com/maps/billing/gmp-billing">free monthly credit is provided by Google</a>.','cf7-google-map')?></li>
                  <li><?=__('next, you need to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key">create an API key</a> and enable the Gmaps Javascript API for that key. ','cf7-google-map')?></li>
                </ul>
              </p>
            </td>
        </tr>
        <tr>
          <th scope="row">
            <label for="cf7_googleMap_enable_geocode"><?=__('Enable address field option','cf7-google-map')?> </label>
          </th>
          <td>
            <input type="checkbox" name="cf7_googleMap_enable_geocode" value="1" <?= checked(1, get_option('cf7_googleMap_enable_geocode'), false ); ?> /><?= __('Enable this option to add address fields to your maps.','cf7-google-map')?>
            <p class="description"><?=__('You will also need to enable <a href="https://developers.google.com/maps/documentation/geocoding/get-api-key">Geocoding API</a> to retrieve physical addresses for locations.','cf7-google-map')?></p>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <label for="cf7_googleMap_enable_places"><?=__('Enable google search box','cf7-google-map')?> </label>
        </th>
        <td>
          <input type="checkbox" name="cf7_googleMap_enable_places" value="1" <?= checked(1, get_option('cf7_googleMap_enable_places'), false ); ?> /><?= __('This adds a <a href="https://developers.google.com/maps/documentation/javascript/examples/places-searchbox">search box</a> to your maps.','cf7-google-map')?>
          <p class="description"><?=__('You will also need to enable <a href="https://developers.google.com/places/web-service/get-api-key">Google Places API</a> to search place names on a map.','cf7-google-map')?></p>
        </td>
      </tr>
    </tbody>
    </table>
    <style>input[name="cf7_googleMap_api_key"]{width:100%; max-width:350px;}</style>
    <?php submit_button();?>
  </form>
</div>
