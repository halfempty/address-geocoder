<?php 
// Options Screen

add_action( 'admin_init', 'address_geocoder_options_init' );
add_action( 'admin_menu', 'address_geocoder_menu' );

/**
 * Init plugin options to white list our options
 */
function address_geocoder_options_init(){
	register_setting( 'address_geocoder_options', 'address_geocoder_options', 'address_geocoder_validate' );
}

/**
 * Load up the menu page
 */

function address_geocoder_menu() {
	add_options_page( 'Address Geocoder Options', 'Address Geocoder', 'manage_options', 'address-geocoder-options', 'address_geocoder_options' );
}


function address_geocoder_options() {

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} ?>
	
<div class="wrap">	

<?php screen_icon();?>

<h2>Address Geocoder Options</h2>


		<form method="post" action="options.php">
			<?php settings_fields( 'address_geocoder_options' ); ?>
			<?php $options = get_option( 'address_geocoder_options' ); ?>

<h3>Show Metabox on Post Types</h3>

<?php

$types = get_post_types('','names'); 

//print_r($options);

$alwaysexclude = array('attachment','revision','nav_menu_item');

foreach ( $types as $key => $value) :
	if ( !in_array( $key, $alwaysexclude ) ) : ?>


		<p><input id="address_geocoder_options[<?php echo $key; ?>]" name="address_geocoder_options[<?php echo $key; ?>]" type="checkbox" value="enabled" <?php
					if( $options[$key] != 'exclude' ) echo 'checked="checked"';	
		?> />
		<label class="description" for="address_geocoder_options[<?php echo $key; ?>]"> <?php echo $value; ?></label></p>


<?php 
	endif;
endforeach;

?>

<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'sampletheme' ); ?>" />
</p>


	</form>
	</div>

<?php }


/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function address_geocoder_validate( $input ) {

	$types = get_post_types('','names'); 

	$alwaysexclude = array('attachment','revision','nav_menu_item');

	foreach ( $types as $key => $value) :
		if ( !in_array( $key, $alwaysexclude ) ) : 
			
			if ( ! isset( $input[$key] ) ) $input[$key] = "exclude";

		endif;
	endforeach;

	return $input;

}

?>