<?php
/**
 * Created by PhpStorm.
 * User: ananth
 * Date: 14/6/17
 * Time: 12:23 AM
 */

function create_cross_sell_templates() {
	create_SMS_postTypes();
	create_email_postTypes();
}

function create_SMS_postTypes() {
	$labels = array(
		'name'               => 'Edugorilla Cross Sell SMS',
		'singular_name'      => 'EduCrossSMS',
		'menu_name'          => 'EduCrossSellSMS',
		'name_admin_bar'     => 'PromotionalCrossSell',
		'add_new'            => 'Add New SMS Template',
		'add_new_item'       => 'Add New Template',
		'new_item'           => 'New Template',
		'edit'               => 'Edit',
		'edit_item'          => 'Edit Template',
		'view'               => 'View',
		'view_item'          => 'View Template',
		'all_items'          => 'All SMS Templates',
		'search_items'       => 'Search Templates',
		'parent'             => 'Parent SMS Template',
		'parent_item_colon'  => 'Parent Templates',
		'not_found'          => 'No templates found.',
		'not_found_in_trash' => 'No templates found in Trash.'
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'rewrite'       => array( 'slug' => 'crossSellSMSs' ),
		'has_archive'   => true,
		'show_in_menu'  => false,
		'menu_position' => 1,
		'menu_icon'     => 'dashicons-carrot',
		'taxonomies'    => array( 'post_tag', 'category', 'location' ),
		'supports'      => array( 'title', 'editor', 'thumbnail' )
	);
	register_post_type( 'cross_sell_sms', $args );
}

function create_email_postTypes() {
	$labels = array(
		'name'               => 'Edugorilla Cross Sell Email',
		'singular_name'      => 'EduCrossEmail',
		'menu_name'          => 'EduCrossSellEmail',
		'name_admin_bar'     => 'PromotionalCrossSellEmail',
		'add_new'            => 'Add New Email Template',
		'add_new_item'       => 'Add New Email',
		'new_item'           => 'New Template',
		'edit'               => 'Edit',
		'edit_item'          => 'Edit Template',
		'view'               => 'View',
		'view_item'          => 'View Template',
		'all_items'          => 'All Email Templates',
		'search_items'       => 'Search Templates',
		'parent'             => 'Parent Email Template',
		'parent_item_colon'  => 'Parent Templates',
		'not_found'          => 'No templates found.',
		'not_found_in_trash' => 'No templates found in Trash.'
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'rewrite'       => array( 'slug' => 'crossSellEmails' ),
		'has_archive'   => true,
		'show_in_menu'  => false,
		'menu_position' => 2,
		'menu_icon'     => 'dashicons-carrot',
		'taxonomies'    => array( 'post_tag', 'category', 'location' ),
		'supports'      => array( 'title', 'editor', 'thumbnail' )
	);
	register_post_type( 'cross_sell_email', $args );
}


add_action( 'init', 'create_cross_sell_templates' );

?>
<?php
/**
 * Add cross_sell custom fields
 */
function add_cross_sell_meta_boxes() {
	add_meta_box("cross_sell_meta_box", "Listing Details", "cross_sell_meta_box", "cross_sell_email", "normal", "high");
	add_meta_box("cross_sell_meta_box", "Listing Details", "cross_sell_meta_box", "cross_sell_sms", "normal", "high");
}
function cross_sell_meta_box()
{
	global $post;
	$custom = get_post_custom( $post->ID );
 
	?>
	<style>.width99 {width:99%;}</style>
	<p>
		<label>Category:</label><br />
						<select name="category_id[]" multiple id="edugorilla_category"
						        class="">
							<?php
							$temparray = array();
							$categories = get_terms('listing_categories', array('hide_empty' => false));

							foreach ($categories as $category) {
								$temparray[$category->parent][$category->term_id] = $category->name;
							}

							foreach ($temparray as $var => $vals) {
								?>

								<option value="<?php echo $var; ?>">
									<?php
									$d = get_term_by('id', $var, 'listing_categories');
									echo $d->name;
									?>
								</option>

								<?php
								foreach ($vals as $index => $val) {
									?>

									<option value="<?php echo $index; ?>">
										<?php echo $val; ?>
									</option>
									<?php
								}
								?>

								<?php
							}
							?>
						</select>
	</p>
	<p>
		<label>Location:</label><br />
						<select name="location" id="edugorilla_location" class="">
							<option value="">Select</option>
							<?php
							$templocationarray = array();
							$edugorilla_locations = get_terms('locations', array('hide_empty' => false));

							foreach ($edugorilla_locations as $edugorilla_location) {
								$templocationarray[$edugorilla_location->parent][$edugorilla_location->term_id] = $edugorilla_location->name;
							}

							foreach ($templocationarray as $var => $vals) {
								if($var!=0){
								?>

								<option value="<?php echo $var; ?>">
									<?php
									$d = get_term_by('id', $var, 'locations');
									echo $d->name;
									?>
								</option>

								<?php
									foreach ( $vals as $index => $val ) {
										?>
										<!-------Use sub locations here to expand locations------>
										<?php
									}
								}
								else {
								foreach ($vals as $index => $val) {
								if(!array_key_exists($index,$templocationarray)){

								?>

								<option value="<?php echo $index; ?>">
										<?php echo $val; ?>
									</option>

								<?php
								}}
							}
								?>

								<?php
							}
							?>
						</select>
	</p>
	<?php
}
/**
 * Save custom field data when creating/updating posts
 */
function save_email_details_custom_fields(){
  global $post;
 
  if ( $post )
  {
	  $category_id = @$_POST['category_id'];
	  if ( ! empty( $category_id ) ) {
				$category_str = implode( ",", $category_id );
			} else {
				$category_str = "-1";
			}
			$location_id = @$_POST['location'];
	  if (empty($location_id)) $location_id = "-1";
	  
    update_post_meta($post->ID, "categories", $category_str);
    update_post_meta($post->ID, "location",$location_id );
  }
}
add_action( 'admin_init', 'add_cross_sell_meta_boxes' );
add_action( 'save_post', 'save_email_details_custom_fields' );

//Adding custom values to posts table
add_filter('manage_cross_sell_email_posts_columns','filter_csp_columns' ) ;
add_filter('manage_cross_sell_sms_posts_columns','filter_csp_columns' ) ;

function filter_csp_columns( $columns ) {
    $columns['mycategory'] = 'Category Details';
	$columns['mylocation'] = 'Location Details';
    return $columns;
}

//Adding filters to display contents in custom columns
add_action( 'manage_posts_custom_column','action_custom_columns_content', 10, 2 );
function action_custom_columns_content ( $column_id, $post_id ) {
    //run a switch statement for all of the custom columns created
    switch( $column_id ) { 
        case 'mycategory':
            $value = get_post_meta($post_id, 'categories', true );
						if ( ! empty( $value ) ) {
							$category_names = array();
							$term_ids       = explode( ",", $value );

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
						if(empty($leads_category))
							$leads_category = "N/A";
			echo $leads_category;
        break;
		case 'mylocation':
				$value = get_post_meta($post_id, 'location', true );
						if ( ! empty( $value ) ) {
							$location_data  = get_term_by( 'id', $value, 'locations' );
							$leads_location = $location_data->name;
						} else {
							$leads_location = "N/A";
						}
						if(empty($leads_location))
							$leads_location = "N/A";
				echo $leads_location;
        break;
        

   }
}

?>
