<?php

function edugorilla_show_location() {
	$term     = strtolower( $_REQUEST['term'] );
	$ptype    = strtolower( $_REQUEST['ptype'] );
	$address  = $_REQUEST['address'];
	$category = json_decode( stripslashes( $_REQUEST['category'] ) );

	$args                   = array();
	$args['posts_per_page'] = - 1;
	$args['post_status']    = 'publish';
	if ( ! empty( $ptype ) ) {
		$args['post_type'] = $ptype;
	}
	if ( ! empty( $term ) ) {
		$args['s'] = $term;
	}

	if ( ! empty( $category ) && ! empty( $address ) ) {
		$args['tax_query']['relation'] = 'AND';
	}

	if ( ! empty( $category ) ) {
		//$address = "%".$address."%";
		$args['tax_query'][0] = array(
			'taxonomy' => 'listing_categories',
			'field'    => 'id',
			'terms'    => $category
		);
	}

	if ( ! empty( $address ) ) {
		//$address = "%".$address."%";
		$args['tax_query'][1] = array(
			'taxonomy' => 'locations',
			'field'    => 'id',
			'terms'    => $address
		);
	}

	$eduction_posts = array();
	$the_query      = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$emails                       = array();
			$phones                       = array();
			$eduction_post                = array();
			$eduction_post['post_id']     = get_the_ID();
			$eduction_post['title']       = get_the_title();
			$eduction_post['listing_url'] = get_permalink( $the_query->ID );

			if ( get_post_meta( get_the_ID(), 'listing_address', true ) ) {
				$eduction_post['address'] = get_post_meta( get_the_ID(), 'listing_address', true );
			} else {
				$eduction_post['address'] = "Unavailable";
			}

			if ( get_post_meta( get_the_ID(), 'listing_verified', true ) ) {
				$eduction_post['flag'] = "verified";
			} else {
				$eduction_post['flag'] = "Unverified";
			}

			if ( get_post_meta( get_the_ID(), 'listing_person', true ) ) {
				$eduction_post['contact_person'] = get_post_meta( get_the_ID(), 'listing_person', true );
			} else {
				$eduction_post['contact_person'] = "Guest";
			}

			if ( get_post_meta( get_the_ID(), 'listing_locations', true ) ) {
				$location_temp                     = get_post_meta( get_the_ID(), 'listing_locations', true );
				$eduction_post['contact_location'] = str_replace( "-", " ", $location_temp[1] ) . ", " . str_replace( "-", " ", $location_temp[0] );
			} else {
				$eduction_post['contact_location'] = "N/A";
			}

			if ( get_post_meta( get_the_ID(), 'listing_listing_category', true ) ) {
				$category_temp = get_post_meta( get_the_ID(), 'listing_listing_category', true );

				$eduction_post['contact_category'] = str_replace( "-", " ", $category_temp[0] ) . ", " . str_replace( "-", " ", $category_temp[1] );
			} else {
				$eduction_post['contact_category'] = "N/A";
			}

			//check whether email values ara available or not.
			if ( get_post_meta( get_the_ID(), 'listing_email', true ) ) {
				$emails[] = get_post_meta( get_the_ID(), 'listing_email', true );
			}
			if ( get_post_meta( get_the_ID(), 'listing_alternate_email', true ) ) {
				$emails[] = get_post_meta( get_the_ID(), 'listing_alternate_email', true );
			}

			$eduction_post['emails'] = implode( ", ", $emails );

			//check whether phone numbers are available or not.
			if ( get_post_meta( get_the_ID(), 'listing_phone', true ) ) {
				$phones[] = get_post_meta( get_the_ID(), 'listing_phone', true );
			}
			if ( get_post_meta( get_the_ID(), 'listing_phone2', true ) ) {
				$phones[] = get_post_meta( get_the_ID(), 'listing_phone2', true );
			}
			if ( get_post_meta( get_the_ID(), 'listing_phone3', true ) ) {
				$phones[] = get_post_meta( get_the_ID(), 'listing_phone3', true );
			}
			if ( get_post_meta( get_the_ID(), 'listing_phone4', true ) ) {
				$phones[] = get_post_meta( get_the_ID(), 'listing_phone4', true );
			}
			if ( get_post_meta( get_the_ID(), 'listing_phone5', true ) ) {
				$phones[] = get_post_meta( get_the_ID(), 'listing_phone5', true );
			}

			$eduction_post['phones'] = implode( ", ", $phones );

			$eduction_post['lat']  = get_post_meta( get_the_ID(), 'listing_map_location_latitude', true );
			$eduction_post['long'] = get_post_meta( get_the_ID(), 'listing_map_location_longitude', true );

			$eduction_post['sendPostDetails'] = "true";
			$eduction_posts[]                 = $eduction_post;
		}
	}
	wp_reset_query();

	$responseObject["postingDetails"]                = $eduction_posts;
	$responseObject["subscriptionPreferenceDetails"] = edugorilla_show_pref_details( $address, $category );

	//$response = json_encode(array_values($eduction_posts));
	$response = json_encode( $responseObject );
	echo $response;
	exit();

}

function edugorilla_show_pref_details( $location_ids, $category ) {
	global $wpdb;
	$categoryArray              = $category;
	$locationArray              = explode( ',', $location_ids );
	$table_name                 = $wpdb->prefix . 'edugorilla_client_preferences';
	$users_table                = $wpdb->users;
	$client_email_addresses     = $wpdb->get_results( "SELECT ut.id AS user_id,ut.display_name AS user_name,ut.user_email AS user_email_id,cpt.* FROM $table_name cpt,$users_table ut WHERE ut.ID=cpt.id" );
	$headers                    = array( 'Content-Type: text/html; charset=UTF-8' );
	$preference_contact_details = array();
//	echo "Got Category : ";
//	foreach($category as $d){
//		echo $d;
//	}
//	echo "..Got Location : $location_ids";
	foreach ( $client_email_addresses as $cea ) {
		$categoryCheck = 0;
		$locationCheck = 0;
		$hasMoneyCheck = 0;
		if ( empty( $cea->category ) ) {
			$categoryCheck = 1;
		} else {
			$givenCategoryArray = explode( ',', $cea->category );
			//echo "..Given Category : $cea->category";
			foreach ( $categoryArray as $currentCategory ) {
				foreach ( $givenCategoryArray as $givenCategory ) {
					if ( $givenCategory == $currentCategory ) {
						$categoryCheck = 1;
					}
				}
			}
		}
		if ( empty( $cea->location ) ) {
			$locationCheck = 1;
		} else {
			$givenLocationArray = explode( ',', $cea->location );
			//echo "..Given Location : $cea->location";
			foreach ( $locationArray as $currentLocation ) {
				foreach ( $givenLocationArray as $givenLocation ) {
					if ( $givenLocation == $currentLocation ) {
						$locationCheck = 1;
					}
				}
			}
		}
		$eduCashHelper = new EduCash_Helper();
		$cashAtHand    = $eduCashHelper->getEduCashForCurrentUser();
		if ( $cashAtHand > 0 ) {
			$hasMoneyCheck = 1;
		}

		//echo "<h2>$cea->preferences AND $cea->category($categoryCheck) AND  $cea->location($locationCheck) for $cea->email_id!</h2>";
		if ( preg_match( '/Instant_Notifications/', $cea->preferences ) AND $categoryCheck == 1 AND $locationCheck == 1 AND $hasMoneyCheck == 1 ) {
			//echo $cea->client_name;
			$contactObject['userId']          = $cea->user_id;
			$contactObject['userName']        = $cea->user_name;
			$contactObject['emailDetails']    = $cea->user_email_id;
			$contactObject['phoneDetails']    = $cea->contact_no;
			$contactObject['autoUnlockLead']  = $cea->unlock_lead;
			$contactObject['sendPrefDetails'] = "true";
			$preference_contact_details[]     = $contactObject;
		}
	}

	return $preference_contact_details;
}

add_action( 'wp_ajax_edugorilla_show_location', 'edugorilla_show_location' );
add_action( 'wp_ajax_nopriv_edugorilla_show_location', 'edugorilla_show_location' );

?>
