<?php
/**
 * Created by PhpStorm.
 * User: ananth
 * Date: 16/6/17
 * Time: 1:20 AM
 */

function create_lead_capture_form() {
	$caller = $_POST['lead_capturing_form'];
	$scType = $_REQUEST['screenType'];

	if ( $scType == "leadEditOption" ) {
		//Stuff to initialize when using this as edit screen.
		$lead_id = $_REQUEST['iid'];
		global $wpdb;
		$q            = "select * from {$wpdb->prefix}edugorilla_lead_details where id=$lead_id";
		$lead_details = $wpdb->get_results( $q, ARRAY_A );

		foreach ( $lead_details as $lead_detail ) {
			$name                = $lead_detail['name'];
			$contact_no          = $lead_detail['contact_no'];
			$email               = $lead_detail['email'];
			$query               = $lead_detail['query'];
			$is_promotional_lead = $lead_detail['is_promotional'];
			$listing_type        = $lead_detail['listing_type'];
			$category_ids        = explode( ",", $lead_detail['category_id'] );
			$keyword             = $lead_detail['keyword'];
			$location            = $lead_detail['location_id'];
		}
	}
	if ( $caller == "selfFormSubmit" ) {
		/** Form was just submitted using submit button, so get Data from the Form **/
		$name                          = $_POST['name'];
		$contact_no                    = $_POST['contact_no'];
		$keyword                       = $_POST['keyword'];
		$email                         = $_POST['email'];
		$listing_type                  = $_POST['listing_type'];
		$query                         = $_POST['query'];
		$category_id                   = $_POST['category_id'];
		$location_id                   = $_POST['location'];
		$edugorilla_institute_datas    = $_POST['edugorilla_institute_datas_name'];
		$edugorilla_subscription_datas = $_POST['edugorilla_subscibed_instant_datas_name'];
		$is_promotional_lead           = $_POST['is_promotional_lead'];

		//echo "<h2>Edugorilla Institute Datas : $edugorilla_institute_datas</h2><br>";
		//echo "<h2>Edugorilla Subscription Datas : $edugorilla_subscription_datas</h2><br><br>";

		/** Error Checking **/
		$errors = array();
		if ( empty( $name ) ) {
			$errors['name'] = "Empty";
		} elseif ( ! preg_match( "/([A-Za-z]+)/", $name ) ) {
			$errors['name'] = "Invalid";
		}

		if ( empty( $contact_no ) && empty( $email ) ) {
			$errors['contact_no'] = "Empty Contact field";
			$errors['email']      = "Empty Email field";
		}

		if ( ! empty( $contact_no ) && ! preg_match( "/([0-9]{10}+)/", $contact_no ) ) {
			$errors['contact_no'] = "Invalid";
		}

		if ( ! empty( $email ) && ( filter_var( $email, FILTER_VALIDATE_EMAIL ) === false ) ) {
			$errors['email'] = "Invalid";
		}

		if ( empty( $query ) ) {
			$errors['query'] = "Empty";
		}

		/** Default Values */
		if ( empty( $contact_no ) ) {
			$contact_no = "N/A";
		}

		if ( empty( $email ) ) {
			$email = "N/A";
		}

		/** Store Leads and alert as necessary */
		if ( empty( $errors ) ) {
			$lead_contact_status     = array();
			$institute_emails_status = array();
			$institute_sms_status    = array();

			if ( ! empty( $category_id ) ) {
				$category_str = implode( ",", $category_id );
			} else {
				$category_str = "-1";
			}

			if ( empty( $location_id ) ) {
				$location_id = "-1";
			}
			if ( empty( $is_promotional_lead ) ) {
				$is_promotional_lead = "no";
			}

			$institute_applicable_datas    = json_decode( stripslashes( $edugorilla_institute_datas ) );
			$subscription_applicable_datas = json_decode( stripslashes( $edugorilla_subscription_datas ) );

			$adminObject = wp_get_current_user();
			$adminId     = $adminObject->ID;

			$lead_detail_values = array(
				'admin_id'       => $adminId,
				'name'           => $name,
				'contact_no'     => $contact_no,
				'email'          => $email,
				'query'          => $query,
				'is_promotional' => $is_promotional_lead,
				'listing_type'   => $listing_type,
				'keyword'        => $keyword,
				'category_id'    => $category_str,
				'location_id'    => $location_id,
				'date_time'      => current_time( 'mysql' )
			);

			global $wpdb;
			$lead_detail_table = $wpdb->prefix . 'edugorilla_lead_details';

			if ( $scType == "leadEditOption" ) {
				$result1 = $wpdb->update( $lead_detail_table, $lead_detail_values, array( 'id' => $lead_id, ) );
			} else {
				$result1 = $wpdb->insert( $lead_detail_table, $lead_detail_values );
			}


			$lead_id    = $wpdb->insert_id;
			$lead_card  = new Lead_Card( $lead_id, $name, $contact_no, $email, $query, $category_str, $location_id, current_time( 'mysql' ), $is_promotional_lead );
			$user_login = str_replace( " ", "_", $name );

			$uid = email_exists( $email );
			if ( $uid ) {
				wp_update_user( array( 'ID' => $uid, 'user_email' => $email ) );
				update_user_meta( $uid, 'user_general_phone', $contact_no );
				update_user_meta( $uid, 'user_general_email', $email );
			} else {
				$userdata = array(
					'user_login' => $user_login,
					'user_pass'  => $contact_no,
					'first_name' => $name,
					'user_email' => $email,
					'user_pass'  => $contact_no
				);
				$user_id  = wp_insert_user( $userdata );

				if ( ! is_wp_error( $user_id ) ) {
					add_user_meta( $user_id, 'user_general_first_name', $name );
					add_user_meta( $user_id, 'user_general_phone', $contact_no );
					add_user_meta( $user_id, 'user_general_email', $email );
				}
			}
			foreach ( $subscription_applicable_datas as $subscription_data_applicable ) {
				$subscription_emails_applicable = $subscription_data_applicable->emailDetails;
				$subscription_phones_applicable = $subscription_data_applicable->phoneDetails;
				$auto_unlock_lead               = $subscription_data_applicable->autoUnlockLead;
				$subscription_applicable_emails = explode( ",", $subscription_emails_applicable );
				$subscription_applicable_phones = explode( ",", $subscription_phones_applicable );
				$subscription_user_id           = $subscription_data_applicable->userId;
				$subscription_user_name         = $subscription_data_applicable->userName;
				$subscription_send_applicable   = $subscription_data_applicable->sendPrefDetails;
				if ( $subscription_send_applicable ) {
					//echo "Emails : $subscription_emails_applicable AND phones : $subscription_phones_applicable";
					$lead_contact_status[] = $subscription_user_name;
					$result2               = send_mail_with_unlock( $is_promotional_lead, $auto_unlock_lead, $subscription_applicable_emails, $subscription_applicable_phones, $subscription_user_name, $subscription_user_id, $lead_card );
				}
			}
			foreach ( $institute_applicable_datas as $institute_data_applicable ) {
				if ( $is_promotional_lead == "yes" ) {
					$edugorilla_email         = get_option( 'edugorilla_email_setting1' );
					$edugorilla_email_body    = stripslashes( $edugorilla_email['body'] );
					$edugorilla_email_subject = str_replace( "{category}", $institute_data_applicable->contact_category, $edugorilla_email['subject'] );
					$email_template_datas     = array(
						"{Contact_Person}" => $institute_data_applicable->contact_person,
						"{category}"       => $institute_data_applicable->contact_category,
						"{location}"       => $institute_data_applicable->contact_location,
						"{listing_URL}"    => $institute_data_applicable->listing_url,
						"{lead_id}"        => $lead_id,
						"{name}"           => $name,
						"{contact no}"     => $contact_no,
						"{email address}"  => $email,
						"{query}"          => $query
					);
					set_lead_data( $lead_id, $name, $contact_no, $institute_data_applicable->contact_category, $email, $institute_data_applicable->contact_location, $query );
					foreach ( $email_template_datas as $var => $email_template_data ) {
						$edugorilla_email_body = str_replace( $var, $email_template_data, $edugorilla_email_body );
					}

					$institute_emails            = explode( ",", $institute_data_applicable->emails );
					$institute_phones            = explode( ",", $institute_data_applicable->phones );
					$client_pref_database        = new ClientEmailPref_Helper();
					$institute_emails            = $client_pref_database->removeUnsubscribedEmails( $institute_emails );
					$log_post_id                 = $institute_data_applicable->post_id;
					$should_send_posting_details = $institute_data_applicable->sendPostDetails;
					if ( $should_send_posting_details ) {
						$lead_contact_status[] = $name;
						$result2               = send_mail_without_unlock( $edugorilla_email_subject, $edugorilla_email_body, $institute_emails, $institute_phones, $institute_data_applicable->contact_person, $lead_id, $log_post_id );
					}
				}
			}

			if ( $result1 ) {
				$lead_contact_status_str = implode( ', ', $lead_contact_status );
				$success                 = "Saved Successfully and contacted : $lead_contact_status_str";
				$name                    = null;
				$contact_no              = null;
				$email                   = null;
				$query                   = null;
				$is_promotional_lead     = null;
				$listing_type            = null;
				$category_ids            = null;
				$keyword                 = null;
				$location                = null;
			} else {
				$lead_detail_array_values = array_values( $lead_detail_values );
				$success                  = "Unable to save these values to leads successfully : $lead_detail_array_values";
			}

			//  foreach($_REQUEST as $var=>$val)$$var="";
		}
	}
	?>

	<div class="wrap">
		<h1>EduGorilla Leads</h1><?php
		if ( $success ) {
			?>
			<div class="updated notice">
				<p><?php echo $success; ?></p>
			</div>
			<?php
		}
		?>
		<form name="lead_capture_details" method="post">
			<table class="form-table">
				<tr>
					<th>Name<sup><font color="red">*</font></sup></th>
					<td>
						<input id="edu_name" name="name" value="<?php echo $name; ?>" placeholder="Type name here...">
						<font color="red"><?php echo $errors['name']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Contact No.<sup><font color="red">*</font></sup></th>
					<td>
						<input id="edu_contact_no" name="contact_no" value="<?php echo $contact_no; ?>"
						       placeholder="Type contact number here">
						<font color="red"><?php echo $errors['contact_no']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Email<sup><font color="red">*</font></sup></th>
					<td>
						<input id="edu_email" name="email" value="<?php echo $email; ?>" placeholder="Type email here">
						<font color="red"><?php echo $errors['email']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Query<sup><font color="red">*</font></sup></th>
					<td>
                        <textarea id="edu_query" name="query" rows="4" cols="65"
                                  placeholder="Type your query here"><?php echo $query; ?></textarea>
						<font color="red"><?php echo $errors['query']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Is it a promotional lead?</th>
					<td>
						<input name="is_promotional_lead" id="is_promotional_lead" type="checkbox"
						       value="yes" <?php if ( $is_promotional_lead == "yes" ) {
							echo "checked";
						} ?>>
					</td>
				</tr>
				<tr id="listing_type_row">
					<th>Listing Type<sup><font color="red">*</font></sup></th>
					<td>
						<select name="listing_type" id="edugorilla_listing_type">
							<option value="">Select</option>
							<option value="coaching">Coaching and Training Institutes (-1)</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Category</th>
					<td>
						<select disabled name="category_id[]" multiple id="edugorilla_category"
						        class="js-example-basic-single">
							<?php
							$temparray  = array();
							$categories = get_terms( 'listing_categories', array( 'hide_empty' => false ) );

							foreach ( $categories as $category ) {
								$temparray[ $category->parent ][ $category->term_id ] = $category->name;
							}

							foreach ( $temparray as $var => $vals ) {
								?>
								<?php if ( in_array( $var, $category_ids ) ) { ?>
									<option value="<?php echo $var; ?>" selected>
										<?php
										$d = get_term_by( 'id', $var, 'listing_categories' );
										echo $d->name;
										?>
									</option>
								<?php } else { ?>
									<option value="<?php echo $var; ?>">
										<?php
										$d = get_term_by( 'id', $var, 'listing_categories' );
										echo $d->name;
										?>
									</option>
								<?php } ?>
								<?php
								foreach ( $vals as $index => $val ) {
									?>
									<?php if ( in_array( $index, $category_ids ) ) { ?>
										<option value="<?php echo $index; ?>" selected>
											<?php echo $val; ?>
										</option>
									<?php } else { ?>
										<option value="<?php echo $index; ?>">
											<?php echo $val; ?>
										</option>
										<?php
									}
								}
								?>

								<?php
							}
							?>
						</select>
						<font color="red"><?php echo $errors['category_id']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Keyword</th>
					<td>
						<input name="keyword" id="edugorilla_keyword" disabled value="<?php echo $keyword; ?>"
						       placeholder="Type keyword here">
						<font color="red"><?php echo $errors['keyword']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Location</th>
					<td>
						<select disabled name="location" id="edugorilla_location" class="js-example-basic-single">
							<option value="">Select</option>
							<?php
							$templocationarray    = array();
							$edugorilla_locations = get_terms( 'locations', array( 'hide_empty' => false ) );

							foreach ( $edugorilla_locations as $edugorilla_location ) {
								$templocationarray[ $edugorilla_location->parent ][ $edugorilla_location->term_id ] = $edugorilla_location->name;
							}

							foreach ( $templocationarray as $var => $vals ) {

								?>
								<?php if ( $var == $location ) { ?>
									<option value="<?php echo $var; ?>" selected>
										<?php
										$d = get_term_by( 'id', $var, 'locations' );
										echo $d->name;
										?>
									</option>
								<?php } else { ?>
									<option value="<?php echo $var; ?>">
										<?php
										$d = get_term_by( 'id', $var, 'locations' );
										echo $d->name;
										?>
									</option>
								<?php } ?>
								<?php
								foreach ( $vals as $index => $val ) {
									?>
									<?php if ( $location == $index ) { ?>
										<option value="<?php echo $index; ?>" selected>
											<?php echo "->" . $val; ?>
										</option>
									<?php } else { ?>
										<option value="<?php echo $index; ?>">
											<?php echo "->" . $val; ?>
										</option>
									<?php } ?>
									<?php
								}
								?>

								<?php
							}
							?>
						</select>
						<input type="button" class="button button-secondary" id="edugorilla_add_location"
						       value="Add New Location">
						<br><br>
						<input type="button" class="button button-secondary" id="edugorilla_filter"
						       value="Filter"><br><br>

						<div id="map"></div>
					</td>
				</tr>
				<tr>
					<th>
						<input type="hidden" id="edugorilla_institute_datas" name="edugorilla_institute_datas_name">
						<input type="hidden" id="edugorilla_subscibed_instant_datas"
						       name="edugorilla_subscibed_instant_datas_name">
						<input type="hidden" name="lead_capturing_form" value="selfFormSubmit">
					</th>
					<td>

						<a id="save_details_button" disabled href="#confirmInstantLeadSend"
						   class="button button-primary">Send
							Details</a>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<!-------Modal------>
	<div id="confirmInstantLeadSend" style="display:none;">
	</div>
	<!---/Modal-------->
	<?php
}

?>