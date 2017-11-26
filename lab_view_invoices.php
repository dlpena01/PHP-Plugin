<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
require_once "/var/www/html/redcap/informatics/redcap_connect.php";
require_once APP_PATH_DOCROOT . 'ProjectGeneral/header.php';
require_once "/var/www/html/redcap/informatics/plugins/lab_plugin_shared.php"; //Contains shared functions
?>

<!-- Testing upload -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>ID Lab View Invoices</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    //function for date picker
    	$(function () {
            var dateFormat = "yy-mm-dd",
                from = $("#from")
            .datepicker({
                defaultDate: "+1d",
                changeMonth: true,
                numberOfMonths: 2
            })
            .on("change", function () {
                to.datepicker("option", "dateFormat", "yy-mm-dd");
                to.datepicker("option", "minDate", getDate(this));
            }),
            to = $("#to").datepicker({
                defaultDate: "+1d",
                changeMonth: true,
                numberOfMonths: 2
            })
            .on("change", function () {
                from.datepicker("option", "dateFormat", "yy-mm-dd");
                from.datepicker("option", "maxDate", getDate(this));
            });
            function getDate(element) {
                var date;
                try {
                    date = $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    date = null;
                }
                return date;
            }
	});	
    	function loadHPD(str){
    		var xmlhttp = new XMLHttpRequest();
    		xmlhttp.onreadystatechange = function(){
    			if(this.readyState == 4 && this.status == 200){
    				document.getElementById("showHPD").innerHTML = this.responseText;
    			}
    		};
    		xmlhttp.open("GET", "lab_view_invoices_data.php?pid=194&hospitalId="+document.getElementById("id").value+"&minDate="+document.getElementById("from").value+"&maxDate="+document.getElementById("to").value, true);
    		xmlhttp.send();
    	}
    </script>
    <script>
        var filesArray = [];
    	function grabFile(t) {
    		if(t.is(':checked')) {
                var reportSelected = $(t).attr('id');
                var filename = document.getElementById("filenameID-" + reportSelected).innerHTML;
                filesArray.push(filename);
    		} else {
    			var reportSelected = $(t).attr('id');
                var filename = document.getElementById("filenameID-" + reportSelected).innerHTML;
                var index = filesArray.indexOf(filename);
                delete filesArray[index];
    			//alert("Cannot find file");
    		}
    	}
        // function sendFax(files) {
        //     var pid = "194";
        //     var hosp_id = document.getElementById("id").value;
        //     hosp_id = JSON.stringify(hosp_id);
        //     files = JSON.stringify(files);
        //     $.ajax({
        //         type: "GET",
        //         url: "fax_reports.php",
        //         data: "pid=194&hospId=" + hosp_id + "&files=" + files,
        //         success: function(data) {
        //             alert("Faxes sent!");
        //         }
        //     });
        // }
        // function faxReport() {
        //     var files = filesArray;
        //     sendFax(files);
        // }
    </script>
    <style>
    td {
  padding: .2em;
  border-bottom: 1px solid #CCC;
  border-color: white;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border-color: white;
    }
    table, td, th {
        padding: 5px;
        border: 1px solid gray;
        border-color: white;
    }
    #border-styles {
        border-top-style:solid;
        border-top-color:gray;
        border-top-width:thin;
        border-bottom-style: solid;
        border-bottom-color:grey;
        border-bottom-width:thin;
        line-height: 3.5em;
    }
    #hosp-fieldset {
        margin: 8px;
        border: 2px solid silver;
        padding: 8px;
        border-radius: 4px;
    }
    #report_table tbody{
        border-bottom: 1px solid black;
        vertical-align: middle;
        display: table-row-group;
    }
    thead.bottom_border{
    border-bottom: 1px solid black;
    }   
    
    	#hosp-legend {
        padding: 2px;
    }
    .pagination a {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: solid;
    transition: background-color .3s;
    
    }
  	.pagination a.active {
        background-color: #de2828;
        color: white;
        }
  	.pagination a:hover:not(.active) {background-color: lightgray;}
    </style>
    </head>

    <?php
    // require_once "/var/www/html/redcap/informatics/redcap_connect.php";
    $project_id='194';
    $event_id = '622';
    $users=REDCap::getUsers();
    REDCap::allowUsers($users);
    REDCap::allowProjects($project_id);
    //queries go here
    $getHospitalName="SELECT distinct H.hosp_name, H.hosp_id FROM informatics.lab_invoice I INNER JOIN informatics.lab_hospital_info H 
    ON I.hospital_id = H.hosp_id ORDER BY hosp_id asc";
    $getHospitalNameResult = mysqli_query($conn, $getHospitalName);
    while($rowHospitalId = mysqli_fetch_assoc($getHospitalNameResult)){
    	$hospIdArray[]= $rowHospitalId['hosp_id'];
    	$hospNameArray[] = $rowHospitalId['hosp_name'];
    }
    // while($rowHospitalName = mysqli_fetch_assoc($getHospitalNameResult)){
    	
    // }
    $maxHospitals = sizeof($hospNameArray);
    mysql_close();
    ?>

    <body>
    <!-- start of html skeleton -->
    <form action="lab_view_invoices_data.php" method="GET">
    <h1 style="text-align:left;">View Invoices</h1>
    <div>
                <fieldset>
                <table>
                <tbody>
                <tr>
                        <td>
                        <br />
                            <label for="from">Start Billing Date </label>
                            <input  type="text" id="from" name="from" />
                            <label for="to">End Billing Date </label>
                            <input type="text" id="to" name="to"/>
                        </td>
                     </tr>
                    <!--  <tr>
                     <td>
                        <b>Status </b>
                        <select name="status" id="status" onchange="loadHPD(this.value)">
                        <option value="0">Not Sent</option>
                        <option value="1">Sent</option>
                        </select>
                        </td>
                     </tr> -->
                     <tr id="border-styles">
                        <td><b>Hospital </b>
                                 
                        <!-- <select name="Hospitals" id="id" onchange="editHospital(this.value)"> -->
                        <select name="Hospitals" id="id" onchange="loadHPD(this.value)">
                        <option value="">Select a hospital</option>
                        <?php
                        $maxHospitals = sizeof($hospIdArray);
                        $x=0;
                        while($x < $maxHospitals){
                        	echo "<option value=" . $hospIdArray[$x] . ">" . $hospNameArray[$x] . "</option>";
                        	$x++;
                        }
                        ?>
                        
                        </select>
                        </td>
                    </tr>
                 </tbody>
                </table>
                    </fieldset>
                </div>
            </form>
            <div id="showHPD"></div>
            <input type="button" value="New Invoice Query" onClick="window.location.reload()">
            <footer><strong>&#169 2017 University of Louisville, Division of Infectious Diseases. All Rights Reserved.</strong></footer>
            
            
            <br />
            <!-- <div id="showFax"></div> -->
    </body>
    </html>
