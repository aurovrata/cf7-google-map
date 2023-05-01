(function($){
	$( document ).ready(
		function(){
			$( 'a[href*="tag-generator-panel-map"].button' ).on(
				'click',
				function(){
					$( 'body' ).arrive(
						'#TB_window',
						function(){
							var et_admin_map = $( '#cf7_admin_map' ), markerLatField, markerLngField, map_zoom, googleMap, googleMarker;
							var tagForm      = et_admin_map.closest( 'form.tag-generator-panel' );
							markerLatField   = $( '#cf7_listing_lat',tagForm );
							markerLngField   = $( '#cf7_listing_lng',tagForm );

							if ( markerLatField.val() == '' ) {
								markerLatField.val( cf7_map_admin_settings.marker_lat );
							}
							if ( markerLngField.val() == '' ) {
								markerLngField.val( cf7_map_admin_settings.marker_lng );
							}
							map_zoom = parseInt( cf7_map_admin_settings.map_zoom );
							if ( ! cf7_map_admin_settings.airplane) {
								et_admin_map.gmap3(
									{
										center : [markerLatField.val(), markerLngField.val()],
										zoom: map_zoom,
										mapTypeId: google.maps.MapTypeId.ROADMAP,
										mapTypeControl: true,
										navigationControl: true,
										scrollwheel: true,
										streetViewControl: true
									}
								).on(
									'zoom_changed',
									function(map, e) {
										$( '#cf7_zoom' ,tagForm ).val( map.getZoom() );
										$( '#cf7_centre_lat' ,tagForm ).val( map.getCenter().lat() );
										$( '#cf7_centre_lng' ,tagForm ).val( map.getCenter().lng() );
										updateTag();
									}
								).on(
									'center_changed',
									function(map, e) {
										$( '#cf7_centre_lat' ,tagForm ).val( map.getCenter().lat() );
										$( '#cf7_centre_lng' ,tagForm ).val( map.getCenter().lng() );
										updateTag();
									}
								).marker(
									{
										position : [cf7_map_admin_settings.marker_lat, cf7_map_admin_settings.marker_lng],
										icon : cf7_map_admin_settings.theme_dir + "/assets/red-marker.png",
										draggable : true
									}
								).on(
									'dragend',
									function(marker, e){
										markerLatField.val( marker.getPosition().lat() );
										markerLngField.val( marker.getPosition().lng() );
										updateTag();
									}
								).then(
									function(result){
										googleMap    = this.get( 0 );
										googleMarker = this.get( 1 );
									}
								);
							} else {
								 et_admin_map.append( '<p style="text-align: center;padding: 93px 0;border: solid 1px;"><em>airplane mode</em></p>' );
							}
							// popuate cf7 tag
							$( '#googleMap-tag-generator div.listings input' ).on(
								'change',
								function(){
									var lat = markerLatField.val();
									var lng = markerLngField.val();
									googleMarker.setPosition( new google.maps.LatLng( lat, lng ) );
									googleMap.panTo( new google.maps.LatLng( lat, lng ) );
								}
							);
							// on checkbox change, update.
							$( '#cf7-google-map-show-address' ).change( updateTag );
							$( '.cf7-gmap-address-fields input' ).change( updateTag );
							function updateTag(){
								 var required = $( 'input[name="required"]', tagForm ).is( ':checked' ),
								   name       = $( '#tag-generator-panel-map-name', tagForm ).val(),
								   id         = $( '#tag-generator-panel-map-id', tagForm ).val(),
								   classes    = $( '#tag-generator-panel-map-class', tagForm ).val(),
								   show       = '';
								if ( $( '#cf7-google-map-show-address' ).is( ':checked' ) ) {
									show = ' ' + $( '.cf7-gmap-address-fields input:checked' ).val() + '_address';
								}

								var lat = markerLatField.val(),
								lng     = markerLngField.val(),
								clat    = $( 'input[name="cf7_centre_lat"]', tagForm ).val(),
								clng    = $( 'input[name="cf7_centre_lng"]', tagForm ).val(),
								zoom    = $( 'input[name="cf7_zoom"]', tagForm ).val(),
								value   = 'zoom:' + zoom + ';clat:' + clat + ';clng:' + clng + ';lat:' + lat + ';lng:' + lng,
								tag     = 'map';
								if (required) {
									 tag = 'map*';
								}
								if ('' != id) {
									id = " id:" + id + " ";
								}
								if ('' != classes) {
									  var classArr = classes.split( ',' );
									  var idx;
									  classes = '';
									for (idx = 0; idx < classArr.length; idx++) {
										classes += " class:" + classArr[idx].trim() + " ";
									}
								}
								$( '.control-box.cf7-googleMap + .insert-box input[name="values"]', tagForm ).val( value );
								$( '.control-box.cf7-googleMap + .insert-box input.tag', tagForm ).val( '[' + tag + ' ' + name + id + classes + show + ' "' + value + '"]' );
							}
							updateTag();
						}
					);
				}
			);

		}
	);
})( jQuery )
