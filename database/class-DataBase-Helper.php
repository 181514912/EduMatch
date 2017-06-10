<?php

class DataBase_Helper
{

	public function add_educash_transaction( $client_id, $lead_id, $educash, $money, $adminComment )
	{
		global $wpdb;
		$transaction_table = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
		$time = current_time('mysql');
		$adminName = wp_get_current_user();
		$insert_status = $wpdb->insert($transaction_table, array(
			'time'        => $time,
			'admin_id'    => $adminName->ID,
			'client_id'   => $client_id,
			'lead_id'     => $lead_id,
			'transaction' => $educash,
			'amount'      => $money,
			'comments'    => $adminComment
		));
		return $insert_status;
	}

	public function get_educash_for_user($current_user_id)
	{
		global $wpdb;
		$current_educash = 0;
		$transaction_table = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
		$sql = "SELECT * FROM $transaction_table WHERE client_id = $current_user_id";
		$total_cash = $wpdb->get_results($sql);
		$i = 0;
		if (count($total_cash) > 0) {
			foreach ($total_cash as $cash) {
				if ($cash->transaction > 0) {
					$date = $cash->time;
					$consumption[$i]['date'] = $date;
					$consumption[$i]['spent'] = $cash->transaction;
					$consumption[$i]['val'] = 0;
					$i = $i + 1;
					$current_educash = $current_educash + ($cash->transaction);
				}
			}
		}
		if ($current_educash < 0)
			$current_educash = 0;
		return $current_educash;
	}


}


?>
