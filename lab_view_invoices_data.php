<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        //script for pagination found at https://jsfiddle.net/u9d1ewsh/
        $(document).ready(function () {
            $('#invoice_table').after('<div id="nav"></div>');
            var rowsShown = 5;
            var rowsTotal = $('#invoice_table tbody tr').length;
            var numPages = rowsTotal / rowsShown;
            for (i = 0; i < numPages ; i++) {
                var PageNum = i + 1;
                $('#nav').append('<a href="#" rel="'+i+'">'+PageNum+'</a> ');
            }
            $('#invoice_table tbody tr').hide();
            $('#invoice_table tbody tr').slice(0, rowsShown).show();
            $('#nav a:first').addClass('active');
            $('#nav a').bind('click', function () {
                $('#nav a').removeClass('active');
                $(this).addClass('active');
                var currentPage = $(this).attr('rel');
                var firstCell = currentPage * rowsShown;
                var lastCell = firstCell + rowsShown;
                $('#invoice_table tbody tr').css('opacity','0.0').hide().slice(firstCell, lastCell).css('display', 'table-row').animate({ opacity: 1 }, 300);
            });
        });
    </script>


<style>
    #invoice_table tr{
        border-bottom: 1px solid black;
        vertical-align: middle;
        display: table-row;
    }
    /*tr #data_invoice {
    	display: none;
    }*/
    thead.bottom_border{
    border-bottom: 1px solid black;
    }  
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
</style>
</head>
<body>
<?php
require_once "/var/www/html/redcap/informatics/redcap_connect.php";//Necessary to connect to the REDCap database
$project_id = '194';
REDCap::allowProjects($project_id);
$users = REDCap::getUsers();
REDCap::allowUsers($users);
$hospitalId = intval($_GET['hospitalId']);
$minDate = ($_GET['minDate']);
$maxDate = ($_GET['maxDate']);
// $status = ($_GET['status']);
//get query for displaying the hospital name depending on the hospital id chosen
// $patient_count = "SELECT count(distinct(report_num)) AS Count FROM informatics.lab_report S INNER JOIN
// informatics.redcap_data D ON S.case_id = D.record
// WHERE D.project_id = $project_id
// AND (S.st_bill_date BETWEEN '$minDate' AND '$maxDate')
// AND (S.end_bill_date BETWEEN '$minDate' AND '$maxDate')";
$invoice_count = "SELECT count(distinct(invoice_num)) AS Count FROM informatics.lab_invoice
WHERE (st_bill_date BETWEEN '$minDate' AND '$maxDate')
AND (end_bill_date BETWEEN '$minDate' AND '$maxDate')
AND hospital_id = $hospitalId";
$invoice_count_result = mysqli_query($conn, $invoice_count);
while($invoice_total_row = mysqli_fetch_assoc($invoice_count_result)){
    $max_invoices = $invoice_total_row['Count'];
}
$getSavedInvoiceInfo = "SELECT I.invoice_num, I.st_bill_date, I.end_bill_date, I.date_created, I.total_charges, H.hosp_name
FROM informatics.lab_invoice I INNER JOIN informatics.lab_hospital_info H
ON I.hospital_id = H.hosp_id
WHERE I.hospital_id = '$hospitalId'
AND(I.st_bill_date BETWEEN '$minDate' AND '$maxDate')
AND(I.end_bill_date BETWEEN '$minDate' AND '$maxDate')
ORDER BY I.invoice_num desc";
$SavedInvoiceInfo = mysqli_query($conn, $getSavedInvoiceInfo);
while($InvoiceNumResult_row = mysqli_fetch_assoc($SavedInvoiceInfo)){
    $getInvoiceNum[] = $InvoiceNumResult_row['invoice_num'];
    $getHospName[] = $InvoiceNumResult_row['hosp_name'];
    $getDateCreated[] = $InvoiceNumResult_row['date_created'];
    $getStDate[] = $InvoiceNumResult_row['st_bill_date'];
    $getEndDate[] = $InvoiceNumResult_row['end_bill_date'];
    $getTotalCharges[] = $InvoiceNumResult_row['total_charges'];
}
// echo "<p>" . $getStatus[0] . "</p>";
// echo "<p>" . $max_patients . "</p>";
// echo "<p>" . $getReportNum[0] . "</p>";
// echo "<p>" . $getReportNum[1] . "</p>";
// echo "<p>" . $getReportNum[2] . "</p>";
// echo "<p>" . $getReportNum[3] . "</p>";
// echo "<p>" . $getReportNum[4] . "</p>";
// echo "<p>" . $getReportNum[5] . "</p>";
//var_dump($max_patients);
if($max_invoices == '0'){
    echo "<p> Your search for invoices returned no results. Please make another search. Thank you. ";
}else{
 
echo "<table id=\"invoice_table\">
                    <br/>
                    <br/>
                    <thead class=\"bottom_border\">
                    <th><b>Invoice Number</b></th>
                    <th><b>Hospital Name</b></th>
                    <th><b>Date Created</b></th>
                    <th><b>Start Billing Date</b></th>
                    <th><b>End Billing Date</b></th>
                    <th><b>Total Charges</b></th>
                    <th><b>View Invoice</b></th>
                    </thead>
                    <tbody>";
$x = 0;
while ($x < $max_invoices) {
                    // Each row's checkbox will have an id corresponding to the index of it's position on the page. The file link will have the same corresponding id as the checkbox so one could
                    // run an onClick event for each checkbox that will grab the filename plus the index id. Maybe a: var id = checkboxID; 
                echo "<tr>
                    <td>" . $getInvoiceNum[$x] .  "</td>
                    <td>" . $getHospName[$x] . "</td>
                    <td>" . $getDateCreated[$x] . "</td>
                    <td>" . $getStDate[$x] . "</td>
                    <td>" . $getEndDate[$x] . "</td>
                    <td>" . $getTotalCharges[$x] ."</td>";
                    // if($getStatus[$x] == '0'){
                    //     echo "<td> Not Sent </td>";
                    // } else {
                    //     echo "<td> Sent </td>";
                    // } 
                    //$getDateCreatedNew = str_replace("-", "", $getDateCreated[$x]);
                    //$filename = $getDateCreatedNew . "-" . $getInvoiceNum[$x];
                    echo "<td>
                    <a href=\"https://capo.louisville.edu/informatics/plugins/lab_displayfile.php?pid=" . $project_id . "&file_type=invoice&id=" . $getInvoiceNum[$x] . "\" id=\"fileLink-" . $x . "\">Invoice #" . $getInvoiceNum[$x] . "</a>
                    </td>           
                </tr>";
                
                $x++;
}
            echo "</tbody>
            </table>";
}
            echo "<br/>
            <br/>
            <div id=\"nav\"></div>";
?>
</body>
</html>
