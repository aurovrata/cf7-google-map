(function($){
	$(document).ready( function(){
    $('a[href*="tag-generator-panel-map"].button').on('click',function(){
      $('body').arrive('#TB_window', function(){

  		var $et_admin_map = $( '#cf7_admin_map' ),
  			marker_lat, marker_lng, map_zoom, googleMap, googleMarker;

  		marker_lat = $( '#cf7_listing_lat' ).val();
  		marker_lng = $( '#cf7_listing_lng' ).val();

  		if ( marker_lat == '' ) marker_lat = cf7_map_admin_settings.marker_lat;
  		if ( marker_lng == '' ) marker_lng = cf7_map_admin_settings.marker_lng;
      map_zoom = parseInt(cf7_map_admin_settings.map_zoom);

  		$et_admin_map.gmap3({
        center : [marker_lat, marker_lng],
		    zoom: map_zoom,
		    mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: true,
        navigationControl: true,
        scrollwheel: true,
        streetViewControl: true
      }).on('zoom_changed', function(map, e) {
        $( '#cf7_zoom' ).val( map.getZoom() );
        updateTag();
      }).on('center_changed', function(map, e) {
        $( '#cf7_centre_lat' ).val( map.getCenter().lat() );
        $( '#cf7_centre_lng' ).val( map.getCenter().lng() );
        updateTag();
      }).marker({
  				position : [marker_lat, marker_lng],
  				icon : cf7_map_admin_settings.theme_dir + "/assets/red-marker.png",
          draggable : true
      }).on('dragend', function(marker, e){
  				$( '#cf7_listing_lat' ).val( marker.getPosition().lat() );
  				$( '#cf7_listing_lng' ).val( marker.getPosition().lng() );
          updateTag();
  		}).then(function(result){
        googleMap = this.get(0);
        googleMarker = this.get(1);
      });
      //popuate cf7 tag
      $('#googleMap-tag-generator div.listings input').on('change',function(){
        var lat = $('input[name="cf7_listing_lat"]', $('form') ).val();
        var lng = $('input[name="cf7_listing_lng"]', $('form') ).val();
        googleMarker.setPosition( new google.maps.LatLng( lat, lng ) );
        googleMap.panTo( new google.maps.LatLng( lat, lng ) );
      });
      function updateTag(){
          var required = $('input[name="required"]', $('form')).is(':checked');
          var name = $('#tag-generator-panel-map-name', $('form') ).val();
          var lat = $('input[name="cf7_listing_lat"]', $('form') ).val();
          var lng = $('input[name="cf7_listing_lng"]', $('form') ).val();
          var clat = $('input[name="cf7_centre_lat"]', $('form')).val();
          var clng = $('input[name="cf7_centre_lng"]', $('form')).val();
          var zoom = $('input[name="cf7_zoom"]', $('form')).val();
          var value = 'zoom:' + zoom + ';clat:' + clat + ';clng:' + clng+ ';lat:' + lat + ';lng:' + lng;;
          var tag = 'map';
          if(required){
            tag = 'map*';
          }
          $('.control-box.cf7-googleMap + .insert-box input[name="values"]', $('form')).val( value );
          $('.control-box.cf7-googleMap + .insert-box input.tag', $('form')).val('['+ tag +' ' + name + ' "' + value + '"]');
      }
      updateTag();
    });
  });

	} );
})(jQuery)
