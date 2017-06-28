<?php
function log_sent_leads()
{

    global $wpdb;
	$temparray  = array();
	$templocationarray    = array();
	$categories = get_terms( 'listing_categories', array( 'hide_empty' => false ) );
	$edugorilla_locations = get_terms( 'locations', array( 'hide_empty' => false ) );
	$lead_table = $wpdb->prefix . 'edugorilla_lead_details';
	$q = "SELECT DISTINCT admin_id from $lead_table";
    $subscriber_ids = $wpdb->get_results($q, 'ARRAY_A');
	$user_meta_helper = new UserMeta_Helper();
	
	foreach ( $categories as $category ) {
		$temparray[ $category->parent ][ $category->term_id ] = $category->name;
	}
	foreach ( $edugorilla_locations as $edugorilla_location ) {
		$templocationarray[ $edugorilla_location->parent ][ $edugorilla_location->term_id ] = $edugorilla_location->name;
	}
//Promotion sent Listing
    $table_name  = $wpdb->prefix . 'edugorilla_lead_contact_log ';
    $search_from_date_form = $_POST['search_from_date_form'];
    $cpage       = $_POST['cpage'];
	if(isset($_POST['btnsubmit'])){
		$cpage=null;}
	else{
	$cpage       = $_POST['cpage'];
	}

	if ( $search_from_date_form == "self" && !empty($_POST['edugorilla_list_date_from']) && !empty($_POST['edugorilla_list_date_to'])) {
		$edugorilla_list_date_from = $_POST['edugorilla_list_date_from'];
		$edugorilla_list_date_to   = $_POST['edugorilla_list_date_to'];
		$q= "select * from $table_name WHERE (date_time BETWEEN '$edugorilla_list_date_from%' AND '$edugorilla_list_date_to%')";
		
    } else {
		$q = "select * from $table_name";
    }
	$count_query = $wpdb->get_results($q);
	$num_rows    = count( $count_query ); //PHP count()
	
    $list_caller = $_REQUEST['list_caller'];

	if ( empty( $cpage ) ) {
		$current_page = 1;
	} else {
		$current_page = $cpage;
	}
    
    $page_size = 30;
	if ( $num_rows % $page_size == 0 ) {
		$promotion_total_pages = $num_rows / $page_size;
	} else {
		$promotion_total_pages = intval( $num_rows / $page_size ) + 1;
	}

	$index = ( $current_page - 1 ) * $page_size;
//end of Promotion send listing

//Leads Listing
	if(!isset($_POST['resetbtn'])){
	$edugorilla_list_date_from2 = $_POST['edugorilla_list_date_from2'];
	$edugorilla_list_date_to2   = $_POST['edugorilla_list_date_to2'];
	$lead_current_page = $_POST['lead_current_page'];
	$location_filter = $_POST['location'];
	$category_values = $_POST['category_id'];
	$added_by = $_POST['subscribers'];
	$name = trim($_POST['name']);
	}else{
	$edugorilla_list_date_from2 = null;
	$edugorilla_list_date_to2   = null;
	$location_filter = null;
	$category_values = null;
	$added_by = null;
	$name = null;
	}
		//checking for empty variables
			if ( ! empty( $category_values ) ) {
				$category_filter = implode( ",", $category_values );
			} else {
				$category_filter = "-1";
			}
			if ( empty( $location_filter ) ) {
				$location_filter = "-1";
			}
			if (empty($added_by)){
			$added_by = -1;
			}
			if (empty($edugorilla_list_date_from2 )){
			$edugorilla_list_date_from2="-1";}
			if( empty($edugorilla_list_date_to2)){
			$edugorilla_list_date_to2="-1";}
			
	if(isset($_POST['btnsubmit2'])  || isset($_POST['resetbtn'])){
		$lead_current_page=null;}
	else{
		$lead_current_page       = $_POST['lead_current_page'];
	}
		//query for calculating graph datas
$qry= "SELECT COUNT(`id`) leads, DATE_FORMAT(`date_time`, '%Y-%m-%d') dates FROM $lead_table WHERE 
IF($added_by = -1 , 1=1 , admin_id = $added_by) AND 
IF('$location_filter' = '-1' , 1=1 , location_id = '$location_filter') AND 
IF('$category_filter' = '-1' , 1=1 , category_id LIKE '%$category_filter%') AND 
IF('$name' = '' , 1=1 , name = '$name') AND 
(date_time BETWEEN IF('$edugorilla_list_date_from2' = '-1' , TRUE , '$edugorilla_list_date_from2%') AND IF('$edugorilla_list_date_to2' = '-1' , '2050-12-31%' , '$edugorilla_list_date_to2%')) GROUP BY dates";

$leads_details = $wpdb->get_results( $qry, 'ARRAY_A');
	$temp_data     = array();
		foreach ( $leads_details as $leads_detail ) {
			$dates = explode("-",$leads_detail['dates']);
			$temp_data[] = array('count' => (int)$leads_detail['leads'],
								 'year'  => (int)$dates[0],
								 'month' => (int)$dates[1] -1,
								 'day'	=> (int)$dates[2]);
		}
	//query for calculating graph datas ends
	
	//query including filters for counting
$q= "SELECT * FROM $lead_table WHERE 
IF($added_by = -1 , 1=1 , admin_id = $added_by) AND 
IF('$location_filter' = '-1' , 1=1 , location_id = '$location_filter') AND 
IF('$category_filter' = '-1' , 1=1 , category_id LIKE '%$category_filter%') AND 
IF('$name' = '' , 1=1 , name = '$name') AND 
(date_time BETWEEN IF('$edugorilla_list_date_from2' = '-1' , TRUE , '$edugorilla_list_date_from2%') AND IF('$edugorilla_list_date_to2' = '-1' , '2050-12-31%' , '$edugorilla_list_date_to2%')) ORDER BY id DESC";
	
	//$q = "select * from $lead_table";
	
	$leads_query       = $wpdb->get_results( $q );
	$all_leads_datas = $wpdb->get_results( $q, 'ARRAY_A' );
	$total_rows        = count( $leads_query ); //counting total rows
	if(isset($_POST['download'])){
	leads_csv_download($all_leads_datas);}
    
	if ( empty( $lead_current_page ) ) {
		$lead_current_page = 1;
	}


	if ( $total_rows % $page_size == 0 ) {
		$leads_total_pages = $total_rows / $page_size;
	} else {
		$leads_total_pages = intval( $total_rows / $page_size ) + 1;
	}

	$lead_index = ( $lead_current_page - 1 ) * $page_size;
//end of Leads listing


    global $wpdb;
	//Promotion sent Listing Datas
	if ( $search_from_date_form == "self" && !empty($_POST['edugorilla_list_date_from']) && !empty($_POST['edugorilla_list_date_to'])) {
		$edugorilla_list_date_from = $_POST['edugorilla_list_date_from'];
		$edugorilla_list_date_to   = $_POST['edugorilla_list_date_to'];
		$q                         = "select * from {$wpdb->prefix}edugorilla_lead_contact_log WHERE (date_time BETWEEN '$edugorilla_list_date_from%' AND '$edugorilla_list_date_to%') order by id desc limit $index, $page_size";
    } else {
		$q = "select * from {$wpdb->prefix}edugorilla_lead_contact_log order by id desc limit $index, $page_size";
    }
	$leads_datas = $wpdb->get_results( $q, 'ARRAY_A' );
	//end of Promotion send listing Datas
	
	//Leads Listing Datas
	$q1 = "SELECT * FROM $lead_table WHERE 
IF($added_by = -1 , 1=1 , admin_id = $added_by) AND 
IF('$location_filter' = '-1' , 1=1 , location_id = '$location_filter') AND 
IF('$category_filter' = '-1' , 1=1 , category_id LIKE '%$category_filter%') AND 
IF('$name' = '' , 1=1 , name = '$name') AND 
(date_time BETWEEN IF('$edugorilla_list_date_from2' = '-1' , TRUE , '$edugorilla_list_date_from2%') AND IF('$edugorilla_list_date_to2' = '-1' , '2050-12-31%' , '$edugorilla_list_date_to2%')) 
ORDER BY id DESC LIMIT $lead_index, $page_size";

		$leads_details = $wpdb->get_results( $q1, 'ARRAY_A' );
	//end of Leads listing Datas
	
    $p = '';
	for ( $i = 1; $i <= $promotion_total_pages; $i ++ ) {
		if ( $i == $current_page ) {
			$p .= "<option value='$i' selected> $i </option>";
		} else {
			$p .= "<option value='$i'>$i</option>";
		}
    }


	$lead_sent_p = '';
	for ( $j = 1; $j <= $leads_total_pages; $j ++ ) {
		if ( $j == $lead_current_page ) {
			$lead_sent_p .= "<option value='$j' selected> $j </option>";
		} else {
			$lead_sent_p .= "<option value='$j'>$j</option>";
		}
	}
    ?>
	<div class="wrap">
		<h1>Logs of Leads Sent till now <a href="admin.php?page=edugorilla" class="button button-primary">Add</a></h1>

		<div id="list-tabs">
			<ul>
				<li><a href="#leads">Sent Leads</a></li>
				<li><a href="#promotion-sent">Sent Promotions To</a></li>
			</ul>
			<div id="promotion-sent">
				<center><h4><?php echo $_REQUEST['success']; ?></h4></center>
				<table class="widefat fixed" cellspacing="0">
					<thead>

					<form method="post">
						<label>Date From</label><input name="edugorilla_list_date_from" id="edugorilla_list_date_from" value="<?php echo $edugorilla_list_date_from; ?>">
						<label>Date To</label><input name="edugorilla_list_date_to" id="edugorilla_list_date_to" value="<?php echo $edugorilla_list_date_to; ?>">
						<input type="hidden" name="search_from_date_form" value="self">
						<input type="submit" name="btnsubmit" class="button action" value="OK">
					
					<div class="alignright actions bulkactions">
							<label>Page No. </label>
							<select name="cpage" onchange='this.form.submit();'>
								<?php echo $p; ?>
							</select>
						
					</div>
					</form>
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
					foreach ( $leads_datas as $leads_data ) {
						$lead_ids[] = $leads_data['post_id'];
					}

					foreach ( $leads_datas as $leads_data ) {
						$category    = "";
						$term_ids    = explode( ",", $leads_data['category_id'] );
						$total_terms = count( $term_ids );
						if ( ! empty( $term_ids ) ) {
							foreach ( $term_ids as $index => $term_id ) {
								$category_data = get_term_by( 'id', $term_id, 'listing_categories' );
								if ( $index == $total_terms - 1 ) {
									$category .= $category_data->name;
								} else {
									$category .= $category_data->name . ",";
								}
							}
						}
						?>
						<tr class="alternate" valign="top">
							<th class="check-column" scope="row"><input id="cb-select-all-1" type="checkbox"
							                                            name="check_list[]"
							                                            value="<?php echo $leads_data['id']; ?>"></th>
							<td class="column-columnname"><?php echo get_the_title( $leads_data['post_id'] ); ?>
								<div class="row-actions">
                                    <span><a href="post.php?post=<?php echo $leads_data['post_id']; ?>&action=edit">
                                            Edit</a> | </span>
									<span><a href="<?php echo get_permalink( $leads_data['post_id'] ); ?>">
                                            View</a> | </span>
									<span><a id="edugorilla_leads_view<?php echo $leads_data['contact_log_id']; ?>"
									         href="#<?php echo $leads_data['contact_log_id']; ?>">
                                            View leads</a> </span>
								</div>
							</td>
							<td class="column-columnname"><?php echo( get_post_meta( $leads_data['post_id'], 'listing_verified', true ) == "on" ? "Verified" : "Unverified" ); ?></td>
							<td class="column-columnname">
								<?php
								$emails_confrms = json_decode( $leads_data['email_status'], true );

								$email_count = array_count_values( $lead_ids );
								if ( ! empty( $emails_confrms ) ) {
									foreach ( $emails_confrms as $email => $status ) {
										if ( $status == true ) {
											$staus = "Success";
										} else {
											$staus = "Unsuccess";
										}
										echo $email . "/" . $staus . ",<br>";
									}
								}
								?>
							</td>
							<td class="column-columnname"><?php echo $email_count[ $leads_data['post_id'] ]; ?></td>
							<td class="column-columnname"><?php echo $leads_data['date_time']; ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
			<div id="leads">

				<table class="widefat fixed" cellspacing="0">
					<thead>
					<form name="lead-form" method="post">
						<label>Date From</label><input type="date" name="edugorilla_list_date_from2" id="edugorilla_list_date_from2" value="<?php echo $edugorilla_list_date_from2; ?>">
						<label>Date To</label><input type="date" name="edugorilla_list_date_to2" id="edugorilla_list_date_to2" value="<?php echo $edugorilla_list_date_to2; ?>">
					
							&nbsp;
							<select name="subscribers">
								<option value="">Added By Any</option>
								<?php
								foreach ($subscriber_ids as $subscriber_id) {
									$cid=$subscriber_id['admin_id'];
								$client_name = $user_meta_helper->getUserNameForUserId( $cid );
								if ( $cid == $added_by ) {
									?>
							<option value="<?php echo $cid; ?>" selected> <?php echo $client_name; ?></option>
							<?php
								} else {
							?>
							<option value="<?php echo $cid; ?>"><?php echo $client_name; ?></option>
							<?php
							}
							}
							?>
							</select>
							&nbsp;
					<select name="category_id[]" multiple id="edugorilla_category">
							<?php

							foreach ( $temparray as $var => $vals ) {
								if($var != 0){
								?>
								<?php if ( in_array( $var, $category_values ) ) { ?>
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
									<?php if ( in_array( $index, $category_values ) ) { ?>
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
								} else {
								foreach ($vals as $index => $val) {
								if(!array_key_exists($index,$temparray)){
								?>
									<?php if ( in_array( $index, $category_values ) ) { ?>
										<option value="<?php echo $index; ?>" selected>
											<?php echo $val; ?>
										</option>
									<?php } else { ?>
										<option value="<?php echo $index; ?>">
											<?php echo $val; ?>
										</option>
									<?php } ?>
									<?php
								} } }
								?>
								<?php
							}
							?>
						</select>
						&nbsp;
						<select name="location" id="edugorilla_location">
							<option value="">All Locations</option>
							<?php
							foreach ( $templocationarray as $var => $vals ) {
								if($var != 0){
								?>
								<?php if ( $var == $location_filter ) { ?>
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
								//Use bellow code to expand for sub locations
								/*foreach ( $vals as $index => $val ) {
									?>
									<?php if ( $location_filter == $index ) { ?>
										<option value="<?php echo $index; ?>" selected>
											<?php echo "->" . $val; ?>
										</option>
									<?php } else { ?>
										<option value="<?php echo $index; ?>">
											<?php echo "->" . $val; ?>
										</option>
									<?php } ?>
									<?php
								}*/
								} else {
								foreach ($vals as $index => $val) {
								if(!array_key_exists($index,$templocationarray)){
								?>
									<?php if ( $location_filter == $index ) { ?>
										<option value="<?php echo $index; ?>" selected>
											<?php echo $val; ?>
										</option>
									<?php } else { ?>
										<option value="<?php echo $index; ?>">
											<?php echo $val; ?>
										</option>
									<?php } ?>
									<?php
								} } }
								?>

								<?php
							}
							?>
						</select>
						<input id="edu_name" name="name" value="<?php echo $name; ?>" placeholder="Enter Leads Name...">
						
						&nbsp;
						<input type="hidden" name="search_from_date_form2" value="self">
						<input type="submit" name="btnsubmit2" class="button action" value="Filter">&nbsp;
						<input type="submit" name="resetbtn" class="button action" value="Reset Filters">
						<label>&nbsp;&nbsp;&nbsp;<b>: <?php echo $total_rows;?> Leads Found</b></label>
						<div id="chart_div"></div>
						<div class="alignright actions bulkactions">
						<input type="button" name="show_map" id="show_map" value="Show Graph">
						<input type="submit" name="download" class="button action" value="Download Logs">
							<label>Page No. </label>
							<select name="lead_current_page" onchange='this.form.submit();'>
								<?php echo $lead_sent_p; ?>
							</select>
						
					</div>
					</form>
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
					
					foreach ( $leads_details as $leads_detail ) {
						$admin_owner_id   = $leads_detail['admin_id'];
						$admin_owner_name = $user_meta_helper->getUserNameForUserId( $admin_owner_id );
						if ( ! empty( $leads_detail['category_id'] ) ) {
							$category_names = array();
							$term_ids       = explode( ",", $leads_detail['category_id'] );

							if ( ! empty( $term_ids ) ) {
								foreach ( $term_ids as $index => $term_id ) {
									$category_data    = get_term_by( 'id', $term_id, 'listing_categories' );
									$category_names[] = $category_data->name;
								}

								$leads_category = implode( ",", $category_names );
							} else {
								$leads_category = "N/A";
							}

						} else {
							$leads_category = "N/A";
						}

						if ( ! empty( $leads_detail['location_id'] ) ) {
							$location_data  = get_term_by( 'id', $leads_detail['location_id'], 'locations' );
							$leads_location = $location_data->name;
						} else {
							$leads_location = "N/A";
						}
						?>
						<tr class="alternate" valign="top">
							<th class="check-column" scope="row"><input id="cb-select-all-1" type="checkbox"
							                                            name="check_list[]"
							                                            value="<?php echo $leads_detail['id']; ?>"></th>
							<td class="column-columnname"><?php echo $leads_detail['name']; ?>
								<div class="row-actions">
                            	 	<span><a
			                                href="admin.php?page=edumatch-main-screen&screenType=leadEditOption&iid=<?php echo $leads_detail['id']; ?>">
                                            Edit</a> | </span>
									<span><a
											href="admin.php?page=edugorilla-delete-lead&iid=<?php echo $leads_detail['id']; ?>">
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
		<-- Showing graph -->
	 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	 <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	 <script>
	 function drawChart() {
		 var jsonData = <?php echo json_encode($temp_data); ?>;
			//console.log(jsonData);

						var data = new google.visualization.DataTable();
						data.addColumn('date', 'X');
						data.addColumn('number', 'leads');
						var data_array = [];
						 for (var i = 0; i < jsonData.length; i++) {
							data_array[i] = [new Date(jsonData[i]['year'],jsonData[i]['month'],jsonData[i]['day']), Number(jsonData[i]['count'])];
						}
						data.addRows(data_array);

						var options = {
							hAxis: {
								title: 'Time'
							},
							vAxis: {
								title: 'Number of Leads',
								format: '#',
							}
							};
						// Instantiate and draw our chart, passing in some options.
						var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
						chart.draw(data, options);
    }
		$(document).on('click', '#show_map', function () {
		google.charts.load('current', {'packages':['corechart', 'line']});
		google.charts.setOnLoadCallback(drawChart);
		});

	 </script>

		<?php
		if ( $list_caller == "self" ) {
			$option = isset( $_POST['choice'] ) ? $_POST['choice'] : false;
			if ( $option ) {
				$checkbox = $_POST['check_list'];
				for ( $i = 0; $i < count( $checkbox ); $i ++ ) {
					$del_id = $checkbox[ $i ];
					global $wpdb;
					$result = $wpdb->delete(
						wp_edugorilla_lead,
						array( 'id' => $del_id ),
						array( '%d' ) );
				}
			}
		}
		?>
	</div>
<div id="edugorilla_view_leads" style="display:none;">

</div>
	<?php
}

function edugorilla_view_leads() {
	global $wpdb;
	$promotion_id = $_REQUEST['promotion_id'];

	if ( ! empty( $promotion_id ) ) {
		$q1            = "select * from {$wpdb->prefix}edugorilla_lead_details where id=$promotion_id ";
		$leads_details = $wpdb->get_results( $q1, 'ARRAY_A' );
		$temp_data     = array();
		foreach ( $leads_details as $leads_detail ) {

			$temp_data['name']       = $leads_detail['name'];
			$temp_data['contact_no'] = $leads_detail['contact_no'];
			$temp_data['email']      = $leads_detail['email'];

			if ( ! empty( $leads_detail['category_id'] ) ) {
				$category_names = array();
				$term_ids       = explode( ",", $leads_detail['category_id'] );

				if ( ! empty( $term_ids ) ) {
					foreach ( $term_ids as $index => $term_id ) {
						$category_data    = get_term_by( 'id', $term_id, 'listing_categories' );
						$category_names[] = $category_data->name;
					}

					$leads_category = implode( ",", $category_names );
				} else {
					$leads_category = "N/A";
				}

			} else {
				$leads_category = "N/A";
			}

			if ( ! empty( $leads_detail['location_id'] ) ) {
				$location_data  = get_term_by( 'id', $leads_detail['location_id'], 'locations' );
				$leads_location = $location_data->name;
			} else {
				$leads_location = "N/A";
			}

			$temp_data['location']  = $leads_location;
			$temp_data['category']  = $leads_category;
			$temp_data['date_time'] = $leads_detail['date_time'];

		}

		echo json_encode( $temp_data );

		exit();
	}

}

add_action( 'wp_ajax_edugorilla_view_leads', 'edugorilla_view_leads' );
add_action( 'wp_ajax_nopriv_edugorilla_view_leads', 'edugorilla_view_leads' );

function leads_csv_download($data, $filename = "LeadLogs.csv", $delimiter=",") {
	if(!$data) return false;
    ob_end_clean();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    $f = fopen('php://output', 'w');
	$user_meta_helper = new UserMeta_Helper();
	$headrow = array('Name','Contact','Email','Query','Category','Location','Added By','Date');
    fputcsv($f, $headrow, $delimiter);
		foreach ($data as $leads_detail) {
						$admin_owner_id   = $leads_detail['admin_id'];
						$admin_owner_name = $user_meta_helper->getUserNameForUserId( $admin_owner_id );
						if ( ! empty( $leads_detail['category_id'] ) ) {
							$category_names = array();
							$term_ids       = explode( ",", $leads_detail['category_id'] );

							if ( ! empty( $term_ids ) ) {
								foreach ( $term_ids as $index => $term_id ) {
									$category_data    = get_term_by( 'id', $term_id, 'listing_categories' );
									$category_names[] = $category_data->name;
								}

								$leads_category = html_entity_decode(implode( ",", $category_names ));
							} else {
								$leads_category = "N/A";
							}

						} else {
							$leads_category = "N/A";
						}

						if ( ! empty( $leads_detail['location_id'] ) ) {
							$location_data  = get_term_by( 'id', $leads_detail['location_id'], 'locations' );
							$leads_location = html_entity_decode($location_data->name);
						} else {
							$leads_location = "N/A";
						}
		$line = array(
				'Name'      => $leads_detail['name'],
				'Contact'   => $leads_detail['contact_no'],
				'Email'     => $leads_detail['email'],
				'Query'     => $leads_detail['query'],
				'Category'  => $leads_category,
				'Location'  => $leads_location,
				'Added_By'  => $admin_owner_name,
				'Date'      => $leads_detail['date_time']
			);
        fputcsv($f, $line, $delimiter);
    }
	fclose($f);
	exit();
}

?>
