<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require("../Module/Database.php");

    if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADDATAGRID" ) { 

        header('Content-type: text/xml');
        echo "<rows>";

            $header = '<head>
                    <column width="50" type="ch" align="center" sort="na">#</column>
                    <column width="60" type="ed" align="left" sort="str">No</column>
                    
                    <column width="100" type="ed" align="left" sort="str">GLID</column>
                    <column width="100" type="ed" align="left" sort="str">Item_Code</column>
                    <column width="100" type="ed" align="left" sort="str">Buying_Office</column>
                    <column width="100" type="ed" align="left" sort="str">Fit_Variable</column>
                    <column width="100" type="ed" align="left" sort="str">Production_Type</column>
                    <column width="100" type="ed" align="left" sort="str">Production_Line</column>
                    <column width="100" type="ed" align="left" sort="str">DS_Sample</column>
                    <column width="100" type="ed" align="left" sort="str">OS_Sample</column>
                    <column width="100" type="ed" align="left" sort="str">ProductionWidth</column>
                    <column width="100" type="ed" align="left" sort="str">ProductionLength</column>

                    <column width="100" type="ed" align="left" sort="str">Sheet_Size</column>
                    <column width="100" type="ed" align="left" sort="str">Stock_Code</column>
                    <column width="100" type="ed" align="left" sort="str">Color_F</column>
                    <column width="100" type="ed" align="left" sort="str">Color_B</column>
                    <column width="100" type="ed" align="left" sort="str">Color_FQ</column>
                    <column width="100" type="ed" align="left" sort="str">Color_BQ</column>
                    <column width="100" type="ed" align="left" sort="str">Varnish_F</column>
                    <column width="100" type="ed" align="left" sort="str">Varnish_B</column>
                    <column width="100" type="ed" align="left" sort="str">Imprint_B</column>
                    <column width="100" type="ed" align="left" sort="str">Imprint_F</column>

                    <column width="100" type="ed" align="left" sort="str">Offset_Level</column>
                    <column width="100" type="ed" align="left" sort="str">Offset_Imp_Front</column>
                    <column width="100" type="ed" align="left" sort="str">Offset_Imp_Back</column>
                    <column width="100" type="ed" align="left" sort="str">Offset_UPS</column>
                    <column width="100" type="ed" align="left" sort="str">Offset_Cut_No</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_Level</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_F_Click</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_B_Click</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_UPS</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_Cut_No</column>

                    <column width="100" type="ed" align="left" sort="str">Digital_Sheet_Size</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_Stock_Code_F</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_DieCut_No</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_Availability</column>
                    <column width="100" type="ed" align="left" sort="str">Hot_Folder</column>
                    <column width="100" type="ed" align="left" sort="str">Variable_F</column>
                    <column width="100" type="ed" align="left" sort="str">Variable_B</column>
                    <column width="100" type="ed" align="left" sort="str">DieCut_Machine</column>
                    <column width="100" type="ed" align="left" sort="str">DieCut_No</column>
                    <column width="100" type="ed" align="left" sort="str">Suited_Machine</column>

                    <column width="100" type="ed" align="left" sort="str">Digital_Machine</column>
                    <column width="100" type="txt" align="left" sort="str">Special_Instruction</column>
                    <column width="100" type="ed" align="left" sort="str">Crocking_Test</column>
                    <column width="100" type="ed" align="left" sort="str">SubContract</column>
                    <column width="100" type="ed" align="left" sort="str">SubContract_Detail</column>
                    <column width="100" type="ed" align="left" sort="str">Process</column>
                    <column width="100" type="ed" align="left" sort="str">Special_Drying_Time</column>
                    <column width="100" type="ed" align="left" sort="str">Standard_LeadTime</column>
                    <column width="100" type="ed" align="left" sort="str">Hole</column>
                    <column width="100" type="ed" align="left" sort="str">UV_F</column>

                    <column width="100" type="ed" align="left" sort="str">UV_B</column>
                    <column width="100" type="ed" align="left" sort="str">Active</column>
                    <column width="100" type="ed" align="left" sort="str">Color_Management</column>
                    <column width="100" type="ed" align="left" sort="str">CS_Sample</column>
                    <column width="100" type="ed" align="left" sort="str">Last_Order_Time</column>
                    <column width="100" type="ed" align="left" sort="str">Last_Revise_Date</column>
                    <column width="100" type="ed" align="left" sort="str">Original_System</column>
                    <column width="100" type="ed" align="left" sort="str">PE_Name</column>
                    <column width="100" type="ed" align="left" sort="str">PE_Receive_Date</column>
                    <column width="100" type="ed" align="left" sort="str">Ready_Date</column>

                    <column width="100" type="ed" align="left" sort="str">Revise_People</column>
                    <column width="100" type="ed" align="left" sort="str">Scrap_Adjustment</column>
                    <column width="100" type="ed" align="left" sort="str">Social_Compliance</column>
                    <column width="100" type="ed" align="left" sort="str">Status</column>
                    <column width="100" type="ed" align="left" sort="str">Setup_Date</column>
                    <column width="100" type="ed" align="left" sort="str">Offset_Extra_Time</column>
                    <column width="100" type="ed" align="left" sort="str">Offset_Waiting_Drying</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_Extra_Time</column>
                    <column width="100" type="ed" align="left" sort="str">Digital_Waiting_Drying</column>
                    <column width="100" type="ed" align="left" sort="str">FSC</column>

                    <column width="100" type="ed" align="left" sort="str">Finishing_Difficult_Rate</column>
                    <column width="100" type="ed" align="left" sort="str">StringCut_ComboTag</column>
                    <column width="100" type="ed" align="left" sort="str">FirstOrder</column>
                    <column width="100" type="ed" align="left" sort="str">Inactive_Reason</column>
                    <column width="100" type="ed" align="left" sort="str">Vật tư thay thế</column>
                    <column width="110" type="ed" align="left" sort="str">Brand Protection</column>

                    <column width="100" type="ed" align="left" sort="str">Updated By</column>
                    <column width="100" type="ed" align="left" sort="str">Updated Date</column>
                    
            </head>';

            // header
            echo $header;

            // get limit data
            $limit = (isset($_GET['limit']) && !empty($_GET['limit']) ) ? $_GET['limit'] : 'limit';
            // if (!is_int($limit) ) $limit = 0;

            // table
            $table = "access_item_information";
            $sql = "SELECT * FROM $table ORDER BY ID DESC ";
            if ($limit == 'limit' ) {
                $sql .= "LIMIT 1000;";
            }
             
            $masterItem = MiQuery( $sql, _conn());
            $index = 0;
            foreach($masterItem as $row) {

                $index++;

                echo '<row id="'. str_replace("&","&amp;",$row['ID']) .'">';
                    echo '<cell>0</cell>';
                    echo '<cell>'. $index .'</cell>';
                    
                    echo '<cell>'. str_replace("&","&amp;",$row['GLID']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Item_Code']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Buying_Office']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Fit_Variable']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Production_Type']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Production_Line']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['DS_Sample']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['OS_Sample']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['ProductionWidth']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['ProductionLength']) .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['Sheet_Size']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Stock_Code']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Color_F']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Color_B']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Color_FQ']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Color_BQ']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Varnish_F']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Varnish_B']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Imprint_B']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Imprint_F']) .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['Offset_Level']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Offset_Imp_Front']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Offset_Imp_Back']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Offset_UPS']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Offset_Cut_No']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Level']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_F_Click']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_B_Click']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_UPS']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Cut_No']) .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Sheet_Size']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Stock_Code_F']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_DieCut_No']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Availability']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Hot_Folder']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Variable_F']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Variable_B']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['DieCut_Machine']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['DieCut_No']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Suited_Machine']) .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Machine']) .'</cell>';
                    echo '<cell>'. htmlspecialchars($row['Special_Instruction'], ENT_QUOTES, 'UTF-8' ) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Crocking_Test']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['SubContract']) .'</cell>';
                    echo '<cell>'. htmlspecialchars($row['SubContract_Detail'], ENT_QUOTES, 'UTF-8' ) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Process']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Special_Drying_Time']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Standard_LeadTime']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Hole']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['UV_F']) .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['UV_B']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Active']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Color_Management']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['CS_Sample']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Last_Order_Time']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Last_Revise_Date']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Original_System']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['PE_Name']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['PE_Receive_Date']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Ready_Date']) .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['Revise_People']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Scrap_Adjustment']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Social_Compliance']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Status']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Setup_Date']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Offset_Extra_Time']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Offset_Waiting_Drying']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Extra_Time']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Digital_Waiting_Drying']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['FSC']) .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['Finishing_Difficult_Rate']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['StringCut_ComboTag']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['FirstOrder']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Inactive_Reason']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['CheckReplaceMaterial']) .'</cell>';

                    echo '<cell>'. $row['Brand_Protection'] .'</cell>';

                    echo '<cell>'. $row['Updated_By'] .'</cell>';
                    echo '<cell>'. $row['Updated_Date'] .'</cell>';
                    
                    

                // foreach($row as $K=>$S) {
                //     echo '<cell>' .str_replace("&","&amp;",$row[$K]). '</cell>';
                // }
                echo '</row>';
            }
        echo "</rows>";
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADPROCESSGRID" ) { 

        header('Content-type: text/xml');
        echo "<rows>";

            $header = '<head>
                    <column width="50" type="ch" align="center" sort="na">#</column>
                    <column width="60" type="ed" align="left" sort="str">No</column>
                    
                    <column width="100" type="ed" align="left" sort="str">Code</column>
                    <column width="100" type="ed" align="left" sort="str">Process</column>
                    <column width="100" type="ed" align="left" sort="str">Process_Ability</column>
                    <column width="100" type="ed" align="left" sort="str">Ability_Unit</column>
                    <column width="100" type="ed" align="left" sort="str">'.htmlspecialchars('<=500').'</column>
                    <column width="100" type="ed" align="left" sort="str">501-2000</column>
                    <column width="100" type="ed" align="left" sort="str">2001-5000</column>
                    <column width="100" type="ed" align="left" sort="str">>5000</column>
                    <column width="100" type="ed" align="left" sort="str">Remark</column>
                    <column width="100" type="ed" align="left" sort="str">VN_vi</column>

                    <column width="70" type="acheck" align="center" sort="str">Save</column>
                    <column width="70" type="acheck" align="center" sort="str">Delete</column>
                    
            </head>';
            
            echo $header;

            $table = "access_item_process";
            
            $masterItem = MiQuery( "SELECT `Code`, `Process`, `Process_Ability`, `Ability_Unit`, `<=500` as `QTY500`, `501-2000` as `QTY501_2000`, `2001-5000` as `QTY2001_5000`, `>5000` as `QTY5001`, `Remark`, `VN_vi`   FROM $table ORDER BY ID ASC;", _conn());
            $index = 0;
            foreach($masterItem as $key => $row) {

                $index++;

                echo '<row id="'.$key.'">';
                    echo '<cell>0</cell>';
                    echo '<cell>'. $index .'</cell>';

                    echo '<cell>'. str_replace("&","&amp;",$row['Code']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Process']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Process_Ability']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Ability_Unit']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['QTY500']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['QTY501_2000']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['QTY2001_5000']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['QTY5001']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['Remark']) .'</cell>';
                    echo '<cell>'. str_replace("&","&amp;",$row['VN_vi']) .'</cell>';

                    echo '<cell></cell>';
                    echo '<cell></cell>';
                echo '</row>';


            }

            // add 5 empty rows
            $last = $index + 5;
            for ($i=($key+1); $i<$last; $i++ ) {
                $index++;
                echo '<row id="'. $i .'">';
                    echo '<cell>0</cell>';
                    echo '<cell>'. $index .'</cell>';
                    
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                echo '</row>';
            }
        echo "</rows>";

    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "SAVEAUTO" ) {
        // default
        $res = false;
        $status = false;
        $message = 'Chưa lưu được dữ liệu';

        // Post
        $dataPost = $_POST['data'];
        // $dataPost = '{"Code":"test","Process":"test","Process_Ability":"","Ability_Unit":"","QTY500":"1","QTY501_2000":"1","QTY2001_5000":"1","QTY5001":"1","Remark":"","VN_vi":""}';
        $dataPost = json_decode($dataPost, true);
        if (empty($dataPost) ) {
            $message = "Không nhận được dữ liệu!!!";
        } else { 

            // get data
            $Code = filter_var(trim($dataPost['Code']), FILTER_SANITIZE_STRING);
            $Process = filter_var(trim($dataPost['Process']), FILTER_SANITIZE_STRING);
            $Process_Ability = filter_var(trim($dataPost['Process_Ability']), FILTER_SANITIZE_STRING);
            $Ability_Unit = filter_var(trim($dataPost['Ability_Unit']), FILTER_SANITIZE_STRING);
            $QTY500 = filter_var(trim($dataPost['QTY500']), FILTER_SANITIZE_STRING);
            $QTY501_2000 = filter_var(trim($dataPost['QTY501_2000']), FILTER_SANITIZE_STRING);
            $QTY2001_5000 = filter_var(trim($dataPost['QTY2001_5000']), FILTER_SANITIZE_STRING);
            $QTY5001 = filter_var(trim($dataPost['QTY5001']), FILTER_SANITIZE_STRING);
            $Remark = filter_var(trim($dataPost['Remark']), FILTER_SANITIZE_STRING);
            $VN_vi = filter_var(trim($dataPost['VN_vi']), FILTER_SANITIZE_STRING);

            $QTY500 = (is_numeric($QTY500)  ) ? (int)$QTY500 : 0;
            $QTY501_2000 = (is_numeric($QTY501_2000) ) ? (int)$QTY501_2000 : 0;
            $QTY2001_5000 = (is_numeric($QTY2001_5000) ) ? (int)$QTY2001_5000 : 0;
            $QTY5001 = (is_numeric($QTY5001) ) ? (float)$QTY5001 : 0;

            // check 
            if (empty($Code) ) {
                $message = "Code không được rỗng.";
            } else if (empty($Process) ) {
                $message = "Process không được rỗng";
            } else if(empty($Process_Ability) ) {
                $message = "Process Ability không được rỗng";
            } else if(empty($Ability_Unit) ) {
                $message = "Ability Unit không được rỗng";
            } else {
                // sql
                $table = "access_item_process";
                $checkData = MiQuery("SELECT `Code` FROM $table WHERE `Code`='$Code';", _conn() );
                if (empty($checkData) ) {
                    $sql = "INSERT INTO $table 
                                (`Code`,`Process`,`Process_Ability`,`Ability_Unit`, `<=500`, `501-2000`, `2001-5000`, `>5000`, `Remark`, `VN_vi`) 
                            VALUES
                                ('$Code','$Process','$Process_Ability','$Ability_Unit', '$QTY500', '$QTY501_2000', '$QTY2001_5000', '$QTY5001', '$Remark', '$VN_vi')
                            ;";
                } else {
                    $sql = "UPDATE 
                                $table 
                            SET 
                                `Process`='$Process', 
                                `Process_Ability`='$Process_Ability', 
                                `Ability_Unit`='$Ability_Unit', 
                                `<=500`='$QTY500', 
                                `501-2000`='$QTY501_2000', 
                                `2001-5000`='$QTY2001_5000', 
                                `>5000`='$QTY5001', 
                                `Remark`='$Remark', 
                                `VN_vi`='$VN_vi'
                            WHERE 
                                `Code`='$Code'
                            ;";
                }
                
                $res = MiNonQuery( $sql, _conn());
                // set message
                if ($res ) {
                    $message = "Cập nhật dữ liệu thành công ";
                    $status = true;
                } else {
                    $message = "Lỗi lưu dữ liệu";
                }

                    
            }

        }


        // result
        $results = array( 'status' => $status, 'message' => $message );
        echo json_encode($results, JSON_UNESCAPED_UNICODE); exit();


    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "DELETEAUTO" ) {
        // default
        $res = false;
        $status = false;
        $message = 'Chưa lưu được dữ liệu';

        // Post
        $dataPost = $_POST['data'];
        $dataPost = json_decode($dataPost, true);
        if (empty($dataPost) ) {
            $message = "Không nhận được dữ liệu!!!";
        } else {
             // get data
             $Code = filter_var(trim($dataPost['Code']), FILTER_SANITIZE_STRING);
             
             // del
             $table = "access_item_process";
             $sql = "DELETE FROM $table WHERE `Code` = '$Code';";
             $res = MiNonQuery( $sql, _conn());
            // set message
            if ($res ) {
                $message = "Cập nhật dữ liệu thành công";
                $status = true;
            } else {
                $message = "Lỗi lưu dữ liệu";
            }
        }

        // result
        $results = array( 'status' => $status, 'message' => $message );
        echo json_encode($results, JSON_UNESCAPED_UNICODE); exit();

    }
    
