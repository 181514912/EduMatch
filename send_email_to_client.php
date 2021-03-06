<?php
$sms_template_datas =array();
function table_for_client()
{
	global $wpdb;
	$table_name6 = $wpdb->prefix.'edugorilla_client_preferences'; //client preferences
	$users_table = $wpdb->users;
	$sql6 = "CREATE TABLE $table_name6 (
				                            id bigint(20) unsigned NOT NULL,
											contact_no varchar(50) NOT NULL,
											preferences varchar(100) NOT NULL,
											location varchar(100) NOT NULL,
											category varchar(100) NOT NULL,
											unsubscribe_sms boolean DEFAULT 0,
											unsubscribe_email boolean DEFAULT 0,
											unlock_lead boolean DEFAULT 1,
											is_active boolean DEFAULT 1,
											related_leads boolean DEFAULT 0,
											PRIMARY KEY id (id),
											FOREIGN KEY (id) REFERENCES $users_table(id)
				  					    ) $charset_collate;";


	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//Creating a table in cureent wordpress
	dbDelta($sql6);

}

//end pluginUninstall function
//Populate the Client Data

function send_mail_with_unlock( $is_promotional_lead, $auto_unlock_lead, $subscription_applicable_emails, $subscription_applicable_phones, $subscription_user_name, $subscription_user_id, $my_lead )
{
	set_lead_data( $my_lead->getId(), $my_lead->getName(), $my_lead->getContactNo(), $my_lead->getCategoryList(), $my_lead->getEmail(), $my_lead->getLocationList(), $my_lead->getQuery() );
	$eduLeadHelper = new EduLead_Helper();
	if ( $auto_unlock_lead == 1 ) {
		$query_status = $eduLeadHelper->set_card_unlock_status_to_db( $subscription_user_id, $my_lead->getId(), 1 );
		//echo "Executed unlock command :$query_status";
	} else {
		$query_status = "Did not execute unlock command!";
		//echo $query_status;
	}
	if ( $is_promotional_lead == "yes" ) {
		$email_setting_options4 = get_option( 'edugorilla_email_setting1' );
		//echo "<br>email_setting_options4 : $email_setting_options4<br>";
		$edugorilla_email_body     = stripslashes( $email_setting_options4['body'] );
		$edugorilla_email_subject4 = stripslashes( $email_setting_options4['subject'] );
		$edugorilla_email_subject  = str_replace( "{category}", $my_lead->getCategoryList(), $edugorilla_email_subject4 );
		write_log( "Sending promotional email to client with subject :" . $edugorilla_email_subject );
		//echo "<br>Sending email to client with subject : $edugorilla_email_subject";

		$email_template_datas = array(
			"{Contact_Person}" => $subscription_user_name,
			"{category}"       => $my_lead->getCategoryList(),
			"{location}"       => $my_lead->getLocationList(),
			"{lead_id}"        => $my_lead->getId(),
			"{name}"           => $my_lead->getName(),
			"{contact no}"     => $my_lead->getContactNo(),
			"{email address}"  => $my_lead->getEmail(),
			"{query}"          => $my_lead->getQuery()
		);

		foreach ( $email_template_datas as $var => $email_template_data ) {
			$edugorilla_email_body = str_replace( $var, $email_template_data, $edugorilla_email_body );
		}
		$institute_emails_status = send_mail_without_unlock( $edugorilla_email_subject, $edugorilla_email_body, $subscription_applicable_emails, $subscription_applicable_phones, $subscription_user_name, $my_lead->getId(), "-1" );
	} else if ( str_starts_with( $query_status, "Success" ) ) {
		$email_setting_options4 = get_option( 'edugorilla_email_setting4' );
		//echo "<br>email_setting_options4 : $email_setting_options4<br>";
		$edugorilla_email_body     = stripslashes( $email_setting_options4['body'] );
		$edugorilla_email_subject4 = stripslashes( $email_setting_options4['subject'] );
		$edugorilla_email_subject  = str_replace( "{category}", $my_lead->getCategoryList(), $edugorilla_email_subject4 );
		write_log( "Sending unlocked email to client with subject :" . $edugorilla_email_subject );
		//echo "<br>Sending email to client with subject : $edugorilla_email_subject";

		$email_template_datas = array(
			"{Contact_Person}" => $subscription_user_name,
			"{category}"       => $my_lead->getCategoryList(),
			"{location}"       => $my_lead->getLocationList(),
			"{lead_id}"        => $my_lead->getId(),
			"{name}"           => $my_lead->getName(),
			"{contact no}"     => $my_lead->getContactNo(),
			"{email address}"  => $my_lead->getEmail(),
			"{query}"          => $my_lead->getQuery()
		);

		foreach ( $email_template_datas as $var => $email_template_data ) {
			$edugorilla_email_body = str_replace( $var, $email_template_data, $edugorilla_email_body );
		}
		$institute_emails_status = send_mail_without_unlock( $edugorilla_email_subject, $edugorilla_email_body, $subscription_applicable_emails, $subscription_applicable_phones, $subscription_user_name, $my_lead->getId(), "-1" );
	} else {
		$edugorilla_email         = get_option( 'edugorilla_email_setting_instant' );
		$edugorilla_email_body    = stripslashes( $edugorilla_email['body'] );
		$edugorilla_email_subject = str_replace( "{category}", $my_lead->getCategoryList(), $edugorilla_email['subject'] );
		$lead_unlock_URL          = $_SERVER['HTTP_HOST'] . "/manage_leads/#edugorilla_leads_sh";
		write_log( "Sending locked email to client with subject :" . $edugorilla_email_subject );

		$email_template_datas     = array(
			"{Contact_Person}"  => $subscription_user_name,
			"{lead_id}"         => $my_lead->getId(),
			"{category}"        => $my_lead->getCategoryList(),
			"{location}"        => $my_lead->getLocationList(),
			"{lead_unlock_URL}" => $lead_unlock_URL
		);

		foreach ( $email_template_datas as $var => $email_template_data ) {
			$edugorilla_email_body = str_replace( $var, $email_template_data, $edugorilla_email_body );
		}
		$institute_emails_status = send_mail_without_unlock( $edugorilla_email_subject, $edugorilla_email_body, $subscription_applicable_emails, $subscription_applicable_phones, $subscription_user_name, $my_lead->getId(), "-1" );
	}
	//echo "<h2>PHP is sending wp_mail json_encode($query_status)!</h2>";
	if ($institute_emails_status) {
		# code...
		//echo "Mail sent";
	}
	return $institute_emails_status;
}

function send_mail_without_unlock( $edugorilla_email_subject, $edugorilla_email_body, $institute_emails, $institute_phones, $contact_name, $post_id, $contact_log_id ) {
	global $wpdb;
	$institute_emails_status = [];
	$institute_sms_status    = [];

	$client_pref_database = new ClientEmailPref_Helper();
	$institute_emails     = $client_pref_database->removeUnsubscribedEmails( $institute_emails );
	$institute_phones     = $client_pref_database->removeUnsubscribedSMSs( $institute_phones );

	//echo "Sending mails to id : " . implode( ";", $institute_emails );
	foreach ( $institute_emails as $institute_email ) {
		add_filter( 'wp_mail_content_type', 'edugorilla_html_mail_content_type' );

		if ( ! empty( $institute_email ) ) {
			$headers                                     = array( 'Content-Type: text/html; charset=UTF-8' );
			//echo "<br>Sending mail to id : '$institute_email' with sub '$edugorilla_email_subject' and body '$edugorilla_email_body'<br>";
			$institute_emails_status[ $institute_email ] = wp_mail( $institute_email, $edugorilla_email_subject, $edugorilla_email_body, $headers );
		}

		remove_filter( 'wp_mail_content_type', 'edugorilla_html_mail_content_type' );

	}

	include_once plugin_dir_path( __FILE__ ) . "api/gupshup.api.php";
	foreach ( $institute_phones as $institute_phone ) {
		$sms_setting_options1 = get_option( 'edugorilla_sms_setting4' );
		$edugorilla_sms_body1 = stripslashes( $sms_setting_options1['body'] );

		$credentials                              = get_option( "ghupshup_credentials" );
		global $sms_template_datas;
			foreach ($sms_template_datas as $var => $sms_template_data) {
				$edugorilla_sms_body1 = str_replace($var, $sms_template_data, $edugorilla_sms_body1);
			}
		$msg                                      = $edugorilla_sms_body1;
		$institute_sms_status[ $institute_phone ] = send_sms( $credentials['user_id'], $credentials['password'], $institute_phone, $msg );
	}

	$result2 = $wpdb->update(
		$wpdb->prefix . 'edugorilla_lead_contact_log',
		array(
			'post_id'      => $post_id,
			'email_status' => json_encode( $institute_emails_status ),
			'sms_status'   => json_encode( $institute_sms_status ),
			'date_time'    => current_time( 'mysql' )
		),
		array( 'contact_log_id' => $contact_log_id, )

	);

	return $result2;
}

function set_lead_data( $leadId, $name, $contact_no, $category, $email, $location, $lead_query )
{
    global $sms_template_datas;
	$sms_template_datas = array(
		"{lead_id}"       => $leadId,
		"{name}"          => $name,
		"{contact no}"    => $contact_no,
		"{email address}" => $email,
		"{query}"         => $lead_query,
		"{category}"      => $category,
		"{location}"      => $location
	);
}

//function to send email to lead
function send_email_to_lead($email, $subject, $body){
	add_filter( 'wp_mail_content_type', 'edugorilla_html_mail_content_type' );
		
		if ( ! empty( $email ) ) {
			$headers                                     = array( 'Content-Type: text/html; charset=UTF-8' );
			$result = wp_mail( $email, $subject, $body, $headers );
		}
		remove_filter( 'wp_mail_content_type', 'edugorilla_html_mail_content_type' );
	return $result;
}
//function to send sms to lead
function send_sms_to_lead($contact_no, $msg, $sms_type){
	include_once plugin_dir_path( __FILE__ ) . "api/gupshup.api.php";
	if($sms_type == 'Transactional'){
		$credentials = get_option( "ghupshup_credentials" );
		$result = send_sms( $credentials['user_id'], $credentials['password'], $contact_no, $msg );
	}else{
		$credentials = get_option( "promotional_credentials" );
		$result = send_sms_promotional( $credentials['user_id'], $credentials['password'], $contact_no, $msg );
	}	
	return $result;
}

//function to send sms to lead
function contact_lead_for_cross_sell( $lead_id, $category_str, $location_id, $lead_email_id, $lead_phone_no ) {
	global $wpdb;
	$custom_post_type_for_email = 'crossSellEmails';
	$custom_post_type_for_sms   = 'crossSellSMSs';
	$results_email              = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_type = 'cross_sell_email' and post_status = 'publish'", $custom_post_type_for_email ), ARRAY_A );
	$results_sms                = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_type = 'cross_sell_sms' and post_status = 'publish'", $custom_post_type_for_sms ), ARRAY_A );
	if ( ! empty( $results_email ) ) {
		$cat_array = explode( ",", $category_str );
		#code...
		foreach ( $results_email as $index => $post ) {
			//$output .= '<option value="' . $post['ID'] . '">' . $post['post_title'] . '</option>';$category_str,$location_id
			$check_category = explode( ",", get_post_meta( $post['ID'], "categories", true ) );
			$check_location = get_post_meta( $post['ID'], "location", true );
			$cat_diff       = array_diff( $cat_array, $check_category );
			if ( empty( $cat_diff ) && ( $check_location === $location_id ) ) {
				$result3 = send_email_to_lead( $lead_email_id, $post['post_title'], $post['post_content'] );
				break;
			}
		}
	}
	if ( ! empty( $results_sms ) ) {
		$cat_array = explode( ",", $category_str );
		#code...
		foreach ( $results_sms as $index => $post ) {
			//$output .= '<option value="' . $post['ID'] . '">' . $post['post_title'] . '</option>';$category_str,$location_id
			$check_category = explode( ",", get_post_meta( $post['ID'], "categories", true ) );
			$check_location = get_post_meta( $post['ID'], "location", true );
			$sms_type		= get_post_meta( $post['ID'], "sms_type", true );
			$cat_diff       = array_diff( $cat_array, $check_category );
			if ( empty( $cat_diff ) && ( $check_location === $location_id ) ) {
				$result4 = send_sms_to_lead( $lead_phone_no, $post['post_content'], $sms_type );
				break;
			}
		}
	}

	$cross_sell_log_table = $wpdb->prefix . 'edugorilla_crosssell_contact_log';
	$wpdb->insert(
		$cross_sell_log_table,
		array(
			'lead_id'      => $lead_id,
			'email_status' => json_encode( $result3 ),
			'sms_status'   => json_encode( $result4 ),
			'date_time'    => current_time( 'mysql' )
		)
	);
}

function str_starts_with($haystack, $needle)
{
	if(!is_string($haystack)) {
		return false;
	}
	return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}

//function to display client preferences form
function get_category_current_user($user_id, $client_data)
{
		global $wpdb;
		$categories_list = get_terms('listing_categories', array('hide_empty' => false));
		if($client_data){
		$count = 1;
		$more_category = "";
		$cat_arr=explode(",",$client_data->category);
		foreach ($cat_arr as $client_) {
			if (strcmp($client_ , "-1") ==0) {
				# code...
				$category_name = "category".$count;
				$category_data = '<br name="br' . $category_name . '" /><input list="categories_list" name="' . $category_name . '" size="30" value="">';
				$removeButton  = '<input type = "button" name = "removeCategory' . $count . '" value = "  -  " onclick = "removeThisRow(' . $count . ')">';
				$more_category = $more_category . $category_data . $removeButton;
				$count         = $count+1;
				
			}else{
				
		foreach ($categories_list as $category_) {
			# code...
			if (preg_match('/'.$category_->term_id.'/', $client_)) {
				# code...
				$category_name = "category".$count;
				$category_data = '<br name="br' . $category_name . '" /><input list="categories_list" name="' . $category_name . '" size="30" value="' . $category_->name . '">';
				$removeButton  = '<input type = "button" name = "removeCategory' . $count . '" value = "  -  " onclick = "removeThisRow(' . $count . ')">';
				$more_category = $more_category . $category_data . $removeButton;
				$count         = $count+1;
				
			}
		}
		}}
}
$category_result = array();
array_push($category_result ,$more_category);
array_push($category_result, $count);
return $category_result;

}

function get_location_current_user($user_id , $client_data)
{
		global $wpdb;
		$location_list = get_terms('locations', array('hide_empty' => false));;
		if($client_data){
		$count2 = 1;
			$more_location = "";
			$loc_arr=explode(",",$client_data->location);
		foreach ($loc_arr as $client_) {
			if (strcmp($client_ , "-1") ==0) {
				# code...
				$location_name = "location" . $count2;
				$more_location = $more_location . '<br name="br' . $location_name . '"/><input list="location_list" name="' . $location_name . '" size="30" value="">';
				$count2        = $count2+1;
				
			}else{
		foreach ($location_list as $location_) {
			# code...
			if (preg_match('/'.$location_->term_id.'/', $client_)) {
				# code...
				$location_name = "location" . $count2;
				$more_location = $more_location . '<br name="br' . $location_name . '"/><input list="location_list" name="' . $location_name . '" size="30" value="' . $location_->name . '">';
				$count2        = $count2+1;
				
			}
		}
		}}

}
$location_result = array();
array_push($location_result ,$more_location);
array_push($location_result, $count2);
return $location_result;
}

function edugorilla_client(){
		$categories_list = get_terms('listing_categories', array('hide_empty' => false));
		$location_list = get_terms('locations', array('hide_empty' => false));
		$user_id = get_current_user_id();
		$category_count_value =1;
		$location_count_value =1;
		$notification = "";
		$in_val = "";
		$dd_val = "";
		$wd_val = "";
		$md_val = "";
		$unsub_email_val = "";
		$unsub_sms_val = "";
		$unlock_val = "";
		global $wpdb;
		$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
		$current_user_data = $wpdb->get_row("SELECT * FROM $table_name  WHERE id = $user_id");
			$notificationString = $current_user_data->preferences;
			//$notificationArray = explode(",", $notificationString);
				if(preg_match('/Instant_Notifications/',$notificationString))
					$in_val = "checked";
				if(preg_match('/Daily_Digest/',$notificationString))
					$dd_val = "checked";
				if(preg_match('/Weekly_Digest/',$notificationString))
					$wd_val = "checked";
				if(preg_match('/Monthly_Digest/',$notificationString))
					$md_val = "checked";
				
        if($current_user_data->unlock_lead == 1)
		    $unlock_val = "checked"; 
        if($current_user_data->unsubscribe_email == 1)
		    $unsub_email_val = "checked";
        if($current_user_data->unsubscribe_sms == 1)
		    $unsub_sms_val = "checked";
                		
		$category_result = get_category_current_user($user_id , $current_user_data);
		$more_category = $category_result[0];
		$category_count_value = $category_result[1];
		$location_result = get_location_current_user($user_id , $current_user_data);
		$more_location = $location_result[0];
		$location_count_value = $location_result[1];
    
	if (isset($_POST['submit_client_pref'])) {
		$unlock_lead_ = $_POST['unlock_lead'];
		$notification_all = $_POST['notification'];
		if (!empty($notification_all)) {
			# code...
			$notification = "";
			foreach ($notification_all as $value) {
				# code...
				$notification = $value . ", " . $notification;
				if($value == "Instant_Notifications")
					$in_val = "checked";
				else if($value == "Daily_Digest")
					$dd_val = "checked";
				else if($value == "Weekly_Digest")
					$wd_val = "checked";
				else if($value == "Monthly_Digest")
					$md_val = "checked";
			}
		}
		$category_count = $_POST['category_count'];
		$location_count = $_POST['location_count'];
		$category_count_value = $category_count;
		$location_count_value = $location_count;
		$category = array();
		$location = array();
		$more_category = "";
		$more_location = "";
		for ($i = 0; $i < $category_count; $i++) {
			# code...
			$category_name = "category".$i;
			if ($i>0) {
				$categoryRowValue = '<br name="br' . $category_name . '" /><input list="categories_list" name="' . $category_name . '" size="30" value="' . $_POST[ $category_name ] . '">';
				$removeButton     = '<input type = "button" name = "removeCategory' . $i . '" value = "  -  " onclick = "removeThisRow(' . $i . ')">';
				$more_category    = $more_category . $categoryRowValue . $removeButton;
			}else
				$category_select_val = $_POST[$category_name];
			array_push($category, $_POST[$category_name]);
		}
		
		for ($i = 0; $i < $location_count; $i++) {
			# code...
			$location_name = "location".$i;
			if ($i>0) {
				# code...
				$more_location = $more_location . '<br name="br' . $location_name . '"/><input list="location_list" name="' . $location_name . '" size="30" value="' . $_POST[ $location_name ] . '">';
			}else
			$location_select_val = $_POST[$location_name];
			array_push($location, $_POST[$location_name]);
		}
		foreach ($category as $category_value) {
			$category_value =  str_replace("&","&amp;",$category_value);
			foreach ($categories_list as $cat_value) {
				//echo $categoryString;
				if(strcmp($cat_value->name , $category_value) == 0){
					$all_cat = $cat_value->term_id . "," . $all_cat;
				}
			}
		}
		foreach ($location as $location_value) {
			$location_value =  str_replace("&","&amp;",$location_value);
			foreach ($location_list as $loc_value) {
				if (strcmp($location_value , $loc_value->name) == 0) {
					# code...
					$all_loc = $loc_value->term_id . "," . $all_loc;
				}
			}
		}

        $not_email = $_POST['not_email'];
		$not_sms = $_POST['not_sms'];
 
		if($not_email == 1){
			$unsub_email_val = "checked";
		} else{
			$unsub_email_val = "";
			$not_email = 0;
		}
		 
		if($not_sms == 1){
			$unsub_sms_val = "checked";
		} else{
			$unsub_sms_val = "";
			$not_sms = 0;
		}
		if ($unlock_lead_ != 1) {
			$unlock_val = "";
			$unlock_lead_ = 0;
		}else
			$unlock_val = "checked";
		
		$user_id = get_current_user_id();
		echo $user_id;
		$user_detail    = get_user_meta($user_id);
		$first_name     = $user_detail['first_name'][0];
		$last_name      = $user_detail['last_name'][0];
		$_client_name   = $first_name . " " . $last_name;
		$_client_email  = $user_detail['user_general_email'][0];
		$client_contact = $user_detail['user_general_phone'][0];
 
		//Insert Data to table

			global $wpdb;
			$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
			if ($wpdb->get_results("SELECT * FROM $table_name WHERE id = $user_id")) {
				$client_result = $wpdb->update($table_name,
					array(
						'preferences' => $notification,
						'location' => $all_loc,
                        'unsubscribe_email' => $not_email,
						'unsubscribe_sms' => $not_sms,
						'unlock_lead' => $unlock_lead_,
						'category' => $all_cat
					)
					,
					array('id' => $user_id)
					, $format = null, $where_format = null);
			} else {
				$client_result = $wpdb->insert(
					$wpdb->prefix . 'edugorilla_client_preferences',
					array(
						'id'                => $user_id,
						'contact_no'        => $client_contact,
						'preferences'       => $notification,
						'unsubscribe_email' => $not_email,
						'unsubscribe_sms'   => $not_sms,
						'location'          => $all_loc,
						'unlock_lead'       => $unlock_lead_,
						'category'          => $all_cat
					)
				);
			}
			if ($client_result)
				$client_success = "Saved Successfully";
			else
				$client_success = "Please try again";
	}
	?>

	<script type="text/javascript">
		function addMoreRows() {
			var ctrC = parseInt(document.getElementById("category_count").value);
			var ctrL = parseInt(document.getElementById("location_count").value);

			//Create an input type dynamically.
			var element_c = document.createElement("input");
			var element_l = document.createElement("input");
			var br1 = document.createElement("br");
			var br2 = document.createElement("br");

			var element_name_c = "category" + ctrC;
			element_c.setAttribute("list", "categories_list");
			element_c.setAttribute("size", 30);
			element_c.setAttribute("name", element_name_c);
			var foo1 = document.getElementById("get_category");
			foo1.insertBefore(br1, foo1.childNodes[0]);
			foo1.insertBefore(element_c, foo1.childNodes[0]);
			ctrC++;
			document.getElementById("category_count").value = ctrC;

			var element_name_l = "location" + ctrL;
			element_l.setAttribute("list", "location_list");
			element_l.setAttribute("size", 30);
			element_l.setAttribute("name", element_name_l);
			//Assign different attributes to the element.
			var foo2 = document.getElementById("get_location");
			foo2.insertBefore(br2, foo2.childNodes[0]);
			foo2.insertBefore(element_l, foo2.childNodes[0]);
			ctrL++;
			document.getElementById("location_count").value = ctrL;
		}

		function removeThisRow(rowId) {
			var locationElem = document.getElementsByName("location" + rowId)[0];
			var categoryElem = document.getElementsByName("category" + rowId)[0];
			var removeButton = document.getElementsByName("removeCategory" + rowId)[0];
			var catLineBreak = document.getElementsByName("brcategory" + rowId)[0];
			var locLineBreak = document.getElementsByName("brlocation" + rowId)[0];
			locationElem.parentNode.removeChild(locationElem);
			categoryElem.parentNode.removeChild(categoryElem);
			removeButton.parentNode.removeChild(removeButton);
			catLineBreak.parentNode.removeChild(catLineBreak);
			locLineBreak.parentNode.removeChild(locLineBreak);
		}
	</script>

	<!-- Client Form -->
	<form action = "" method = "post">
		<p><?php echo $client_success; ?></p>
		<table>
			<tr>
				<td rowspan = "4">Notification Preferences<sup><font color = "red">*</font></sup> :</td>
				<td colspan = "2"><input type = "checkbox" name = "notification[]" id = "notification"
				                       value = "Instant_Notifications" <?php echo $in_val ?>>Instant Notification
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Daily_Digest" <?php echo $dd_val ?>>Daily
					Digest
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Weekly_Digest" <?php echo $wd_val ?> >Weekly
					Digest
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Monthly_Digest" <?php echo $md_val ?>>Monthly
					Digest
				</td>
			</tr>
			<tr>
				<td rowspan="2">Unsubscribe Preferences<sup><font color="red">*</font></sup> :</td>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "not_email"
				                         value="1" <?php echo $unsub_email_val ?>>Unsubscribe from all Emails
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "not_sms"
				                         value="1" <?php echo $unsub_sms_val ?>>Unsubscribe from all SMS<br/>
				</td>
			</tr>
			<tr>
				<td rowspan = "2">Subscribe for following Categories :</td>
				<td>Location</td>
				<td>Category</td>
			</tr>
			<tr>
				<td>
					<?php $location = get_terms('locations', array('hide_empty' => false)); ?>
					<datalist id = "location_list">
						<?php foreach ($location as $value) { ?>
						<option value = "<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id = "get_location">
						<input list = "location_list" name = "location0" size = "30" value = "<?php echo $location_select_val?>">
						<?php echo $more_location ?>
					</div>
					<input type = "text" hidden name = "location_count" id = "location_count" value = "<?php echo $location_count_value ?>"></td>
				<td>
					<?php $categories = get_terms('listing_categories', array('hide_empty' => false)); ?>
					<datalist id = "categories_list">
						<?php foreach ($categories as $value) { ?>
						<option value = "<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id = "get_category">
						<input list = "categories_list" name = "category0" size = "30" value = "<?php
						echo $category_select_val ?>"><input type="button" name="removeCategory0" value="  -  "
						                                     onclick="removeThisRow(0)">
						<?php echo $more_category ?>
						<input type="button" value="  +  " onclick="addMoreRows()">
					</div>
					<input type = "text" hidden name = "category_count" id = "category_count" value = "<?php echo $category_count_value ?>">
					</td>
			</tr>
			<tr>
				<td>Automatically Unlock the Lead :</td>
				<td><input type = "checkbox" name = "unlock_lead" value = "1" <?php echo $unlock_val ?>></td>
			</tr>
			<tr><td><input type = "submit" name = "submit_client_pref"/></td></tr>
		</table>
	</form>
	<?php
}

add_shortcode('client_preference_form', 'edugorilla_client');


add_action('mail_send_daily', 'do_this_daily');
add_action('mail_send_weekly', 'do_this_weekly');
add_action('mail_send_monthly', 'do_this_monthly');

function my_email_activation()
{
	$time_day = date("y-m-d") . " 17:00:00";
	$daily_time = strtotime($time_day);

	$startdate = strtotime("Friday");
	$week_time = date("y", $startdate) . '-' . date("m", $startdate) . '-' . date("d", $startdate) . " 12:00:00";
	$weekly_time = strtotime($week_time);

	wp_schedule_event($daily_time, 'daily', 'mail_send_daily');
	wp_schedule_event($weekly_time, 'weekly', 'mail_send_weekly');
	wp_schedule_event($weekly_time, 'monthly', 'mail_send_monthly');
}


function do_this_weekly()
{
	//do something weekly 
	// send mail every week at 12PM on Friday
	$edugorilla_email = get_option('email_setting_form_weekly');
	$edugorilla_email_body = stripslashes($edugorilla_email['body']);
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2 = $wpdb->prefix . 'edugorilla_client_preferences';
	$users_table = $wpdb->users;
	$lead_details = $wpdb->get_results("SELECT * FROM $table_name1");
	$client_email_addresses = $wpdb->get_results("SELECT ut.display_name AS client_name,ut.user_email AS email_id,cpt.* FROM $table_name2 cpt,$users_table ut WHERE ut.ID=cpt.id");


	foreach ($client_email_addresses as $client) {
		# code...
		if(preg_match('/Weekly_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if((preg_match('/'.$lead_detail->category_id.'/',$client->category) OR empty($client->category)) AND (preg_match('/'.$lead_detail->location_id.'/',$client->location) OR  empty($client->location))){
					# code...
					if ($category_val == null || $location_val == null){
						# code...
						$categories_all = get_terms('listing_categories', array('hide_empty' => false));
						$location_all = get_terms('locations', array('hide_empty' => false));
						foreach ($categories_all as $value) {
							# code...
							if($lead_detail->category_id == $value->term_id)
								$category_val = $value->name;
						}

						foreach ($location_all as $value2) {
							# code...
							if($lead_detail->location_id == $value2->term_id)
								$location_val = $value2->name;
						}
					}
					$category_location_lead_count = $category_location_lead_count+1;
				}
			}
			$edugorilla_email_subject = str_replace("{category}", $category_val,
				$edugorilla_email['subject']);
			$email_template_datas = array("{Contact_Person}" => $client->client_name, "{category}" => $category_val,"{location}" => $location_val, "{category_location_lead_count}" => $category_location_lead_count);
			foreach ($email_template_datas as $var => $email_template_data) {
				$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
			}

			$headers = "";
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($client->email_id, $edugorilla_email_subject, ucwords($edugorilla_email_body), $headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		}
	}
}

function do_this_daily()
{
	//do something every day
	// send mail every day at 5PM
	$edugorilla_email = get_option('email_setting_form_daily');
	$edugorilla_email_body = stripslashes($edugorilla_email['body']);
	global $wpdb;
	$table_name1            = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2            = $wpdb->prefix . 'edugorilla_client_preferences';
	$users_table            = $wpdb->users;
	$lead_details           = $wpdb->get_results("SELECT * FROM $table_name1");
	$client_email_addresses = $wpdb->get_results( "SELECT ut.display_name AS user_name,ut.user_email AS user_email_id,cpt.* FROM $table_name2 cpt,$users_table ut WHERE ut.ID=cpt.id" );


	foreach ($client_email_addresses as $client) {
		# code...
		if(preg_match('/Daily_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if((preg_match('/'.$lead_detail->category_id.'/',$client->category) OR empty($client->category)) AND (preg_match('/'.$lead_detail->location_id.'/',$client->location) OR  empty($client->location))){
					# code...
					if ($category_val == null || $location_val == null){
						# code...
						$categories_all = get_terms('listing_categories', array('hide_empty' => false));
						$location_all = get_terms('locations', array('hide_empty' => false));
						foreach ($categories_all as $value) {
							# code...
							if($lead_detail->category_id == $value->term_id)
								$category_val = $value->name;
						}

						foreach ($location_all as $value2) {
							# code...
							if($lead_detail->location_id == $value2->term_id)
								$location_val = $value2->name;
						}
					}
					$category_location_lead_count = $category_location_lead_count+1;
				}
			}
			$edugorilla_email_subject = str_replace("{category}", $category_val,
				$edugorilla_email['subject']);
			$email_template_datas     = array( "{Contact_Person}"               => $client->user_name,
			                                   "{category}"                     => $category_val,
			                                   "{location}"                     => $location_val,
			                                   "{category_location_lead_count}" => $category_location_lead_count
			);
			foreach ($email_template_datas as $var => $email_template_data) {
				$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
			}

			$headers = "";
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($client->email_id, $edugorilla_email_subject, ucwords($edugorilla_email_body), $headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		}
	}

}

function do_this_monthly()
{
	//do something every month
	// send mail every month at 12PM on Friday
	$edugorilla_email = get_option('email_setting_form_monthly');
	$edugorilla_email_body = stripslashes($edugorilla_email['body']);
	global $wpdb;
	$table_name1            = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2            = $wpdb->prefix . 'edugorilla_client_preferences';
	$users_table            = $wpdb->users;
	$lead_details           = $wpdb->get_results("SELECT * FROM $table_name1");
	$client_email_addresses = $wpdb->get_results( "SELECT ut.display_name AS user_name,ut.user_email AS user_email_id,cpt.* FROM $table_name2 cpt,$users_table ut WHERE ut.ID=cpt.id" );

	foreach ($client_email_addresses as $client) {
		# code...
		if(preg_match('/Monthly_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if((preg_match('/'.$lead_detail->category_id.'/',$client->category) OR empty($client->category)) AND (preg_match('/'.$lead_detail->location_id.'/',$client->location) OR  empty($client->location))){
					# code...
					if ($category_val == null || $location_val == null){
						# code...
						$categories_all = get_terms('listing_categories', array('hide_empty' => false));
						$location_all = get_terms('locations', array('hide_empty' => false));
						foreach ($categories_all as $value) {
							# code...
							if($lead_detail->category_id == $value->term_id)
								$category_val = $value->name;
						}

						foreach ($location_all as $value2) {
							# code...
							if($lead_detail->location_id == $value2->term_id)
								$location_val = $value2->name;
						}
					}
					$category_location_lead_count = $category_location_lead_count+1;
				}
			}
			$edugorilla_email_subject = str_replace("{category}", $category_val,
				$edugorilla_email['subject']);
			$email_template_datas     = array( "{Contact_Person}"               => $client->user_name,
			                                   "{category}"                     => $category_val,
			                                   "{location}"                     => $location_val,
			                                   "{category_location_lead_count}" => $category_location_lead_count
			);
			foreach ($email_template_datas as $var => $email_template_data) {
				$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
			}

			$headers = "";
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail( $client->user_email_id, $edugorilla_email_subject, ucwords( $edugorilla_email_body ), $headers );
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		}
	}

}


function my_deactivation()
{
	wp_clear_scheduled_hook('mail_send_daily');
	wp_clear_scheduled_hook('mail_send_weekly');
	wp_clear_scheduled_hook('mail_send_monthly');
}

//custom cron intervals for weekly and monthly
add_filter('cron_schedules', 'monthly_add_weekly_cron_schedule');
function monthly_add_weekly_cron_schedule($schedules)
{
	$schedules['weekly'] = array(
		'interval' => 604800, // 1 week in seconds
		'display' => __('Once Weekly'),
	);

	$schedules['monthly'] = array(
		'interval' => 2592000, // 1 month in seconds
		'display' => __('Once Monthly'),
	);

	return $schedules;
}


?>
