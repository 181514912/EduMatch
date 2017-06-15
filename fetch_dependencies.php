<?php

include_once plugin_dir_path( __FILE__ ) . "backend_dashboard/plugin_hooks.php";
include_once plugin_dir_path( __FILE__ ) . "view.php";
include_once plugin_dir_path( __FILE__ ) . "edit.php";
include_once plugin_dir_path( __FILE__ ) . "delete.php";
include_once plugin_dir_path( __FILE__ ) . "otp.php";
include_once plugin_dir_path( __FILE__ ) . "subscriber_list.php";
include_once plugin_dir_path( __FILE__ ) . "educash_allotment_and_history.php";
include_once plugin_dir_path( __FILE__ ) . "backend_client_preferences.php";
include_once plugin_dir_path( __FILE__ ) . "backend_dashboard/calculate_subscribers_to_contact.php";
include_once plugin_dir_path( __FILE__ ) . "backend_dashboard/lead_capture_form/lead_capture_form.php";
include_once plugin_dir_path( __FILE__ ) . "backend_dashboard/sent_leads.php";
include_once plugin_dir_path( __FILE__ ) . 'frontend/class-Lead-Card.php'; /*Cards used for displaying leads */
include_once plugin_dir_path( __FILE__ ) . 'frontend/class-Custom-Lead-API.php'; /*API to be used for displaying leads */
include_once plugin_dir_path( __FILE__ ) . 'frontend/class-EduCash-Helper.php'; /*Utility class used for dealing with EduCash */
include_once plugin_dir_path( __FILE__ ) . 'frontend/class-EduLead-Helper.php'; /*Utility class used for managing Leads */
include_once plugin_dir_path( __FILE__ ) . 'database/class-DataBase-Helper.php'; /*Utility class used for dealing with Database */
include_once plugin_dir_path( __FILE__ ) . 'database/class-ClientEmailPref-Helper.php'; /*Utility class used for dealing with Client preferences */
include_once plugin_dir_path( __FILE__ ) . 'database/class-UserMeta-Helper.php'; /*Utility class used for dealing with Meta information */
include_once plugin_dir_path( __FILE__ ) . "send_email_to_client.php";

include_once plugin_dir_path( __FILE__ ) . "cross_selling/advertisement_template.php";

include_once plugin_dir_path( __FILE__ ) . "manage_leads.php";
require_once plugin_dir_path( __FILE__ ) . 'frontend/taxonomies/class-edugorilla-taxonomy-edu-categories.php';


function script() {
	wp_enqueue_style( 'select2-css', plugins_url( '/libs/select2/select2.css', __FILE__ ) );
	wp_enqueue_style( 'modal-css', plugins_url( '/css/jquery.modal.css', __FILE__ ) );
	wp_enqueue_style( 'jquery-ui-styles', "//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" );

	wp_enqueue_script(
		'select2-script',                         // Handle
		plugins_url( '/libs/select2/select2.js', __FILE__ ),  // Path to file
		array( 'jquery' )                             // Dependancies
	);

	wp_enqueue_script(
		'modal-script',                         // Handle
		plugins_url( '/js/jquery.modal.js', __FILE__ ),  // Path to file
		array( 'jquery' )                             // Dependancies
	);
	wp_enqueue_script(
		'script',                         // Handle
		plugins_url( '/js/backend_clientlib.js', __FILE__ ),  // Path to file
		array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-tabs' ) // Dependancies
	);

}

add_action( 'admin_enqueue_scripts', 'script', 2000 );

function edugorilla_html_mail_content_type() {
	return 'text/html';
}


include_once plugin_dir_path( __FILE__ ) . "backend_dashboard/lead_templates/email_setting.php";
include_once plugin_dir_path( __FILE__ ) . "backend_dashboard/lead_templates/sms_setting.php";
include_once plugin_dir_path( __FILE__ ) . "inc/shortcode_transaction_history.php";
include_once plugin_dir_path( __FILE__ ) . "inc/shortcode_educash_payment.php";

function edugorilla_shortcode_require() {
	wp_enqueue_script( 'ajaxlib2', 'https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js' );
	wp_enqueue_script( 'bootjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js' );
	wp_enqueue_script( 'angularJs', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.js' );
	wp_enqueue_script( 'angularAnimate', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-animate.js' );
	wp_enqueue_script( 'domURL', plugins_url( '/libs/domurl/domurl.js', __FILE__ ) );

	wp_enqueue_style( 'custom_css', plugins_url( '/frontend/css/edu-match-frontend.css', __FILE__ ), array(), rand( 111, 9999 ), 'all' );
	wp_enqueue_style( 'custom_css', plugins_url( '/frontend/css/lead-portal-animations.css', __FILE__ ), array(), rand( 111, 9999 ), 'all' );
	wp_enqueue_style( 'custom_css', plugins_url( '/backend_dashboard/lead_capture_form/lead_capture_style.css', __FILE__ ), array(), rand( 111, 9999 ), 'all' );

	wp_enqueue_script(
		'angular-leads-script',                         // Handle
		plugins_url( '/frontend/js/lead-portal.js', __FILE__ ),  // Path to file
		array( 'angularJs' )                             // Dependancies
	);
}

if ( ! function_exists( 'write_log' ) ) {
	function write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}


add_action( 'wp_enqueue_scripts', 'edugorilla_shortcode_require' );
?>
