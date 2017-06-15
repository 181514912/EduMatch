<?php
require_once __DIR__ . '/frontend/class-EduCash-Helper.php';
function subscribers_list()
{
    global $wpdb;
//Client's Listing
    $table_name = $wpdb->prefix . 'edugorilla_client_preferences ';
	$users_table = $wpdb->users;
    $count_query = $wpdb->get_results("SELECT * FROM $table_name");
    $num_rows = count($count_query); //PHP count()

    $cpage = $_REQUEST['cpage'];
    $list_caller = $_REQUEST['list_caller'];

	if(empty($cpage)) $current_page = 1;
	else $current_page = $cpage;
    
    $page_size = 50;
    if ($num_rows % $page_size == 0)
        $promotion_total_pages = $num_rows / $page_size;
    else
        $promotion_total_pages = intval($num_rows / $page_size) + 1;

    $index = ($current_page - 1) * $page_size;
//end of Client's listing


    global $wpdb;
        $q = "select * from $table_name order by id desc limit $index, $page_size";
    $leads_datas = $wpdb->get_results($q, 'ARRAY_A');

    $p = '';
    for ($i = 1; $i <= $promotion_total_pages; $i++) {
        if ($i == $current_page)
            $p .= "<option value='$i' selected> $i </option>";
        else
            $p .= "<option value='$i'>$i</option>";
    }

    ?>
    <div class="wrap">
        
        <div id="list-tabs">
          <div id="promotion-sent">
            <center><h4><?php echo $_REQUEST['success']; ?></h4></center>
                <table class="widefat fixed" cellspacing="0">
                    <thead>
					<label>Subscribers Details</label>
                    <div class="alignright actions bulkactions">
                        <form name="f10" action="admin.php">
                        	<input type="hidden" name="page" value="client-listing">
                            <label>Page No. </label>
                            <select name="cpage" onchange='this.form.submit();'>
                                <?php echo $p; ?>
                            </select>	
                        </form>
                    </div>
                    <tr>
                        <th id="cb" class="manage-column column-cb check-column" scope="col">
                        	<input id="cb-select-all-1" style="margin-top:16px;" type="checkbox">
                    	</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Name</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Status</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Company Name</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Contact No</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Total EduCash Earned</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Total EduCash Consumed</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Total EduCash Remaining</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Subscribed Category-Location</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Last activity date</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th id="cb" class="manage-column column-cb check-column" scope="col">
                        	<input id="cb-select-all-1" style="margin-top:16px;" type="checkbox">
                    	</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Name</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Status</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Company Name</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Contact No</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Total EduCash Earned</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Total EduCash Consumed</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Total EduCash Remaining</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Subscribed Category-Location</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Last activity date</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php
					$categories_list = get_terms('listing_categories', array('hide_empty' => false));
					$location_list = get_terms('locations', array('hide_empty' => false));
					$client_educash_helper = new EduCash_Helper();
                    foreach ($leads_datas as $leads_data) {
						# codes...
						$all_cat_loc = '';
						$client_ID_result=$leads_data['id'];
						$category = explode(',', $leads_data['category']);
						$location = explode(',', $leads_data['location']);
						$isactive = $leads_data['is_active'];
						$count = count($category);
						$all_meta_for_user = get_user_meta( $client_ID_result );
						$client_firstname = $all_meta_for_user['user_general_first_name'][0];
						if(empty($client_firstname))
							$client_firstname = $all_meta_for_user['first_name'][0];
						$client_lastname = $all_meta_for_user['user_general_last_name'][0];
						if(empty($client_lastname))
							$client_lastname = $all_meta_for_user['last_name'][0];
						$client_name = $client_firstname." ".$client_lastname;
						$client_companyname = $all_meta_for_user['user_general_company_name'][0];
						if(empty($client_companyname))
							$client_companyname = "N/A";
						$client_phone_number = $all_meta_for_user['user_general_phone'][0];
						if(empty($client_phone_number))
							$client_phone_number = "N/A";
						for ($i = 0; $i < $count-1; $i++) {
									foreach ($categories_list as $cat_value) {
										//echo $categoryString;
									if (strcmp($cat_value->term_id, $category[$i]) == 0 ) {
										$all_cat_loc = $all_cat_loc.$cat_value->name;
										}
									}
									if($location[$i] == ''){
										$all_cat_loc = $all_cat_loc." - N/A,<br>";
										continue;
									}
									foreach ($location_list as $loc_value) {
										if (strcmp($loc_value->term_id, $location[$i]) ==0 ) {
										# code...
										$all_cat_loc = $all_cat_loc." - ".$loc_value->name . ",<br>" ;
										}
									}
						}
						if($all_cat_loc == '')
							$all_cat_loc = "N/A";
						$earned = $client_educash_helper->getEduCashEarned($client_ID_result);
						$consumed = $client_educash_helper->getEduCashConsumed($client_ID_result);
						$lastactivity = $client_educash_helper->getLastActive($client_ID_result);
						$remaining = $earned - $consumed;
                        ?>
                        <tr class="alternate" valign="top">
                            <th class="check-column" scope="row"><input id="cb-select-all-1" type="checkbox" name="check_list[]"
                                                                        value="<?php echo $client_ID_result; ?>"></th>
                            <td class="column-columnname"><?php echo $client_name; ?>
                                <div class="row-actions">
								<span><a href="admin.php?page=client_preferences_page&iid=<?php echo $client_ID_result; ?>" target="_blank">
                                            Edit</a> | </span>
								<?php
									if ($isactive == 0){
								?>
                                    <span><a href="admin.php?page=edugorilla-activate-client&iid=<?php echo $client_ID_result; ?>" target="_blank">
                                            Activate</a> | </span>
								<?php
									}
									else {
								?>
									<span><a href="admin.php?page=edugorilla-deactivate-client&iid=<?php echo $client_ID_result; ?>" target="_blank">
                                            Deactivate</a> | </span>
								<?php
									}
								?>
                            	</div>
                            </td>
							<td class="column-columnname">
							<?php
									if ($isactive == 0){
								?>
                                    Inactive
								<?php
									}
									else {
								?>
									Active
								<?php
									}
								?>
							</td>
                            <td class="column-columnname"><?php echo $client_companyname; ?></td>
                            <td class="column-columnname"><?php echo $client_phone_number; ?></td>
                            <td class="column-columnname"><?php echo $earned; ?></td>
							<td class="column-columnname"><?php echo $consumed; ?></td>
							<td class="column-columnname"><?php echo $remaining; ?></td>
							<td class="column-columnname"><?php echo $all_cat_loc; ?></td>
                            <td class="column-columnname"><?php echo $lastactivity; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
          </div>
        </div>
        
       
        <?php
        /*if ($list_caller == "self") {
            $option = isset($_POST['choice']) ? $_POST['choice'] : false;
            if ($option) {
                $checkbox = $_POST['check_list'];
                for ($i = 0; $i < count($checkbox); $i++) {
                    $del_id = $checkbox[$i];
                    global $wpdb;
                    $result = $wpdb->delete(
                        wp_edugorilla_lead,
                        array('id' => $del_id),
                        array('%d'));
                }
            }
        }*/
        ?>
    </div>
<div id="edugorilla_view_leads" style="display:none;">
	
</div>
    <?php
}

?>
