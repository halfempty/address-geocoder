<?php
/*
Plugin Name: Address Geocoder
Plugin URI: http://martyspellerberg.com/address-geocode-wordpress-plugin/
Description: A simple plugin for saving location data with posts. Conveniently converts addresses to lat/lng from the Post/Page Edit screen.
Version: 0.1
Author: Marty Spellerberg
Author URI: http://martyspellerberg.com
License: GPLv2+
*/

add_action( 'admin_init', 'martygeocoder_admin_init' );

function martygeocoder_admin_init() {
	wp_register_script( 'googlemaps', 'http://maps.googleapis.com/maps/api/js?key=AIzaSyBUUGSskO3GEjKLHjT4EIV-H2_Xs3MfPiA&sensor=false' );
	wp_enqueue_script( 'googlemaps' );

	wp_register_script( 'marty_geocode_js', plugins_url('/address-geocoder.js', __FILE__) );
	wp_enqueue_script( 'marty_geocode_js' );

	foreach (array('post','page') as $type) {
		add_meta_box('martygeocoder', 'Geocoder', 'martygeocoder_setup', $type, 'normal', 'high');
	}
	
	add_action('save_post','martygeocoder_save');

}

function martygeocoder_setup() {
	global $post;
	$address = get_post_meta($post->ID,'martygeocoderaddress',TRUE);
	$latlng = get_post_meta($post->ID,'martygeocoderlatlng',TRUE); ?>

	<div style="overflow: hidden; width: 100%;">
	<div id="geocodepreview" style="float: right; width: 200px; height: 140px; border: 1px solid #DFDFDF;"></div>

	<div style="margin-right: 215px">
	<p><label for="martygeocoderaddress">Address</label><input type="text" class="widefat" name="martygeocoderaddress" value="<?php if(!empty($address)) echo $address; ?>"/></p>
	<p><label for="martygeocoderlatlng">Lat/Lng</label><input type="text" class="widefat" name="martygeocoderlatlng" value="<?php if(!empty($latlng)) echo $latlng; ?>"/></p>
	<p><a id="geocode" class="button">Geocode Address</a></p>


	</div>
	</div>
	<?php echo '<input type="hidden" name="martygeocoder_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function martygeocoder_save($post_id) {
		if (!wp_verify_nonce($_POST['martygeocoder_noncename'],__FILE__)) return $post_id;

		if ($_POST['post_type'] == 'page') {
			if (!current_user_can('edit_page', $post_id)) return $post_id;
		} else {
			if (!current_user_can('edit_post', $post_id)) return $post_id;
		}

		$current_address = get_post_meta($post_id, 'martygeocoderaddress', TRUE);	
		$new_address = $_POST['martygeocoderaddress'];
		martygeocoder_clean($new_address);

		if ($current_address) {
			if (is_null($new_address)) delete_post_meta($post_id,'martygeocoderaddress');
			else update_post_meta($post_id,'martygeocoderaddress',$new_address);
		} elseif (!is_null($new_address)) {
			add_post_meta($post_id,'martygeocoderaddress',$new_address,TRUE);
		}


		$current_latlng = get_post_meta($post_id, 'martygeocoderlatlng', TRUE);	
		$new_latlng = $_POST['martygeocoderlatlng'];
		martygeocoder_clean($new_latlng);

		if ($current_latlng) {
			if (is_null($new_latlng)) delete_post_meta($post_id,'martygeocoderlatlng');
			else update_post_meta($post_id,'martygeocoderlatlng',$new_latlng);
		} elseif (!is_null($new_latlng)) {
			add_post_meta($post_id,'martygeocoderlatlng',$new_latlng,TRUE);
		}

		return $post_id;
}


function martygeocoder_clean(&$arr) {
	if (is_array($arr)) {
		foreach ($arr as $i => $v) {
			if (is_array($arr[$i])) {
				martygeocoder_clean($arr[$i]);
				if (!count($arr[$i])) unset($arr[$i]);
			} else {
				if (trim($arr[$i]) == '') unset($arr[$i]);
			}
		}

		if (!count($arr)) $arr = NULL;
	}
}


// End dashboard

function get_geocode_latlng($postid) {
	$martygeocoder = get_post_meta($postid, 'martygeocoderlatlng', true);	
	return $martygeocoder;
}

function get_geocode_address($postid) {
	$martygeocoder = get_post_meta($postid, 'martygeocoderaddress', true);	
	return $martygeocoder;
}


 ?>