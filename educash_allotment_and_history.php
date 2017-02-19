<?php
function allocate_educash_form_page()
{
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
    $users_table = $wpdb->prefix.users;

//Checking if the admin has filled adequate information to submit the form to allot educash and inserting the legal values in table

		if ($_POST['submit']) {
			if (empty($_POST['clientName'])) {
            echo '<script>alert("The field of client email cannot be blank");</script>';
            }
			else {
			if (empty($_POST['educash'])) {
            echo '<script>alert("The field of educash cannot be blank");</script>';
            }
			else {
			$clientName = $_POST['clientName'];
            $check_client = $wpdb->get_var("SELECT COUNT(ID) from $users_table WHERE user_email = '$clientName' ");
            if($check_client == 0){
                echo '<script>alert("This client does not exist in our database");</script>';
            }
			else{
            $educash_added = $_POST['educash'];
            $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientName' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE client_id = '$client_ID_result' ");
            $final_total = $total + $educash_added;
            if($final_total>=0){
            $money = $_POST['money'];
            $adminName = wp_get_current_user();
            $adminComment = $_POST['adminComment'];
            $time = current_time('mysql');
            $wpdb->insert($table_name3, array(
                'time' => $time,
                'admin_id' => $adminName->ID,
                'client_id' => $client_ID_result,
                'transaction' => $educash_added,
                'amount' => $money,
                'comments' => $adminComment
            ));
			
			update_user_meta($client_ID_result, 'user_general_first_name', $_POST['client_firstname']);
			update_user_meta($client_ID_result, 'user_general_last_name', $_POST['client_lastname']);
			update_user_meta($client_ID_result, 'user_address_street_and_number', $_POST['client_street']);
			update_user_meta($client_ID_result, 'user_address_city', $_POST['client_city']);
			update_user_meta($client_ID_result, 'user_address_postal_code', $_POST['client_postalcode']);
			update_user_meta($client_ID_result, 'user_address_county', $_POST['client_state']);
			update_user_meta($client_ID_result, 'user_address_country', $_POST['client_country']);
			
			$all_meta_for_user = get_user_meta( $client_ID_result );
	        $client_firstname = $all_meta_for_user['user_general_first_name'][0];
	        $client_lastname = $all_meta_for_user['user_general_last_name'][0];
	        $client_street = $all_meta_for_user['user_address_street_and_number'][0];
	        $client_city = $all_meta_for_user['user_address_city'][0];
	        $client_postal_code = $all_meta_for_user['user_address_postal_code'][0];
	        $client_state = $all_meta_for_user['user_address_county'][0];
	        $client_country = $all_meta_for_user['user_address_country'][0];
           }
        }
		}
		}
		}

		if ($_POST['SUBMIT']) {
        if (empty($_POST['clientName1'])) {
            $clientnamerr = "<span  style='color:red;'>* This field cannot be blank</span>";
        } else {
			if (empty($_POST['educash1'])) {
            $educasherr = "<span style='color:red;'>* This field cannot be blank</span>";
          }
		    else{
            $clientName = $_POST['clientName1'];
            $check_client = $wpdb->get_var("SELECT COUNT(ID) from $users_table WHERE user_email = '$clientName' ");
             if($check_client == 0){
                $invalid_client = "<span style='color:red'>This client does not exist in our database</span>";
             }
             else {
			 $educash_added = $_POST['educash1'];
			 $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE client_id = '$client_ID_result' ");
             $final_total = $total + $educash_added;
              if($final_total>=0){
			   $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientName' ");
		       $all_meta_for_user = get_user_meta( $client_ID_result );
	           $client_firstname = $all_meta_for_user['user_general_first_name'][0];
	           $client_lastname = $all_meta_for_user['user_general_last_name'][0];
	           $client_street = $all_meta_for_user['user_address_street_and_number'][0];
	           $client_city = $all_meta_for_user['user_address_city'][0];
	           $client_postal_code = $all_meta_for_user['user_address_postal_code'][0];
	           $client_state = $all_meta_for_user['user_address_county'][0];
	           $client_country = $all_meta_for_user['user_address_country'][0];
               }
		}
		}
		}
		}

//Form to allocate educash
?>
<style>
.modalbg {
    display: none;
	position:fixed;
	top:0;
	right:0;
    z-index: 1;
    padding-top: 80px;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-contentbg {
    background-color: #fefefe;
	position:relative;
	margin:auto;
    padding: 0;
    border: 1px solid #888;
    width: 50%;
	height:auto;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

.closebg {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.closebg:hover,
.closebg:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-headerbg {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}

.modal-bodybg {padding: 2px 16px;}
</style>
<script>
    function validate_allotment_form() {
    var x = document.getElementById("clientName11").value;
    var y = document.getElementById("educash11").value;
    var z = document.getElementById("money11").value;
    if (x == "" && (y == "" || y == 0)) {
        document.getElementById('errmsg1').innerHTML = "* This field cannot be blank";
        document.getElementById('errmsg2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (x == "") {
        document.getElementById('errmsg1').innerHTML = "* This field cannot be blank";
        return false;
    }
    if (y == "" || y == 0) {
        document.getElementById('errmsg2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (z < 0) {
        document.getElementById('errmsg3').innerHTML = "* This field cannot be negative";
		return false;
    }
	if(x != "" && y != 0 && z >= 0) {
	    return true;
	}
}
    function validate_final_allotment_form(){
    var x = document.getElementById("clientName22").value;
    var y = document.getElementById("educash22").value;
    var z = document.getElementById("money22").value;
	
    if (x == "" && (y == "" || y == 0)) {
        document.getElementById('errmsgf1').innerHTML = "* This field cannot be blank";
        document.getElementById('errmsgf2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (x == "") {
        document.getElementById('errmsgf1').innerHTML = "* This field cannot be blank";
        return false;
    }
    if (y == "" || y == 0) {
        document.getElementById('errmsgf2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (z < 0) {
        document.getElementById('errmsgf3').innerHTML = "* This field cannot be negative";
		return false;
    }
	for( i = 0; i < 7; i++ ){
		if(document.getElementsByClassName('compulsory_popup_field')[i].value == ""){
			document.getElementsByClassName('compulsory_popup_field_error')[i].innerHTML = "* This field cannot be blank";
			return false;
		}
	}
	if(x != "" && y != 0 && z >= 0){
		return confirm("Are you sure you want to submit this entry ?");
		}
}
</script>
<div id='myModalbg' class="modalbg">
    <div class="modal-contentbg">
    <div class="modal-headerbg">
      <span class="closebg">&times;</span>
      <center><h2>You are about to make the follwing entry:</h2></center>
    </div>
    <div class="modal-bodybg">
<div class="wrap">
<form name="myForm" method='post' onsubmit="return validate_final_allotment_form()" action="<?php echo $_SERVER['REQUEST_URI'];?>">
<table class="form-table">

				<tr>
					<th>Street and number<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' name = 'client_street' class = 'compulsory_popup_field' value = "<?php echo $client_street; ?>" maxlength='100'>
						<span style='color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Client Firstname<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' name = 'client_firstname' class = 'compulsory_popup_field' value = "<?php echo $client_firstname;?>" maxlength='100'>
						<span style='color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
				</tr>
				<tr>
					<th>Postal code<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text'  name = 'client_postalcode' class = 'compulsory_popup_field' value = "<?php echo $client_postal_code; ?>" maxlength='100'>
						<span style='color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Client Lastname<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' name = 'client_lastname' class = 'compulsory_popup_field' value = "<?php echo $client_lastname; ?>" maxlength='100'>
						<span style='color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
				</tr>
				<tr>
					<th>City<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' name = 'client_city' class = 'compulsory_popup_field' value = "<?php echo $client_city; ?>" maxlength='100'>
						<span style='color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Client Email<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' id='clientName22' class='popup_input_field' name='clientName' value = "<?php echo $_POST['clientName1']; ?>" maxlength='100'>
						<span style='color:red;' id='errmsgf1'></span>
					</td>
				</tr>
				<tr>
					<th>State / County<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' name = 'client_state' class = 'compulsory_popup_field' value = "<?php echo $client_state; ?>" maxlength='100'>
						<span style='color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>EduCash (Enter educash to be allotted)<sup><font color="red">*</font></sup></th>
					<td>
						<input type='number' id='educash22' class='popup_input_field' name='educash' min='-100000000' value = "<?php echo $_POST['educash1']; ?>" max='100000000'>
						<span style='color:red;' id='errmsgf2'></span>
					</td>
				</tr>
				<tr>
					<th>Country<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' name = 'client_country' class = 'compulsory_popup_field' value = "<?php echo $client_country; ?>" maxlength='100'>
						<span style='color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Amount (Amount paid by client)</th>
					<td>
						<input type='number' id='money22' class='popup_input_field' name='money' min='0' value = "<?php echo $_POST['money1']; ?>" max='100000000'>
						<span style='color:red;' id='errmsgf3'></span>
					</td>
				</tr>
</table><br/>
<center><b>Comments (optional)</b><br/><textarea rows='4' cols='60' id='adminComment22' class='popup_input_field' name='adminComment' maxlength='500'><?php echo $_POST['adminComment1']; ?></textarea><br/><br/>
						<input type='submit' name='submit'><br/><br/></center>

			
</form>
</div>
    </div>
    </div>
</div>
        <div class="wrap">
		<h1>Use this form to allocate educash to a client</h1>
		
		<form method='post' onsubmit = "return validate_allotment_form()" action="<?php echo $_SERVER['REQUEST_URI'];?>">
			<table class="form-table">
				<tr>
					<th>Client Email<sup><font color="red">*</font></sup></th>
					<td>
						<input type='text' id='clientName11' name='clientName1' value= "<?php echo $_POST['clientName1']; ?>" placeholder = 'Type email here...' maxlength='100'>
						<span style='color:red;' id='errmsg1'></span>
						<span><?php echo $clientnamerr; echo $invalid_client;?></span>
					</td>
				</tr>
				<tr>
					<th>EduCash (Enter educash to be allotted)<sup><font color="red">*</font></sup></th>
					<td>
						<input type='number' id='educash11' name='educash1' min='-100000000' value = "<?php echo $_POST['educash1']; ?>" max='100000000'>
						<span><?php echo $educasherr;?> </span>
                        <span style='color:red;' id='errmsg2'></span>
					</td>
				</tr>
				<tr>
					<th>Amount (Amount paid by client)</th>
					<td>
						<input type='number' id='money11' name='money1' min='0' value = "<?php echo $_POST['money1']; ?>" max='100000000'>
						<span style='color:red;' id='errmsg3'></span>
					</td>
				</tr>
				<tr>
					<th>Comments (optional)</th>
					<td>
                        <textarea rows='4' cols='60' id='adminComment11' name='adminComment1' maxlength='500'><?php echo $_POST['adminComment1']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>
						<input type="hidden">
					</th>
					<td>
						<input type='submit' name='SUBMIT'>
					</td>
				</tr>
			</table>
            </form>
			</div>
<?php
      if ($_POST['SUBMIT']) {
        if ((!empty($_POST['clientName1'])) && (!empty($_POST['educash1'])) && (!($check_client == 0)) && $final_total >= 0) {
			echo "<script>function display_dialogue(){var modal = document.getElementById('myModalbg');
		 modal.style.display = 'block';
         var spanbg = document.getElementsByClassName('closebg')[0];
         spanbg.onclick = function() {
         modal.style.display = 'none';
        }
        window.onclick = function(event) {
        if (event.target == modal) {
        modal.style.display = 'none';
        }
			}}
	    display_dialogue();</script>";
		};
		
		if($final_total < 0){
			   echo "<center><span style='color:red;'>The total balance that the client ".$_POST['clientName']." has
                 is ".$total. ". Your entry will leave this client with negative amount of educash which is not allowed.</span></center>";
		    }
	  }

//Displaying the transaction made just now if the values are legal and sending a mail to respective client otherwise displaying error message

    $client_display_name = $wpdb->get_var("SELECT display_name FROM $users_table WHERE user_email = '$clientName' ");
    if ($_POST['submit'] && (!empty($_POST['clientName'])) && (!empty($_POST['educash'])) && (!($check_client == 0))) {
        if($final_total<0){
           echo "<center><span style='color:red;'>The total balance that the client ".$_POST['clientName']." has
                 is ".$total. ". Your entry will leave this client with negative amount of educash which is not allowed.</span></center>";
        }
        else{
        $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE client_id = '$client_ID_result' ");
        $sum = 0;
            foreach($results as $e) {
                $educash_add = $e->transaction;
                $sum = $sum + $educash_add;
                if($sum<0){$sum = 0;}
            }
        $edugorilla_email_datas = get_option('edugorilla_email_setting2');
        $edugorilla_email_datas2 = get_option('edugorilla_email_setting3');
        $arr1 = array("{Contact_Person}", "{ReceivedCount}", "{EduCashCount}", "{EduCashUrl}");
        $to = $clientName;
        if($educash_added>0){
        $positive_email_subject = $edugorilla_email_datas['subject'];
        $subject =  $positive_email_subject;
        $arr2 = array($client_display_name, $educash_added, $sum, "https://edugorilla.com/");
        $positive_email_body = str_replace($arr1, $arr2, $edugorilla_email_datas['body']);
        $message =  $positive_email_body;

//Creating invoice

        require('pdf_library/invoice_functions.php');
        $pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
        $pdf->AddPage();
        $pdf->Image("https://electronicsguide.000webhostapp.com/wp-content/uploads/2017/01/eg_logo.jpg",10,10,53.898305,60);
        $pdf->right_blocks(70, 25, 20, "EduGorilla");
		$pdf->right_blocks(71, 35, 12, "U74999UP2016PTC088614");
        $pdf->addCompanyAddress("House No. 4719/A,\n".
                                "Sector 23A,\n" .
                                "Gurgaon - 122002,\n".
                                "India.\n" .
                                "hello@edugorilla.com\n\n".
                                "+91 9410007819");
        $pdf->addClientAddress($client_firstname.' '.$client_lastname."\n".
		                       $client_street."\n".
                               $client_city.' - '.$client_postal_code."\n".
                               $client_state."\n" .
                               $client_country);
        $pdf->left_blocks(80, 90, "DATE: "." ".date("Y/m/d"));
        $pdf->left_blocks(80, 100, "AMOUNT IN RUPEES: ".$money."/-");
        $pdf->right_blocks(2, 80, 16, "INVOICE");
        $pdf->right_blocks(1, 90, 12, "Bill To:");
        $cols=array( "ITEM"      => 61,
                     "RATE"      => 43,
                     "QUANTITY"  => 43,
                     "AMOUNT"    => 43,);
        $pdf->addCols( $cols);
        $cols=array( "ITEM"      => "C",
                     "RATE"      => "C",
                     "QUANTITY"  => "C",
                     "AMOUNT"    => "C");
        $pdf->addLineFormat( $cols);
        $pdf->addLineFormat($cols);
        $y    = 157;
		$educash_rate = get_option("current_rate");
        $line = array( "ITEM"      => "EDUCASH",
                       "RATE"      => $educash_rate['rate'],
                       "QUANTITY"  => $educash_added,
                       "AMOUNT"    => "Rs. ".$money."/-");
        $size = $pdf->addLine( $y, $line );
        $y   += $size + 2;
        $pdf->left_blocks(80, 200, "TOTAL: ");
        $pdf->left_blocks(80, 220, "PAYMENT MADE: ");

		$pdf->left_blocks(40, 200, "Rs. ".$money."/-");
        $pdf->left_blocks(40, 220, "Rs. ".$money."/-");
        $pdf->right_blocks(25, 200, 16, "Thanks For Your Business");

		$file_name = sys_get_temp_dir();
		$file_name.= "/invoice.pdf";
		$pdf->Output($file_name , "F");
		$attachment = array($file_name);

		wp_mail( $to, $subject, $message, "Content-type: text/html; charset=iso-8859-1", $attachment);
        }
        else{
        $negative_email_subject = $edugorilla_email_datas2['subject'];
        $subject =  $negative_email_subject;
        $negative_educash = $educash*(-1);
        $arr3 = array($client_display_name, $negative_educash, $sum, "https://edugorilla.com/");
        $negative_email_body = str_replace($arr1, $arr3, $edugorilla_email_datas2['body']);
        $message =  $negative_email_body;
		 wp_mail($to, $subject, $message, "Content-type: text/html; charset=iso-8859-1");
        }

        $r = $wpdb->get_row("SELECT * FROM $table_name3 WHERE time = '$time' ");
        echo "<center></p>You have made the following entry just now:</p>";
        echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
        echo "<tr><td>" . $r->id . "</td><td>" . $adminName->user_email . "</td><td>" . $clientName . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
        echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
        echo "</table></center><br/><br/>";
      }
   }
}
function transaction_history_form_page()
{
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
    $users_table = $wpdb->prefix.users;

//Checking if the admin has filled atleast one field to submit the form to see history

    if ($_POST['Submit']) {
        if (empty($_POST['admin_Name']) && empty($_POST['client_Name']) && empty($_POST['date']) && empty($_POST['date2'])) {
            $all_four_error = "<span style='color:red;'> * All four fields cannot be blank</span>";
        }
    }

//Form to see history of educash transactions
?>
    <div class = "wrap">
    <h1>Use this form to know the history of educash transactions</h1>
    <p style='color:green;'>Fill atleast one field<p>
    <form method='post' action="<?php echo $_SERVER['REQUEST_URI'];?>">
             <table class="form-table">
				<tr>
					<th>Admin Email</th>
					<td>
						<input type='text' name='admin_Name' placeholder = 'Type admin email here...' maxlength='100'>
					</td>
				</tr>
				<tr>
					<th>Client Email</th>
					<td>
						<input type='text' name='client_Name' placeholder = 'Type client email here...' max='100'>
					</td>
				</tr>
				<tr>
					<th>Date From: </th>
					<td>
						<input type='date' name='date' min='1990-12-31' max='2050-12-31'>
					</td>
				</tr>
				<tr>
					<th>Date To: </th>
					<td>
						<input type='date' name='date2' min='1990-12-31' max='2050-12-31'>
					</td>
				</tr>
				<tr>
					<th>
						<input type="hidden">
					</th>
					<td>
						<input type='submit' name='Submit'>
					</td>
				</tr>
				<tr>
					<th>
						<input type="hidden">
					</th>
					<td>
						<?php echo $all_four_error;?>
					</td>
				</tr>
			 </table>
    </form>
<?php
//Displaying the history of required fields

       $admin_Name = $_POST['admin_Name'];
       $client_Name = $_POST['client_Name'];
       $admin_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$admin_Name' ");
       $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$client_Name' ");
       $date = $_POST['date'];
       $date2 = $_POST['date2'];
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && !empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && !empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done from ".$_POST['date']." is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done till ".$_POST['date2']." is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && !empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            echo "<center><span style='color:green;'>Total educash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done from ".$_POST['date']." to ".$_POST['date2']." is:</p>";
            echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
}
?>
