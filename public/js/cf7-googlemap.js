(function($){
//init funciton for maps.
  const $map_forms = $('.cf7-google-map-container').closest('form.wpcf7-form');

  $.fn.initCF7googleMap = function(){
    let $map_container = $(this);
    if(!$map_container.is('.cf7-google-map-container')) return false;
    let $et_map = $( '.cf7-googlemap', $map_container),
      field = $et_map.attr('id').replace('cf7-googlemap-',''),
      googleMap, googleMarker,
      show_address = $map_container.data('show-address'),
      has_address = $('div.cf7-googlemap-address-fields', $map_container).length>0,
      geocoder = (!cf7GoogleMap.plane_mode && cf7GoogleMap.geocode) ? new google.maps.Geocoder:null,
      $form = $et_map.closest('form.wpcf7-form'),
      search = $('input#search-'+field, $map_container ),
      $manual = $('input#manual-address-'+field, $map_container ),
      autoLine ='', /*track automated values of line address*/
      $location_lat = $('#lat-'+field, $map_container),
      $location_lng = $('#lng-'+field, $map_container),
      $location_clat = $('#clat-'+field, $map_container),
      $location_clng = $('#clng-'+field, $map_container),
      $location_zoom = $('#zoom-'+field, $map_container),
      $location_address = $('#address-'+field, $map_container),
      $location = $('input#'+field, $map_container ),
      countryField = $('input#country-'+field, $map_container ),
      stateField = $('input#state-'+field, $map_container ),
      cityField = $('input#city-'+field, $map_container ),
      lineField = $('input#line-'+field, $map_container ),$map3;

    $map_container.on('update.cf7-google-map', function(event){
      if(show_address) $location_address.val(JSON.stringify( Object.values(event.address) ));
    });


    function fireMarkerUpdate(marker, e){
      $location_lat.val(marker.getPosition().lat());
      $location_lng.val( marker.getPosition().lng());
      $location.val( marker.getPosition().lat() + "," + marker.getPosition().lng() );
      //console.log(marker);
      $location_zoom.val( marker.getMap().zoom);
      $location_clat.val( marker.getMap().getCenter().lat() );
      $location_clng.val( marker.getMap().getCenter().lng() );
      //reverse lookup the address
      if("true" == $manual.val()){
        return;
      }
      let latlng = {lat: marker.getPosition().lat(), lng: marker.getPosition().lng()};
      if(cf7GoogleMap.geocode){
        geocoder.geocode({'location': latlng}, function(results, status) {
          if (status === 'OK') {
            if (results[1] && show_address){
              let addObj = parseGeolocationAddress('', results[1].address_components);
              /** @since 1.4.0 trigger address event */
              let event = $.Event("update.cf7-google-map", {
                  'gm3':$map3,
                  'gmap':googleMap,
                  'marker':marker,
                  'address': addObj,
                  bubbles: true,
                  cancelable: true
                }
              );
              $map_container.trigger(event);
              if(has_address){
                let state = addObj.state;
                if(''!=addObj.pin){
                  state = addObj.state + " " + addObj.pin;
                }
                //set address fields
                autoLine = addObj.line;
                countryField.val(addObj.country);
                stateField.val(state);
                cityField.val(addObj.city);
                lineField.val(addObj.line);
              }
            }
          } else {
            window.alert('Google Geocoder failed due to: ' + status);
          }
        });
      }
      /** @since 1.6.0 trigger address event */
      let event = $.Event("drag.cf7-google-map", {
          'settings': {
            'center': [$location_clat.val(), $location_clng.val()],
            'zoom':$location_zoom.val(),
            'type':cf7GoogleMap.map_type,
            'marker':[$location_lat.val(), $location_lng.val()],
          },
          bubbles: true,
          cancelable: true
        }
      );
      $map_container.trigger(event);
      return $map_container;
    }
    const iclat = $location_clat.val(),
      iclng = $location_clng.val(),
      ilat = $location_lat.val(),
      ilng  = $location_lng.val(),
      izoom = $location_zoom.val();
    const map_settings = {
      center: [( ''!=iclat ? iclat:$map_container.data('clat') ),  ( ''!=iclng ? iclng : $map_container.data('clng') )],
      zoom: (''!= izoom ? parseInt(izoom) : parseInt($map_container.data('zoom')) ),
      mapTypeId: google.maps.MapTypeId[cf7GoogleMap.map_type]
    }
    const marker_settings = {
      position : [( ''!=ilat ? ilat:$map_container.data('lat') ),  ( ''!=ilng ? ilng : $map_container.data('lng') )],
    }
    $map3 = $et_map.gmap3({...map_settings, ...cf7GoogleMap.gmap3_settings});
    $map3.marker({...marker_settings, ...cf7GoogleMap.marker_settings}).on('dragend', fireMarkerUpdate).then(function(map){
      googleMap = this.get(0);
      googleMarker = this.get(1);
      /** @since 1.6.0 trigger address event */
      let event = $.Event("init.cf7-google-map", {
          'gm3':$map3,
          'gmap':googleMap,
          'marker':googleMarker,
          'settings': {
            'center': [$location_clat.val(), $location_clng.val()],
            'zoom':$location_zoom.val(),
            'type':cf7GoogleMap.map_type,
            'marker':[$location_lat.val(), $location_lng.val()],
          },
          bubbles: true,
          cancelable: true
        }
      );
      $map_container.trigger(event);
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
          googleMarker = $map3.marker( {//new google.maps.Marker({
            draggable: cf7GoogleMap.marker_settings.draggable,
            icon: cf7GoogleMap.marker_settings.icon,
            title: place.name,
            position: place.geometry.location
          }).on('dragend', fireMarkerUpdate).then(function(result){
            markers.push(result);
          });
          /** @since 1.3.2 fix search box results. */
          $location.val(place.geometry.location.lat()+","+place.geometry.location.lng());
          if(show_address){
            let addObj = parseGeolocationAddress('', place.address_components);
            /** @since 1.4.0 trigger address event */
            let event = $.Event("update.cf7-google-map", {
                'gm3':$map3,
                'gmap':googleMap,
                'marker':markers[0],
                'address': addObj,
                bubbles: true,
                cancelable: true
              }
            );
            $map_container.trigger(event);

            if(has_address){
              let state = addObj.state;
              if(''!=addObj.pin){
                state = addObj.state + " " + addObj.pin;
              }
              //set address fields
              autoLine = addObj.line;
              countryField.val(addObj.country);
              stateField.val(state);
              cityField.val(addObj.city);
              lineField.val(addObj.line);
            }
          }
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
    //if address line is manually changed, freeze the automated address
    $('.cf7-googlemap-address-line', $map_container).on('change', function(e){
      if($(this).val() != autoLine){
        $manual.val(true);
      }
      let event = $.Event("update.cf7-google-map", {
        'gm3':$map3,
        'gmap':googleMap,
        'marker':googleMarker,
        'address': {
          'line': lineField.val(),
          'city':cityField.val(),
          'state':stateField.val(),
          'country':countryField.val()
        },
        bubbles: true,
        cancelable: true
      });
      $map_container.trigger(event);
    });

    //if the $form contains jquery tabs, let's refresh the map
    $form.on( "tabsactivate", function( event ){
      if( $.contains($(event.trigger),$et_map) ){
        google.maps.event.trigger($et_map, 'resize');
      }
    });
    //if the $form contains jquery accordion, let's refresh the map
    $form.on( "accordionactivate", function( event ){
      if( $.contains($(event.trigger),$et_map) ){
        google.maps.event.trigger($et_map, 'resize');
      }
    });

    if($et_map.is('.wpcf7-validates-as-required')){
      /** @since 1.7.0 */
      $map_container.on('drag.cf7-google-map',function(){
        $('.wpcf7-not-valid-tip', $map_container).remove();
      })
    }
    //set the width of the search field
    let map_width =  $et_map.css('width');
    $('div.cf7-google-map-search', $map_container).css('width','calc(' + map_width + ' - 10px)');

    //on map resize
    $et_map.resize(function(){
      map_width =  $(this).css('width');
      $('div.cf7-google-map-search', $map_container).css('width','calc(' + map_width + ' - 10px)');
      google.maps.event.trigger(googleMap, 'resize');
    });
    //search button
    $('.cf7-google-map-search .dashicons-search', $map_container).on('click', function(){
      $(this).siblings('.cf7marker-address').show().val('').focus();
      $(this).hide();
      $(this).siblings('.dashicons-no-alt').show();
    });
    $('.cf7-google-map-search .dashicons-no-alt', $map_container).on('click', function(){
      $(this).closeCF7gmapSearchField();
    });
  }//end init_cf7_google_maps().

  //find the address from the marker position
  function parseGeolocationAddress(name, addressComponents) {
    let idx, jdx, lineObj,
      city="", state="", pin="", country="", line="",
      lineArr = ['','','',''],
      cityArr = ['','','','','',''],
      stateArr = ['','','','',''];
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
    return {
      'line': line,
      'city':city,
      'state':state,
      'pin':pin,
      'country':country
    };
  }
  $.fn.closeCF7gmapSearchField = function(){
    let $this = $(this);
    $this.siblings('.cf7marker-address').hide();
    $this.hide();
    $this.siblings('.dashicons-search').show();
  }

  /*@since 1.2.0 init map once Post My CF7 $form has loaded.*/
  if(! cf7GoogleMap.plane_mode){
    $map_forms.filter('.cf7_2_post form.wpcf7-form').each(function(){
      let $form=$(this),
        id = $form.closest('.cf7_2_post').attr('id');
      $form.on(id, function(e){
        $('.cf7-google-map-container', $form).initCF7googleMap();
      });
    });
    /** @since 1.7.3 Smart grid new tab/row field init. */
    $map_forms.on('sgTabAdded sgRowAdded', function(e){
      let $newElm = $(e.target);
      if('sgRowAdded'==e.type) $newElm = $('.row.cf7-sg-table[data-row='+e.row+']',$newElm);
      $('.cf7-google-map-container', $newElm).initCF7googleMap();
    });
    $(document).ready( function(){
      $map_forms.not('.cf7_2_post form.wpcf7-form').each(function(){
        let $form = $(this);
        $('.cf7-google-map-container', $form).initCF7googleMap();
      })
    }) //end document ready.
  }else{
    $et_map.append('<p style="text-align: center;padding: 93px 0;border: solid 1px;"><em>airplane mode</em></p>');
  }

})(jQuery)
