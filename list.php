<?php
function form_list()
{
    global $wpdb;
//Promotion sent Listing
    $table_name = $wpdb->prefix . 'edugorilla_lead_contact_log ';
    $count_query = $wpdb->get_results("SELECT * FROM $table_name");
    $num_rows = count($count_query); //PHP count()

    $cpage = $_REQUEST['cpage'];
    $list_caller = $_REQUEST['list_caller'];

	if(empty($cpage)) $current_page = 1;
	else $current_page = $cpage;
    
    $page_size = 30;
    if ($num_rows % $page_size == 0)
        $promotion_total_pages = $num_rows / $page_size;
    else
        $promotion_total_pages = intval($num_rows / $page_size) + 1;

    $index = ($current_page - 1) * $page_size;
//end of Promotion send listing

//Leads Listing
    $lead_table = $wpdb->prefix . 'edugorilla_lead_details';
    $leads_query = $wpdb->get_results("SELECT * FROM $lead_table");
    $total_rows = count($leads_query); //counting total rows
    $lead_current_page = $_REQUEST['lead_current_page'];

	if(empty($lead_current_page)) $lead_current_page = 1;
	
    
    if ($total_rows % $page_size == 0)
        $leads_total_pages = $total_rows / $page_size;
    else
        $leads_total_pages = intval($total_rows / $page_size) + 1;

    $lead_index = ($lead_current_page - 1) * $page_size;
//end of Leads listing




    global $wpdb;
    $search_from_date_form = $_POST['search_from_date_form'];
    if ($search_from_date_form == "self") {
        $edugorilla_list_date_from = $_POST['edugorilla_list_date_from'];
        $edugorilla_list_date_to = $_POST['edugorilla_list_date_to'];
        $q = "select * from {$wpdb->prefix}edugorilla_lead_contact_log WHERE (date_time BETWEEN '$edugorilla_list_date_from%' AND '$edugorilla_list_date_to%') order by id desc limit $index, $page_size";
    } else {
        $q = "select * from {$wpdb->prefix}edugorilla_lead_contact_log order by id desc limit $index, $page_size";
    }
    $leads_datas = $wpdb->get_results($q, 'ARRAY_A');

    $p = '';
    for ($i = 1; $i <= $promotion_total_pages; $i++) {
        if ($i == $current_page)
            $p .= "<option value='$i' selected> $i </option>";
        else
            $p .= "<option value='$i'>$i</option>";
    }


	$lead_sent_p = '';
	for ($j = 1; $j <= $leads_total_pages; $j++) {
        if ($j == $lead_current_page)
            $lead_sent_p .= "<option value='$j' selected> $j </option>";
        else
            $lead_sent_p .= "<option value='$j'>$j</option>";
    }
    ?>
    <div class="wrap">
	    <h1>Leads <a href="admin.php?page=edugorilla" class="button button-primary">Add</a></h1>
        
        <div id="list-tabs">
          <ul>
            <li><a href="#leads">Leads</a></li>
            <li><a href="#promotion-sent">Promotion Sent</a></li>
          </ul>
          <div id="promotion-sent">
            <center><h4><?php echo $_REQUEST['success']; ?></h4></center>
                <table class="widefat fixed" cellspacing="0">
                    <thead>
                   
                    <form method="post">
                        <label>Date From</label><input name="edugorilla_list_date_from" id="edugorilla_list_date_from">
                        <label>Date To</label><input name="edugorilla_list_date_to" id="edugorilla_list_date_to">
                        <input type="hidden" name="search_from_date_form" value="self">
                        <input type="submit" class="button action" value="OK">
                    </form>
                    <div class="alignright actions bulkactions">
                        <form name="f10" action="admin.php">
                        	<input type="hidden" name="page" value="Listing">
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
                        <th id="columnname" class="manage-column column-columnname" scope="col">Institute Name</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Flag</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Email/Status</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Email Count(s)</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Date Time</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th id="cb" class="manage-column column-cb check-column" scope="col">
                        	<input id="cb-select-all-1" style="margin-top:16px;" type="checkbox">
                    	</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Institute Name</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Flag</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Email/Status</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Email Count(s)</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Date Time</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php
                    $lead_ids = array();
                    foreach ($leads_datas as $leads_data) {
                        $lead_ids[] = $leads_data['post_id'];
                    }
        
                    foreach ($leads_datas as $leads_data) {
                        $category = "";
                        $term_ids = explode(",", $leads_data['category_id']);
                        $total_terms = count($term_ids);
                        if (!empty($term_ids)) {
                            foreach ($term_ids as $index => $term_id) {
                                $category_data = get_term_by('id', $term_id, 'listing_categories');
                                if ($index == $total_terms - 1)
                                    $category .= $category_data->name;
                                else
                                    $category .= $category_data->name . ",";
                            }
                        }
                        ?>
                        <tr class="alternate" valign="top">
                            <th class="check-column" scope="row"><input id="cb-select-all-1" type="checkbox" name="check_list[]"
                                                                        value="<?php echo $leads_data['id']; ?>"></th>
                            <td class="column-columnname"><?php echo get_the_title($leads_data['post_id']); ?>
                                <div class="row-actions">
                                    <span><a href="post.php?post=<?php echo $leads_data['post_id']; ?>&action=edit">
                                            Edit</a> | </span>
                                    <span><a href="<?php echo get_permalink($leads_data['post_id']); ?>">
                                            View</a> | </span>
                                	 <span><a id="edugorilla_leads_view<?php echo $leads_data['contact_log_id']; ?>" href="#<?php echo $leads_data['contact_log_id']; ?>"  >
                                            View leads</a> </span>
                                </div>
                            </td>
                            <td class="column-columnname"><?php echo (get_post_meta($leads_data['post_id'], 'listing_verified', true) == "on"? "Verified": "Unverified"); ?></td>
                            <td class="column-columnname">
                                <?php
                                $emails_confrms = json_decode($leads_data['email_status'], true);
        
                                $email_count = array_count_values($lead_ids);
                                if (!empty($emails_confrms)) {
                                    foreach ($emails_confrms as $email => $status) {
                                        if ($status == true) $staus = "Success";
                                        else  $staus = "Unsuccess";
                                        echo $email . "/" . $staus . ",<br>";
                                    }
                                }
                                ?>
                            </td>
                            <td class="column-columnname"><?php echo $email_count[$leads_data['post_id']]; ?></td>
                            <td class="column-columnname"><?php echo $leads_data['date_time']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
          </div>
          <div id="leads">
          
          		<table class="widefat fixed" cellspacing="0">
                    <thead>
                    <div class="alignright actions bulkactions">
                        <form name="f9" action="admin.php">
                        	<input type="hidden" name="page" value="Listing">
                            <label>Page No. </label>
                            <select name="lead_current_page" onchange='this.form.submit();'>
                                <?php echo $lead_sent_p; ?>
                            </select>	
                        </form>
                    </div>
                    <tr>
                        <th id="cb" class="manage-column column-cb check-column" scope="col">
                        	<input id="cb-select-all-1" style="margin-top:16px;" type="checkbox">
                    	</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Name</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Contact#</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Email</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Query</th>
                     	<th id="columnname" class="manage-column column-columnname" scope="col">Lead's Category</th>
                    	<th id="columnname" class="manage-column column-columnname" scope="col">Lead's Location</th>
	                    <th id="columnname" class="manage-column column-columnname" scope="col">Added By</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Date Time</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th id="cb" class="manage-column column-cb check-column" scope="col">
                        	<input id="cb-select-all-1" style="margin-top:16px;" type="checkbox">
                    	</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Name</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Contact#</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Email</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Lead's Query</th>
                    	<th id="columnname" class="manage-column column-columnname" scope="col">Lead's Category</th>
                    	<th id="columnname" class="manage-column column-columnname" scope="col">Lead's Location</th>
	                    <th id="columnname" class="manage-column column-columnname" scope="col">Added By</th>
                        <th id="columnname" class="manage-column column-columnname" scope="col">Date Time</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php
                    $q1 = "select * from {$wpdb->prefix}edugorilla_lead_details order by id desc limit $lead_index, $page_size";
                    	$leads_details = $wpdb->get_results($q1, 'ARRAY_A');
						foreach($leads_details as $leads_detail)
                        {
	                        $admin_owner_id   = $leads_detail['admin_id'];
	                        $user_meta_helper = new UserMeta_Helper();
	                        $admin_owner_name = $user_meta_helper->getUserNameForUserId( $admin_owner_id );
 							if(!empty($leads_detail['category_id']))
        					{
        						$category_names = array();
            					$term_ids = explode(",", $leads_detail['category_id']);
            					
            					if (!empty($term_ids)) {
                					foreach ($term_ids as $index => $term_id) {
                  						$category_data = get_term_by('id', $term_id, 'listing_categories');
                   						$category_names[] = $category_data->name;
                					}
                                
            	 					$leads_category = implode(",",$category_names);
             					}else $leads_category = "N/A";
        	
        					}else $leads_category = "N/A";  
        
        					if (!empty($leads_detail['location_id'])) {
        							$location_data = get_term_by('id', $leads_detail['location_id'], 'locations');
        							$leads_location = $location_data->name;
           					 }else
            				{
            					$leads_location = "N/A";
           					 }
                    ?>
                        <tr class="alternate" valign="top">
                            <th class="check-column" scope="row"><input id="cb-select-all-1" type="checkbox" name="check_list[]"
                                                                        value="<?php echo $leads_detail['id']; ?>"></th>
                            <td class="column-columnname"><?php echo  $leads_detail['name']; ?>
                                <div class="row-actions">
                            	 	<span><a href="admin.php?page=edugorilla-edit-lead&iid=<?php echo $leads_detail['id']; ?>">
                                            Edit</a> | </span>
                                    <span><a href="admin.php?page=edugorilla-delete-lead&iid=<?php echo $leads_detail['id']; ?>">
                                            Delete</a> | </span>
                            	</div>
                            </td>
                            <td class="column-columnname"><?php echo $leads_detail['contact_no']; ?></td>
                            <td class="column-columnname"><?php echo $leads_detail['email']; ?></td>
                        	<td class="column-columnname"><?php echo $leads_detail['query']; ?></td>
                        	<th class="manage-column column-columnname"><?php echo $leads_category; ?></th>
                    		<th class="manage-column column-columnname"><?php echo $leads_location; ?></th>
	                        <td class="column-columnname"><?php echo $admin_owner_name; ?></td>
                            <td class="column-columnname"><?php echo $leads_detail['date_time']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
          
          </div>
        </div>
        
       
        <?php
        if ($list_caller == "self") {
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
        }
        ?>
    </div>
<div id="edugorilla_view_leads" style="display:none;">
	
</div>
    <?php
}

function edugorilla_view_leads()
{
	global $wpdb;
	$promotion_id = $_REQUEST['promotion_id'];

	if(!empty($promotion_id))
    {
        $q1 = "select * from {$wpdb->prefix}edugorilla_lead_details where id=$promotion_id ";
$leads_details = $wpdb->get_results($q1, 'ARRAY_A');
$temp_data = array();
foreach($leads_details as $leads_detail)
{
    
    $temp_data['name'] = $leads_detail['name'];
    $temp_data['contact_no'] = $leads_detail['contact_no'];
    $temp_data['email'] = $leads_detail['email'];
    
    if(!empty($leads_detail['category_id']))
    {
        $category_names = array();
        $term_ids = explode(",", $leads_detail['category_id']);
        
        if (!empty($term_ids)) {
            foreach ($term_ids as $index => $term_id) {
                $category_data = get_term_by('id', $term_id, 'listing_categories');
                $category_names[] = $category_data->name;
            }
        
            $leads_category = implode(",",$category_names);
        }else $leads_category = "N/A";

    }else $leads_category = "N/A";  

    if (!empty($leads_detail['location_id'])) 
    {
            $location_data = get_term_by('id', $leads_detail['location_id'], 'locations');
            $leads_location = $location_data->name;
    }
    else
    {
        $leads_location = "N/A";
    }
    
    $temp_data['location'] = $leads_location;
    $temp_data['category'] = $leads_category;
    $temp_data['date_time'] = $leads_detail['date_time'];
   
}

	echo json_encode($temp_data);
    
    exit();
    }
	
}

add_action('wp_ajax_edugorilla_view_leads', 'edugorilla_view_leads');
add_action('wp_ajax_nopriv_edugorilla_view_leads', 'edugorilla_view_leads');

?>
