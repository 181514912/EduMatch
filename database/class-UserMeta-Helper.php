<?php

class UserMeta_Helper
{
	public function getUserNameForUserId( $user_id ) {
		$userObject = get_userdata( $user_id );
		if ( ! $userObject ) {
			return "Automatic Lead";
		}
		$user_name = $userObject->data->display_name;

		return $user_name;
	}

	public function getUserIdFromEmail( $clientEmail ) {
		global $wpdb;
		$users_table = $wpdb->users;
		$client_ID   = $wpdb->get_var( "SELECT ID FROM $users_table WHERE user_email = '$clientEmail' " );

		return $client_ID;
	}

	public function getMetaDetails($user_id, $key)
	{

	}

	public function getMetaDetailsForCurrentUser($key)
	{
		$userObject = wp_get_current_user();
		$user_id = $userObject->ID;
		return $this->getMetaDetails($user_id, $key);
	}

	public function setMetaDetails($user_id, $key, $value)
	{

	}

	public function setMetaDetailsForcurrentUser($key, $value)
	{
		$userObject = wp_get_current_user();
		$user_id = $userObject->ID;
		return $this->setMetaDetails($user_id, $key, $value);
	}

}


?>
