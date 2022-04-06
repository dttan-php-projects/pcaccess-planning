<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    ini_set('memory_limit','-1'); // enabled the full memory available.
	ini_set('max_execution_time',99999999999);  // set time 10 minutes

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

    function load($max, $count, $rowCount, $spreadsheet )
    { 
        // database
        $conn = _conn();
        $table = "access_item_information";

        do {
            
            // get data
            $start = $count;
            $end = (($start+5000) >= $max) ? $max : ($start+5000);

            $sql = "SELECT * FROM $table ORDER BY `ID` DESC LIMIT $start,$end;";
            $statement = mysqli_query($conn, $sql);
            if (mysqli_num_rows($statement) > 0 ) {

                while($element = mysqli_fetch_assoc($statement)) {

                    $count++;
                    $rowCount++;

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

                    // add to excel file
                    $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $count );

                    $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $GLID);
                    $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $Item_Code);
                    $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $Buying_Office);
                    $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, $Fit_Variable);
                    $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, $Production_Type);
                    $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, $Production_Line);
                    $spreadsheet->getActiveSheet()->SetCellValue('H' . $rowCount, $OS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('I' . $rowCount, $DS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('J' . $rowCount, $ProductionWidth);
                    $spreadsheet->getActiveSheet()->SetCellValue('K' . $rowCount, $ProductionLength);

                    $spreadsheet->getActiveSheet()->SetCellValue('L' . $rowCount, $Sheet_Size);
                    $spreadsheet->getActiveSheet()->SetCellValue('M' . $rowCount, $Stock_Code);    
                    $spreadsheet->getActiveSheet()->SetCellValue('N' . $rowCount, $Color_F);    
                    $spreadsheet->getActiveSheet()->SetCellValue('O' . $rowCount, $Color_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('P' . $rowCount, $Color_FQ);
                    $spreadsheet->getActiveSheet()->SetCellValue('Q' . $rowCount, $Color_BQ);
                    $spreadsheet->getActiveSheet()->SetCellValue('R' . $rowCount, $Varnish_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('S' . $rowCount, $Varnish_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('T' . $rowCount, $Imprint_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('U' . $rowCount, $Imprint_F);

                    $spreadsheet->getActiveSheet()->SetCellValue('V' . $rowCount, $Offset_Level);
                    $spreadsheet->getActiveSheet()->SetCellValue('W' . $rowCount, $Offset_Imp_Front);
                    $spreadsheet->getActiveSheet()->SetCellValue('X' . $rowCount, $Offset_Imp_Back);
                    $spreadsheet->getActiveSheet()->SetCellValue('Y' . $rowCount, $Offset_UPS);
                    $spreadsheet->getActiveSheet()->SetCellValue('Z' . $rowCount, $Offset_Cut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AA' . $rowCount, $Digital_Level);
                    $spreadsheet->getActiveSheet()->SetCellValue('AB' . $rowCount, $Digital_F_Click);
                    $spreadsheet->getActiveSheet()->SetCellValue('AC' . $rowCount, $Digital_B_Click);
                    $spreadsheet->getActiveSheet()->SetCellValue('AD' . $rowCount, $Digital_UPS);
                    $spreadsheet->getActiveSheet()->SetCellValue('AE' . $rowCount, $Digital_Cut_No);

                    $spreadsheet->getActiveSheet()->SetCellValue('AF' . $rowCount, $Digital_Sheet_Size);
                    $spreadsheet->getActiveSheet()->SetCellValue('AG' . $rowCount, $Digital_Stock_Code_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('AH' . $rowCount, $Digital_DieCut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AI' . $rowCount, $Digital_Availability);
                    $spreadsheet->getActiveSheet()->SetCellValue('AJ' . $rowCount, $Hot_Folder);
                    $spreadsheet->getActiveSheet()->SetCellValue('AK' . $rowCount, $Variable_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('AL' . $rowCount, $Variable_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('AM' . $rowCount, $DieCut_Machine);
                    $spreadsheet->getActiveSheet()->SetCellValue('AN' . $rowCount, $DieCut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AO' . $rowCount, $Suited_Machine);

                    $spreadsheet->getActiveSheet()->SetCellValue('AP' . $rowCount, $Digital_Machine);
                    $spreadsheet->getActiveSheet()->SetCellValue('AQ' . $rowCount, $Special_Instruction);
                    $spreadsheet->getActiveSheet()->SetCellValue('AR' . $rowCount, $Crocking_Test);
                    $spreadsheet->getActiveSheet()->SetCellValue('AS' . $rowCount, $SubContract);
                    $spreadsheet->getActiveSheet()->SetCellValue('AT' . $rowCount, $SubContract_Detail);
                    $spreadsheet->getActiveSheet()->SetCellValue('AU' . $rowCount, $Process);
                    $spreadsheet->getActiveSheet()->SetCellValue('AV' . $rowCount, $Special_Drying_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('AW' . $rowCount, $Standard_LeadTime);
                    $spreadsheet->getActiveSheet()->SetCellValue('AX' . $rowCount, $Hole);
                    $spreadsheet->getActiveSheet()->SetCellValue('AY' . $rowCount, $UV_F);

                    $spreadsheet->getActiveSheet()->SetCellValue('AZ' . $rowCount, $UV_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('BA' . $rowCount, $Active);
                    $spreadsheet->getActiveSheet()->SetCellValue('BB' . $rowCount, $Color_Management);
                    $spreadsheet->getActiveSheet()->SetCellValue('BC' . $rowCount, $CS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('BD' . $rowCount, $Last_Order_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BE' . $rowCount, $Last_Revise_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BF' . $rowCount, $Original_System);
                    $spreadsheet->getActiveSheet()->SetCellValue('BG' . $rowCount, $PE_Name);
                    $spreadsheet->getActiveSheet()->SetCellValue('BH' . $rowCount, $PE_Receive_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BI' . $rowCount, $Ready_Date);

                    $spreadsheet->getActiveSheet()->SetCellValue('BJ' . $rowCount, $Revise_People);
                    $spreadsheet->getActiveSheet()->SetCellValue('BK' . $rowCount, $Scrap_Adjustment);
                    $spreadsheet->getActiveSheet()->SetCellValue('BL' . $rowCount, $Social_Compliance);
                    $spreadsheet->getActiveSheet()->SetCellValue('BM' . $rowCount, $Status);
                    $spreadsheet->getActiveSheet()->SetCellValue('BN' . $rowCount, $Setup_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BO' . $rowCount, $Offset_Extra_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BP' . $rowCount, $Offset_Waiting_Drying);
                    $spreadsheet->getActiveSheet()->SetCellValue('BQ' . $rowCount, $Digital_Extra_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BR' . $rowCount, $Digital_Waiting_Drying);
                    $spreadsheet->getActiveSheet()->SetCellValue('BS' . $rowCount, $FSC);

                    $spreadsheet->getActiveSheet()->SetCellValue('BT' . $rowCount, $Finishing_Difficult_Rate);
                    $spreadsheet->getActiveSheet()->SetCellValue('BU' . $rowCount, $StringCut_ComboTag);
                    $spreadsheet->getActiveSheet()->SetCellValue('BV' . $rowCount, $FirstOrder);
                    $spreadsheet->getActiveSheet()->SetCellValue('BW' . $rowCount, $Inactive_Reason);
                    $spreadsheet->getActiveSheet()->SetCellValue('BX' . $rowCount, $CheckReplaceMaterial);
                    $spreadsheet->getActiveSheet()->SetCellValue('BY' . $rowCount, $Brand_Protection);

                    $spreadsheet->getActiveSheet()->SetCellValue('BZ' . $rowCount, $Updated_By);
                    $spreadsheet->getActiveSheet()->SetCellValue('CA' . $rowCount, $Updated_Date);


                }
            }

            // đệ qui
            load($max, $count, $rowCount, $spreadsheet);

        } while ($end <= $max );

    }

    function load2($max, $count, $rowCount, $spreadsheet )
    { 
        // database
        $conn = _conn();
        $table = "access_item_information";

        do {
            
            // get data
            $start = $count+1;
            $end = (isset($end) ) ? $max : ((int)($max/2) );

            $sql = "SELECT * FROM $table ORDER BY `ID` DESC LIMIT $start,$end;";
            $statement = mysqli_query($conn, $sql);
            if (mysqli_num_rows($statement) > 0 ) {

                while($element = mysqli_fetch_assoc($statement)) {

                    $count++;
                    $rowCount++;

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

                    // add to excel file
                    $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $count );

                    $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $GLID);
                    $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $Item_Code);
                    $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $Buying_Office);
                    $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, $Fit_Variable);
                    $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, $Production_Type);
                    $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, $Production_Line);
                    $spreadsheet->getActiveSheet()->SetCellValue('H' . $rowCount, $OS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('I' . $rowCount, $DS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('J' . $rowCount, $ProductionWidth);
                    $spreadsheet->getActiveSheet()->SetCellValue('K' . $rowCount, $ProductionLength);

                    $spreadsheet->getActiveSheet()->SetCellValue('L' . $rowCount, $Sheet_Size);
                    $spreadsheet->getActiveSheet()->SetCellValue('M' . $rowCount, $Stock_Code);    
                    $spreadsheet->getActiveSheet()->SetCellValue('N' . $rowCount, $Color_F);    
                    $spreadsheet->getActiveSheet()->SetCellValue('O' . $rowCount, $Color_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('P' . $rowCount, $Color_FQ);
                    $spreadsheet->getActiveSheet()->SetCellValue('Q' . $rowCount, $Color_BQ);
                    $spreadsheet->getActiveSheet()->SetCellValue('R' . $rowCount, $Varnish_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('S' . $rowCount, $Varnish_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('T' . $rowCount, $Imprint_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('U' . $rowCount, $Imprint_F);

                    $spreadsheet->getActiveSheet()->SetCellValue('V' . $rowCount, $Offset_Level);
                    $spreadsheet->getActiveSheet()->SetCellValue('W' . $rowCount, $Offset_Imp_Front);
                    $spreadsheet->getActiveSheet()->SetCellValue('X' . $rowCount, $Offset_Imp_Back);
                    $spreadsheet->getActiveSheet()->SetCellValue('Y' . $rowCount, $Offset_UPS);
                    $spreadsheet->getActiveSheet()->SetCellValue('Z' . $rowCount, $Offset_Cut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AA' . $rowCount, $Digital_Level);
                    $spreadsheet->getActiveSheet()->SetCellValue('AB' . $rowCount, $Digital_F_Click);
                    $spreadsheet->getActiveSheet()->SetCellValue('AC' . $rowCount, $Digital_B_Click);
                    $spreadsheet->getActiveSheet()->SetCellValue('AD' . $rowCount, $Digital_UPS);
                    $spreadsheet->getActiveSheet()->SetCellValue('AE' . $rowCount, $Digital_Cut_No);

                    $spreadsheet->getActiveSheet()->SetCellValue('AF' . $rowCount, $Digital_Sheet_Size);
                    $spreadsheet->getActiveSheet()->SetCellValue('AG' . $rowCount, $Digital_Stock_Code_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('AH' . $rowCount, $Digital_DieCut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AI' . $rowCount, $Digital_Availability);
                    $spreadsheet->getActiveSheet()->SetCellValue('AJ' . $rowCount, $Hot_Folder);
                    $spreadsheet->getActiveSheet()->SetCellValue('AK' . $rowCount, $Variable_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('AL' . $rowCount, $Variable_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('AM' . $rowCount, $DieCut_Machine);
                    $spreadsheet->getActiveSheet()->SetCellValue('AN' . $rowCount, $DieCut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AO' . $rowCount, $Suited_Machine);

                    $spreadsheet->getActiveSheet()->SetCellValue('AP' . $rowCount, $Digital_Machine);
                    $spreadsheet->getActiveSheet()->SetCellValue('AQ' . $rowCount, $Special_Instruction);
                    $spreadsheet->getActiveSheet()->SetCellValue('AR' . $rowCount, $Crocking_Test);
                    $spreadsheet->getActiveSheet()->SetCellValue('AS' . $rowCount, $SubContract);
                    $spreadsheet->getActiveSheet()->SetCellValue('AT' . $rowCount, $SubContract_Detail);
                    $spreadsheet->getActiveSheet()->SetCellValue('AU' . $rowCount, $Process);
                    $spreadsheet->getActiveSheet()->SetCellValue('AV' . $rowCount, $Special_Drying_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('AW' . $rowCount, $Standard_LeadTime);
                    $spreadsheet->getActiveSheet()->SetCellValue('AX' . $rowCount, $Hole);
                    $spreadsheet->getActiveSheet()->SetCellValue('AY' . $rowCount, $UV_F);

                    $spreadsheet->getActiveSheet()->SetCellValue('AZ' . $rowCount, $UV_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('BA' . $rowCount, $Active);
                    $spreadsheet->getActiveSheet()->SetCellValue('BB' . $rowCount, $Color_Management);
                    $spreadsheet->getActiveSheet()->SetCellValue('BC' . $rowCount, $CS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('BD' . $rowCount, $Last_Order_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BE' . $rowCount, $Last_Revise_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BF' . $rowCount, $Original_System);
                    $spreadsheet->getActiveSheet()->SetCellValue('BG' . $rowCount, $PE_Name);
                    $spreadsheet->getActiveSheet()->SetCellValue('BH' . $rowCount, $PE_Receive_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BI' . $rowCount, $Ready_Date);

                    $spreadsheet->getActiveSheet()->SetCellValue('BJ' . $rowCount, $Revise_People);
                    $spreadsheet->getActiveSheet()->SetCellValue('BK' . $rowCount, $Scrap_Adjustment);
                    $spreadsheet->getActiveSheet()->SetCellValue('BL' . $rowCount, $Social_Compliance);
                    $spreadsheet->getActiveSheet()->SetCellValue('BM' . $rowCount, $Status);
                    $spreadsheet->getActiveSheet()->SetCellValue('BN' . $rowCount, $Setup_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BO' . $rowCount, $Offset_Extra_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BP' . $rowCount, $Offset_Waiting_Drying);
                    $spreadsheet->getActiveSheet()->SetCellValue('BQ' . $rowCount, $Digital_Extra_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BR' . $rowCount, $Digital_Waiting_Drying);
                    $spreadsheet->getActiveSheet()->SetCellValue('BS' . $rowCount, $FSC);

                    $spreadsheet->getActiveSheet()->SetCellValue('BT' . $rowCount, $Finishing_Difficult_Rate);
                    $spreadsheet->getActiveSheet()->SetCellValue('BU' . $rowCount, $StringCut_ComboTag);
                    $spreadsheet->getActiveSheet()->SetCellValue('BV' . $rowCount, $FirstOrder);
                    $spreadsheet->getActiveSheet()->SetCellValue('BW' . $rowCount, $Inactive_Reason);
                    $spreadsheet->getActiveSheet()->SetCellValue('BX' . $rowCount, $CheckReplaceMaterial);
                    $spreadsheet->getActiveSheet()->SetCellValue('BY' . $rowCount, $Brand_Protection);

                    $spreadsheet->getActiveSheet()->SetCellValue('BZ' . $rowCount, $Updated_By);
                    $spreadsheet->getActiveSheet()->SetCellValue('CA' . $rowCount, $Updated_Date);


                }
            }

            ob_clean();

            // đệ qui
            load($max, $count, $rowCount, $spreadsheet);

        } while ($end <= $max );

    }

    // database
    $conn = _conn();
    $table = "access_item_information";

    // create
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

    // set the names of header cells

        // set Header, width
        $columns = array(
            'A', 
            'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 
            'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 
            'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 
            'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 
            'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 
            'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 
            'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 
            'BT', 'BU', 'BV', 'BW', 'BX', 
            'BY', 'BZ', 'CA'
        );
        
        // // Add new sheet
        // $spreadsheet->createSheet();

        // Add some data
        $spreadsheet->setActiveSheetIndex(0);

        // active and set title
        $spreadsheet->getActiveSheet()->setTitle('MasterData');

        $headers = array( 
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

        $id = 0;
        foreach ($headers as $header) {
            for ($index = $id; $index < count($headers); $index++) {
                // width
                if ($id == 0 ) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columns[$index])->setWidth(6);
                } else {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columns[$index])->setWidth(20);
                }
                

                // headers
                $spreadsheet->getActiveSheet()->setCellValue($columns[$index] . '1', $header);

                $id++;
                break;
            }
        }


    // Font
    $spreadsheet->getActiveSheet()->getStyle('A1:CA1')->getFont()->setBold(true)->setName('Arial')->setSize(10);
    $spreadsheet->getActiveSheet()->getStyle('A1:CA1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('3399ff');
    $spreadsheet->getActiveSheet()->getStyle('A:CA')->getFont()->setName('Arial')->setSize(10);

    // get limit
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 'limit';

    // get data
    $sql = "SELECT * FROM $table ORDER BY `ID` DESC";
    // check limit 
    if ($limit == 'all' ) {
        // để chặn trường hợp người dùng tự bỏ vào limit trên link
    } else {
        $sql .= " LIMIT 1000;";
    }

    // ============== GET DATA ==============================================

        // count all
        $statement = mysqli_query($conn, "SELECT count(*) as `COUNT` FROM $table;");
        $max = mysqli_fetch_assoc($statement)['COUNT'];

        $count = 0;
        $rowCount = 1;
        $start = 0;
        $end = 1000;

        $max = 6000;
        while ($end <= $max ) {

            $sql = "SELECT * FROM $table ORDER BY `ID` ASC LIMIT $start, $end;";
            $statement = mysqli_query($conn, $sql);

            if (mysqli_num_rows($statement) > 0 ) {

                while($element = mysqli_fetch_assoc($statement)) {

                    $count++;
                    $rowCount++;

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

                    // add to excel file
                    $spreadsheet->getActiveSheet()->SetCellValue('A' . $rowCount, $count );

                    $spreadsheet->getActiveSheet()->SetCellValue('B' . $rowCount, $GLID);
                    $spreadsheet->getActiveSheet()->SetCellValue('C' . $rowCount, $Item_Code);
                    $spreadsheet->getActiveSheet()->SetCellValue('D' . $rowCount, $Buying_Office);
                    $spreadsheet->getActiveSheet()->SetCellValue('E' . $rowCount, $Fit_Variable);
                    $spreadsheet->getActiveSheet()->SetCellValue('F' . $rowCount, $Production_Type);
                    $spreadsheet->getActiveSheet()->SetCellValue('G' . $rowCount, $Production_Line);
                    $spreadsheet->getActiveSheet()->SetCellValue('H' . $rowCount, $OS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('I' . $rowCount, $DS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('J' . $rowCount, $ProductionWidth);
                    $spreadsheet->getActiveSheet()->SetCellValue('K' . $rowCount, $ProductionLength);

                    $spreadsheet->getActiveSheet()->SetCellValue('L' . $rowCount, $Sheet_Size);
                    $spreadsheet->getActiveSheet()->SetCellValue('M' . $rowCount, $Stock_Code);    
                    $spreadsheet->getActiveSheet()->SetCellValue('N' . $rowCount, $Color_F);    
                    $spreadsheet->getActiveSheet()->SetCellValue('O' . $rowCount, $Color_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('P' . $rowCount, $Color_FQ);
                    $spreadsheet->getActiveSheet()->SetCellValue('Q' . $rowCount, $Color_BQ);
                    $spreadsheet->getActiveSheet()->SetCellValue('R' . $rowCount, $Varnish_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('S' . $rowCount, $Varnish_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('T' . $rowCount, $Imprint_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('U' . $rowCount, $Imprint_F);

                    $spreadsheet->getActiveSheet()->SetCellValue('V' . $rowCount, $Offset_Level);
                    $spreadsheet->getActiveSheet()->SetCellValue('W' . $rowCount, $Offset_Imp_Front);
                    $spreadsheet->getActiveSheet()->SetCellValue('X' . $rowCount, $Offset_Imp_Back);
                    $spreadsheet->getActiveSheet()->SetCellValue('Y' . $rowCount, $Offset_UPS);
                    $spreadsheet->getActiveSheet()->SetCellValue('Z' . $rowCount, $Offset_Cut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AA' . $rowCount, $Digital_Level);
                    $spreadsheet->getActiveSheet()->SetCellValue('AB' . $rowCount, $Digital_F_Click);
                    $spreadsheet->getActiveSheet()->SetCellValue('AC' . $rowCount, $Digital_B_Click);
                    $spreadsheet->getActiveSheet()->SetCellValue('AD' . $rowCount, $Digital_UPS);
                    $spreadsheet->getActiveSheet()->SetCellValue('AE' . $rowCount, $Digital_Cut_No);

                    $spreadsheet->getActiveSheet()->SetCellValue('AF' . $rowCount, $Digital_Sheet_Size);
                    $spreadsheet->getActiveSheet()->SetCellValue('AG' . $rowCount, $Digital_Stock_Code_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('AH' . $rowCount, $Digital_DieCut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AI' . $rowCount, $Digital_Availability);
                    $spreadsheet->getActiveSheet()->SetCellValue('AJ' . $rowCount, $Hot_Folder);
                    $spreadsheet->getActiveSheet()->SetCellValue('AK' . $rowCount, $Variable_F);
                    $spreadsheet->getActiveSheet()->SetCellValue('AL' . $rowCount, $Variable_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('AM' . $rowCount, $DieCut_Machine);
                    $spreadsheet->getActiveSheet()->SetCellValue('AN' . $rowCount, $DieCut_No);
                    $spreadsheet->getActiveSheet()->SetCellValue('AO' . $rowCount, $Suited_Machine);

                    $spreadsheet->getActiveSheet()->SetCellValue('AP' . $rowCount, $Digital_Machine);
                    $spreadsheet->getActiveSheet()->SetCellValue('AQ' . $rowCount, $Special_Instruction);
                    $spreadsheet->getActiveSheet()->SetCellValue('AR' . $rowCount, $Crocking_Test);
                    $spreadsheet->getActiveSheet()->SetCellValue('AS' . $rowCount, $SubContract);
                    $spreadsheet->getActiveSheet()->SetCellValue('AT' . $rowCount, $SubContract_Detail);
                    $spreadsheet->getActiveSheet()->SetCellValue('AU' . $rowCount, $Process);
                    $spreadsheet->getActiveSheet()->SetCellValue('AV' . $rowCount, $Special_Drying_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('AW' . $rowCount, $Standard_LeadTime);
                    $spreadsheet->getActiveSheet()->SetCellValue('AX' . $rowCount, $Hole);
                    $spreadsheet->getActiveSheet()->SetCellValue('AY' . $rowCount, $UV_F);

                    $spreadsheet->getActiveSheet()->SetCellValue('AZ' . $rowCount, $UV_B);
                    $spreadsheet->getActiveSheet()->SetCellValue('BA' . $rowCount, $Active);
                    $spreadsheet->getActiveSheet()->SetCellValue('BB' . $rowCount, $Color_Management);
                    $spreadsheet->getActiveSheet()->SetCellValue('BC' . $rowCount, $CS_Sample);
                    $spreadsheet->getActiveSheet()->SetCellValue('BD' . $rowCount, $Last_Order_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BE' . $rowCount, $Last_Revise_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BF' . $rowCount, $Original_System);
                    $spreadsheet->getActiveSheet()->SetCellValue('BG' . $rowCount, $PE_Name);
                    $spreadsheet->getActiveSheet()->SetCellValue('BH' . $rowCount, $PE_Receive_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BI' . $rowCount, $Ready_Date);

                    $spreadsheet->getActiveSheet()->SetCellValue('BJ' . $rowCount, $Revise_People);
                    $spreadsheet->getActiveSheet()->SetCellValue('BK' . $rowCount, $Scrap_Adjustment);
                    $spreadsheet->getActiveSheet()->SetCellValue('BL' . $rowCount, $Social_Compliance);
                    $spreadsheet->getActiveSheet()->SetCellValue('BM' . $rowCount, $Status);
                    $spreadsheet->getActiveSheet()->SetCellValue('BN' . $rowCount, $Setup_Date);
                    $spreadsheet->getActiveSheet()->SetCellValue('BO' . $rowCount, $Offset_Extra_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BP' . $rowCount, $Offset_Waiting_Drying);
                    $spreadsheet->getActiveSheet()->SetCellValue('BQ' . $rowCount, $Digital_Extra_Time);
                    $spreadsheet->getActiveSheet()->SetCellValue('BR' . $rowCount, $Digital_Waiting_Drying);
                    $spreadsheet->getActiveSheet()->SetCellValue('BS' . $rowCount, $FSC);

                    $spreadsheet->getActiveSheet()->SetCellValue('BT' . $rowCount, $Finishing_Difficult_Rate);
                    $spreadsheet->getActiveSheet()->SetCellValue('BU' . $rowCount, $StringCut_ComboTag);
                    $spreadsheet->getActiveSheet()->SetCellValue('BV' . $rowCount, $FirstOrder);
                    $spreadsheet->getActiveSheet()->SetCellValue('BW' . $rowCount, $Inactive_Reason);
                    $spreadsheet->getActiveSheet()->SetCellValue('BX' . $rowCount, $CheckReplaceMaterial);
                    $spreadsheet->getActiveSheet()->SetCellValue('BY' . $rowCount, $Brand_Protection);

                    $spreadsheet->getActiveSheet()->SetCellValue('BZ' . $rowCount, $Updated_By);
                    $spreadsheet->getActiveSheet()->SetCellValue('CA' . $rowCount, $Updated_Date);


                }
            }

            $start = $end+1;
            $end = (($end+1000) >= $max) ? $max: ($end+1000);
        }
        

    // ============== OUTPUT ==============================================

        
        // set filename for excel file to be exported
        $filename = 'PC_MasterData_' . date("Y_m_d__H_i_s") . ".xlsx";

        // header: generate excel file
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\""); 
        
        header('Cache-Control: max-age=0');

        // writer
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        // free up memory
        $spreadsheet->disconnectWorksheets();
        $spreadsheet->garbageCollect();
        unset($spreadsheet);

        mysqli_close($conn);

    
    