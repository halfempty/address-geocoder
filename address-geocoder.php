<?php
/*
Plugin Name: Address Geocoder
Plugin URI: http://martyspellerberg.com/address-geocode-wordpress-plugin/
Description: A simple plugin for saving location data with posts. Conveniently converts addresses to lat/lng from the Post/Page Edit screen.
Version: 0.6
Contributors: martyspellerberg, mgibbs189
Author: Marty Spellerberg
Author URI: http://martyspellerberg.com
License: GPLv2+
*/

class Address_Geocoder
{

    public $valid_post_types = array();
    public $options;


    function __construct() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }


    /**
     * Initialize
     */
    function admin_init() {

        // Class properties
        $post_types = get_post_types();
        $excluded = array( 'attachment', 'revision', 'nav_menu_item' );
        $this->valid_post_types = array_diff( $post_types, $excluded );
        $this->options = get_option( 'address_geocoder_options' );

        // Actions
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

        // Settings
        register_setting( 'address_geocoder_options', 'address_geocoder_options', array( $this, 'validate_options' ) );
    }


    /**
     * Register the menu item under "Settings"
     */
    function admin_menu() {
        add_options_page( 'Address Geocoder', 'Address Geocoder', 'manage_options', 'address-geocoder-options', array( $this, 'options_page' ) );
    }


    /**
     * Enqueue admin scripts
     */
    function admin_enqueue_scripts() {
        wp_register_script( 'googlemaps', 'http://maps.googleapis.com/maps/api/js?key=AIzaSyBUUGSskO3GEjKLHjT4EIV-H2_Xs3MfPiA&sensor=false' );
        wp_register_script( 'marty_geocode_js', plugins_url('/address-geocoder.js', __FILE__) );

        wp_enqueue_script( 'googlemaps' );
        wp_enqueue_script( 'marty_geocode_js' );
    }


    /**
     * Add the "Geocoder" meta box to the appropriate pages
     */
    function add_meta_boxes( $post_type ) {
        if ( in_array( $post_type, $this->valid_post_types ) && 'exclude' != $this->options[ $post_type ] ) {
            add_meta_box( 'martygeocoder', 'Geocoder', array( $this, 'meta_box_html' ), $post_type, 'normal', 'high' );
        }
    }


    /**
     * Generate the meta box HTML
     */
    function meta_box_html( $object, $box ) {

        wp_nonce_field( 'save_latlng', 'geocoder_nonce' );
?>
        <div style="overflow:hidden; width:100%">
            <div id="geocodepreview" style="float:right; width:240px; height:180px; border:1px solid #DFDFDF"></div>
            <div style="margin-right:215px">
                <p>
                    <label for="martygeocoderaddress">Address</label><br />
                    <input style="width:300px" type="text" name="martygeocoderaddress" id="martygeocoderaddress" value="<?php echo esc_attr( get_post_meta( $object->ID, 'martygeocoderaddress', true ) ); ?>" />
                </p>
                <p>
                    <label for="martygeocoderlatlng">Lat/Lng</label><br />
                    <input style="width:300px" type="text" name="martygeocoderlatlng" id="martygeocoderlatlng" value="<?php echo esc_attr( get_post_meta( $object->ID, 'martygeocoderlatlng', true ) ); ?>" />
                </p>
                <p>
                    <a id="geocode" class="button">Geocode Address</a>
                </p>
            </div>
        </div>
<?php
    }


    /**
     * Generate the options page HTML
     */
    function options_page() {

        if ( ! isset( $_REQUEST['settings-updated'] ) ) {
            $_REQUEST['settings-updated'] = false;
        }

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
?>

<div class="wrap">    
    <h2><?php _e( 'Address Geocoder' ); ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields( 'address_geocoder_options' ); ?>
        <?php $options = get_option( 'address_geocoder_options' ); ?>
        <h3>Show Metabox on Post Types</h3>

        <?php foreach ( $this->valid_post_types as $post_type ) : ?>
        <?php $checked = ( 'exclude' != $options[ $post_type ] ) ? ' checked="checked"' : ''; ?>
        <p>
            <input type="checkbox" id="geocoder-type-<?php echo $post_type; ?>" name="address_geocoder_options[<?php echo $post_type ?>]" value="enabled" <?php echo $checked; ?> />
            <label class="description" for="geocoder-type-<?php echo $post_type; ?>"><?php echo $post_type; ?></label> 
        </p>
        <?php endforeach; ?>

        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
        </p>
    </form>
</div>

<?php
    }


    /**
     * Validate the options
     */
    function validate_options( $input ) {
        foreach ( $this->valid_post_types as $post_type ) {
            if ( !isset( $input[ $post_type ] ) ) {
                $input[ $post_type ] = 'exclude';
            }
        }
        return $input;
    }


    /**
     * Attach geocode data to posts
     */
    function save_post( $post_id, $post ) {

        // Skip when nonce isn't present
        if ( !isset( $_POST['geocoder_nonce'] ) || !wp_verify_nonce( $_POST['geocoder_nonce'], 'save_latlng' ) ) {
            return $post_id;
        }

        // Ensure proper access rights
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        // Save address
        $address_key = 'martygeocoderaddress';
        $address = isset( $_POST[ $address_key ] ) ? sanitize_text_field( $_POST[ $address_key ] ) : '';
        if ( empty( $address ) ) {
            delete_post_meta( $post_id, $address_key );
        }
        else {
            update_post_meta( $post_id, $address_key, $address );
        }

        // Save lat/lng
        $latlng_key = 'martygeocoderlatlng';
        $latlng = isset( $_POST[ $latlng_key ] ) ? sanitize_text_field( $_POST[ $latlng_key ] ) : '';
        if ( empty( $latlng ) ) {
            delete_post_meta( $post_id, $latlng_key );
        }
        else {
            update_post_meta( $post_id, $latlng_key, $latlng );
        }
    }
}


$address_geocoder = new Address_Geocoder();


// Backwards compatibility
function get_geocode_latlng( $post_id ) {
    return get_post_meta( $post_id, 'martygeocoderlatlng', true );
}

function get_geocode_lat( $post_id ) {
    $latlng = get_post_meta( $post_id, 'martygeocoderlatlng', true );
    $latlng = explode( ',', $latlng );
    return substr( $latlng[0], 1 );
}

function get_geocode_lng( $post_id ) {
    $latlng = get_post_meta( $post_id, 'martygeocoderlatlng', true );
    $latlng = explode( ',', $latlng );
    return substr( $latlng[1], 0, -1 );
}

function get_geocode_address( $post_id ) {
    return get_post_meta( $post_id, 'martygeocoderaddress', true ); 
}
