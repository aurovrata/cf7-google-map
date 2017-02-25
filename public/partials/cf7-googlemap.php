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
$show_address = 'cf7-googlemap-address-hide';
$show_address = 'cf7-googlemap-address-show';
//error_log("GoogleMap: ".$value."\n".print_r($slide_values,true));
$zoom = explode(':',$map_values[0]); //zoom:
$clat = explode(':',$map_values[1]); //lat:
$clng = explode(':',$map_values[2]); //lng:
$lat = explode(':',$map_values[3]); //lat:
$lng = explode(':',$map_values[4]); //lng:

$map_type = apply_filters('cf7_google_map_default_type','ROADMAP');
$map_type = strtoupper($map_type);
switch($map_type){
  case 'ROADMAP':
  case 'SATELLITE';
  case 'HYBRID':
  case 'TERRAIN':
    break;
  default:
    $map_type = 'ROADMAP';
    break;
}
$map_control = apply_filters('cf7_google_map_controls',true, $tag->name);
$navigation_control = apply_filters('cf7_google_map_navigation_controls',true, $tag->name);
$scrollwheel = apply_filters('cf7_google_map_scrollwheel',true, $tag->name);
$street_view = apply_filters('cf7_google_map_navigation_controls',true, $tag->name);
$marker_icon_path = apply_filters('cf7_google_map_marker_icon_url_path', $plugin_url .'assets/red-marker.png', $tag->name);
//check that the file actually exists
$exists   = false;
if (!$exists && in_array('curl', get_loaded_extensions())) {
	$ch = curl_init($marker_icon_path);
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
if(!$exists){ //reset the marker
  debug_msg("unable to locate marker icon url:".$marker_icon_path);
  $marker_icon_path = $plugin_url .'assets/red-marker.png';
}
//HTML cf7 form
?>
<div class="wpcf7-form-control-wrap cf7-google-map-container <?php echo $tag->name?> <?php echo $show_address;?>">
  <div id="cf7-googlemap-<?php echo $tag->name?>" class="cf7-googlemap <?php echo $class?>"></div>
  <div class="cf7-google-map-search">
    <span class="dashicons dashicons-search"></span><span class="dashicons dashicons-no-alt"></span>
    <input name="address-<?php echo $tag->name?>" id="address-<?php echo $tag->name?>" value="" class="cf7marker-address" type="text">
    <input name="lat-<?php echo $tag->name?>" id="lat-<?php echo $tag->name?>" value="" class="cf7marker-lat" type="hidden">
    <input name="lng-<?php echo $tag->name?>" id="lng-<?php echo $tag->name?>" value="" class="cf7marker-lng" type="hidden">
    <input name="<?php echo $tag->name?>" id="<?php echo $tag->name?>" value="" class="cf7marker-ll" type="hidden">
    <input name="manual-address-<?php echo $tag->name?>" id="manual-address-<?php echo $tag->name?>" value="false" type="hidden">
  </div>
  <div class="cf7-googlemap-address-fields">
    <label for="line-<?php echo $tag->name?>"><?php echo apply_filters('cf7_google_map_address_label','Address', $tag->name);?><br />
    <input name="line-<?php echo $tag->name?>" id="line-<?php echo $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
    <label for="city-<?php echo $tag->name?>"><?php echo apply_filters('cf7_google_map_city_label','City',$tag->name);?><br />
    <input name="city-<?php echo $tag->name?>" id="city-<?php echo $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
    <label for="state-<?php echo $tag->name?>"><?php echo apply_filters('cf7_google_map_pincode_label','State &amp; Pincode',$tag->name);?><br />
    <input name="state-<?php echo $tag->name?>" id="state-<?php echo $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
    <label for="country-<?php echo $tag->name?>"><?php echo apply_filters('cf7_google_map_country_label','Country',$tag->name);?><br />
    <input name="country-<?php echo $tag->name?>" id="country-<?php echo $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
  </div>
</div>
<?php echo $validation_error;?>
<script type="text/javascript">
(function($){
	$(document).ready( function(){
		var et_map = $( '#cf7-googlemap-<?php echo $tag->name?>' ),
			googleMap, googleMarker;
    var map_container = et_map.closest('.cf7-google-map-container');
    var geocoder = new google.maps.Geocoder;
    var form = et_map.closest('form.wpcf7-form');
    var address = $('input#address-<?php echo $tag->name?>', map_container );
    var manual = $('input#manual-address-<?php echo $tag->name?>', map_container );
    var autoLine =''; //track automated values of line address

		et_map.gmap3({
      center : [<?php echo $clat[1]?>, <?php echo $clng[1]?>],
	    zoom: <?php echo $zoom[1] ?>,
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: <?php echo ($map_control) ? 'true':'false';?>,
      navigationControl: <?php echo ($navigation_control) ? 'true':'false';?>,
      scrollwheel: <?php echo ($scrollwheel) ? 'true':'false';?>,
      streetViewControl: <?php echo ($street_view) ? 'true':'false';?>
    }).marker({
			position : [<?php echo $lat[1]?>, <?php echo $lng[1]?>],
			icon : "<?php echo $marker_icon_path ?>",
      draggable : true
    }).on('dragend', function(marker, e){
      $('input#lat-<?php echo $tag->name?>', map_container ).val(marker.getPosition().lat());
      $('input#lng-<?php echo $tag->name?>', map_container ).val( marker.getPosition().lng());
      $('input#<?php echo $tag->name?>', map_container ).val( marker.getPosition().lat() + "," + marker.getPosition().lng() );

      //reverse lookup the address
      if("true" == manual.val()){
        return;
      }
      var latlng = {lat: marker.getPosition().lat(), lng: marker.getPosition().lng()};
      geocoder.geocode({'location': latlng}, function(results, status) {
        if (status === 'OK') {
          if (results[1]) {
            console.log(results);
            var geoAddress = results[1].formatted_address;
            address.val(geoAddress);
            setAddressFields('', results[1].address_components);
          } else {
            address.val('Unknown location');
          }
        } else {
          window.alert('Google Geocoder failed due to: ' + status);
        }
      });
		}).then(function(result){
      googleMap = this.get(0);
      googleMarker = this.get(1);
    });
    //locate the searched address
    var autocomplete = new google.maps.places.Autocomplete(address.get(0),{types:["geocode"]});
    autocomplete.bindTo('bounds', googleMap);

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      //marker.setVisible(false);
      //input.className = '';
      var place = autocomplete.getPlace();
      console.log(place);
      if (!place.geometry) {
        // Inform the user that the place was not found and return.
        console.log('No locations found for '+place.name);
        return;
      }

      // If the place has a geometry, then present it on a map.
      if (place.geometry.viewport) {
        googleMap.fitBounds(place.geometry.viewport);
      } else {
        googleMap.setCenter(place.geometry.location);
        googleMap.setZoom(17);  // Why 17? Because it looks good.
      }
      //place markert to position
      googleMarker.setPosition(place.geometry.location);
      //console.log(place);
      //update the address fields
      setAddressFields(place.name, place.address_components);
    });

    var countryField = $('input#country-<?php echo $tag->name?>', map_container );
    var stateField = $('input#state-<?php echo $tag->name?>', map_container );
    var cityField = $('input#city-<?php echo $tag->name?>', map_container );
    var lineField = $('input#line-<?php echo $tag->name?>', map_container );

    //find the address from the marker position
    function setAddressFields(name, addressComponents) {
      var idx, jdx, lineObj;
      var city, state, pin, country, line;
      var lineArr, cityArr, stateArr;
      lineArr = ['','','',''];
      cityArr = ['','','','','',''];
      stateArr = ['','','','',''];
      line=city=state=pin=country='';

      if(''!= name){
        lineArr[0]=name;
      }

      for(idx=0 ; idx<addressComponents.length ; idx++){
        lineObj = addressComponents[idx];
        for(jdx=0; jdx<lineObj.types.length; jdx++){
          switch(lineObj.types[jdx]){
            case 'bus_station':
            case 'establishment':
            case 'point_of_interest':
            case 'transit_station':
            case 'premise':
            case 'subpremise':
              lineArr[0]= lineObj.long_name;
              break;
            case 'street_number':
              lineArr[1]= lineObj.long_name;
              break;
            case 'street_address':
              lineArr[2]= lineObj.long_name;
              break;
            case 'route':
              lineArr[3]= lineObj.long_name;
              break;
            case 'neighborhood':
              cityArr[0]= lineObj.long_name;
              break;
            case 'sublocality_level_5':
              cityArr[0]= lineObj.long_name;
              break;
            case 'sublocality_level_4':
              cityArr[1]= lineObj.long_name;
              break;
            case 'sublocality_level_3':
              cityArr[2]= lineObj.long_name;
              break;
            case 'sublocality_level_2':
              cityArr[3]= lineObj.long_name;
              break;
            case 'sublocality':
              if(jdx==lineObj.types.length){
                cityArr[4]= lineObj.long_name;
              }
              break;
            case 'sublocality_level_1':
              cityArr[4]= lineObj.long_name;
              break;
            case 'locality':
              cityArr[5] = lineObj.long_name;
              break;
            case 'administrative_area_level_5':
              stateArr[0] = lineObj.long_name;
              break;
            case 'administrative_area_level_4':
              stateArr[1] = lineObj.long_name;
              break;
            case 'administrative_area_level_3':
              stateArr[2] = lineObj.long_name;
              break;
            case 'administrative_area_level_2':
              stateArr[3] = lineObj.long_name;
              break;
            case 'administrative_area_level_1':
              stateArr[4] = lineObj.long_name;
              break;
            case 'country':
              country = lineObj.long_name;
            case 'postal_code':
              pin = lineObj.long_name;
              console.log("pin: "+pin);
              break;

          }
        }
      }

      lineArr = $.unique(lineArr);
      cityArr = $.unique(cityArr);
      stateArr = $.unique(stateArr);
      for(idx=0; idx<lineArr.length;idx++){
        if(''!= lineArr[idx]){
          line += lineArr[idx]+", ";
        }
      }
      for(idx=0; idx<cityArr.length;idx++){
        if(''!= cityArr[idx]){
          city += cityArr[idx]+", ";
        }
      }
      for(idx=0; idx<stateArr.length;idx++){
        if(''!= stateArr[idx] && !~cityArr.indexOf(stateArr[idx])){
          state += stateArr[idx]+", ";
        }
      }
      if(''!=pin){
        state = state + " " + pin;
      }
      //set address fields
      autoLine = line;
      countryField.val(country);
      stateField.val(state);
      cityField.val(city);
      lineField.val(line);

    }
    //if address line is manually changed, freeze the automated address
    lineField.on('change', function(){
      if($(this).val() != autoLine){
        manual.val(true);
      }
    });
    //if the form contains jquery tabs, let's refresh the map
    form.on( "tabsactivate", function( event ){
      if( $.contains($(event.trigger),et_map) ){
        google.maps.event.trigger(et_map, 'resize');
      }
    });
    //if the form contains jquery accordion, let's refresh the map
    form.on( "accordionactivate", function( event ){
      if( $.contains($(event.trigger),et_map) ){
        google.maps.event.trigger(et_map, 'resize');
      }
    });
    if(et_map.is('.wpcf7-validates-as-required')){
      form.on('submit', function(event){
        if('' == $('input#lat-<?php echo $tag->name?>', form ).val() ){
          et_map.after('<span role="alert" class="wpcf7-not-valid-tip">The location is required.</span>');
        }
      });
    }
    //set the width of the search field
    var map_width =  et_map.css('width');
    $('div.cf7-google-map-search', map_container).css('width','calc(' + map_width + ' - 10px)');
    //on map resize
    et_map.resize(function(){
      map_width =  $(this).css('width');
      $('div.cf7-google-map-search', map_container).css('width','calc(' + map_width + ' - 10px)');
      google.maps.event.trigger(googleMap, 'resize');
    });
    //search button
    $('.cf7-google-map-search .dashicons-search', map_container).on('click', function(){
      $(this).siblings('.cf7marker-address').show();
      $(this).hide();
      $(this).siblings('.dashicons-no-alt').show();
    });
    $('.cf7-google-map-search .dashicons-no-alt', map_container).on('click', function(){
      $(this).siblings('.cf7marker-address').hide();
      $(this).hide();
      $(this).siblings('.dashicons-search').show();
    });
	});
})(jQuery)
</script>
