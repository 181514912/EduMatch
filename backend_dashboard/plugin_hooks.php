<?php

function create_edugorilla_lead_table() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name1     = $wpdb->prefix . 'edugorilla_lead_details'; //The table containing all the leads.
	$sql1            = "CREATE TABLE $table_name1 (
                                            id int(11) NOT NULL AUTO_INCREMENT,
                                            admin_id int(11)  DEFAULT 0 NOT NULL,
                                            name varchar(200) NOT NULL,
                                            contact_no varchar(50) NOT NULL,
                                            email varchar(200) NOT NULL,
                                            query text(500) NOT NULL,
                                            is_promotional text(500) NOT NULL,
                                            listing_type text(500) NOT NULL,
                                            category_id text(500) NOT NULL,
                                            keyword text(500) NOT NULL,
                                            location_id varchar(200) NOT NULL,
                                            date_time varchar(200) NOT NULL,
                                            PRIMARY KEY id (id)
                                        ) $charset_collate;";


	$table_name2 = $wpdb->prefix . 'edugorilla_lead_contact_log'; //Logs when the subscribers were contacted.
	$sql2        = "CREATE TABLE $table_name2 (
                                            id int(11) NOT NULL AUTO_INCREMENT,
                                            contact_log_id int(11) NOT NULL,
                                            post_id int(11) NOT NULL,
                                            email_status text NOT NULL,
                                            sms_status text NOT NULL,
                                            date_time varchar(200) NOT NULL,
                                            PRIMARY KEY id (id)
                                        ) $charset_collate;";

	$cross_sell_log_table = $wpdb->prefix . 'edugorilla_crosssell_contact_log'; //Logs when the leads were contacted.
	$sql3                 = "CREATE TABLE $cross_sell_log_table (
                                            id int(11) NOT NULL AUTO_INCREMENT,
                                            lead_id int(11)  DEFAULT 0 NOT NULL,
                                            email_status text NOT NULL,
                                            sms_status text NOT NULL,
                                            date_time varchar(200) NOT NULL,
                                            PRIMARY KEY id (id)
                                        ) $charset_collate;";


	$table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions'; //Transaction history for EduCash.
	$sql4        = "CREATE TABLE $table_name3 (
                                            id mediumint(9) NOT NULL AUTO_INCREMENT,
                                            admin_id int(9) NOT NULL,
                                            client_id int(9) NOT NULL,
                                            lead_id DEFAULT 0 int(15) NOT NULL,
                                            transaction int(9) DEFAULT 0 NOT NULL,
                                            amount int(9) DEFAULT 0 NOT NULL,
                                            time datetime NOT NULL,
                                            comments varchar(500) DEFAULT 'No comment' NOT NULL,
                                            PRIMARY KEY  (id)
                                        ) $charset_collate;";

	$table_name4 = $wpdb->prefix . 'edugorilla_lead_client_mapping'; //Mapping between client id and lead id
	$sql5        = "CREATE TABLE $table_name4 (
				                            id int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											client_id int(15) NOT NULL,
											lead_id DEFAULT 0 int(15) NOT NULL,
											is_unlocked boolean DEFAULT 0 NOT NULL,
                                            is_hidden boolean DEFAULT 0 NOT NULL,
						                    date_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
									        operation SMALLINT(1) NOT NULL DEFAULT '1'
				  					    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	//Creating a table in current wordpress
	dbDelta( $sql1 );
	dbDelta( $sql2 );
	dbDelta( $sql3 );
	dbDelta( $sql4 );
	dbDelta( $sql5 );

}

function update_edugorilla_database_schema() {
	global $wpdb;
//	$lead_details_table        = $wpdb->prefix . 'edugorilla_lead_details';
//	$lead_table_add_column_sql = "ALTER TABLE $lead_details_table ADD admin_id INT(11) NOT NULL DEFAULT '0' AFTER id";
//	$leadDetailsAdmin          = $wpdb->get_row( "SELECT * FROM $lead_details_table" );
	//Add column if not present.
//	if ( ! isset( $leadDetailsAdmin->admin_id ) ) {
//		$wpdb->query( $lead_table_add_column_sql );
//		echo "Ran the SQL command : $lead_table_add_column_sql";
//	}

	//Change default value of a column.
	$client_pref_table              = $wpdb->prefix . 'edugorilla_client_preferences';
	$unlock_lead_default_column_sql = "ALTER TABLE $client_pref_table ADD DEFAULT 1 FOR unlock_lead";
	$wpdb->query( $unlock_lead_default_column_sql );

//	$table_name2 = $wpdb->prefix . 'edugorilla_lead_contact_log';
//	$wpdb->query( "DROP TABLE IF EXISTS $table_name2" );

	create_edugorilla_lead_table();
}

//Add custom field to users profile
add_action( 'show_user_profile', 'users_extra_fields' );
add_action( 'edit_user_profile', 'users_extra_fields' );
function users_extra_fields() {
	$user_ID = $_REQUEST['user_id'];
	$com_add = get_user_meta( $user_ID, "user_general_company_name" );
	if ( is_array( $com_add ) ) {
		$comadd = $com_add[0];
	}

	?>
	<h3>Extra Fields</h3>
	<table class="form-table">
		<tr>
			<th><label for="user_general_company_name">Company Name</label></th>
			<td><input type="text" id="user_general_company_name"

			           name="user_general_company_name" value="<?php echo esc_attr( $comadd ); ?>"/><br/>
				<span class="description">Enter company name here.</span></td>
		</tr>
	</table>
	<?php

}

add_action( 'personal_options_update', 'users_save_fields' );
add_action( 'edit_user_profile_update', 'users_save_fields' );
function users_save_fields() {
	$user_ID = $_REQUEST['user_id'];
	if ( current_user_can( 'edit_user', $user_ID ) ) {
		update_user_meta( $user_ID, "user_general_company_name", $_POST['user_general_company_name'] );
	}
}

//custom code ends
//register_activation_hook( __FILE__, 'create_edugorilla_lead_table' );
register_activation_hook( __FILE__, 'update_edugorilla_database_schema' );

function edugorilla_lead_plugin_uninstall() {
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2 = $wpdb->prefix . 'edugorilla_lead_contact_log';
	$table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
	$table_name4 = $wpdb->prefix . 'edugorilla_lead_client_mapping';
	$table_name6 = $wpdb->prefix . 'edugorilla_client_preferences';
	$wpdb->query( "DROP TABLE IF EXISTS $table_name1" );
	$wpdb->query( "DROP TABLE IF EXISTS $table_name2" );
	$wpdb->query( "DROP TABLE IF EXISTS $table_name3" );
	$wpdb->query( "DROP TABLE IF EXISTS $table_name4" );
	$wpdb->query( "DROP TABLE IF EXISTS $table_name6" );
}//end pluginUninstall function

//hook into WordPress when its being deactivated, uncommenting the following line will cause data loss
//register_deactivation_hook(__FILE__, 'edugorilla_lead_plugin_uninstall');

add_action( "admin_menu", "create_edugorilla_menus" );

function create_edugorilla_menus() {
	global $name, $label;
	$taxonomies = get_taxonomies( array(), 'objects' );
	foreach ( $taxonomies as $taxonomy ) {
		if ( $taxonomy->name == 'edu_categories' ) {
			$name  = $taxonomy->name;
			$label = $taxonomy->label;
		}
	}
  	$icon_url = plugins_url( 'pdf_library/plugin_icon.png', dirname(__FILE__) );
	add_object_page(
		'EduMatchMainScreen',
		'EduMatch',
		'read',
		'edumatch-main-screen',
		'create_lead_capture_form',
		$icon_url
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Lead capture form',
		'Lead capture form',
		'read',
		'edumatch-main-screen',
		'create_lead_capture_form'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Leads',
		'Lead Logs',
		'read',
		'sent-lead-logs',
		'log_sent_leads'
	);
	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Subscriber Listing',
		'Subscribers',
		'read',
		'client-listing',
		'subscribers_list'
	);
	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | OTP',
		'OTP',
		'read',
		'edugorilla-verify-sms',
		'edugorilla_otp'
	);

	add_submenu_page(
		'',
		'EduMatch | Edit Lead',
		'Promotion Sent Edit',
		'read',
		'edugorilla-edit-lead',
		'edugorilla_lead_edit'
	);

	add_submenu_page(
		'',
		'EduMatch | Delete Lead',
		'View Lead',
		'read',
		'edugorilla-delete-lead',
		'edugorilla_lead_delete'
	);
	add_submenu_page(
		'',
		'EduMatch | Delete Client',
		'View Client',
		'read',
		'edugorilla-deactivate-client',
		'edugorilla_client_delete'
	);
	add_submenu_page(
		'',
		'EduMatch | Active Client',
		'View Client',
		'read',
		'edugorilla-activate-client',
		'edugorilla_client_active'
	);
	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Allocate EduCash',
		'Allocate EduCash',
		'read',
		'allocate_educash_form_page',
		'allocate_educash_form_page'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Transaction History',
		'Transaction History',
		'read',
		'transaction_history_form_page',
		'transaction_history_form_page'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Subscriber Preferences',
		'Subscriber Preferences',
		'read',
		'client_preferences_page',
		'client_preferences_page'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Conversion Tables',
		'Conversion Tables',
		'read',
		'conversion-tables',
		'conversion_tables'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | ' . $label,
		$label,
		'edit_posts',
		'edit-tags.php?taxonomy=' . $name,
		false
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Third Party Settings',
		'Settings',
		'manage_options',
		'edugorilla-settings',
		'edugorilla_settings'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Template of Email (Leads)',
		'Lead Email Templates',
		'read',
		'edugorilla-email-setting',
		'edugorilla_email_setting'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Template of SMS (Leads)',
		'Lead SMS Templates',
		'read',
		'edugorilla-sms-setting',
		'edugorilla_sms_setting'
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Advert Email',
		'Cross Sell Emails',
		'manage_options',
		'edit.php?post_type=cross_sell_email',
		false
	);

	add_submenu_page(
		'edumatch-main-screen',
		'EduMatch | Advert SMS',
		'Cross Sell SMS',
		'manage_options',
		'edit.php?post_type=cross_sell_sms',
		false
	);
}

?>
