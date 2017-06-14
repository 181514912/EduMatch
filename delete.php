<?php
	function app_output_buffer() {
	  ob_start();
	} // soi_output_buffer

	add_action('init', 'app_output_buffer');
	function edugorilla_lead_delete()
    {
    	$iid = $_REQUEST['iid'];
    	$pl_delete_form = $_REQUEST['pl_delete_form'];
    	global $wpdb;
	    $q = "select * from {$wpdb->prefix}edugorilla_lead_details where id=$iid";
    	$leads_datas = $wpdb->get_results($q, 'ARRAY_A');
    	foreach($leads_datas as $leads_data);
    	$lead_name = $leads_data['name'];
    
    	if($pl_delete_form == "self")
        {
	        $wpdb->delete($wpdb->prefix . 'edugorilla_lead_details', array('id' => $iid));
        	wp_redirect(admin_url('admin.php?page=Listing', 'http')); 
        	exit;
        }
?>
	<div class="wrap">
		<h1>Promotional Leads List</h1>
    <form method="get" action="admin.php">
    	<input type="hidden" name ="page" value="edugorilla-delete-lead">
    	<input type=hidden name="iid" value="<?php echo $iid; ?>">
    	<table class="widefat fixed" cellspacing="0">
			<tbody>
				<tr>
					<th>Do you want to delete <?php echo $lead_name; ?> ?</th>
				</tr>
            	<tr>
                		<td><input type="submit"  class="button button-primary" value="Yes">   <a href="admin.php?page=Listing" class="button button-primary">No</a></td>
            	</tr>
				
			</tbody>
		</table>
        <input type=hidden name="pl_delete_form" value="self">
    </form>
	</div>
<?php
    }
		function edugorilla_client_delete()
    {
    	$iid = $_REQUEST['iid'];
    	$pl_delete_form = $_REQUEST['pl_delete_form'];
    	global $wpdb;
		$all_meta_for_user = get_user_meta( $iid );
		$client_firstname = $all_meta_for_user['user_general_first_name'][0];
			if(empty($client_firstname))
				$client_firstname = $all_meta_for_user['first_name'][0];
		$client_lastname = $all_meta_for_user['user_general_last_name'][0];
			if(empty($client_lastname))
				$client_lastname = $all_meta_for_user['last_name'][0];
		$client_name = "' ".$client_firstname." ".$client_lastname." '";
    	if($pl_delete_form == "self")
        {
			$value = 0;
	        $wpdb->update($wpdb->prefix . 'edugorilla_client_preferences', array('is_active'=>$value), array('id'=>$iid));
        	wp_redirect(admin_url('admin.php?page=client-listing', 'http')); 
        	exit;
        }
?>
	<div class="wrap">
		<h1>Client List</h1>
    <form method="get" action="admin.php">
    	<input type="hidden" name ="page" value="edugorilla-deactivate-client">
    	<input type=hidden name="iid" value="<?php echo $iid; ?>">
    	<table class="widefat fixed" cellspacing="0">
			<tbody>
				<tr>
					<th>Do you want to deactivate <?php echo $client_name; ?> ?</th>
				</tr>
            	<tr>
                		<td><input type="submit"  class="button button-primary" value="Yes">   <a href="admin.php?page=client-listing" class="button button-primary">No</a></td>
            	</tr>
				
			</tbody>
		</table>
        <input type=hidden name="pl_delete_form" value="self">
    </form>
	</div>
<?php
    }
		function edugorilla_client_active()
    {
    	$iid = $_REQUEST['iid'];
    	$pl_active_form = $_REQUEST['pl_active_form'];
    	global $wpdb;
		$all_meta_for_user = get_user_meta( $iid );
		$client_firstname = $all_meta_for_user['user_general_first_name'][0];
			if(empty($client_firstname))
				$client_firstname = $all_meta_for_user['first_name'][0];
		$client_lastname = $all_meta_for_user['user_general_last_name'][0];
			if(empty($client_lastname))
				$client_lastname = $all_meta_for_user['last_name'][0];
		$client_name = "' ".$client_firstname." ".$client_lastname." '";
    	if($pl_active_form == "self")
        {
	        $value = 1;
	        $wpdb->update($wpdb->prefix . 'edugorilla_client_preferences', array('is_active'=>$value), array('id'=>$iid));
        	wp_redirect(admin_url('admin.php?page=client-listing', 'http')); 
        	exit;
        }
?>
	<div class="wrap">
		<h1>Client List</h1>
    <form method="get" action="admin.php">
    	<input type="hidden" name ="page" value="edugorilla-activate-client">
    	<input type=hidden name="iid" value="<?php echo $iid; ?>">
    	<table class="widefat fixed" cellspacing="0">
			<tbody>
				<tr>
					<th>Do you want to activate <?php echo $client_name; ?> ?</th>
				</tr>
            	<tr>
                		<td><input type="submit"  class="button button-primary" value="Yes">   <a href="admin.php?page=client-listing" class="button button-primary">No</a></td>
            	</tr>
				
			</tbody>
		</table>
        <input type=hidden name="pl_active_form" value="self">
    </form>
	</div>
<?php
    }
?>
