<?php

class ClientEmailPref_Helper
{
	public function removeUnsubscribedEmails($filterEmailIds)
	{
		global $wpdb;
		$resultEmailIds = array();
		$users_table = $wpdb->users;
		$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
		$client_email_addresses = $wpdb->get_results("SELECT ut.display_name AS client_name,ut.user_email AS email_id,cpt.* FROM $table_name cpt,$users_table ut WHERE ut.ID=cpt.id");
		foreach ($client_email_addresses as $cea) {
			if (in_array($cea->email_id, $filterEmailIds)) {
				if ($cea->unsubscribe_email != 0) {
					$filterEmailIds = array_diff($filterEmailIds, array($cea->email_id));
				}
			}
		}
		return $filterEmailIds;
	}

	public function removeUnsubscribedSMSs( $filterPhoneNumbers ) {
		global $wpdb;
		$resultPhoneNums         = array();
		$users_table             = $wpdb->users;
		$table_name              = $wpdb->prefix . 'edugorilla_client_preferences';
		$client_preference_table = $wpdb->get_results( "SELECT ut.display_name AS client_name,ut.user_email AS email_id,cpt.* FROM $table_name cpt,$users_table ut WHERE ut.ID=cpt.id" );
		foreach ( $client_preference_table as $cea ) {
			if ( in_array( $cea->contact_no, $filterPhoneNumbers ) ) {
				if ( $cea->unsubscribe_sms != 0 ) {
					$filterPhoneNumbers = array_diff( $filterPhoneNumbers, array( $cea->contact_no ) );
				}
			}
		}

		return $filterPhoneNumbers;
	}
}


?>
