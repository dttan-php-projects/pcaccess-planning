<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ('../Module/Database.php');
    require_once ("./vendor/autoload.php"); 

    function formatDate($date)
    {
        $result = '';
        if (!empty($date) ) {
            $detache = implode('/', $date );
            // Sau khi tách thành mảng, kiểm tra xem có đúng định dạng tháng/ngày/năm không
            if (isset($detache[0]) && isset($detache[1]) && isset($detache[2]) ) {
                if (checkdate($detache[0], $detache[1], $detache[2]) ) {
                    $result = date('Y-m-d', strtotime($date));
                }
            }
        }

        return $result;

    }

    $conn = _conn();
    $table = "access_item_information";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
    if (empty($updateBy) ) {
        $updateBy = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : "";
    }
    $message = "Not Submit";
    if (isset($_POST["submit"])) {

        $allowedFileType = ['application/vnd.ms-excel', 'application/octet-stream', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (in_array($_FILES["file"]["type"], $allowedFileType)) {

            $file_name = 'PC_MasterItem_' . $_SERVER['REMOTE_ADDR'] . '_' . $updateBy . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            $targetPath = './Excel/' . $file_name;
            
            // hàm move_uploaded_file k sử dụng được (có thể do bị hạn chế quyền của thư mục tmp)
            if (copy($_FILES['file']['tmp_name'], $targetPath)) {
                // echo "Đã upload file : $targetPath <br />\n";
            } else {
                $message = "Problem in Importing Excel Data";
            }

            // init PhpSpreadsheet Xlsx
                $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            // get sheet 0 (sheet 1)
                $spreadSheet = $Reader->load($targetPath)->getSheet(0);
                $allDataInSheet = $spreadSheet->toArray(null, true, true, true);
            // check col name exist


                $createArray = array( 
                    'GLID', 'Item_Code', 'Buying_Office', 'Fit_Variable', 'Production_Type', 'Production_Line', 'OS_Sample', 'DS_Sample', 'ProductionWidth', 'ProductionLength',
                    'Sheet_Size', 'Stock_Code', 'Color_F', 'Color_B', 'Color_FQ', 'Color_BQ', 'Varnish_F', 'Varnish_B', 'Imprint_B', 'Imprint_F',
                    'Offset_Level', 'Offset_Imp_Front', 'Offset_Imp_Back', 'Offset_UPS', 'Offset_Cut_No', 'Digital_Level', 'Digital_F_Click', 'Digital_B_Click', 'Digital_UPS', 'Digital_Cut_No',
                    'Digital_Sheet_Size', 'Digital_Stock_Code_F', 'Digital_DieCut_No', 'Digital_Availability', 'Hot_Folder', 'Variable_F', 'Variable_B', 'DieCut_Machine', 'DieCut_No', 'Suited_Machine', 
                    'Digital_Machine', 'Special_Instruction', 'Crocking_Test', 'SubContract', 'SubContract_Detail', 'Process', 'Special_Drying_Time', 'Standard_LeadTime', 'Hole', 'UV_F', 
                    'UV_B', 'Active', 'Color_Management', 'CS_Sample', 'Last_Order_Time', 'Last_Revise_Date', 'Original_System', 'PE_Name', 'PE_Receive_Date', 'Ready_Date', 
                    'Revise_People', 'Scrap_Adjustment', 'Social_Compliance', 'Status', 'Setup_Date', 'Offset_Extra_Time', 'Offset_Waiting_Drying', 'Digital_Extra_Time', 'Digital_Waiting_Drying', 'FSC', 
                    'Finishing_Difficult_Rate', 'StringCut_ComboTag', 'FirstOrder', 'Inactive_Reason', 'CheckReplaceMaterial', 'Brand_Protection'
                );
                $makeArray = array( 
                    'GLID' => 'GLID', 
                    'Item_Code' => 'Item_Code', 
                    'Buying_Office' => 'Buying_Office',
                    'Fit_Variable' => 'Fit_Variable',
                    'Production_Type' => 'Production_Type',
                    'Production_Line' => 'Production_Line',
                    'OS_Sample' => 'OS_Sample',
                    'DS_Sample' => 'DS_Sample',
                    'ProductionWidth' => 'ProductionWidth',
                    'ProductionLength' => 'ProductionLength',
                    
                    'Sheet_Size' => 'Sheet_Size',
                    'Stock_Code' => 'Stock_Code',
                    'Color_F' => 'Color_F',
                    'Color_B' => 'Color_B',
                    'Color_FQ' => 'Color_FQ',
                    'Color_BQ' => 'Color_BQ',
                    'Varnish_F' => 'Varnish_F',
                    'Varnish_B' => 'Varnish_B',
                    'Imprint_B' => 'Imprint_B',
                    'Imprint_F' => 'Imprint_F',

                    'Offset_Level' => 'Offset_Level',
                    'Offset_Imp_Front' => 'Offset_Imp_Front',
                    'Offset_Imp_Back' => 'Offset_Imp_Back',
                    'Offset_UPS' => 'Offset_UPS',
                    'Offset_Cut_No' => 'Offset_Cut_No',
                    'Digital_Level' => 'Digital_Level',
                    'Digital_F_Click' => 'Digital_F_Click',
                    'Digital_B_Click' => 'Digital_B_Click',
                    'Digital_UPS' => 'Digital_UPS',
                    'Digital_Cut_No' => 'Digital_Cut_No',

                    'Digital_Sheet_Size' => 'Digital_Sheet_Size',
                    'Digital_Stock_Code_F' => 'Digital_Stock_Code_F',
                    'Digital_DieCut_No' => 'Digital_DieCut_No',
                    'Digital_Availability' => 'Digital_Availability',
                    'Hot_Folder' => 'Hot_Folder',
                    'Variable_F' => 'Variable_F',
                    'Variable_B' => 'Variable_B',
                    'DieCut_Machine' => 'DieCut_Machine',
                    'DieCut_No' => 'DieCut_No',
                    'Suited_Machine' => 'Suited_Machine',

                    'Digital_Machine' => 'Digital_Machine',
                    'Special_Instruction' => 'Special_Instruction',
                    'Crocking_Test' => 'Crocking_Test',
                    'SubContract' => 'SubContract',
                    'SubContract_Detail' => 'SubContract_Detail',
                    'Process' => 'Process',
                    'Special_Drying_Time' => 'Special_Drying_Time',
                    'Standard_LeadTime' => 'Standard_LeadTime',
                    'Hole' => 'Hole',
                    'UV_F' => 'UV_F',

                    'UV_B' => 'UV_B',
                    'Active' => 'Active',
                    'Color_Management' => 'Color_Management',
                    'CS_Sample' => 'CS_Sample',
                    'Last_Order_Time' => 'Last_Order_Time',
                    'Last_Revise_Date' => 'Last_Revise_Date',
                    'Original_System' => 'Original_System',
                    'PE_Name' => 'PE_Name',
                    'PE_Receive_Date' => 'PE_Receive_Date',
                    'Ready_Date' => 'Ready_Date',

                    'Revise_People' => 'Revise_People',
                    'Scrap_Adjustment' => 'Scrap_Adjustment',
                    'Social_Compliance' => 'Social_Compliance',
                    'Status' => 'Status',
                    'Setup_Date' => 'Setup_Date',
                    'Offset_Extra_Time' => 'Offset_Extra_Time',
                    'Offset_Waiting_Drying' => 'Offset_Waiting_Drying',
                    'Digital_Extra_Time' => 'Digital_Extra_Time',
                    'Digital_Waiting_Drying' => 'Digital_Waiting_Drying',
                    'FSC' => 'FSC',

                    'Finishing_Difficult_Rate' => 'Finishing_Difficult_Rate',
                    'StringCut_ComboTag' => 'StringCut_ComboTag',
                    'FirstOrder' => 'FirstOrder',
                    'Inactive_Reason' => 'Inactive_Reason',
                    'CheckReplaceMaterial' => 'CheckReplaceMaterial',
                    'Brand_Protection' => 'Brand_Protection'

                );
                $SheetDataKey = array();
                foreach ($allDataInSheet as $dataInSheet) {
                    foreach ($dataInSheet as $key => $value) {
                        if (in_array(trim($value), $createArray)) {
                            $value = preg_replace('/\s+/', '', $value);
                            $SheetDataKey[trim($value)] = $key;
                        } else { }
                    }
                }
				
            // check data
                $flag = 0;
                $data = array_diff_key($makeArray, $SheetDataKey);
                if (empty($data)) { $flag = 1; }
            
            // load data
                if ($flag == 1) {
                    
                    for ($i = 2; $i <= count($allDataInSheet); $i++) {
                        // get col key
                            $GLID = $SheetDataKey['GLID']; 
                            $Item_Code = $SheetDataKey['Item_Code']; 
                            $Buying_Office = $SheetDataKey['Buying_Office']; 

                            $Fit_Variable = $SheetDataKey['Fit_Variable']; 
                            $Production_Type = $SheetDataKey['Production_Type']; 
                            $Production_Line = $SheetDataKey['Production_Line']; 
                            $OS_Sample = $SheetDataKey['OS_Sample']; 
                            $DS_Sample = $SheetDataKey['DS_Sample']; 
                            $ProductionWidth = $SheetDataKey['ProductionWidth']; 
                            $ProductionLength = $SheetDataKey['ProductionLength']; 

                            $Sheet_Size = $SheetDataKey['Sheet_Size']; 
                            $Stock_Code = $SheetDataKey['Stock_Code']; 
                            $Color_F = $SheetDataKey['Color_F']; 
                            $Color_B = $SheetDataKey['Color_B']; 
                            $Color_FQ = $SheetDataKey['Color_FQ']; 
                            $Color_BQ = $SheetDataKey['Color_BQ']; 
                            $Varnish_F = $SheetDataKey['Varnish_F']; 
                            $Varnish_B = $SheetDataKey['Varnish_B']; 
                            $Imprint_B = $SheetDataKey['Imprint_B']; 
                            $Imprint_F = $SheetDataKey['Imprint_F']; 

                            $Offset_Level = $SheetDataKey['Offset_Level']; 
                            $Offset_Imp_Front = $SheetDataKey['Offset_Imp_Front']; 
                            $Offset_Imp_Back = $SheetDataKey['Offset_Imp_Back']; 
                            $Offset_UPS = $SheetDataKey['Offset_UPS']; 
                            $Offset_Cut_No = $SheetDataKey['Offset_Cut_No']; 
                            $Digital_Level = $SheetDataKey['Digital_Level']; 
                            $Digital_F_Click = $SheetDataKey['Digital_F_Click']; 
                            $Digital_B_Click = $SheetDataKey['Digital_B_Click']; 
                            $Digital_UPS = $SheetDataKey['Digital_UPS']; 
                            $Digital_Cut_No = $SheetDataKey['Digital_Cut_No']; 

                            $Digital_Sheet_Size = $SheetDataKey['Digital_Sheet_Size']; 
                            $Digital_Stock_Code_F = $SheetDataKey['Digital_Stock_Code_F']; 
                            $Digital_DieCut_No = $SheetDataKey['Digital_DieCut_No']; 
                            $Digital_Availability = $SheetDataKey['Digital_Availability']; 
                            $Hot_Folder = $SheetDataKey['Hot_Folder']; 
                            $Variable_F = $SheetDataKey['Variable_F']; 
                            $Variable_B = $SheetDataKey['Variable_B']; 
                            $DieCut_Machine = $SheetDataKey['DieCut_Machine']; 
                            $DieCut_No = $SheetDataKey['DieCut_No']; 
                            $Suited_Machine = $SheetDataKey['Suited_Machine']; 

                            $Digital_Machine = $SheetDataKey['Digital_Machine']; 
                            $Special_Instruction = $SheetDataKey['Special_Instruction']; 
                            $Crocking_Test = $SheetDataKey['Crocking_Test']; 
                            $SubContract = $SheetDataKey['SubContract']; 
                            $SubContract_Detail = $SheetDataKey['SubContract_Detail']; 
                            $Process = $SheetDataKey['Process']; 
                            $Special_Drying_Time = $SheetDataKey['Special_Drying_Time']; 
                            $Standard_LeadTime = $SheetDataKey['Standard_LeadTime']; 
                            $Hole = $SheetDataKey['Hole']; 
                            $UV_F = $SheetDataKey['UV_F']; 

                            $UV_B = $SheetDataKey['UV_B']; 
                            $Active = $SheetDataKey['Active']; 
                            $Color_Management = $SheetDataKey['Color_Management']; 
                            $CS_Sample = $SheetDataKey['CS_Sample']; 
                            $Last_Order_Time = $SheetDataKey['Last_Order_Time']; 
                            $Last_Revise_Date = $SheetDataKey['Last_Revise_Date']; 
                            $Original_System = $SheetDataKey['Original_System']; 
                            $PE_Name = $SheetDataKey['PE_Name']; 
                            $PE_Receive_Date = $SheetDataKey['PE_Receive_Date']; 
                            
                            $Ready_Date = $SheetDataKey['Ready_Date']; 

                            $Revise_People = $SheetDataKey['Revise_People']; 
                            $Scrap_Adjustment = $SheetDataKey['Scrap_Adjustment']; 
                            $Social_Compliance = $SheetDataKey['Social_Compliance']; 
                            $Status = $SheetDataKey['Status']; 
                            $Setup_Date = $SheetDataKey['Setup_Date']; 
                            // $Setup_Date = date('Y-m-d H:i:s', strtotime($SheetDataKey['Setup_Date']) ); 

                            $Offset_Extra_Time = $SheetDataKey['Offset_Extra_Time']; 
                            $Offset_Waiting_Drying = $SheetDataKey['Offset_Waiting_Drying']; 
                            $Digital_Extra_Time = $SheetDataKey['Digital_Extra_Time']; 
                            $Digital_Waiting_Drying = $SheetDataKey['Digital_Waiting_Drying']; 
                            $FSC = $SheetDataKey['FSC']; 

                            $Finishing_Difficult_Rate = $SheetDataKey['Finishing_Difficult_Rate']; 
                            $StringCut_ComboTag = $SheetDataKey['StringCut_ComboTag']; 
                            $FirstOrder = $SheetDataKey['FirstOrder']; 
                            $Inactive_Reason = $SheetDataKey['Inactive_Reason']; 
                            $CheckReplaceMaterial = $SheetDataKey['CheckReplaceMaterial']; 
                            $Brand_Protection = $SheetDataKey['Brand_Protection']; 
                            

                        // get data 
                            $GLID = filter_var(trim($allDataInSheet[$i][$GLID]), FILTER_SANITIZE_STRING);
                            $Item_Code = filter_var(trim(strtoupper($allDataInSheet[$i][$Item_Code]) ), FILTER_SANITIZE_STRING);
                            $Buying_Office = filter_var(trim($allDataInSheet[$i][$Buying_Office]), FILTER_SANITIZE_STRING);

                            $Fit_Variable = filter_var(trim($allDataInSheet[$i][$Fit_Variable]), FILTER_SANITIZE_STRING);
                            $Production_Type = filter_var(trim($allDataInSheet[$i][$Production_Type]), FILTER_SANITIZE_STRING);
                            $Production_Line = filter_var(trim($allDataInSheet[$i][$Production_Line]), FILTER_SANITIZE_STRING);
                            $OS_Sample = filter_var(trim($allDataInSheet[$i][$OS_Sample]), FILTER_SANITIZE_STRING);
                            $DS_Sample = filter_var(trim($allDataInSheet[$i][$DS_Sample]), FILTER_SANITIZE_STRING);
                            $ProductionWidth = filter_var(trim($allDataInSheet[$i][$ProductionWidth]), FILTER_SANITIZE_STRING);
                            $ProductionLength = filter_var(trim($allDataInSheet[$i][$ProductionLength]), FILTER_SANITIZE_STRING);

                            $Sheet_Size = filter_var(trim($allDataInSheet[$i][$Sheet_Size]), FILTER_SANITIZE_STRING);
                            $Stock_Code = filter_var(trim($allDataInSheet[$i][$Stock_Code]), FILTER_SANITIZE_STRING);
                            $Color_F = filter_var(trim($allDataInSheet[$i][$Color_F]), FILTER_SANITIZE_STRING);
                            $Color_B = filter_var(trim($allDataInSheet[$i][$Color_B]), FILTER_SANITIZE_STRING);
                            $Color_FQ = filter_var(trim($allDataInSheet[$i][$Color_FQ]), FILTER_SANITIZE_STRING);
                            $Color_BQ = filter_var(trim($allDataInSheet[$i][$Color_BQ]), FILTER_SANITIZE_STRING);
                            $Varnish_F = filter_var(trim($allDataInSheet[$i][$Varnish_F]), FILTER_SANITIZE_STRING);
                            $Varnish_B = filter_var(trim($allDataInSheet[$i][$Varnish_B]), FILTER_SANITIZE_STRING);
                            $Imprint_B = filter_var(trim($allDataInSheet[$i][$Imprint_B]), FILTER_SANITIZE_STRING);
                            $Imprint_F = filter_var(trim($allDataInSheet[$i][$Imprint_F]), FILTER_SANITIZE_STRING);

                            $Offset_Level = filter_var(trim($allDataInSheet[$i][$Offset_Level]), FILTER_SANITIZE_STRING);
                            $Offset_Imp_Front = filter_var(trim($allDataInSheet[$i][$Offset_Imp_Front]), FILTER_SANITIZE_STRING);
                            $Offset_Imp_Back = filter_var(trim($allDataInSheet[$i][$Offset_Imp_Back]), FILTER_SANITIZE_STRING);
                            $Offset_UPS = filter_var(trim($allDataInSheet[$i][$Offset_UPS]), FILTER_SANITIZE_STRING);
                            $Offset_Cut_No = filter_var(trim($allDataInSheet[$i][$Offset_Cut_No]), FILTER_SANITIZE_STRING);
                            $Digital_Level = filter_var(trim($allDataInSheet[$i][$Digital_Level]), FILTER_SANITIZE_STRING);
                            $Digital_F_Click = filter_var(trim($allDataInSheet[$i][$Digital_F_Click]), FILTER_SANITIZE_STRING);
                            $Digital_B_Click = filter_var(trim($allDataInSheet[$i][$Digital_B_Click]), FILTER_SANITIZE_STRING);
                            $Digital_UPS = filter_var(trim($allDataInSheet[$i][$Digital_UPS]), FILTER_SANITIZE_STRING);
                            $Digital_Cut_No = filter_var(trim($allDataInSheet[$i][$Digital_Cut_No]), FILTER_SANITIZE_STRING);

                            $Digital_Sheet_Size = filter_var(trim($allDataInSheet[$i][$Digital_Sheet_Size]), FILTER_SANITIZE_STRING);
                            $Digital_Stock_Code_F = filter_var(trim($allDataInSheet[$i][$Digital_Stock_Code_F]), FILTER_SANITIZE_STRING);
                            $Digital_DieCut_No = filter_var(trim($allDataInSheet[$i][$Digital_DieCut_No]), FILTER_SANITIZE_STRING);
                            $Digital_Availability = filter_var(trim($allDataInSheet[$i][$Digital_Availability]), FILTER_SANITIZE_STRING);
                            $Hot_Folder = filter_var(trim($allDataInSheet[$i][$Hot_Folder]), FILTER_SANITIZE_STRING);
                            $Variable_F = filter_var(trim($allDataInSheet[$i][$Variable_F]), FILTER_SANITIZE_STRING);
                            $Variable_B = filter_var(trim($allDataInSheet[$i][$Variable_B]), FILTER_SANITIZE_STRING);
                            $DieCut_Machine = filter_var(trim($allDataInSheet[$i][$DieCut_Machine]), FILTER_SANITIZE_STRING);
                            $DieCut_No = filter_var(trim($allDataInSheet[$i][$DieCut_No]), FILTER_SANITIZE_STRING);
                            $Suited_Machine = filter_var(trim($allDataInSheet[$i][$Suited_Machine]), FILTER_SANITIZE_STRING);

                            $Digital_Machine = filter_var(trim($allDataInSheet[$i][$Digital_Machine]), FILTER_SANITIZE_STRING);
                            $Special_Instruction = filter_var(trim($allDataInSheet[$i][$Special_Instruction]), FILTER_SANITIZE_STRING);
                            $Crocking_Test = filter_var(trim($allDataInSheet[$i][$Crocking_Test]), FILTER_SANITIZE_STRING);
                            $SubContract = filter_var(trim($allDataInSheet[$i][$SubContract]), FILTER_SANITIZE_STRING);
                            $SubContract_Detail = filter_var(trim($allDataInSheet[$i][$SubContract_Detail]), FILTER_SANITIZE_STRING);
                            $Process = filter_var(trim($allDataInSheet[$i][$Process]), FILTER_SANITIZE_STRING);
                            $Special_Drying_Time = filter_var(trim($allDataInSheet[$i][$Special_Drying_Time]), FILTER_SANITIZE_STRING);
                            $Standard_LeadTime = filter_var(trim($allDataInSheet[$i][$Standard_LeadTime]), FILTER_SANITIZE_STRING);
                            $Hole = filter_var(trim($allDataInSheet[$i][$Hole]), FILTER_SANITIZE_STRING);
                            $UV_F = filter_var(trim($allDataInSheet[$i][$UV_F]), FILTER_SANITIZE_STRING);

                            $UV_B = filter_var(trim($allDataInSheet[$i][$UV_B]), FILTER_SANITIZE_STRING);
                            $Active = filter_var(trim($allDataInSheet[$i][$Active]), FILTER_SANITIZE_STRING);
                            $Color_Management = filter_var(trim($allDataInSheet[$i][$Color_Management]), FILTER_SANITIZE_STRING);
                            $CS_Sample = filter_var(trim($allDataInSheet[$i][$CS_Sample]), FILTER_SANITIZE_STRING);
                            $Last_Order_Time = filter_var(trim($allDataInSheet[$i][$Last_Order_Time]), FILTER_SANITIZE_STRING);
                            $Last_Revise_Date = filter_var(trim($allDataInSheet[$i][$Last_Revise_Date]), FILTER_SANITIZE_STRING);
                            $Original_System = filter_var(trim($allDataInSheet[$i][$Original_System]), FILTER_SANITIZE_STRING);
                            $PE_Name = filter_var(trim($allDataInSheet[$i][$PE_Name]), FILTER_SANITIZE_STRING);
                            $PE_Receive_Date = filter_var(trim($allDataInSheet[$i][$PE_Receive_Date]), FILTER_SANITIZE_STRING);
                            $Ready_Date = filter_var(trim($allDataInSheet[$i][$Ready_Date]), FILTER_SANITIZE_STRING);
                            
                            $Ready_Date = date('Y-m-d', strtotime($Ready_Date) ); 

                            $Revise_People = filter_var(trim($allDataInSheet[$i][$Revise_People]), FILTER_SANITIZE_STRING);
                            $Scrap_Adjustment = filter_var(trim($allDataInSheet[$i][$Scrap_Adjustment]), FILTER_SANITIZE_STRING);
                            $Social_Compliance = filter_var(trim($allDataInSheet[$i][$Social_Compliance]), FILTER_SANITIZE_STRING);
                            $Status = filter_var(trim($allDataInSheet[$i][$Status]), FILTER_SANITIZE_STRING);
                            $Setup_Date = filter_var(trim($allDataInSheet[$i][$Setup_Date]), FILTER_SANITIZE_STRING);
                            $Offset_Extra_Time = filter_var(trim($allDataInSheet[$i][$Offset_Extra_Time]), FILTER_SANITIZE_STRING);
                            $Offset_Waiting_Drying = filter_var(trim($allDataInSheet[$i][$Offset_Waiting_Drying]), FILTER_SANITIZE_STRING);
                            $Digital_Extra_Time = filter_var(trim($allDataInSheet[$i][$Digital_Extra_Time]), FILTER_SANITIZE_STRING);
                            $Digital_Waiting_Drying = filter_var(trim($allDataInSheet[$i][$Digital_Waiting_Drying]), FILTER_SANITIZE_STRING);
                            $FSC = filter_var(trim($allDataInSheet[$i][$FSC]), FILTER_SANITIZE_STRING);

                            $Finishing_Difficult_Rate = filter_var(trim($allDataInSheet[$i][$Finishing_Difficult_Rate]), FILTER_SANITIZE_STRING);
                            $StringCut_ComboTag = filter_var(trim($allDataInSheet[$i][$StringCut_ComboTag]), FILTER_SANITIZE_STRING);
                            $FirstOrder = filter_var(trim($allDataInSheet[$i][$FirstOrder]), FILTER_SANITIZE_STRING);
                            $Inactive_Reason = filter_var(trim($allDataInSheet[$i][$Inactive_Reason]), FILTER_SANITIZE_STRING);
                            $CheckReplaceMaterial = filter_var(trim($allDataInSheet[$i][$CheckReplaceMaterial]), FILTER_SANITIZE_STRING);
                            $Brand_Protection = trim($allDataInSheet[$i][$Brand_Protection]);
                            $Brand_Protection = ($Brand_Protection == "1" || $Brand_Protection == 1 ) ? $Brand_Protection = "1" : "0";
                            
                            
                        
                        // check empty data
                        if (empty($GLID) || ($GLID == ' ') || ($GLID == '') ) continue;
                        
                        // get data
                            $updateData[] = array(
                                'GLID' => $GLID, 
                                'Item_Code' => $Item_Code, 
                                'Buying_Office' => $Buying_Office,
                                'Fit_Variable' => $Fit_Variable,
                                'Production_Type' => $Production_Type,
                                'Production_Line' => $Production_Line,
                                'OS_Sample' => $OS_Sample,
                                'DS_Sample' => $DS_Sample,
                                'ProductionWidth' => $ProductionWidth,
                                'ProductionLength' => $ProductionLength,
                                
                                'Sheet_Size' => $Sheet_Size,
                                'Stock_Code' => $Stock_Code,
                                'Color_F' => $Color_F,
                                'Color_B' => $Color_B,
                                'Color_FQ' => $Color_FQ,
                                'Color_BQ' => $Color_BQ,
                                'Varnish_F' => $Varnish_F,
                                'Varnish_B' => $Varnish_B,
                                'Imprint_B' => $Imprint_B,
                                'Imprint_F' => $Imprint_F,

                                'Offset_Level' => $Offset_Level,
                                'Offset_Imp_Front' => $Offset_Imp_Front,
                                'Offset_Imp_Back' => $Offset_Imp_Back,
                                'Offset_UPS' => $Offset_UPS,
                                'Offset_Cut_No' => $Offset_Cut_No,
                                'Digital_Level' => $Digital_Level,
                                'Digital_F_Click' => $Digital_F_Click,
                                'Digital_B_Click' => $Digital_B_Click,
                                'Digital_UPS' => $Digital_UPS,
                                'Digital_Cut_No' => $Digital_Cut_No,

                                'Digital_Sheet_Size' => $Digital_Sheet_Size,
                                'Digital_Stock_Code_F' => $Digital_Stock_Code_F,
                                'Digital_DieCut_No' => $Digital_DieCut_No,
                                'Digital_Availability' => $Digital_Availability,
                                'Hot_Folder' => $Hot_Folder,
                                'Variable_F' => $Variable_F,
                                'Variable_B' => $Variable_B,
                                'DieCut_Machine' => $DieCut_Machine,
                                'DieCut_No' => $DieCut_No,
                                'Suited_Machine' => $Suited_Machine,

                                'Digital_Machine' => $Digital_Machine,
                                'Special_Instruction' => htmlspecialchars($Special_Instruction, ENT_QUOTES),
                                'Crocking_Test' => $Crocking_Test,
                                'SubContract' => $SubContract,
                                'SubContract_Detail' => htmlspecialchars($SubContract_Detail, ENT_QUOTES),
                                'Process' => $Process,
                                'Special_Drying_Time' => $Special_Drying_Time,
                                'Standard_LeadTime' => $Standard_LeadTime,
                                'Hole' => $Hole,
                                'UV_F' => $UV_F,

                                'UV_B' => $UV_B,
                                'Active' => $Active,
                                'Color_Management' => $Color_Management,
                                'CS_Sample' => $CS_Sample,
                                'Last_Order_Time' => $Last_Order_Time,
                                'Last_Revise_Date' => $Last_Revise_Date,
                                'Original_System' => $Original_System,
                                'PE_Name' => $PE_Name,
                                'PE_Receive_Date' => $PE_Receive_Date,
                                'Ready_Date' => $Ready_Date,

                                'Revise_People' => $Revise_People,
                                'Scrap_Adjustment' => $Scrap_Adjustment,
                                'Social_Compliance' => $Social_Compliance,
                                'Status' => $Status,
                                'Setup_Date' => $Setup_Date,
                                'Offset_Extra_Time' => $Offset_Extra_Time,
                                'Offset_Waiting_Drying' => $Offset_Waiting_Drying,
                                'Digital_Extra_Time' => $Digital_Extra_Time,
                                'Digital_Waiting_Drying' => $Digital_Waiting_Drying,
                                'FSC' => 'FSC',

                                'Finishing_Difficult_Rate' => $Finishing_Difficult_Rate,
                                'StringCut_ComboTag' => $StringCut_ComboTag,
                                'FirstOrder' => $FirstOrder,
                                'Inactive_Reason' => $Inactive_Reason,
                                'CheckReplaceMaterial' => $CheckReplaceMaterial,
                                'Brand_Protection' => $Brand_Protection,
                                'Updated_By' => $updateBy,
                                'Updated_Date' => date('Y-m-d H:i:s')
                            );
                    }
                }

                $index = 0;
                $error_sql = '';
                $count = 0; // đếm thành công
                if (!empty($updateData) ) {
                    
                    $results = true;

                    // load data
                    foreach ($updateData as $keyU => $updateItem ) {
                        
                        $index++;
                        $update_sql = "" ; // reset update_sql

                        $GLID = $updateItem['GLID'];
                        
                        // check empty data
                        if (empty($GLID) || ($GLID == ' ') || ($GLID == '') ) continue;

                        // add
                            $sql = "SELECT `GLID` FROM $table WHERE `GLID`='$GLID' ORDER BY ID DESC LIMIT 1;";
                            $query = mysqli_query($conn, $sql);
                            if (!$query ) {
                                $error_sql .= "$index. Error: $sql \n";
                            } else {
                                if (mysqli_num_rows($query) > 0 ) {
                                    $update_sql = " UPDATE $table SET ";
                                    foreach ($updateItem as $key => $value ) {
                                        if ($key == 'GLID' ) continue; // bỏ qua khóa chính
                                        $update_sql .= "`$key`='$value', "; // thêm vào giá trị
                                    }

                                    // cắt bỏ dấu phẩy cuối cùng
                                        $update_sql = substr($update_sql, 0, -1);
                                        $update_sql = rtrim($update_sql,","); 

                                    // where 
                                        $update_sql .= " WHERE `GLID`='$GLID' ; ";

                                    // exe update data
                                        $results = mysqli_query($conn, $update_sql); 
                                        if (!$results ) {
                                            $error_sql .= "$index. Error: $update_sql ; <br />\n";
                                            $message = "Import Data (Update) Error. Has stopped $index row ";
                                            break;
                                        } else {
                                            // delete row data updated
                                            unset($updateData[$keyU]);
                                            $count++;
                                            $message = "Import Data (Update) Success ";
                                        }
                                    
                                }
                            }
                    }

                    // Check update success, after insert data
                        if ($results ) {

                            // print_r($updateData); exit();
                                    
                            if (!empty($updateData) ) {
                                // init insert query
                                    $insert_sql = "INSERT INTO $table ( ";
                                // Lấy các cột trong bảng để chuẩn bị thêm dữ liệu
                                    foreach ($updateData as $k => $colArr ) { 

                                        foreach ($colArr as $kC => $valC ) {
                                            $insert_sql .= "$kC, ";
                                        }

                                        break;
                                    }
                                // cắt bỏ dấu phẩy cuối cùng
                                    $insert_sql = substr($insert_sql, 0, -1);
                                    $insert_sql = rtrim($insert_sql,","); 
                                // Nối đoạn sql cho hoàn chỉnh
                                    $insert_sql .= ") VALUES ";
                                // Load insert data
                                foreach ($updateData as $updateItem ) {
                                    // Lấy dữ liệu của 1 dòng
                                        $insert_sql .= "( ";
                                        foreach ($updateItem as $keyI => $valueI ) { $insert_sql .= "'$valueI', "; }
                                    // cắt bỏ dấu phẩy cuối cùng
                                        $insert_sql = substr($insert_sql, 0, -1);
                                        $insert_sql = rtrim($insert_sql,","); 
                                    // đóng ngoặc 1 dòng dữ liệu
                                        $insert_sql .= "), ";
                                    
                                }

                                // cắt bỏ dấu phẩy cuối cùng
                                    $insert_sql = substr($insert_sql, 0, -1);
                                    $insert_sql = rtrim($insert_sql,","); 
                                    $count++;
                            }

                            

                            // echo "insert_sql: $insert_sql "; exit();

                            // Xử lý update code vật tư mới
                                if (!empty($insert_sql) ) {
                                    $results = mysqli_query($conn, $insert_sql);
                                    if (!$results ) {
                                        $error_sql .= "Insert Error: $insert_sql \n";
                                        $message = "Import Data (Insert) Error ";
                                    } else {
                                        $message = "Import Data (Insert) Success ";
                                    }    
                                }
                        }
                }

            // write log error file 
                $fp = fopen('LogError.txt', 'a');//opens file in append mode  
                $error_sql = !empty($error_sql) ? $error_sql : "Import 0 Error. Import $count Rows Success ";
                $txt = "\n======================== IMPORT MASTER ITEM " . date('Y-m-d H:i:s') . " ======================== \n" . $error_sql;
                fwrite($fp, $txt);
                fclose($fp);
            // and close db
                mysqli_close($conn);
            
        } else {
            $message = "Invalid File Type. Upload Excel File.";
        }
    }


?>
<script>
    var message = '<?php  echo $message; ?>';
    alert(message);
    window.location="./index.php";
</script>
