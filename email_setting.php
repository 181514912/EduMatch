<?php
function edugorilla_email_setting()
{
?>
    <div class="wrap">
        <h1>Promotional Email Template</h1>
     
        <div id="tabs">
          <ul>
              <li><a href="#tabs-lead-received">Lead Received</a></li>
              <li><a href="#tabs-educash-added">EduCash Added</a></li>
              <li><a href="#tabs-educash-deducted">EduCash Deducted</a></li>
              <li><a href="#tabs-instant-email">Instant Email</a></li>
              <li><a href="#tabs-daily-digest-email">Daily Digest Email</a></li>
              <li><a href="#tabs-4">Tab 4</a></li>
              <li><a href="#tabs-5">Tab 5</a></li>
          </ul>
            <div id="tabs-lead-received">
            <?php
                $email_setting_form1 = $_POST['email_setting_form1'];
                if ($email_setting_form1 == "self") {
                    $errors1 = array();
                    $edugorilla_email_subject1 = $_POST['edugorilla_subject1'];
                    $edugorilla_email_body1 = $_POST['edugorilla_body1'];
                    if (empty($edugorilla_email_subject1)) $errors1['edugorilla_subject1'] = "Empty";
            
                    if (empty($edugorilla_email_body1)) $errors1['edugorilla_body1'] = "Empty";
            
                    if (empty($errors1)) {
                        $edugorilla_email_setting1 = array('subject' => stripslashes($edugorilla_email_subject1), 'body' => stripslashes($edugorilla_email_body1));
            
                        update_option("edugorilla_email_setting1", $edugorilla_email_setting1);
                        $success1 = "Email Settings Saved Successfully.";
                    	$email_setting_options1 = get_option('edugorilla_email_setting1');
            
                    	$edugorilla_email_subject1 = stripslashes($email_setting_options1['subject']);
            
                   		$edugorilla_email_body1 = stripslashes($email_setting_options1['body']);
                    }
                } else {
                    $email_setting_options1 = get_option('edugorilla_email_setting1');
            
                    $edugorilla_email_subject1 = stripslashes($email_setting_options1['subject']);
            
                    $edugorilla_email_body1 = stripslashes($email_setting_options1['body']);
            
                }
     
                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Subject <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="edugorilla_subject1" value="<?php echo stripslashes($edugorilla_email_subject1); ?>"
                                       placeholder="Type Email Subject here...">
                                <font color="red"><?php echo $errors1['edugorilla_subject1']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Body template<sup><font color="red">*</font></sup></th>
                            <td>
                               <?php
									$content = $edugorilla_email_body1;
									$editor_id = 'edugorilla_body1';

									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors1['edugorilla_body1']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="email_setting_form1" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
            <div id="tabs-educash-added">
            <?php
            
                $email_setting_form2 = $_POST['email_setting_form2'];
                if ($email_setting_form2 == "self") {
                    $errors2 = array();
                    $edugorilla_email_subject2 = $_POST['edugorilla_subject2'];
                    $edugorilla_email_body2 = $_POST['edugorilla_body2'];
                    if (empty($edugorilla_email_subject2)) $errors2['edugorilla_subject2'] = "Empty";
            
                    if (empty($edugorilla_email_body2)) $errors2['edugorilla_body2'] = "Empty";
            
                    if (empty($errors2)) {
                        $edugorilla_email_setting2 = array('subject' => stripslashes($edugorilla_email_subject2), 'body' => stripslashes($edugorilla_email_body2));
            
                        update_option("edugorilla_email_setting2", $edugorilla_email_setting2);
                        $success2 = "Email Settings Saved Successfully.";
                    
                    	$email_setting_options2 = get_option('edugorilla_email_setting2');
                    	$edugorilla_email_subject2 = stripslashes($email_setting_options2['subject']);
                    	$edugorilla_email_body2 = stripslashes($email_setting_options2['body']);
                    }
                } else {
                    $email_setting_options2 = get_option('edugorilla_email_setting2');
                    $edugorilla_email_subject2 = stripslashes($email_setting_options2['subject']);
                    $edugorilla_email_body2 = stripslashes($email_setting_options2['body']);
            
                }
                
                if ($success2) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success2; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Subject <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="edugorilla_subject2" value="<?php echo $edugorilla_email_subject2; ?>"
                                       placeholder="Type Email Subject here...">
                                <font color="red"><?php echo $errors2['edugorilla_subject2']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Body template<sup><font color="red">*</font></sup></th>
                            <td>
                                 <?php
									$content = $edugorilla_email_body2;
									$editor_id = 'edugorilla_body2';

									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors2['edugorilla_body2']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="email_setting_form2" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
            <div id="tabs-educash-deducted">
            <?php
                $email_setting_form3 = $_POST['email_setting_form3'];
                if ($email_setting_form3 == "self") {
                    $errors3 = array();
                    $edugorilla_email_subject3 = $_POST['edugorilla_subject3'];
                    $edugorilla_email_body3 = $_POST['edugorilla_body3'];
                    if (empty($edugorilla_email_subject3)) $errors3['edugorilla_subject3'] = "Empty";
            
                    if (empty($edugorilla_email_body3)) $errors3['edugorilla_body3'] = "Empty";
            
                    if (empty($errors3)) {
                        $edugorilla_email_setting3 = array('subject' => stripslashes($edugorilla_email_subject3), 'body' => stripslashes($edugorilla_email_body3));
            
                        update_option("edugorilla_email_setting3", $edugorilla_email_setting3);
                        $success3 = "Email Settings Saved Successfully.";
                    	$email_setting_options3 = get_option('edugorilla_email_setting3');
                    	$edugorilla_email_subject3 = stripslashes($email_setting_options3['subject']);
                    	$edugorilla_email_body3 = stripslashes($email_setting_options3['body']);
                    }
                } else {
                    $email_setting_options3 = get_option('edugorilla_email_setting3');
                    $edugorilla_email_subject3 = stripslashes($email_setting_options3['subject']);
                    $edugorilla_email_body3 = stripslashes($email_setting_options3['body']);
            
                }
                
                if ($success3) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success3; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Subject <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="edugorilla_subject3" value="<?php echo $edugorilla_email_subject3; ?>"
                                       placeholder="Type Email Subject here...">
                                <font color="red"><?php echo $errors3['edugorilla_subject3']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Body template<sup><font color="red">*</font></sup></th>
                            <td>
                                 <?php
									$content = $edugorilla_email_body3;
									$editor_id = 'edugorilla_body3';
									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors3['edugorilla_body3']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="email_setting_form3" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
            <div id="tabs-instant-email">
                <?php
                $email_setting_form1 = $_POST['email_setting_form1'];
                if ($email_setting_form1 == "self") {
                    $errors1 = array();
                    $edugorilla_email_subject1 = $_POST['edugorilla_subject1'];
                    $edugorilla_email_body1 = $_POST['edugorilla_body1'];
                    if (empty($edugorilla_email_subject1)) $errors1['edugorilla_subject1'] = "Empty";

                    if (empty($edugorilla_email_body1)) $errors1['edugorilla_body1'] = "Empty";

                    if (empty($errors1)) {
                        $edugorilla_email_setting1 = array('subject' => stripslashes($edugorilla_email_subject1), 'body' => stripslashes($edugorilla_email_body1));

                        update_option("edugorilla_email_setting1", $edugorilla_email_setting1);
                        $success1 = "Email Settings Saved Successfully.";
                        $email_setting_options1 = get_option('edugorilla_email_setting1');

                        $edugorilla_email_subject1 = stripslashes($email_setting_options1['subject']);

                        $edugorilla_email_body1 = stripslashes($email_setting_options1['body']);
                    }
                } else {
                    $email_setting_options1 = get_option('edugorilla_email_setting1');

                    $edugorilla_email_subject1 = stripslashes($email_setting_options1['subject']);

                    $edugorilla_email_body1 = stripslashes($email_setting_options1['body']);

                }

                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
                ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Subject <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="edugorilla_subject1"
                                       value="<?php echo stripslashes($edugorilla_email_subject1); ?>"
                                       placeholder="Type Email Subject here...">
                                <font color="red"><?php echo $errors1['edugorilla_subject1']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Body template<sup><font color="red">*</font></sup></th>
                            <td>
                                <?php
                                $content = $edugorilla_email_body1;
                                $editor_id = 'edugorilla_body1';

                                wp_editor($content, $editor_id);
                                ?>
                                <font color="red"><?php echo $errors1['edugorilla_body1']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="email_setting_form1" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div id="tabs-daily-digest-email">
                <?php
                $email_setting_form1 = $_POST['email_setting_form1'];
                if ($email_setting_form1 == "self") {
                    $errors1 = array();
                    $edugorilla_email_subject1 = $_POST['edugorilla_subject1'];
                    $edugorilla_email_body1 = $_POST['edugorilla_body1'];
                    if (empty($edugorilla_email_subject1)) $errors1['edugorilla_subject1'] = "Empty";

                    if (empty($edugorilla_email_body1)) $errors1['edugorilla_body1'] = "Empty";

                    if (empty($errors1)) {
                        $edugorilla_email_setting1 = array('subject' => stripslashes($edugorilla_email_subject1), 'body' => stripslashes($edugorilla_email_body1));

                        update_option("edugorilla_email_setting1", $edugorilla_email_setting1);
                        $success1 = "Email Settings Saved Successfully.";
                        $email_setting_options1 = get_option('edugorilla_email_setting1');

                        $edugorilla_email_subject1 = stripslashes($email_setting_options1['subject']);

                        $edugorilla_email_body1 = stripslashes($email_setting_options1['body']);
                    }
                } else {
                    $email_setting_options1 = get_option('edugorilla_email_setting1');

                    $edugorilla_email_subject1 = stripslashes($email_setting_options1['subject']);

                    $edugorilla_email_body1 = stripslashes($email_setting_options1['body']);

                }

                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
                ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Subject <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="edugorilla_subject1"
                                       value="<?php echo stripslashes($edugorilla_email_subject1); ?>"
                                       placeholder="Type Email Subject here...">
                                <font color="red"><?php echo $errors1['edugorilla_subject1']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Body template<sup><font color="red">*</font></sup></th>
                            <td>
                                <?php
                                $content = $edugorilla_email_body1;
                                $editor_id = 'edugorilla_body1';

                                wp_editor($content, $editor_id);
                                ?>
                                <font color="red"><?php echo $errors1['edugorilla_body1']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="email_setting_form1" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
         <div id="tabs-4">
            <?php
                $email_setting_form4 = $_POST['email_setting_form4'];
                if ($email_setting_form4 == "self") {
                    $errors4 = array();
                    $edugorilla_email_subject4 = $_POST['edugorilla_subject4'];
                    $edugorilla_email_body4 = $_POST['edugorilla_body4'];
                    if (empty($edugorilla_email_subject4)) $errors4['edugorilla_subject4'] = "Empty";
            
                    if (empty($edugorilla_email_body4)) $errors4['edugorilla_body4'] = "Empty";
            
                    if (empty($errors4)) {
                        $edugorilla_email_setting4 = array('subject' => stripslashes($edugorilla_email_subject4), 'body' => stripslashes($edugorilla_email_body4));
            
                        update_option("edugorilla_email_setting4", $edugorilla_email_setting4);
                        $success4 = "Email Settings Saved Successfully.";
                    	$email_setting_options4 = get_option('edugorilla_email_setting4');
                    	$edugorilla_email_subject4 = stripslashes($email_setting_options4['subject']);
                    	$edugorilla_email_body4 = stripslashes($email_setting_options4['body']);
                    }
                } else {
                    $email_setting_options4 = get_option('edugorilla_email_setting4');
                    $edugorilla_email_subject4 = stripslashes($email_setting_options4['subject']);
                    $edugorilla_email_body4 = stripslashes($email_setting_options4['body']);
            
                }
            
                if ($success4) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success4; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Subject <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="edugorilla_subject4" value="<?php echo $edugorilla_email_subject4; ?>"
                                       placeholder="Type Email Subject here...">
                                <font color="red"><?php echo $errors4['edugorilla_subject4']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Body template<sup><font color="red">*</font></sup></th>
                            <td>
                               <?php
									$content = $edugorilla_email_body4;
									$editor_id = 'edugorilla_body4';
									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors4['edugorilla_body4']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="email_setting_form4" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
          
        <div id="tabs-5">
            <?php
                $email_setting_form5 = $_POST['email_setting_form5'];
                if ($email_setting_form5 == "self") {
                    $errors5 = array();
                    $edugorilla_email_subject5 = $_POST['edugorilla_subject5'];
                    $edugorilla_email_body5 = $_POST['edugorilla_body5'];
                    if (empty($edugorilla_email_subject5)) $errors5['edugorilla_subject5'] = "Empty";
            
                    if (empty($edugorilla_email_body5)) $errors5['edugorilla_body5'] = "Empty";
            
                    if (empty($errors5)) {
                        $edugorilla_email_setting5 = array('subject' => stripslashes($edugorilla_email_subject5), 'body' => stripslashes($edugorilla_email_body5));
            
                        update_option("edugorilla_email_setting5", $edugorilla_email_setting5);
                        $success5 = "Email Settings Saved Successfully.";
                    	
                    	 $email_setting_options5 = get_option('edugorilla_email_setting5');
                   		 $edugorilla_email_subject5 = stripslashes($email_setting_options5['subject']);
                    	 $edugorilla_email_body5 = stripslashes($email_setting_options5['body']);
                    }
                } else {
                    $email_setting_options5 = get_option('edugorilla_email_setting5');
                    $edugorilla_email_subject5 = stripslashes($email_setting_options5['subject']);
                    $edugorilla_email_body5 = stripslashes($email_setting_options5['body']);
            
                }
                
                if ($success5) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success5; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>Subject <sup><font color="red">*</font></sup></th>
                            <td>
                                <input name="edugorilla_subject5" value="<?php echo $edugorilla_email_subject5; ?>"
                                       placeholder="Type Email Subject here...">
                                <font color="red"><?php echo $errors5['edugorilla_subject5']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th>Body template<sup><font color="red">*</font></sup></th>
                            <td>
                               <?php
									$content = $edugorilla_email_body5;
									$editor_id = 'edugorilla_body5';
									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors5['edugorilla_body5']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="email_setting_form5" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
        </div>
       
    </div>
    <?php
}

?>
