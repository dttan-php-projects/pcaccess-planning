<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

ini_set('memory_limit', '-1'); // enabled the full memory available.
ini_set('max_execution_time', 99999999999);  // set time 10 minutes

require_once('../Module/Database.php');
require_once("./vendor/autoload.php");

function formatDate($date)
{
    $result = '';
    if (!empty($date)) {
        $detache = implode('/', $date);
        // Sau khi tách thành mảng, kiểm tra xem có đúng định dạng tháng/ngày/năm không
        if (isset($detache[0]) && isset($detache[1]) && isset($detache[2])) {
            if (checkdate($detache[0], $detache[1], $detache[2])) {
                $result = date('Y-m-d', strtotime($date));
            }
        }
    }

    return $result;
}

// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 

// database
$conn = _conn();
$table = "access_item_information";

$queryexport = ("SELECT * FROM $table ORDER BY ID DESC LIMIT 100;");

// Column names 
$fields = array( 
    'No.',
    'GLID', 'Item_Code', 'Buying_Office', 'Fit_Variable', 'Production_Type', 'Production_Line', 'OS_Sample', 'DS_Sample', 'ProductionWidth', 'ProductionLength',
    'Sheet_Size', 'Stock_Code', 'Color_F', 'Color_B', 'Color_FQ', 'Color_BQ', 'Varnish_F', 'Varnish_B', 'Imprint_B', 'Imprint_F',
    'Offset_Level', 'Offset_Imp_Front', 'Offset_Imp_Back', 'Offset_UPS', 'Offset_Cut_No', 'Digital_Level', 'Digital_F_Click', 'Digital_B_Click', 'Digital_UPS', 'Digital_Cut_No',
    'Digital_Sheet_Size', 'Digital_Stock_Code_F', 'Digital_DieCut_No', 'Digital_Availability', 'Hot_Folder', 'Variable_F', 'Variable_B', 'DieCut_Machine', 'DieCut_No', 'Suited_Machine', 
    'Digital_Machine', 'Special_Instruction', 'Crocking_Test', 'SubContract', 'SubContract_Detail', 'Process', 'Special_Drying_Time', 'Standard_LeadTime', 'Hole', 'UV_F', 
    'UV_B', 'Active', 'Color_Management', 'CS_Sample', 'Last_Order_Time', 'Last_Revise_Date', 'Original_System', 'PE_Name', 'PE_Receive_Date', 'Ready_Date', 
    'Revise_People', 'Scrap_Adjustment', 'Social_Compliance', 'Status', 'Setup_Date', 'Offset_Extra_Time', 'Offset_Waiting_Drying', 'Digital_Extra_Time', 'Digital_Waiting_Drying', 'FSC', 
    'Finishing_Difficult_Rate', 'StringCut_ComboTag', 'FirstOrder', 'Inactive_Reason', 'CheckReplaceMaterial', 'Brand_Protection', 'Updated By', 'Updated Date'
);

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 

$result = mysqli_query($conn, $queryexport);

$index = 0;

if (mysqli_num_rows($result) > 0 ) {
    while ($element = mysqli_fetch_assoc($result)) {

        $index++;
    
        // get data
        $GLID = trim($element['GLID']);
    
        if (empty($GLID) || $GLID == " " ) continue;
    
        $Item_Code = trim($element['Item_Code']);
        $Buying_Office = trim($element['Buying_Office']);
        $Fit_Variable = trim($element['Fit_Variable']);
        $Production_Type = trim($element['Production_Type']);
        $Production_Line = trim($element['Production_Line']);
        $OS_Sample = trim($element['OS_Sample']);
        $DS_Sample = trim($element['DS_Sample']);
        $ProductionWidth = trim($element['ProductionWidth']);
        $ProductionLength = trim($element['ProductionLength']);
        
        $Sheet_Size = trim($element['Sheet_Size']);
        $Stock_Code = trim($element['Stock_Code']);
        $Color_F = trim($element['Color_F']);
        $Color_B = trim($element['Color_B']);
        $Color_FQ = trim($element['Color_FQ']);
        $Color_BQ = trim($element['Color_BQ']);
        $Varnish_F = trim($element['Varnish_F']);
        $Varnish_B = trim($element['Varnish_B']);
        $Imprint_B = trim($element['Imprint_B']);
        $Imprint_F = trim($element['Imprint_F']);
    
        $Offset_Level = trim($element['Offset_Level']);
        $Offset_Imp_Front = trim($element['Offset_Imp_Front']);
        $Offset_Imp_Back = trim($element['Offset_Imp_Back']);
        $Offset_UPS = trim($element['Offset_UPS']);
        $Offset_Cut_No = trim($element['Offset_Cut_No']);
        $Digital_Level = trim($element['Digital_Level']);
        $Digital_F_Click = trim($element['Digital_F_Click']);
        $Digital_B_Click = trim($element['Digital_B_Click']);
        $Digital_UPS = trim($element['Digital_UPS']);
        $Digital_Cut_No = trim($element['Digital_Cut_No']);
    
        $Digital_Sheet_Size = trim($element['Digital_Sheet_Size']);
        $Digital_Stock_Code_F = trim($element['Digital_Stock_Code_F']);
        $Digital_DieCut_No = trim($element['Digital_DieCut_No']);
        $Digital_Availability = trim($element['Digital_Availability']);
        $Hot_Folder = trim($element['Hot_Folder']);
        $Variable_F = trim($element['Variable_F']);
        $Variable_B = trim($element['Variable_B']);
        $DieCut_Machine = trim($element['DieCut_Machine']);
        $DieCut_No = trim($element['DieCut_No']);
        $Suited_Machine = trim($element['Suited_Machine']);
    
        $Digital_Machine = trim($element['Digital_Machine']);
        $Special_Instruction = trim($element['Special_Instruction']);
        $Crocking_Test = trim($element['Crocking_Test']);
        $SubContract = trim($element['SubContract']);
        $SubContract_Detail = trim($element['SubContract_Detail']);
        $Process = trim($element['Process']);
        $Special_Drying_Time = trim($element['Special_Drying_Time']);
        $Standard_LeadTime = trim($element['Standard_LeadTime']);
        $Hole = trim($element['Hole']);
        $UV_F = trim($element['UV_F']);
    
        $UV_B = trim($element['UV_B']);
        $Active = trim($element['Active']);
        $Color_Management = trim($element['Color_Management']);
        $CS_Sample = trim($element['CS_Sample']);
        $Last_Order_Time = trim($element['Last_Order_Time']);
        $Last_Revise_Date = trim($element['Last_Revise_Date']);
        $Original_System = trim($element['Original_System']);
        $PE_Name = trim($element['PE_Name']);
        $PE_Receive_Date = trim($element['PE_Receive_Date']);
        $Ready_Date = trim($element['Ready_Date']);
    
        $Revise_People = trim($element['Revise_People']);
        $Scrap_Adjustment = trim($element['Scrap_Adjustment']);
        $Social_Compliance = trim($element['Social_Compliance']);
        $Status = trim($element['Status']);
        $Setup_Date = trim($element['Setup_Date']);
        $Offset_Extra_Time = trim($element['Offset_Extra_Time']);
        $Offset_Waiting_Drying = trim($element['Offset_Waiting_Drying']);
        $Digital_Extra_Time = trim($element['Digital_Extra_Time']);
        $Digital_Waiting_Drying = trim($element['Digital_Waiting_Drying']);
        $FSC = trim($element['FSC']);
    
        $Finishing_Difficult_Rate = trim($element['Finishing_Difficult_Rate']);
        $StringCut_ComboTag = trim($element['StringCut_ComboTag']);
        $FirstOrder = trim($element['FirstOrder']);
        $Inactive_Reason = trim($element['Inactive_Reason']);
        $CheckReplaceMaterial = trim($element['CheckReplaceMaterial']);
        $Brand_Protection = trim($element['Brand_Protection']);
    
        $Updated_By = trim($element['Updated_By']);
        $Updated_Date = trim($element['Updated_Date']);
    
        $lineData = array(
            $index, 
            $GLID, $Item_Code, $Buying_Office, $Fit_Variable, $Production_Type, $Production_Line, $OS_Sample, $DS_Sample, $ProductionWidth, $ProductionLength,
            $Sheet_Size, $Stock_Code, $Color_F, $Color_B, $Color_FQ, $Color_BQ, $Varnish_F, $Varnish_B, $Imprint_B, $Imprint_F
        );
    
        array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
    
    } 
} else { 
    $excelData .= 'No records found...'. "\n"; 
} 



// set filename for excel file to be exported
$fileName = 'PC_MasterData_' . date("Y_m_d__H_i_s") . '.xls';

// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 

mysqli_close($conn);
