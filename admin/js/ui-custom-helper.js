/*
* Template file from CF7 Smart Grid plugin
* cf7-grid-layout/admin/js/ui-custom-helper.js
*/
var cf7sgCustomHelperModule = (function (cch) {
	// add class for show_address attribute in the shortcode.
	// [map your-location show_address "lat:12.45;lng:80.09"]
	cch.map = function(shortcode){
		const regex        = /\[(map|map\*)\s(.[^\s\"\'\]]*)(?:\s(.[^\]]*))\]/img;
		let match, helpers = {'js':[],'php':[]}, showAddress = false;
		while ((match = regex.exec( shortcode )) !== null) {
			if (match.indexOf( 'show_address' ) > -1) {
				helpers.php[helpers.php.length] = 'map-show_address';
				helpers.js[helpers.js.length]   = 'map-show_address';
				showAddress                     = true;
			}
		}
		if ( ! showAddress && cf7sgHelper.geocode) {
			helpers.js[helpers.js.length] = 'map-manual_address';
		}
		return helpers;
	}
	return cch;
}(cf7sgCustomHelperModule || {}));
