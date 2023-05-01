<li class="cf7sg-tag-map">
  <a class="helper" data-cf72post="//NOTE: do not embeded in a document.ready function.
  const $map = $('#cf7-googlemap-your-location'); //jquery map element.
  const $container = $map.closest('.cf7-google-map-container');
  $container.on('init.cf7-google-map',function(e){
	let settings = e.settings; //map center, marker position, type and zoom.
	//$map is the DOM element on which the gmap3 object was initialised.
	//to draw a cirle using gmap3 library, for example...
	e.gm3.circle({
	  center: settings.center,
	  radius : 750,
	  fillColor : '#FFAF9F',
	  strokeColor : '#FF512F'
	});
	//google marker object is e.marker
	//google map object is e.gmap
  });" href="javascript:void(0);"><?php esc_html_e( 'Bind', 'cf7-grid-layout' ); ?></a> <?php esc_html_e( 'map initialisation event.', 'cf7-google-map' ); ?>
</li>
<li class="cf7sg-tag-map">
  <a class="helper" data-cf72post="const $map = $('#cf7-googlemap-{$field_name}'); //jquery map element.
  const $container = $map.closest('.cf7-google-map-container');
  $container.on('drag.cf7-google-map',function(e){
	let settings = e.settings; //map center, marker position, type and zoom.
	//$map is the DOM element on which the gmap3 object was initialised.
	//to draw a cirle usign the gmap3 library, for example...
	e.gm3.circle({
	  center: settings.marker,
	  radius : 750,
	  fillColor : '#FFAF9F',
	  strokeColor : '#FF512F'
	});
	//google marker object is e.marker
	//google map object is e.gmap, useful to bind google events.
  });" href="javascript:void(0);"><?php esc_html_e( 'Bind', 'cf7-grid-layout' ); ?></a> <?php esc_html_e( 'drag marker event.', 'cf7-google-map' ); ?>
</li>
<li class="map-show_address map-manual_address">
  <a class="helper" data-cf72post="const $map = $('#cf7-googlemap-{$field_name}'); //map DOM element as jQuery object.
  const $container = $map.closest('.cf7-google-map-container');
  $container.on('update.cf7-google-map',function(e){
	let address = e.address; //geocode address update, or manual update of address fields.
	//google marker object is e.marker
	//google map object is e.gmap, useful to bind google events.
	//e.gm3 contains the gmap3 library object, so to set a circle around the marker...
	e.gm3.circle({
	  center: [e.marker.position.lat(),e.marker.position.lng()],
	  radius : 750,
	  fillColor : '#b0b14eb1',
	  strokeColor : '#fff100ff'
	});
  });" href="javascript:void(0);"><?php esc_html_e( 'Bind', 'cf7-grid-layout' ); ?></a> <?php esc_html_e( 'address update event.', 'cf7-google-map' ); ?>
</li>
