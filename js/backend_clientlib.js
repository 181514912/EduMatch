jQuery(document).ready(function( $ ) {
/***Listing type***/
	/**$(".wp-submenu-head").each(function()
	{**/
		//alert($(".wp-submenu-head:contains('Listings')").text());
   /** });**/
	$("#edugorilla_list_date_from").datepicker({ dateFormat: 'yy-mm-dd' });
	$("#edugorilla_list_date_to").datepicker({ dateFormat: 'yy-mm-dd' });
	if($(".wp-submenu-head:contains('Listings')").text() == "Listings")
    {
    	$(".wp-submenu-head:contains('Listings')").parent().find("li").each(function(){
        	if($(this).text() != "Listings")
            {
            	var id=$(this).find("a").attr("href").replace("edit.php?post_type=", " ").trim();
            	$('#edugorilla_listing_type').append($("<option></option>").attr("value",id).text($(this).find("a").text()));
            }
        });
  				 
    }


	$(document).on("change","#edugorilla_listing_type",function(){
    	if($(this).val() !== "")
        {
        	$("#edugorilla_keyword").removeAttr("disabled");
        	$("#edugorilla_category").removeAttr("disabled");
        	$("#edugorilla_location").removeAttr("disabled");
        }
    	else
        {
        	$("#edugorilla_keyword").attr("disabled","disabled");
        	$("#edugorilla_category").attr("disabled","disabled");
        	$("#edugorilla_location").attr("disabled","disabled");
        }

    });


$(document).on('click','a[id^=edugorilla_leads_view]', function(){

	var pid = $(this).attr('href').replace("#","");
	
	$.ajax({
            	url: ajaxurl,
                type: 'GET',
         		data: {
            			'action':'edugorilla_view_leads',
            			'promotion_id': pid
          			  },
                dataType: 'json',
         		success: function(data) 
            	{
                	
                	var cnfbox = "<table class='widefat fixed' align='center' width='100%' border=1><tr><th>Name</th><th>Email</th><th>Contact No.</th><th>Date Time</th></tr>";
                
                     	cnfbox += "<tr><td>"+data.name+"</td><td>"+data.email+"</td><td>"+data.contact_no+"</td><td>"+data.date_time+"</td></tr>";
               
                	cnfbox += "</table>";
                
                	$('#edugorilla_view_leads').html(cnfbox);
                	$('#edugorilla_view_leads').modal();
                },
    			error: function(err)
            	{
                	console.log(err);
                }
    	});
});



$(document).on('click','#edugorilla_filter',function(){
		var institute_data;
		     
		var ptype = $("#edugorilla_listing_type").val();
		var keyword = $('#edugorilla_keyword').val();
		var location = $('#edugorilla_location').val();
		var category = $("#edugorilla_category").val();
	var categoryString = JSON.stringify(category);
		
    		$.ajax({
            	url: ajaxurl,
                type: 'GET',
         		data: {
            			'action':'edugorilla_show_location',
            			'ptype': ptype,
                		'term': keyword,
                        'address' : location,
		            'category': categoryString
          			  },
                dataType: 'json',
                beforeSend: function()
            	{
                },
         		success: function(data) 
            	{
		            $("#edugorilla_institute_datas").val(JSON.stringify(data['postingDetails']));
		            $("#edugorilla_subscibed_instant_datas").val(JSON.stringify(data['subscriptionPreferenceDetails']));

		            var eduName = $("#edu_name").val();
		            var eduEmail = $("#edu_email").val();
		            var eduContactNo = $("#edu_contact_no").val();
		            var eduQuery = $("#edu_query").val();
		            var eduInstituteDatas = $("#edugorilla_institute_datas").val();
		            var eduSubscriptionDatas = $("#edu_email").val();
		            if (eduName !== "" && eduEmail !== "" && eduContactNo !== "" && eduQuery !== "")
                    {
	                    if (eduInstituteDatas !== "" || eduSubscriptionDatas !== "") {
		                    $('#save_details_button').removeAttr("disabled");
		                    //We should be showing modal in any case.
		                    // if ($("#is_promotional_lead").is(":checked")) {
		                    $('#save_details_button').attr("rel", "modal:open");
		                    // } else {
		                    //    $('#save_details_button').attr("onclick", "document.lead_capture_details.submit();");
		                    // }
	                    }
                    }

                var address = $("#edugorilla_location option:selected").text().replace("->","");
              	
                var geocoder =  new google.maps.Geocoder();
               
    geocoder.geocode( { 'address': address}, function(results, status) {
       
          	var points;
          	var zoom;
  				if(typeof results[0] === "undefined")
                {
                	points = {lat: parseFloat(0), lng: parseFloat(0)};
                	zoom = 1;
                }
    			else
                {
                	points = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                	zoom = 5;
                }


	    var cnfbox = "<table class='widefat fixed' align='center' width='100%' border=1><tr><th>Send</th><th>Institude Name</th><th>Email(s)</th><th>SMS(s)</th><th>Flag</th></tr>";
        			 	var map = new google.maps.Map(document.getElementById('map'), {
         					 zoom: zoom,
        				 	center: points
       				 	});
                
                 var infowindow = new google.maps.InfoWindow();

	    if ($("#is_promotional_lead").is(":checked")) {
		    $.each(data['postingDetails'], function (i, v) {

			    cnfbox += "<tr id=" + v.post_id + "><td><input type='checkbox' class='confirmSendPostingDetails' checked='true'/></td><td><a href='" + v.listing_url + "'>" + v.title + "</a></td><td>" + v.emails + "</td><td>" + v.phones + "</td><td>" + v.flag + "</td></tr>";

			    var marker = new google.maps.Marker({
				    position: new google.maps.LatLng(v.lat, v.long),
				    map: map
			    });
			    google.maps.event.addListener(marker, 'click', (function (marker, i) {
				    return function () {
					    infowindow.setContent('Institute Name: <b><a href="' + v.listing_url + '">' + v.title + "</a></b><br>Address: <b>" + v.address + "</b><br>Latitude <b>" + v.lat + "</b><br>Longitude <b>" + v.long + "</b><br>Flag <b>" + v.flag + "</b>");
					    infowindow.open(map, marker);
				    }
			    })(marker, i));
		    });
	    }

	    $.each(data['subscriptionPreferenceDetails'], function (i, v) {
		    cnfbox += "<tr id=" + v.userId + "><td><input type='checkbox' class='confirmSendPrefDetails' checked='true'/></td><td>" + v.userName + "</td><td class='emailPrefDetails'>" + v.emailDetails + "</td><td class='phonePrefDetails'>" + v.phoneDetails + "</td></tr>";
	    });

	    cnfbox += "</table><center><button id='confirm' onclick='document.lead_capture_details.submit();'>Confirm</button></center>";
               		$("#confirmation").html(cnfbox);
 
        });

	            },
                error: function(err)
            	{
                	console.log(err);
                }
            });
					 
});


	$('#edugorilla_category').select2({placeholder: 'Select category'});
	$('#edugorilla_location').select2();
	$( "#tabs" ).tabs();
	$( "#list-tabs" ).tabs();
	$(document).on('click','#is_promotional_lead',function() {
  		if ($(this).is(':checked')) {
    		$("#save_details_button").text('Send Details');
  		} else {
		    //$("#save_details_button").text('Save Details');
  		}
	});

	$(document).on('change', '.confirmSendPostingDetails', function () {
		var postingDetails = document.getElementById('edugorilla_institute_datas').value;
		var postingObjs = JSON.parse(postingDetails);
		var affectedRow = event.target.parentElement.parentElement;
		var affectedId = affectedRow.id;
		var postingCounter = postingObjs.length;
		var isPositive = this.checked;
		while (postingCounter--) {
			var postingObj = postingObjs[postingCounter];
			var postingId = postingObj.post_id;
			if (postingId == affectedId) {
				if (!isPositive) {
					postingObj.sendPostDetails = false;
				} else {
					postingObj.sendPostDetails = true;
				}
			}
		}
		var postingJSON = JSON.stringify(postingObjs);
		$("#edugorilla_institute_datas").val(postingJSON);
	});

	$(document).on('change', '.confirmSendPrefDetails', function () {
		var prefDetails = document.getElementById('edugorilla_subscibed_instant_datas').value;
		var prefObjs = JSON.parse(prefDetails);
		var affectedRow = event.target.parentElement.parentElement;
		var affectedId = affectedRow.id;
		var affectedEmails = $(affectedRow).find('.emailPrefDetails')[0].innerHTML;
		var affectedPhoneNumbers = $(affectedRow).find('.phonePrefDetails')[0].innerHTML;
		var prefCounter = prefObjs.length;
		var isPositive = this.checked;
		while (prefCounter--) {
			var prefObj = prefObjs[prefCounter];
			var userId = prefObj.userId;
			if (userId == affectedId) {
				if (!isPositive) {
					prefObj.sendPrefDetails = false;
				} else {
					prefObj.sendPrefDetails = true;
				}
			}
		}
		var prefJSON = JSON.stringify(prefObjs);
		$("#edugorilla_subscibed_instant_datas").val(prefJSON);
	});
    
	
});