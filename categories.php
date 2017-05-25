<?php
function edu_categories()
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
    
    $page_size = 10;
    if ($num_rows % $page_size == 0)
        $promotion_total_pages = $num_rows / $page_size;
    else
        $promotion_total_pages = intval($num_rows / $page_size) + 1;

    $index = ($current_page - 1) * $page_size;
//end of Promotion send listing
    ?>
	<div style="display: flex;">
<form name="lead_capture_details" method="post" style="display:inline-block;">
    <div style='float:left;'>
	<h2>Add New Category</h2>
			<table class="form-table">
				<tr>
				<th>
					<label for="edu_name">Name</label></br>
						<input id="edu_name" name="name" value="" placeholder="" size="40">
						<font color="red"><?php echo $errors['name']; ?></font>
						<p>The name is how it appears on your site.</p>
				</th>
				</tr>
				<tr>
					<th>
					<label for="edu_slug">Slug</label></br>
						<input id="edu_slug" name="name" value="" placeholder="" size="40">
						<font color="red"><?php echo $errors['name']; ?></font>
						<p>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</p>
				</th>
				</tr>
				<tr>
					<th>
					<label for="tag-description">Description</label></br>
						<textarea name="description" id="tag-description" rows="5" cols="40"></textarea>
						<p>The description is not prominent by default; however, some themes may show it.</p>
					</th>
				</tr>
				<tr>
				<td>

						<a id="save_details_button" class="button button-primary">Send
							Details</a>
					</td>
				</tr>
		</table>
		</div>
		</form>
	<form action="confirm.php" method="post" style="inline-block;">
	<div style="float:right;">
        <h2>Category List </h2>
            <h4><?php echo $_REQUEST['success']; ?></h4>
                <table class="wp-list-table widefat fixed" cellspacing="">
                    <thead>
                   
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
                            </td>
                            <td class="column-columnname"><?php echo $email_count[$leads_data['post_id']]; ?></td>
                            <td class="column-columnname"><?php echo $leads_data['date_time']; ?></td>
                        </tr>
                    
                    </tbody>
                </table>
        </div>
		</form>
	</div>
<div id="edugorilla_view_leads" style="display:none;">
	
</div>
    <?php
}
add_action('wp_ajax_edugorilla_view_leads', 'edugorilla_view_leads');
add_action('wp_ajax_nopriv_edugorilla_view_leads', 'edugorilla_view_leads');

?>
