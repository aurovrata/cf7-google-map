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
if($plugin_url .'assets/red-marker.png' !== $marker_icon_path && !@getimagesize($marker_icon_path) ){ //reset the marker
  debug_msg("unable to locate marker icon url: ".$marker_icon_path);
  $marker_icon_path = $plugin_url .'assets/red-marker.png';
}
//check that the file actually exists
$exists = false;
if ( in_array('curl', get_loaded_extensions()) ) {
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

//HTML cf7 form
?>
<span class="wpcf7-form-control-wrap cf7-google-map-container <?= $tag->name?> <?= $show_address;?>">
  <div id="cf7-googlemap-<?= $tag->name?>" class="cf7-googlemap <?= $class?>"></div>
  <div class="cf7-google-map-search">
    <span class="dashicons dashicons-search"></span><span class="dashicons dashicons-no-alt"></span>
    <input name="address-<?= $tag->name?>" id="address-<?= $tag->name?>" value="" class="cf7marker-address" type="text">
    <input name="zoom-<?= $tag->name?>" id="zoom-<?= $tag->name?>" value="<?= $zoom[1]?>" type="hidden">
    <input name="clat-<?= $tag->name?>" id="clat-<?= $tag->name?>" value="<?= $clat[1]?>" type="hidden">
    <input name="clng-<?= $tag->name?>" id="clng-<?= $tag->name?>" value="<?= $clng[1]?>" type="hidden">
    <input name="lat-<?= $tag->name?>" id="lat-<?= $tag->name?>" value="<?= $lat[1]?>" type="hidden">
    <input name="lng-<?= $tag->name?>" id="lng-<?= $tag->name?>" value="<?= $lng[1]?>" type="hidden">
    <input name="<?= $tag->name?>" id="<?= $tag->name?>" value="" type="hidden">
    <input name="manual-address-<?= $tag->name?>" id="manual-address-<?= $tag->name?>" value="false" type="hidden">
  </div>
  <div class="cf7-googlemap-address-fields">
    <label for="line-<?= $tag->name?>"><?= apply_filters('cf7_google_map_address_label','Address', $tag->name);?><br />
    <input name="line-<?= $tag->name?>" id="line-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
    <label for="city-<?= $tag->name?>"><?= apply_filters('cf7_google_map_city_label','City',$tag->name);?><br />
    <input name="city-<?= $tag->name?>" id="city-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
    <label for="state-<?= $tag->name?>"><?= apply_filters('cf7_google_map_pincode_label','State &amp; Pincode',$tag->name);?><br />
    <input name="state-<?= $tag->name?>" id="state-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
    <label for="country-<?= $tag->name?>"><?= apply_filters('cf7_google_map_country_label','Country',$tag->name);?><br />
    <input name="country-<?= $tag->name?>" id="country-<?= $tag->name?>" value="" class="cf7-googlemap-address" type="text"></label>
  </div>
</span>
<?= $validation_error;?>
<script type="text/javascript">
(function($){
	$(document).ready( function(){
		var et_map = $( '#cf7-googlemap-<?= $tag->name?>' ),
			googleMap, googleMarker;
    var map_container = et_map.closest('.cf7-google-map-container');
    <?php if(! class_exists( 'Airplane_Mode_Core' ) || !Airplane_Mode_Core::getInstance()->enabled()):?>
    var geocoder = new google.maps.Geocoder;
    <?php endif;?>
    var form = et_map.closest('form.wpcf7-form');
    var address = $('input#address-<?= $tag->name?>', map_container );
    var manual = $('input#manual-address-<?= $tag->name?>', map_container );
    var autoLine =''; //track automated values of line address
    var $location_lat = $('#lat-<?= $tag->name?>', map_container);
    var $location_lng = $('#lng-<?= $tag->name?>', map_container);
    var $location_clat = $('#clat-<?= $tag->name?>', map_container);
    var $location_clng = $('#clng-<?= $tag->name?>', map_container);
    var $location_zoom = $('#zoom-<?= $tag->name?>', map_container);
    var $location = $('input#<?= $tag->name?>', map_container );
    //address fields
    var countryField = $('input#country-<?= $tag->name?>', map_container );
    var stateField = $('input#state-<?= $tag->name?>', map_container );
    var cityField = $('input#city-<?= $tag->name?>', map_container );
    var lineField = $('input#line-<?= $tag->name?>', map_container );
    //var link = ' https://www.google.com/maps/search/?api=1&query=';

    function init(){
  		et_map.gmap3({
        center : [$location_clat.val(), $location_clng.val()],
  	    zoom: parseInt($location_zoom.val()),
  	    mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: <?= ($map_control) ? 'true':'false';?>,
        navigationControl: <?= ($navigation_control) ? 'true':'false';?>,
        scrollwheel: <?= ($scrollwheel) ? 'true':'false';?>,
        streetViewControl: <?= ($street_view) ? 'true':'false';?>
      }).marker({
  			position : [$location_lat.val(), $location_lng.val()],
  			icon : "<?= $marker_icon_path ?>",
        draggable : true
      }).on('dragend', function(marker, e){
        $location_lat.val(marker.getPosition().lat());
        $location_lng.val( marker.getPosition().lng());
        $location.val( marker.getPosition().lat() + "," + marker.getPosition().lng() );
        //console.log(marker);
        $location_zoom.val( marker.getMap().zoom);
        $location_clat.val( marker.getMap().getCenter().lat() );
        $location_clng.val( marker.getMap().getCenter().lng() );
        //reverse lookup the address
        if("true" == manual.val()){
          return;
        }
        var latlng = {lat: marker.getPosition().lat(), lng: marker.getPosition().lng()};
        geocoder.geocode({'location': latlng}, function(results, status) {
          if (status === 'OK') {
            if (results[1]) {
              //console.log(results);
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
        //input.className = '';
        var place = autocomplete.getPlace();
        //console.log(place);
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
        $location_lat.val(place.geometry.location.lat());
        $location_lng.val( place.geometry.location.lng());
        $location.val( place.geometry.location.lat() + "," + place.geometry.location.lng() );
        $location_zoom.val(17);
        $location_clat.val( place.geometry.location.lat() );
        $location_clng.val( place.geometry.location.lng() );
        //update the address fields
        setAddressFields(place.name, place.address_components);
        $('.cf7-google-map-search .dashicons-no-alt', map_container).closeCF7gmapSearchField();
      });
    }
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
              //console.log("pin: "+pin);
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
        if('' == $('input#lat-<?= $tag->name?>', form ).val() ){
          et_map.after('<span role="alert" class="wpcf7-not-valid-tip">The location is required.</span>');
        }
      });
    }
    //set the width of the search field
    var map_width =  et_map.css('width');
    $('div.cf7-google-map-search', map_container).css('width','calc(' + map_width + ' - 10px)');
    /*@since 1.2.0 init map once Post My CF7 Form has loaded.*/
    var $cf7Form = et_map.closest('form.wpcf7-form');
  <?php if(! class_exists( 'Airplane_Mode_Core' ) || ! Airplane_Mode_Core::getInstance()->enabled()):?>
    if($cf7Form.is('.cf7_2_post form.wpcf7-form')){
      var id = $cf7Form.closest('.cf7_2_post').attr('id');
      $cf7Form.on(id, function(event){
        init();
      });
    }else{
      init();
    }
  <?php else:?>
    et_map.append('<p style="text-align: center;padding: 93px 0;border: solid 1px;"><em>airplane mode</em></p>');
  <?php endif;?>
    //on map resize
    et_map.resize(function(){
      map_width =  $(this).css('width');
      $('div.cf7-google-map-search', map_container).css('width','calc(' + map_width + ' - 10px)');
      google.maps.event.trigger(googleMap, 'resize');
    });
    //search button
    $('.cf7-google-map-search .dashicons-search', map_container).on('click', function(){
      $(this).siblings('.cf7marker-address').show().val('').focus();
      $(this).hide();
      $(this).siblings('.dashicons-no-alt').show();
    });
    $('.cf7-google-map-search .dashicons-no-alt', map_container).on('click', function(){
      $(this).closeCF7gmapSearchField();
    });
	});
  $.fn.closeCF7gmapSearchField = function(){
    $(this).siblings('.cf7marker-address').hide();
    $(this).hide();
    $(this).siblings('.dashicons-search').show();
  }
})(jQuery)
</script>
