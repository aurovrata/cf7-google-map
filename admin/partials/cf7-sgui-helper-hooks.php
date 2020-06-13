<li class="cf7sg-tag-map">
  <a class="helper" data-cf72post="add_filter( 'cf7_google_map_default_type','{$field_name_slug}_change_map_type',10,2);
/**
* Filter map types.
* @param string $type type must be either ROADMAP/SATELLITE/TERRAIN/HYBRID.
* @param string $field the field name being populated.
*/
function {$field_name_slug}_change_map_type($type, $field){
  //type must be either ROADMAP/SATELLITE/TERRAIN/HYBRID.
  if( '{$field_name}' !== $field) $type = 'SATELLITE';
  return $type;
}" href="javascript:void(0);"><?=__('Filter','cf7-grid-layout')?></a> <?=__('map type.','cf7-google-map')?>
</li>
<li class="cf7sg-tag-map">
  <a class="helper" data-cf72post="add_filter( 'cf7_google_map_settings','{$field_name_slug}_map_settings',10,2);
/**
* Filter map settings and controls.
* @param array $settings an array of settings.
* @param string $field the field name being populated.
*/
function {$field_name_slug}_map_settings($settings, $field){
  if( '{$field_name}' !== $field){
    $settings['mapTypeControl']= false; //hide (true by default).
    $settings['navigationControl']= false; //hide (true by default).
    $settings['streetViewControl']= false; //hide (true by default).
    $settings['zoomControl']=false; //hide (false by default).
    $settings['rotateControl']=true; //show (false by default).
    $settings['fullscreenControl']=true; //show (false by default).
    $settings['rotateControl']= true; //show (false by default).
    $settings['zoom']= 12; //set by default to the value initialised at the time of creating the form tag.
    $settings['center'] = array('11.936825', '79.834278'); //set by default to the value initialised at the time of creating the form tag.
  }
  return $settings;
}" href="javascript:void(0);"><?=__('Filter','cf7-grid-layout')?></a> <?=__('map settings.','cf7-google-map')?>
</li>
<li class="cf7sg-tag-map">
  <a class="helper" data-cf72post="add_filter( 'cf7_google_map_marker_settings','{$field_name_slug}_marker_settings',10,2);
/**
* Filter marker settings.
* @param array $settings an array of settings.
* @param string $field the field name being populated.
*/
function {$field_name_slug}_map_settings($settings, $field){
  if( '{$field_name}' !== $field){
    $settings['icon'] = ... //set your image url here.
    $settings['draggable'] = false; //true by default.
    $settings['position'] = array('11.936825', '79.834278'); //set by default to the value initialised at the time of creating the form tag.
  }
  return $settings;
}" href="javascript:void(0);"><?=__('Filter','cf7-grid-layout')?></a> <?=__('marker settings.','cf7-google-map')?>
</li>
<li class="map-show_address">
  <a class="helper" data-cf72post="add_filter( 'cf7_google_map_address_field_label','{$field_name_slug}_address_labels',10,3);
/**
* Filter address field labels.
* @param string $label a field label.
* @param string $type a field type id.
* @param string $field the field name being populated.
*/
function {$field_name_slug}_address_labels($label, $type, $field){
  if( '{$field_name}' !== $field){
    switch($type){
      case 'adresse':
        $label = 'Address'; //change the address field label.
        break;
      case 'ville':
        $label = 'City'; //change the city field label.
        break;
      case 'code':
        $label = 'Pincode &amp; State'; //change the pin code/state field label.
        break;
      case 'pays':
        $label = 'Country'; //change the country field label.
        break;
    }
  }
  return $label;
}" href="javascript:void(0);"><?=__('Filter','cf7-grid-layout')?></a> <?=__('address field labels.','cf7-google-map')?>
</li>
