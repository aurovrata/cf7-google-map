(function($){
  $(document).ready( function(){
    let et_map = $( '#cf7-googlemap-'+cf7GoogleMap.field ),
      googleMap, googleMarker;
    let map_container = et_map.closest('.cf7-google-map-container');
    let show_address = map_container.data('show-address');
    let has_address = $('div.cf7-googlemap-address-fields', map_container).length>0;

    /** @since 1.5.0. move script into own file, and use localisation */
    let geocoder = null;
    if(!cf7GoogleMap.plane_mode){
      if(cf7GoogleMap.geocode) geocoder = new google.maps.Geocoder;
    }
    let form = et_map.closest('form.wpcf7-form');
    let search = $('input#search-'+cf7GoogleMap.field, map_container );
    let manual = $('input#manual-address-'+cf7GoogleMap.field, map_container );
    let autoLine =''; /*track automated values of line address*/
    let $location_lat = $('#lat-'+cf7GoogleMap.field, map_container);
    let $location_lng = $('#lng-'+cf7GoogleMap.field, map_container);
    let $location_clat = $('#clat-'+cf7GoogleMap.field, map_container);
    let $location_clng = $('#clng-'+cf7GoogleMap.field, map_container);
    let $location_zoom = $('#zoom-'+cf7GoogleMap.field, map_container);
    let $location_address = $('#address-'+cf7GoogleMap.field, map_container);

    let $location = $('input#'+cf7GoogleMap.field, map_container );
    //address fields
    let countryField = $('input#country-'+cf7GoogleMap.field, map_container );
    let stateField = $('input#state-'+cf7GoogleMap.field, map_container );
    let cityField = $('input#city-'+cf7GoogleMap.field, map_container );
    let lineField = $('input#line-'+cf7GoogleMap.field, map_container );
    //let link = ' https://www.google.com/maps/search/?api=1&query=';

    map_container.on('update.cf7-google-map', function(event){
      if(show_address) $location_address.val(JSON.stringify( Object.values(event.address) ));
    });
    function init(){
      function fireMarkerUpdate(marker, e){
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
        let latlng = {lat: marker.getPosition().lat(), lng: marker.getPosition().lng()};
        if(cf7GoogleMap.geocode){
          geocoder.geocode({'location': latlng}, function(results, status) {
            if (status === 'OK') {
              if (results[1] && show_address) setAddressFields('', results[1].address_components);
            } else {
              window.alert('Google Geocoder failed due to: ' + status);
            }
          });
        }
      }
      const map_settings = {
        center: [$location_clat.val(), $location_clng.val()],
        zoom: parseInt($location_zoom.val()),
        mapTypeId: google.maps.MapTypeId[cf7GoogleMap.map_type]
      }
      const marker_settings = {
        position : [$location_lat.val(), $location_lng.val()],
      }
      let $map3 = et_map.gmap3({...map_settings, ...cf7GoogleMap.gmap3_settings}).marker({...marker_settings, ...cf7GoogleMap.marker_settings}).on('dragend', fireMarkerUpdate).then(function(result){
        googleMap = this.get(0);
        googleMarker = this.get(1);
      });
      let markers = [googleMarker];
      //locate the searched address
      let searchBox = null;
      if(cf7GoogleMap.places){
        searchBox = new google.maps.places.SearchBox(search.get(0));
        // Bias the SearchBox results towards current map's viewport.
        googleMap.addListener('bounds_changed', function() {
          searchBox.setBounds(googleMap.getBounds());
        });

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          let places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
            marker = null;
          });
          markers = [];

          // For each place, get the icon, name and location.
          let bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            let icon = {
              url: cf7GoogleMap.marker_settings.icon,
            };

            // Create a marker for each place.
            let marker = $map3.marker( {//new google.maps.Marker({
              draggable: cf7GoogleMap.marker_settings.draggable,
              icon: cf7GoogleMap.marker_settings.icon,
              title: place.name,
              position: place.geometry.location
            }).on('dragend', fireMarkerUpdate).then(function(result){
              markers.push(result);
            });
            /** @since 1.3.2 fix search box results. */
            $location.val(place.geometry.location.lat()+","+place.geometry.location.lng());
            if(show_address) setAddressFields('', place.address_components);
            /** @since 1.4.3 set mail tags bug fix */
            $location_lat.val(place.geometry.location.lat());
            $location_lng.val(place.geometry.location.lng());
            //google.maps.event.addListener(marker, 'dragend', fireMarkerUpdate);
            //markers.push(marker);

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          googleMap.fitBounds(bounds);
        });
      }
    }


    //find the address from the marker position
    function setAddressFields(name, addressComponents) {
      let idx, jdx, lineObj;
      let city, state, pin, country, line;
      let lineArr, cityArr, stateArr;
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
      /** @since 1.4.0 trigger address event */
      let event = $.Event("update.cf7-google-map", {
          'address': {
            'line': line,
            'city':city,
            'state':state,
            'pin':pin,
            'country':country
          },
          bubbles: true,
          cancelable: true
        }
      );
      map_container.trigger(event);
      if(has_address){
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
    }
    //if address line is manually changed, freeze the automated address
    $('.cf7-googlemap-address-fields').on('change', manualAddress);

     function manualAddress(){
      if($(this).val() != autoLine){
        manual.val(true);
      }
      let event = $.Event("update.cf7-google-map", {
          'address': {
            'line': lineField.val(),
            'city':cityField.val(),
            'state':stateField.val(),
            'country':countryField.val()
          },
          bubbles: true,
          cancelable: true
        }
      );
      map_container.trigger(event);
    }
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
        if('' == $('input#lat-'+cf7GoogleMap.field, form ).val() ){
          et_map.after('<span role="alert" class="wpcf7-not-valid-tip">The location is required.</span>');
        }
      });
    }
    //set the width of the search field
    let map_width =  et_map.css('width');
    $('div.cf7-google-map-search', map_container).css('width','calc(' + map_width + ' - 10px)');
    /*@since 1.2.0 init map once Post My CF7 Form has loaded.*/
    let $cf7Form = et_map.closest('form.wpcf7-form');
  if(! cf7GoogleMap.plane_mode){
    if($cf7Form.is('.cf7_2_post form.wpcf7-form')){
      let id = $cf7Form.closest('.cf7_2_post').attr('id');
      $cf7Form.on(id, function(event){
        init();
      });
    }else{
      init();
    }
  }else{
    et_map.append('<p style="text-align: center;padding: 93px 0;border: solid 1px;"><em>airplane mode</em></p>');
  }
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
