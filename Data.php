<?php 
    require("./Module/Database.php");

    $OrderHandler = "";
    if(!isset($_COOKIE["ZeroIntranet"])) return;
    else $OrderHandler = $_COOKIE["ZeroIntranet"];

    $table_item_info = "access_item_information";
    $table_process = "access_item_process";

    $table_accessory = "access_item_accessory";
    $table_lines = "access_id_lines";
    $table_receiving = "access_order_receiving";

    $table_request = "access_rbo_request";
    $table_list = "access_order_list";

    $table_customer_request = "access_customer_request";

    $table_vnso = "au_avery.vnso";
    $table_soview_text = "au_avery.oe_soview_text";

    $table_remark = "access_lot_remark";
    $table_progress_track = "access_progress_track";
    $table_inventory_list = "access_inventory_list";
    $table_fgs_data = "access_fgs_data";
    $table_mla = "access_mla";
    $table_digital_maxscrap = "access_digital_maxscrap";
    $table_digital_printing_scrap = "access_digital_printing_scrap";
    $table_digital_scrap_special = "access_digital_scrap_special";

    $table_offset_printing_scrap = "access_offset_printing_scrap";
    $table_item_remark = "access_item_remark";
    $table_order_issue = "access_order_issue";
    $table_optinal_pewindow = "access_optinal_pewindow";
    $table_trimcard_rbo_print = "trimcard_rbo_print";

    
    // Tính khoảng cách 2 ngày, Trả về một số count, Nếu count > 0 (ngày 1 lớn hơn ngày 2) và ngược lại
	function subDistanceDate($date1, $date2) 
	{
		$count = 0;
		if (!empty($date1) && !empty($date2) ) {
			$date1 = date('Y-m-d', strtotime($date1) );
			$date2 = date('Y-m-d', strtotime($date2) );
			
			$count = abs(strtotime($date1) - strtotime($date2) );
		}
		
		return $count;

	}

    
    if(isset($_GET["EVENT"]) && isset($_GET["GLID"]) && $_GET["EVENT"] == "LOADACCESSORY") {
        $Stock_Code_Array = array();
        $RowTable = MiQuery( "SELECT Stock_Code, Digital_Stock_Code_F FROM $table_item_info GROUP BY Stock_Code, Digital_Stock_Code_F", _conn());
        foreach($RowTable as $row) {
            if(!in_array($row["Stock_Code"], $Stock_Code_Array)) array_push($Stock_Code_Array, $row["Stock_Code"]);
            if(!in_array($row["Digital_Stock_Code_F"], $Stock_Code_Array)) array_push($Stock_Code_Array, $row["Digital_Stock_Code_F"]);
        }

		header('Content-type: text/xml');
			echo '<rows>
                <head>
                <column width="100" type="co" align="left" filter="true" auto="true" sort="str" xmlcontent="1">
                    Process';

                    $RowTable = MiQuery( "SELECT process FROM $table_process;", _conn());
                    foreach($RowTable as $row) {
						echo '<option value="'.str_replace("&","&amp;",$row["process"]).'">'.str_replace("&","&amp;",$row["process"]).'</option>';
					}
            echo    '</column>
                <column width="100" type="co" editable="false" align="left" sort="str">Accessory';
                    
					foreach($Stock_Code_Array as $R) {
						echo '<option value="'.str_replace("&","&amp;",$R).'">'.str_replace("&","&amp;",$R).'</option>';
					}
            echo    '</column>
                <column width="*" type="txt" auto="true" cache="true" align="left" sort="str">Remark</column>
                <settings>
                <colwidth>px</colwidth>
                </settings>
                </head>';
        $TurnOn = 0;
        $GLID = $_GET["GLID"];
        if($GLID != "") {
            $RowTable = MiQuery( "SELECT ID, Process, Accessory, Remark FROM $table_accessory WHERE GLID = '$GLID';", _conn());
            foreach($RowTable as $row) {
                echo '<row id="'. $row['ID'] .'">';
                echo '<cell>' .str_replace("&","&amp;",$row['Process']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Accessory']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Remark']). '</cell>';
                echo '</row>';
                $TurnOn = 1;
            }
        } 
        $IDT = 1;
        while ($IDT < 7) {
            echo '<row id="Temp' . $IDT . '">';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '</row>';
            $IDT++;
        }
        echo '</rows>';
    } else if(isset($_GET["EVENT"]) && isset($_GET["GLID"]) && $_GET["EVENT"] == "LOADORDERITEM") {
        $GLID = $_GET["GLID"];
        $ArrJobJacket = array();

        //@TanDoan - 20210513: Giữ nguyên và thay đổi lấy active = 1  (AND ACTIVE='1')

        $RowTable = MiQuery( 
            "SELECT ORDER_NUMBER, LINE_NUMBER, QTY, ITEM, SHIP_TO_CUSTOMER, '' AS SOLD_TO_CUSTOMER, BILL_TO_CUSTOMER, UOM, REQUEST_DATE, PROMISE_DATE, CUSTOMER_REQUEST, ISSUE_ORDER, 
                (SELECT JOBJACKET FROM $table_lines WHERE ORDER_NUMBER = A.ORDER_NUMBER AND LINE_NUMBER = A.LINE_NUMBER AND Active = '1' AND JOBJACKET <> 'Array' ORDER BY ID DESC LIMIT 1) AS JOBJACKET 
                FROM $table_receiving A WHERE ID IN (SELECT ID FROM (SELECT MAX(ID) AS ID FROM $table_receiving WHERE ITEM = '$GLID' AND ACTIVE='1'  GROUP BY ORDER_NUMBER,LINE_NUMBER) AS X) ORDER BY A.ORDER_NUMBER, CONVERT(A.LINE_NUMBER,UNSIGNED INTEGER);", _conn());

        


        $ArrSO = array();
        foreach($RowTable as $R) {
            if(!in_array($R["JOBJACKET"], $ArrJobJacket)) $ArrJobJacket []= $R["JOBJACKET"];
            $ArrSO []= $R["ORDER_NUMBER"] . "-" . $R["LINE_NUMBER"];
        }

        

        $RowTableJobJacket = MiQuery( "SELECT ID AS JOBJACKET, ActiveOrder FROM $table_list WHERE ID IN ('" . implode("','", $ArrJobJacket) . "');", _conn());
        foreach($RowTable as $K=>$R) { 
            foreach($RowTableJobJacket as $r) {
                if($R["JOBJACKET"] == $r["JOBJACKET"] && $r["ActiveOrder"] != "1") {
                    $RowTable[$K]["JOBJACKET"] = "";
                }
            } 
        }

        $RowTableRemark = MiQuery("SELECT B.RBO AS RBO,A.BILL_TO, A.SHIP_TO, A.REMARK_DRILL, A.REMARK_1, A.REMARK_2, B.REMARK_DRILL AS CREMARK_DRILL, B.REMARK_1 AS CREMARK_1, B.REMARK_2 AS CREMARK_2 
        FROM $table_customer_request A JOIN $table_request B ON A.RBO = B.TYPERBO
        UNION ALL
        SELECT A.RBO,A.BILL_TO, A.SHIP_TO, A.REMARK_DRILL, A.REMARK_1, A.REMARK_2, B.REMARK_DRILL AS CREMARK_DRILL, B.REMARK_1 AS CREMARK_1, B.REMARK_2 AS CREMARK_2 
        FROM $table_customer_request A LEFT JOIN $table_request B ON A.RBO = B.TYPERBO WHERE B.RBO IS NULL;", _conn());
        $RowTableGLID = MiQuery("SELECT Buying_Office AS BO, Hole FROM $table_item_info WHERE GLID = '$GLID' LIMIT 1;", _conn());
        if(count($RowTableGLID) != 0) $RowTableGLID = $RowTableGLID[0];
        $StringUn = '<rows>';
        $StringIn = '<rows>';
        $ShipTo = "";
        $BillTo = "";
        foreach($RowTable as $R) {
            $RemarkDrill = array();
            $Remark1 = array();
            $Remark2 = array();
            $ShipTo = $R["SHIP_TO_CUSTOMER"];
            $BillTo = $R["BILL_TO_CUSTOMER"];
            foreach($RowTableRemark as $X){
                if(
                    ($X["RBO"] == "" && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == "" && $X["SHIP_TO"] == "" && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == "" && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == "" && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == "" && $X["BILL_TO"] == $BillTo)
                ){
                    if(!in_array($X["REMARK_DRILL"], $RemarkDrill) && (strtoupper($RowTableGLID["Hole"]) == "TRUE" || $RowTableGLID["Hole"] == "1")) $RemarkDrill []= $X["REMARK_DRILL"];
                    if(!in_array($X["REMARK_1"], $Remark1)) $Remark1 []= $X["REMARK_1"];
                    if(!in_array($X["REMARK_2"], $Remark2)) $Remark2 []= $X["REMARK_2"];
                }
            }
            $AF = "";
            $Style = "";
            if(in_array("AF", $RemarkDrill)) {
                if(!in_array("NEED DRILLING / KHOAN LỖ", $RemarkDrill))$RemarkDrill []= "NEED DRILLING / KHOAN LỖ";
                $AF = "AF";
                $Style = 'style="background:red;color:white"';
            }
            $R["CUSTOMER_REQUEST"] .= implode(" | ",$RemarkDrill) . " " . implode(" | ",$Remark1) . "" . implode(" | ",$Remark2);
            foreach($R as $K=>$Row) $R[$K] = str_replace("&","&amp;",$R[$K]);
            $R["SOLINE"] = $R["ORDER_NUMBER"] . "-" . $R["LINE_NUMBER"];
            if($R["JOBJACKET"] == "") {

                // bỏ đi những đơn có PD trễ hơn ngày hiện tại 15 ngày
                    $currentDate = date('Y-m-d');
                    $DateCheck = date('Y-m-d', strtotime("-15 days", strtotime(date('Y-m-d')) ) );
                    $RequestDateCheck = date('Y-m-d', strtotime($R['REQUEST_DATE']));
                    $subCheck = ($RequestDateCheck >= $DateCheck) ? 1 : 0 ;
                    if ($subCheck == 1 ) {
                        $StringUn .= '<row id="' . $R["SOLINE"] . '">';
                        $StringUn .= '<cell></cell>';
                        $StringUn .= '<cell>' . $R["SOLINE"] . '</cell>';
                        $StringUn .= '<cell>' . $R["ORDER_NUMBER"] . '</cell>';
                        $StringUn .= '<cell>' . $R["QTY"] . '</cell>';
                        $StringUn .= '<cell>' . $R["UOM"] . '</cell>';
                        $StringUn .= '<cell>' . $R["SOLD_TO_CUSTOMER"] . '</cell>';
                        $StringUn .= '<cell>' . $R["REQUEST_DATE"] . '</cell>';
                        $StringUn .= '<cell>' . $R["PROMISE_DATE"] . '</cell>';
                        $StringUn .= '<cell>' . $R["JOBJACKET"] . '</cell>';
                        $StringUn .= '<cell>' . $R["SHIP_TO_CUSTOMER"] . '</cell>';
                        $StringUn .= '<cell>' . $R["BILL_TO_CUSTOMER"] . '</cell>';
                        $StringUn .= '<cell ' . $Style . '>' . $AF . '</cell>';
                        $StringUn .= '<cell><![CDATA[' . $R["CUSTOMER_REQUEST"] . ']]></cell>';
                        $StringUn .= '</row>';
                        if($R["ISSUE_ORDER"] == "1") MiNonQuery("UPDATE $table_receiving SET ISSUE_ORDER = 0 WHERE ORDER_NUMBER = '" . $R["ORDER_NUMBER"] . "' AND LINE_NUMBER = '" . $R["LINE_NUMBER"] . "';", _conn());
                    } else {

                    }

                
            } else {
                $StringIn .= '<row id="' . $R["SOLINE"] . '">';
                $StringIn .= '<cell>1</cell>';
                $StringIn .= '<cell>' . $R["SOLINE"] . '</cell>';
                $StringIn .= '<cell>' . $R["ORDER_NUMBER"] . '</cell>';
                $StringIn .= '<cell>' . $R["QTY"] . '</cell>';
                $StringIn .= '<cell>' . $R["UOM"] . '</cell>';
                $StringIn .= '<cell>' . $R["SOLD_TO_CUSTOMER"] . '</cell>';
                $StringIn .= '<cell>' . $R["REQUEST_DATE"] . '</cell>';
                $StringIn .= '<cell>' . $R["PROMISE_DATE"] . '</cell>';
                $StringIn .= '<cell>' . $R["JOBJACKET"] . '</cell>';
                $StringIn .= '<cell>' . $R["SHIP_TO_CUSTOMER"] . '</cell>';
                $StringIn .= '<cell>' . $R["BILL_TO_CUSTOMER"] . '</cell>';
                $StringIn .= '<cell>' . $AF . '</cell>';
                $StringIn .= '<cell>' . $R["CUSTOMER_REQUEST"] . '</cell>';
                $StringIn .= '</row>';
                if($R["ISSUE_ORDER"] == "0") MiNonQuery("UPDATE $table_receiving SET ISSUE_ORDER = 1 WHERE ORDER_NUMBER = '" . $R["ORDER_NUMBER"] . "' AND LINE_NUMBER = '" . $R["LINE_NUMBER"] . "';", _conn());
            }
        }
      
        $StringUn .= '</rows>';
        $StringIn .= '</rows>';
        $DataShow = array();
        $DataShow["UN"] = $StringUn;
        $DataShow["IN"] = $StringIn;
        echo json_encode($DataShow);


    }  else if(isset($_GET["EVENT"]) && isset($_GET["GLID"]) && $_GET["EVENT"] == "LOADORDERITEM2") {
        $GLID = $_GET["GLID"];
        $RowTable = MiQuery( 
                            "SELECT A.ORDER_NUMBER, A.LINE_NUMBER, A.QTY, A.SOLD_TO_CUSTOMER, A.REQUEST_DATE, A.PROMISE_DATE, A.SHIP_TO_CUSTOMER, A.BILL_TO_CUSTOMER,  
                                (
                                    SELECT JOBJACKET FROM $table_lines B LEFT JOIN $table_list C ON B.JOBJACKET = C.ID 
                                    WHERE B.ORDER_NUMBER = A.ORDER_NUMBER AND B.LINE_NUMBER = A.LINE_NUMBER AND C.ActiveOrder = '1' AND B.ACTIVE = 1 ORDER BY B.ID DESC LIMIT 1
                                ) AS JOBJACKET FROM $table_vnso A WHERE A.ITEM = '$GLID' ORDER BY A.ORDER_NUMBER, CONVERT(A.LINE_NUMBER,UNSIGNED INTEGER);", _conn());

        $RowTableRemark = MiQuery("SELECT RBO ,BILL_TO, SHIP_TO, REMARK_DRILL, REMARK_1, REMARK_2 FROM $table_customer_request;", _conn());
        $RowTableGLID = MiQuery("SELECT Buying_Office AS BO, Hole FROM $table_item_info WHERE GLID = '$GLID' LIMIT 1;", _conn());
        if(count($RowTableGLID) != 0) $RowTableGLID = $RowTableGLID[0];
        $StringUn = '<rows>';
        $StringIn = '<rows>';
        $ShipTo = "";
        $BillTo = "";
        foreach($RowTable as $R) {
            $R["CUSTOMER_REQUEST"] = "";
            $RemarkDrill = array();
            $Remark1 = array();
            $Remark2 = array();
            $ShipTo = $R["SHIP_TO_CUSTOMER"];
            $BillTo = $R["BILL_TO_CUSTOMER"];
            foreach($RowTableRemark as $X){
                if(
                    ($X["RBO"] == "" && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == "" && $X["SHIP_TO"] == "" && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == "" && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == "" && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == "" && $X["BILL_TO"] == $BillTo)
                ){
                    if(!in_array($X["REMARK_DRILL"], $RemarkDrill) && (strtoupper($RowTableGLID["Hole"]) == "TRUE" || $RowTableGLID["Hole"] == "1")) $RemarkDrill []= $X["REMARK_DRILL"];
                    if(!in_array($X["REMARK_1"], $Remark1)) $Remark1 []= $X["REMARK_1"];
                    if(!in_array($X["REMARK_2"], $Remark2)) $Remark2 []= $X["REMARK_2"];
                }
            }
            $AF = "";
            $Style = "";
            if(in_array("AF", $RemarkDrill)) {
                if(!in_array("NEED DRILLING / KHOAN LỖ", $RemarkDrill))$RemarkDrill []= "NEED DRILLING / KHOAN LỖ";
                $AF = "AF";
                $Style = 'style="background:red;color:white"';
            }
            $R["CUSTOMER_REQUEST"] = implode(" | ",$RemarkDrill) . " " . implode(" | ",$Remark1) . "" . implode(" | ",$Remark2);
            foreach($R as $K=>$Row) $R[$K] = str_replace("&","&amp;",$R[$K]);
            $R["SOLINE"] = $R["ORDER_NUMBER"] . "-" . $R["LINE_NUMBER"];
            if($R["JOBJACKET"] == "") {
                $StringUn .= '<row id="' . $R["SOLINE"] . '">';
                $StringUn .= '<cell></cell>';
                $StringUn .= '<cell>' . $R["SOLINE"] . '</cell>';
                $StringUn .= '<cell>' . $R["ORDER_NUMBER"] . '</cell>';
                $StringUn .= '<cell>' . $R["QTY"] . '</cell>';
                $StringUn .= '<cell></cell>';
                $StringUn .= '<cell>' . $R["SOLD_TO_CUSTOMER"] . '</cell>';
                $StringUn .= '<cell>' . $R["REQUEST_DATE"] . '</cell>';
                $StringUn .= '<cell>' . $R["PROMISE_DATE"] . '</cell>';
                $StringUn .= '<cell>' . $R["JOBJACKET"] . '</cell>';
                $StringUn .= '<cell>' . $R["SHIP_TO_CUSTOMER"] . '</cell>';
                $StringUn .= '<cell>' . $R["BILL_TO_CUSTOMER"] . '</cell>';
                $StringUn .= '<cell ' . $Style . '>' . $AF . '</cell>';
                $StringUn .= '<cell><![CDATA[' . $R["CUSTOMER_REQUEST"] . ']]></cell>';
                $StringUn .= '</row>';
            } else {
                $StringIn .= '<row id="' . $R["SOLINE"] . '">';
                $StringIn .= '<cell>1</cell>';
                $StringIn .= '<cell>' . $R["SOLINE"] . '</cell>';
                $StringIn .= '<cell>' . $R["ORDER_NUMBER"] . '</cell>';
                $StringIn .= '<cell>' . $R["QTY"] . '</cell>';
                $StringIn .= '<cell></cell>';
                $StringIn .= '<cell>' . $R["SOLD_TO_CUSTOMER"] . '</cell>';
                $StringIn .= '<cell>' . $R["REQUEST_DATE"] . '</cell>';
                $StringIn .= '<cell>' . $R["PROMISE_DATE"] . '</cell>';
                $StringIn .= '<cell>' . $R["JOBJACKET"] . '</cell>';
                $StringIn .= '<cell>' . $R["SHIP_TO_CUSTOMER"] . '</cell>';
                $StringIn .= '<cell>' . $R["BILL_TO_CUSTOMER"] . '</cell>';
                $StringIn .= '<cell>' . $AF . '</cell>';
                $StringIn .= '<cell>' . $R["CUSTOMER_REQUEST"] . '</cell>';
                $StringIn .= '</row>';
            }
        }
        $StringUn .= '</rows>';
        $StringIn .= '</rows>';
        $DataShow = array();
        $DataShow["UN"] = $StringUn;
        $DataShow["IN"] = $StringIn;
        echo json_encode($DataShow);
    } else if(isset($_GET["EVENT"]) && isset($_GET["GLID"]) && $_GET["EVENT"] == "LOADORDERITEMNON") {
        $GLID = $_GET["GLID"];
        $ORDER_NUMBER = $_GET["ORDER_NUMBER"];
        $RowTable = MiQuery( 
                            "SELECT A.ORDER_NUMBER, A.LINE_NUMBER, A.QTY, '' AS SOLD_TO_CUSTOMER, A.REQUEST_DATE, A.PROMISE_DATE, A.SHIP_TO_CUSTOMER, A.BILL_TO_CUSTOMER,  
                                (
                                    SELECT JOBJACKET FROM $table_lines B LEFT JOIN $table_list C ON B.JOBJACKET = C.ID 
                                    WHERE B.ORDER_NUMBER = A.ORDER_NUMBER AND B.LINE_NUMBER = A.LINE_NUMBER AND C.ActiveOrder = '1' AND B.ACTIVE = 1 ORDER BY B.ID DESC LIMIT 1
                                ) AS JOBJACKET FROM $table_soview_text A WHERE A.ITEM = '$GLID' AND A.ORDER_NUMBER = '$ORDER_NUMBER' ORDER BY A.ORDER_NUMBER, CONVERT(A.LINE_NUMBER,UNSIGNED INTEGER);", _conn());
        $RowTableRemark = MiQuery("SELECT B.RBO AS RBO,A.BILL_TO, A.SHIP_TO, A.REMARK_DRILL, A.REMARK_1, A.REMARK_2, B.REMARK_DRILL AS CREMARK_DRILL, B.REMARK_1 AS CREMARK_1, B.REMARK_2 AS CREMARK_2 
                                    FROM $table_customer_request A JOIN $table_request B ON A.RBO = B.TYPERBO
                                    UNION ALL
                                    SELECT A.RBO,A.BILL_TO, A.SHIP_TO, A.REMARK_DRILL, A.REMARK_1, A.REMARK_2, B.REMARK_DRILL AS CREMARK_DRILL, B.REMARK_1 AS CREMARK_1, B.REMARK_2 AS CREMARK_2 
                                    FROM $table_customer_request A LEFT JOIN $table_request B ON A.RBO = B.TYPERBO WHERE B.RBO IS NULL;", _conn());
        $RowTableGLID = MiQuery("SELECT Buying_Office AS BO, Hole FROM $table_item_info WHERE GLID = '$GLID' LIMIT 1;", _conn());
        if(count($RowTableGLID) != 0) $RowTableGLID = $RowTableGLID[0];
        header('Content-type: text/xml');
        $StringUn = '<rows>';
        $ShipTo = "";
        $BillTo = "";
        foreach($RowTable as $R) {
            $R["CUSTOMER_REQUEST"] = "";
            $RemarkDrill = array();
            $Remark1 = array();
            $Remark2 = array();
            $ShipTo = $R["SHIP_TO_CUSTOMER"];
            $BillTo = $R["BILL_TO_CUSTOMER"];
            foreach($RowTableRemark as $X){
                if(
                    ($X["RBO"] == "" && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == "" && $X["SHIP_TO"] == "" && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == "" && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == "" && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == $BillTo) ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == $ShipTo && $X["BILL_TO"] == "") ||
                    ($X["RBO"] == $RowTableGLID["BO"] && $X["SHIP_TO"] == "" && $X["BILL_TO"] == $BillTo)
                ){
                    if(!in_array($X["REMARK_DRILL"], $RemarkDrill) && (strtoupper($RowTableGLID["Hole"]) == "TRUE" || $RowTableGLID["Hole"] == "1")) $RemarkDrill []= $X["REMARK_DRILL"];
                    if(!in_array($X["CREMARK_DRILL"], $RemarkDrill) && (strtoupper($RowTableGLID["Hole"]) == "TRUE" || $RowTableGLID["Hole"] == "1")) $RemarkDrill []= $X["CREMARK_DRILL"];
                    if(!in_array($X["REMARK_1"], $Remark1)) $Remark1 []= $X["REMARK_1"];
                    if(!in_array($X["CREMARK_1"], $Remark1)) $Remark1 []= $X["CREMARK_1"];
                    if(!in_array($X["REMARK_2"], $Remark2)) $Remark2 []= $X["REMARK_2"];
                    if(!in_array($X["CREMARK_2"], $Remark2)) $Remark2 []= $X["CREMARK_2"];
                }
            }
            $AF = "";
            $Style = "";
            if(in_array("AF", $RemarkDrill)) {
                if(!in_array("NEED DRILLING / KHOAN LỖ", $RemarkDrill))$RemarkDrill []= "NEED DRILLING / KHOAN LỖ";
                $AF = "AF";
                $Style = 'style="background:red;color:white"';
            }
            $R["CUSTOMER_REQUEST"] = implode(" | ",$RemarkDrill) . " " . implode(" | ",$Remark1) . "" . implode(" | ",$Remark2);

            foreach($R as $K=>$Row) $R[$K] = str_replace("&","&amp;",$R[$K]);
            $R["SOLINE"] = $R["ORDER_NUMBER"] . "-" . $R["LINE_NUMBER"];
                $StringUn .= '<row id="' . $R["SOLINE"] . '">';
                if($R["JOBJACKET"] != "") $StringUn .= '<cell></cell>'; 
                else $StringUn .= '<cell style="color:white;background:black;font-weight:bold"><![CDATA[ADD]]></cell>';
                $StringUn .= '<cell>' . $R["SOLINE"] . '</cell>';
                $StringUn .= '<cell>' . $R["ORDER_NUMBER"] . '</cell>';
                $StringUn .= '<cell>' . $R["QTY"] . '</cell>';
                $StringUn .= '<cell></cell>';
                $StringUn .= '<cell>' . $R["SOLD_TO_CUSTOMER"] . '</cell>';
                $StringUn .= '<cell>' . date("d/m/Y",strtotime($R["REQUEST_DATE"])) . '</cell>';
                $StringUn .= '<cell>' . date("d/m/Y",strtotime($R["PROMISE_DATE"])) . '</cell>';
                $StringUn .= '<cell>' . $R["JOBJACKET"] . '</cell>';
                $StringUn .= '<cell>' . $R["SHIP_TO_CUSTOMER"] . '</cell>';
                $StringUn .= '<cell>' . $R["BILL_TO_CUSTOMER"] . '</cell>';
                $StringUn .= '<cell ' . $Style . '>' . $AF . '</cell>';
                $StringUn .= '<cell><![CDATA[' . $R["CUSTOMER_REQUEST"] . ']]></cell>';
                $StringUn .= '</row>';
        }
        $StringUn .= '</rows>';
        echo $StringUn;
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "GETLOTOLD") {
        header('Content-type: text/xml');
			echo '<rows>
                <head>
                <column width="*" type="ro" align="left" filter="true" auto="true" xmlcontent="1">A</column>
                <column width="*" type="ed" align="left" filter="true" auto="true" xmlcontent="1">B</column>
                <column width="*" type="ro" align="left" filter="true" auto="true" xmlcontent="1">C</column>
                <column width="*" type="ed" align="left" filter="true" auto="true" xmlcontent="1">D</column>
                <column width="*" type="ro" align="left" filter="true" auto="true" xmlcontent="1">E</column>
                <column width="*" type="ed" align="left" filter="true" auto="true" xmlcontent="1">F</column>
                <column width="*" type="ro" align="left" filter="true" auto="true" xmlcontent="1">G</column>
                <column width="*" type="ed" align="left" filter="true" auto="true" xmlcontent="1">H</column>
                <settings>
                <colwidth>px</colwidth>
                </settings>
                </head>';
        $i = 1;
        while ($i < 11) {
            echo '<row id="' . $i . '">';
            echo '<cell>LOT ' . str_pad($i, 2, '0', STR_PAD_LEFT) . ':</cell>';
            echo '<cell></cell>';
            echo '<cell>LOT ' . str_pad($i + 10, 2, '0', STR_PAD_LEFT) . ':</cell>';
            echo '<cell></cell>';
            echo '<cell>LOT ' . str_pad($i + 20, 2, '0', STR_PAD_LEFT) . ':</cell>';
            echo '<cell></cell>';
            echo '<cell>LOT ' . str_pad($i + 30, 2, '0', STR_PAD_LEFT) . ':</cell>';
            echo '<cell></cell>';
            echo '</row>';
            $i++;
        }
        echo '</rows>';
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "GETLOT") {
        $JJ = $_GET["JOBJACKET"];
        $RowTable = MiQuery( "SELECT LotName, Qty FROM $table_remark WHERE JobJacket = '$JJ';", _conn());
        header('Content-type: text/xml');
			echo '<rows>
                <head>
                    <column width="100" type="ro" align="left" filter="true" auto="true" xmlcontent="1">LOT</column>
                    <column width="*" type="ed" align="left" filter="true" auto="true" xmlcontent="1">Quantity</column>
                    <settings>
                        <colwidth>px</colwidth>
                    </settings>
                </head>';
        $i = 1;
        foreach($RowTable as $R) {
            echo '<row id="' . $i . '">';
            echo '<cell>' . $R["LotName"] . '</cell>';
            echo '<cell>' . $R["Qty"] . '</cell>';
            echo '<cell></cell>';
            echo '</row>';
            $i++;
        }
        while ($i < 301) {
            echo '<row id="' . $i . '">';
            echo '<cell>LOT ' . str_pad($i, 2, '0', STR_PAD_LEFT) . ':</cell>';
            echo '<cell></cell>';
            echo '</row>';
            $i++;
        }
        echo '</rows>';
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "GETSOLINE") {
        $TurnOn = 0;
        $JJ = $_GET["JJ"];

        // @TanDoan: Check xem Đơn đã làm hay chưa, Nếu chưa làm thì lấy ACTIVE = 0, ngược lại lấy ACTIVE = 1 để hiển thị list SO#
        $ActiveOrder = MiQueryScalar("SELECT `ActiveOrder` FROM $table_list WHERE `ID`='$JJ' AND `ActiveOrder`='1' ORDER BY `Num` DESC LIMIT 1; ", _conn() );
        $ACTIVE = ($ActiveOrder == '1' ) ? '1' : '0';

        $RowTable = MiQuery( "SELECT JOBJACKET, SO_Line, SO_Line_Qty, ORDER_NUMBER, LINE_NUMBER, REMARK, ID FROM $table_lines WHERE JobJacket = '$JJ' AND ACTIVE = '$ACTIVE';", _conn());

		header('Content-type: text/xml');
			echo '<rows>
                <head>
                <column width="30" type="ro" align="left" filter="true" auto="true" sort="str" xmlcontent="1"></column>
                <column width="90" type="ed" align="left" filter="true" auto="true" sort="str" xmlcontent="1">SO Line</column>
                <column width="70" type="ed" editable="false" align="left" sort="str">Order</column>
                <column width="40" type="ed" editable="false" align="left" sort="str">Line</column>
                <column width="40" type="ed" auto="true" cache="true" align="left" sort="str">Qty</column>
                <column width="100" type="ro" auto="true" cache="true" align="left" sort="str">JobJacket</column>
                <column width="*" type="ed" auto="true" cache="true" align="left" sort="str">Remark</column>
                <settings>
                <colwidth>px</colwidth>
                </settings>
                </head>';
                
                foreach($RowTable as $row) {
                    echo '<row id="'. $row['ID'] .'">';
                    echo '<cell><![CDATA[<a style="color:red;font-weight:bold">X</a>]]></cell>';
                    // echo '<cell><![CDATA[<button style=\'width:100%;height:90%\'></button>]]></cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['SO_Line']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['ORDER_NUMBER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['LINE_NUMBER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['SO_Line_Qty']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['JOBJACKET']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['REMARK']). '</cell>';
                    echo '</row>';
                    $TurnOn = 1;
                }
                

        $i = 1;
        while ($i < 15) {
            echo '<row id="X' . $i . '">';
            echo '<cell><![CDATA[<a style="color:red;font-weight:bold">X</a>]]></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '</row>';
            $i++;
        }
        echo '</rows>';
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "GETSOLINECOPY") {
        $TurnOn = 0;
        $JJ = $_GET["JJ"];
		// header('Content-type: text/xml');
			echo '<rows>
                <head>
                <column width="30" type="ro" align="left" filter="true" auto="true" sort="str" xmlcontent="1"></column>
                <column width="90" type="ed" align="left" filter="true" auto="true" sort="str" xmlcontent="1">SO Line</column>
                <column width="70" type="ed" editable="false" align="left" sort="str">Order</column>
                <column width="40" type="ed" editable="false" align="left" sort="str">Line</column>
                <column width="40" type="ed" auto="true" cache="true" align="left" sort="str">Qty</column>
                <column width="100" type="ro" auto="true" cache="true" align="left" sort="str">JobJacket</column>
                <column width="*" type="ed" auto="true" cache="true" align="left" sort="str">Remark</column>
                <settings>
                <colwidth>px</colwidth>
                </settings>
                </head>';

                // @TanDoan: Check xem Đơn đã làm hay chưa, Nếu chưa làm thì lấy ACTIVE = 0, ngược lại lấy ACTIVE = 1 để hiển thị list SO#
                $ActiveOrder = MiQueryScalar("SELECT `ActiveOrder` FROM $table_list WHERE `ID`='$JJ' AND `ActiveOrder`='1' ORDER BY `Num` DESC LIMIT 1; ", _conn() );
                $ACTIVE = ($ActiveOrder == '1' ) ? '1' : '0';

                $RowTable = MiQuery( "SELECT JOBJACKET, SO_Line, SO_Line_Qty, ORDER_NUMBER, LINE_NUMBER, REMARK, ID FROM $table_lines WHERE JobJacket = '$JJ' AND ACTIVE = '$ACTIVE';", _conn());
                foreach($RowTable as $row) {
                    echo '<row id="'. $row['ID'] .'">';
                    echo '<cell><![CDATA[<a style="color:red;font-weight:bold">X</a>]]></cell>';
                    // echo '<cell><![CDATA[<button style=\'width:100%;height:90%\'></button>]]></cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['SO_Line']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['ORDER_NUMBER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['LINE_NUMBER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['SO_Line_Qty']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['JOBJACKET']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['REMARK']). '</cell>';
                    echo '</row>';
                    $TurnOn = 1;
                }
                

        $i = 1;
        while ($i < 15) {
            echo '<row id="X' . $i . '">';
            echo '<cell><![CDATA[<a style="color:red;font-weight:bold">X</a>]]></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '<cell></cell>';
            echo '</row>';
            $i++;
        }
        echo '</rows>||';
        $RowTable = MiQuery( "SELECT * FROM $table_list WHERE ID = '$JJ';", _conn());
        echo json_encode($RowTable);
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADPROCESS") {
        $JJ = $_GET["JJ"];
        $GLID = $_GET["GLID"];
        $StringMain = '<rows>';
        $RowTable = MiQuery( "SELECT A.Num, B.Code, A.Process_Sequence, A.Process, A.Planing_Finish_Date, B.Ability_Unit, B.Process_Ability, `<=500` AS X, `501-2000` AS Y, `2001-5000` AS Z, `>5000` AS W FROM $table_progress_track A LEFT JOIN $table_process B ON A.Process = B.Process WHERE A.ID = '$JJ' ORDER BY A.Process_Sequence ASC;", _conn());
            foreach($RowTable as $row) {
                $StringMain .= '<row id="'. $row['Num'] .'">';
                $StringMain .= '<cell style="background: gray">X</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Process_Sequence']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Process']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Planing_Finish_Date']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Process_Ability']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Ability_Unit']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['X']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Y']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Z']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['W']). '</cell>';
                $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Code']). '</cell>';
                $StringMain .= '</row>';
            }
        $StringMain .= '</rows>';
        $RowTable = MiQuery( "SELECT Color_FQ, Color_BQ, Offset_Imp_Back, Offset_Imp_Front, Color_Management, Finishing_Difficult_Rate, Scrap_Adjustment, Process, Item_Code FROM $table_item_info WHERE GLID = '$GLID';", _conn());
        $RowTable = $RowTable[0];
        $ArrMain = array();
        if($RowTable["Offset_Imp_Back"] == "") $RowTable["Offset_Imp_Back"] = 0;
        if($RowTable["Offset_Imp_Front"] == "") $RowTable["Offset_Imp_Front"] = 0;
        if($RowTable["Color_FQ"] == "") $RowTable["Color_FQ"] = "0";
        if($RowTable["Color_BQ"] == "") $RowTable["Color_BQ"] = "0";
        $ArrMain["Color_FQ"] = $RowTable["Color_FQ"];
        $ArrMain["Color_BQ"] = $RowTable["Color_BQ"];
        $ArrMain["Offset_Imp"] = $RowTable["Offset_Imp_Back"] + $RowTable["Offset_Imp_Front"];
        $ArrMain["Color_Management"] = $RowTable["Color_Management"];
        $ArrMain["Finishing_Difficult_Rate"] = $RowTable["Finishing_Difficult_Rate"];
        $ArrMain["Scrap_Adjustment"] = $RowTable["Scrap_Adjustment"];
        $ArrMain["Process"] = $RowTable["Process"];
        $ArrMain["Item_Code"] = $RowTable["Item_Code"];
        $ArrMain["MAIN"] = $StringMain;
        echo json_encode($ArrMain);
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADINVENTORY"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = $_POST[$ids . "_c0"];
            $C1 = $_POST[$ids . "_c1"];
            $C2 = $_POST[$ids . "_c2"];
            $C3 = $_POST[$ids . "_c3"];
            $C4 = $_POST[$ids . "_c4"];
            $C5 = $_POST[$ids . "_c5"];
            $SQLString = "UPDATE $table_inventory_list SET StockCode = '$C1', UnitPrice = '$C2', Sort = '$C3', Remark = '$C4', Active = $C5 WHERE ID = '$C0'";
            MiNonQuery( $SQLString, _conn());
            echo $SQLString;
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT ID, StockCode, UnitPrice, Sort, Remark, Active FROM $table_inventory_list", _conn());
                foreach($retval as $row) {
                    echo '<row id="'. str_replace("&","&amp;",str_replace("\"","''",clean($row['StockCode']))) .'">';
                    echo '<cell>' .$row['ID']. '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",clean($row['StockCode'])). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",clean($row['UnitPrice'])). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",clean($row['Sort'])). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",clean($row['Remark'])). '</cell>';
                    echo '<cell>' .$row['Active']. '</cell>';
                    echo '</row>';
                }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADFGS"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = $_POST[$ids . "_c0"];
            $C1 = $_POST[$ids . "_c1"];
            $C2 = $_POST[$ids . "_c2"];
            $C3 = $_POST[$ids . "_c3"];
            $C4 = $_POST[$ids . "_c4"];
            $C5 = $_POST[$ids . "_c5"];
            $C6 = $_POST[$ids . "_c6"];
            $C2 = str_replace(",","",$C2);
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            if($CNavi == "inserted")
            {    
                $SQLString = "INSERT INTO $table_fgs_data (
                                IDCODE,
                                GLID,
                                FGS_IN_OUT,
                                JOB_ID,
                                Record_Date,
                                Record_Name,
                                Remark)
                                VALUES
                                (
                                '$ids',
                                '$C1',
                                '$C2',
                                '$C3',
                                '$C4',
                                '$C5',
                                '$C6');";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "updated") {    
                $SQLString = "UPDATE $table_fgs_data SET GLID = '$C1',
                                FGS_IN_OUT = '$C2',
                                JOB_ID = '$C3',
                                Record_Date = '$C4',
                                Record_Name = '$C5',
                                Remark = '$C6' WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "deleted") {    
                $SQLString = "DELETE FROM $table_fgs_data WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());
            }
            echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT IDCODE, GLID, FGS_IN_OUT, JOB_ID, Record_Date, Record_Name, Remark FROM $table_fgs_data WHERE GLID <> '' ORDER BY ID DESC LIMIT 500;", _conn());
                foreach($retval as $row) {
                    echo '<row id="'. str_replace("&","&amp;",$row['IDCODE']) .'">';
                    echo '<cell>' .$row['IDCODE']. '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['GLID']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['FGS_IN_OUT']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['JOB_ID']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['Record_Date']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['Record_Name']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['Remark']). '</cell>';
                    echo '</row>';
                }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADFGSTOTAL"){
        header('Content-type: text/xml');
        echo "<rows>";
        $retval = MiQuery( "SELECT GLID, SUM(FGS_IN_OUT) AS QTY FROM $table_fgs_data GROUP BY GLID;", _conn());
            foreach($retval as $K=>$row) {
                echo '<row id="'. str_replace("&","&amp;",$row['GLID']) .'">';
                echo '<cell>' .$K. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['GLID']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['QTY']). '</cell>';
                echo '</row>';
            }
        echo "</rows>";
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADMLA"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = $_POST[$ids . "_c0"];
            $C1 = $_POST[$ids . "_c1"];
            $C2 = $_POST[$ids . "_c2"];
            $C3 = $_POST[$ids . "_c3"];
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            if($CNavi == "inserted") {    
                $SQLString = "INSERT INTO $table_mla ( IDCODE, RBO, MLA, Remark) VALUES ( '$ids', '$C1', '$C2', '$C3');";
                MiNonQuery( $SQLString, _conn());

            } else if($CNavi == "updated") {    
                $SQLString = "UPDATE $table_mla SET RBO = '$C1', MLA = '" .str_replace("&amp;","&",$C2) ."', Remark = '$C3' WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());

            } else if($CNavi == "deleted") {    
                $SQLString = "DELETE FROM $table_mla WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());

            }
            echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT IDCODE, RBO, MLA, Remark FROM $table_mla;", _conn());
            foreach($retval as $row) {
                echo '<row id="' .$row['IDCODE']. '">';
                echo '<cell>' .$row['IDCODE']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['RBO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['MLA']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Remark']). '</cell>';
                echo '</row>';
            }

            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADDIGITALMAXSCRAP"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = $_POST[$ids . "_c0"];
            $C1 = $_POST[$ids . "_c1"];
            $C2 = $_POST[$ids . "_c2"];
            $C3 = $_POST[$ids . "_c3"];
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            if($CNavi == "inserted") {    
                $SQLString = "INSERT INTO $table_digital_maxscrap ( IDCODE, GLID, MAXScrap, Remark) VALUES ( '$ids', '$C1', '$C2', '$C3');";
                MiNonQuery( $SQLString, _conn());

            } else if($CNavi == "updated") {    
                $SQLString = "UPDATE $table_digital_maxscrap SET GLID = '$C1', MAXScrap = '$C2', Remark = '$C3' WHERE IDCode = '$ids'";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "deleted") {    
                $SQLString = "DELETE FROM $table_digital_maxscrap WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());
            }
            echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT IDCode, GLID, MAXScrap, Remark FROM $table_digital_maxscrap;", _conn());
            foreach($retval as $row) {
                $i++;
                echo '<row id="' .$row['IDCode']. '">';
                echo '<cell>' .$row['IDCode']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['GLID']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['MAXScrap']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Remark']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADCUSTOMERREQUEST"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = str_replace("&amp;","&",$_POST[$ids . "_c0"]);
            $C1 = str_replace("&amp;","&",$_POST[$ids . "_c1"]);
            $C1 = str_replace("&#39;","'",$_POST[$ids . "_c1"]);
            // $C1 = htmlspecialchars($C1, ENT_QUOTES, 'UTF-8');
            $C2 = str_replace("&amp;","&",$_POST[$ids . "_c2"]);
            $C3 = str_replace("&amp;","&",$_POST[$ids . "_c3"]);
            $C4 = str_replace("&amp;","&",$_POST[$ids . "_c4"]);
            $C5 = str_replace("&amp;","&",$_POST[$ids . "_c5"]);
            $C6 = str_replace("&amp;","&",$_POST[$ids . "_c6"]);
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            if($CNavi == "inserted") {    
                $SQLString = "INSERT INTO $table_customer_request
                                (
                                IDCODE,
                                RBO,
                                BILL_TO,
                                SHIP_TO,
                                REMARK_DRILL,
                                REMARK_1,
                                REMARK_2)
                                VALUES
                                (
                                '$ids',
                                '$C1',
                                '$C2',
                                '$C3',
                                '$C4',
                                '$C5',
                                '$C6');";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "updated") {    
                $SQLString = "UPDATE $table_customer_request SET RBO = '$C1', BILL_TO = '$C2', SHIP_TO = '$C3', REMARK_DRILL = '$C4', REMARK_1 = '$C5', REMARK_2 = '$C6' WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "deleted") {    
                $SQLString = "DELETE FROM $table_customer_request WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());
            }
            echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT * FROM $table_customer_request;", _conn());
            foreach($retval as $row) {
                $i++;
                echo '<row id="' .$row['IDCODE']. '">';
                echo '<cell>' .$row['IDCODE']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['RBO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['BILL_TO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SHIP_TO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['REMARK_DRILL']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['REMARK_1']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['REMARK_2']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADRBOREQUEST"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = str_replace("&amp;","&",$_POST[$ids . "_c0"]);
            $C1 = str_replace("&amp;","&",$_POST[$ids . "_c1"]);
            $C2 = str_replace("&amp;","&",$_POST[$ids . "_c2"]);
            $C3 = str_replace("&amp;","&",$_POST[$ids . "_c3"]);
            $C4 = str_replace("&amp;","&",$_POST[$ids . "_c4"]);
            $C5 = str_replace("&amp;","&",$_POST[$ids . "_c5"]);
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            if($CNavi == "inserted") {    
                $SQLString = "INSERT INTO $table_request
                                (
                                IDCODE,
                                RBO,
                                TYPERBO,
                                REMARK_DRILL,
                                REMARK_1,
                                REMARK_2)
                                VALUES
                                (
                                '$ids',
                                '$C1',
                                '$C2',
                                '$C3',
                                '$C4',
                                '$C5');";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "updated") {    
                $SQLString = "UPDATE $table_request SET RBO = '$C1', TYPERBO = '$C2', REMARK_DRILL = '$C3', REMARK_1 = '$C4', REMARK_2 = '$C5' WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "deleted") {    
                $SQLString = "DELETE FROM $table_request WHERE IDCODE = '$ids'";
                MiNonQuery( $SQLString, _conn());
            }
            echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT * FROM $table_request;", _conn());
            foreach($retval as $row) {
                $i++;
                echo '<row id="' .$row['IDCODE']. '">';
                echo '<cell>' .$row['IDCODE']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['RBO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['TYPERBO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['REMARK_DRILL']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['REMARK_1']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['REMARK_2']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADPRINTINGSCRAP"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = str_replace("&amp;","&",$_POST[$ids . "_c0"]);
            $C1 = str_replace("&amp;","&",$_POST[$ids . "_c1"]);
            $C2 = str_replace("&amp;","&",$_POST[$ids . "_c2"]);
            $C3 = str_replace("&amp;","&",$_POST[$ids . "_c3"]);
            $C4 = str_replace("&amp;","&",$_POST[$ids . "_c4"]);
            $C5 = str_replace("&amp;","&",$_POST[$ids . "_c5"]);
            $C6 = str_replace("&amp;","&",$_POST[$ids . "_c6"]);
            $C7 = str_replace("&amp;","&",$_POST[$ids . "_c7"]);
            $SQLString = "UPDATE $table_digital_printing_scrap SET COLOR = '$C1', SIDE = '$C2', SETUP_BASELINE = '$C3', `0-50` = '$C4', `0-500` = '$C5', `>500` = '$C6', MAX = '$C7' WHERE ID = '$ids'";
            MiNonQuery( $SQLString, _conn());
            header('Content-type: text/xml');
            echo "<?xml version='1.0' ?><data><action type='updated' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT ID,COLOR, SIDE, SETUP_BASELINE, `0-50`, `0-500`, `>500`, MAX FROM $table_digital_printing_scrap;", _conn());
            foreach($retval as $row) {
                $i++;
                echo '<row id="' .$row['ID']. '">';
                echo '<cell>' .$row['ID']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['COLOR']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SIDE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SETUP_BASELINE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['0-50']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['0-500']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['>500']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['MAX']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADSCRAPSPECIAL"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = str_replace("&amp;","&",$_POST[$ids . "_c0"]);
            $C1 = str_replace("&amp;","&",$_POST[$ids . "_c1"]);
            $C2 = str_replace("&amp;","&",$_POST[$ids . "_c2"]);
            $C3 = str_replace("&amp;","&",$_POST[$ids . "_c3"]);
            $C4 = str_replace("&amp;","&",$_POST[$ids . "_c4"]);
            $C5 = str_replace("&amp;","&",$_POST[$ids . "_c5"]);
            $C6 = str_replace("&amp;","&",$_POST[$ids . "_c6"]);
            $C7 = str_replace("&amp;","&",$_POST[$ids . "_c7"]);
            $SQLString = "UPDATE $table_digital_scrap_special SET COLOR = '$C1', SIDE = '$C2', SETUP_BASELINE = '$C3', `0-50` = '$C4', `0-500` = '$C5', `>500` = '$C6', MAX = '$C7' WHERE ID = '$ids'";
            MiNonQuery( $SQLString, _conn());
            header('Content-type: text/xml');
            echo "<?xml version='1.0' ?><data><action type='updated' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT ID,COLOR, SIDE, SETUP_BASELINE, `0-50`,`0-500`, `>500`, MAX FROM $table_digital_scrap_special;", _conn());
            foreach($retval as $row) {
                $i++;
                echo '<row id="' .$row['ID']. '">';
                echo '<cell>' .$row['ID']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['COLOR']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SIDE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SETUP_BASELINE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['0-50']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['0-500']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['>500']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['MAX']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADOFFSETSCRAP"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = str_replace("&amp;","&",$_POST[$ids . "_c0"]);
            $C1 = str_replace("&amp;","&",$_POST[$ids . "_c1"]);
            $C2 = str_replace("&amp;","&",$_POST[$ids . "_c2"]);
            $C3 = str_replace("&amp;","&",$_POST[$ids . "_c3"]);
            $C4 = str_replace("&amp;","&",$_POST[$ids . "_c4"]);
            $C5 = str_replace("&amp;","&",$_POST[$ids . "_c5"]);
            $C6 = str_replace("&amp;","&",$_POST[$ids . "_c6"]);
            $C7 = str_replace("&amp;","&",$_POST[$ids . "_c7"]);
            $C8 = str_replace("&amp;","&",$_POST[$ids . "_c8"]);
            $SQLString = "UPDATE $table_offset_printing_scrap SET COLOR = '$C1', SIDE = '$C2', SETUP_BASELINE = '$C3', LOT_SCRAP = '$C4', MACHINE_STOP_FREQUENCE = '$C5', 
                            MACHINE_STOP_ALLOWANCE = '$C6', QUALITY_CHECK_FREQUENCE = '$C7', QUALITY_CHECK_ALLOWANCE = '$C8' WHERE ID = '$ids'";
            MiNonQuery( $SQLString, _conn());
            header('Content-type: text/xml');
            echo "<?xml version='1.0' ?><data><action type='updated' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT COLOR, SIDE, SETUP_BASELINE, LOT_SCRAP, MACHINE_STOP_FREQUENCE, MACHINE_STOP_ALLOWANCE, QUALITY_CHECK_FREQUENCE, QUALITY_CHECK_ALLOWANCE FROM $table_offset_printing_scrap;", _conn());
            foreach($retval as $row) {
                $i++;
                echo '<row id="' .$row['ID']. '">';
                echo '<cell>' .$row['ID']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['COLOR']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SIDE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SETUP_BASELINE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['LOT_SCRAP']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['MACHINE_STOP_FREQUENCE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['MACHINE_STOP_ALLOWANCE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['QUALITY_CHECK_FREQUENCE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['QUALITY_CHECK_ALLOWANCE']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "LOADDATAGLID"){
        $GLID = $_POST["GLID"];
        $DataGLID = MiQuery( "SELECT Digital_Machine, Digital_Sheet_Size, Digital_Stock_Code_F, Digital_Cut_No, Digital_UPS, Sheet_Size, Stock_Code, Offset_Cut_No, Offset_UPS, Suited_Machine FROM $table_item_info WHERE GLID = '$GLID';", _conn());
        echo json_encode($DataGLID[0]);
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "GETDATE"){
        echo date("Y-m-d H:i:s");
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADPPCRECEIVING"){
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            echo "<?xml version='1.0' ?><data>";
            header('Content-type: text/xml');
            foreach(explode(",",$_POST["ids"]) as $K)
            {
                $ids = $K;
                $C0 = $_POST[$ids . "_c0"];
                $C1 = $_POST[$ids . "_c1"];
                $C2 = $_POST[$ids . "_c2"];
                $C3 = $_POST[$ids . "_c3"];
                $C4 = $_POST[$ids . "_c4"];
                $C5 = $_POST[$ids . "_c5"];
                $C6 = $_POST[$ids . "_c6"];
                $C7 = $_POST[$ids . "_c7"];
                $C8 = $_POST[$ids . "_c8"];
                $C9 = $_POST[$ids . "_c9"];
                $C10 = $_POST[$ids . "_c10"];
                $C11 = $_POST[$ids . "_c11"];
                $C12 = $_POST[$ids . "_c12"];
                $C13 = $_POST[$ids . "_c13"];
                $C14 = $_POST[$ids . "_c14"];
                $C15 = $_POST[$ids . "_c15"];
                $C0 = str_replace("\n","", $C0);
                $C1 = str_replace("\n","", $C1);
                $C2 = str_replace("\n","", $C2);
                $C3 = str_replace("\n","", $C3);
                $C4 = str_replace("\n","", $C4);
                $C5 = str_replace("\n","", $C5);
                $C6 = str_replace("\n","", $C6);
                $C7 = str_replace("\n","", $C7);
                $C8 = str_replace("\n","", $C8);
                $C9 = str_replace("\n","", $C9);
                $C10 = str_replace("\n","", $C10);
                $C11 = str_replace("\n","", $C11);
                $C12 = str_replace("\n","", $C12);
                $C13 = str_replace("\n","", $C13);
                $C14 = str_replace("\n","", $C14);
                $C15 = str_replace("\n","", $C15);
                $CNavi = $_POST[$ids . "_!nativeeditor_status"];

                $X = explode("-", $C1);
                $LINE_NUMBER = "";
                $ORDER_NUMBER = $X[0];
                if(count($X) == 2) {
                    $LINE_NUMBER = $X[1];
                }

                if($CNavi == "inserted")
                {    
                    $SQLString = "INSERT INTO $table_receiving
                                    (
                                        IDCODE,
                                        ORDER_NUMBER,
                                        LINE_NUMBER,
                                        ORDER_LINE,
                                        CUSTOMER_PO,
                                        ORDERED_ITEM,
                                        ITEM,
                                        QTY,
                                        UOM,
                                        REQUEST_DATE,
                                        PROMISE_DATE,
                                        SHIP_TO_CUSTOMER,
                                        BILL_TO_CUSTOMER,
                                        PPC_RECEIVING_TIME,
                                        ISSUE_ORDER,
                                        CUSTOMER_REQUEST)
                                    VALUES
                                    (
                                        '$ids',
                                        '$C1',
                                        '$C2',
                                        '$C3',
                                        '$C4',
                                        '$C5',
                                        '$C6',
                                        '$C7',
                                        '$C8',
                                        '$C9',
                                        '$C10',
                                        '$C11',
                                        '$C12',
                                        NOW(),
                                        '$C14',
                                        '$C15');";
                    MiNonQuery( $SQLString, _conn());
                } else if($CNavi == "updated")
                {    
                    $SQLString = "UPDATE $table_receiving SET
                                        ISSUE_ORDER = '$C14',
                                        CUSTOMER_REQUEST = '$C15' WHERE ID = '$ids';";
                    MiNonQuery( $SQLString, _conn());
                } else if($CNavi == "deleted")
                {    
                    $SQLString = "DELETE FROM $table_receiving WHERE ID = '$ids';";
                    echo $SQLString;
                    MiNonQuery( $SQLString, _conn());
                }
                echo "<action type='$CNavi' sid='$ids' tid='$ids' ></action>";            
            }
            echo "</data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT ID AS IDCODE, ORDER_NUMBER, LINE_NUMBER, ORDER_LINE, CUSTOMER_PO, ORDERED_ITEM, ITEM, QTY, UOM, REQUEST_DATE, PROMISE_DATE,
                                        SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, PPC_RECEIVING_TIME, ISSUE_ORDER, CUSTOMER_REQUEST FROM $table_receiving WHERE PPC_RECEIVING_TIME BETWEEN '$DateFrom' AND '$DateTo';
                                    ", _conn());
                foreach($retval as $row) {
                    foreach($row as $X=>$Y) {
                        $row[$X] = str_replace("&","&amp;",clean($row[$X]));
                    }
                    echo '<row id="'. str_replace("&","&amp;",$row['IDCODE']) .'">';
                    echo '<cell>' .$row['IDCODE']. '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['ORDER_NUMBER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['LINE_NUMBER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['ORDER_LINE']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['CUSTOMER_PO']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['ORDERED_ITEM']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['ITEM']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['QTY']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['UOM']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['REQUEST_DATE']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['PROMISE_DATE']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['SHIP_TO_CUSTOMER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['BILL_TO_CUSTOMER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['PPC_RECEIVING_TIME']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['ISSUE_ORDER']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['CUSTOMER_REQUEST']). '</cell>';
                    echo '</row>';
                }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADPPCRECEIVINGSOLINE"){
        $SOLINE = $_GET["SOLINE"];
        $SOLINE = explode("-",$SOLINE)[0];
        header('Content-type: text/xml');
        echo "<rows>";
        $retval = MiQuery( "SELECT ID AS IDCODE, ORDER_NUMBER, LINE_NUMBER, ORDER_LINE, CUSTOMER_PO, ORDERED_ITEM, ITEM, QTY, UOM, REQUEST_DATE, PROMISE_DATE,
                                    SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, PPC_RECEIVING_TIME, ISSUE_ORDER, CUSTOMER_REQUEST,  
                                    (SELECT JOBJACKET FROM $table_lines WHERE ORDER_NUMBER = A.ORDER_NUMBER AND LINE_NUMBER = A.LINE_NUMBER AND Active = '1' ORDER BY ID DESC LIMIT 1) AS JOBJACKET 
                                    FROM $table_receiving A WHERE ORDER_NUMBER = '$SOLINE';
                                ", _conn());
        $ArrJob = array();
        foreach($retval as $R) if(!in_array($R["JOBJACKET"],$ArrJob)) $ArrJob []= $R["JOBJACKET"];
        $RowTableJob = MiQuery( 
            "SELECT ID AS JOBJACKET, Order_Style, Urgent_Status, ActiveOrder FROM $table_list WHERE ID IN ('" . implode("','", $ArrJob) . "');", _conn());
            foreach($retval as $row) {
                foreach($row as $X=>$Y) {
                    $row[$X] = str_replace("&","&amp;",clean($row[$X]));
                }
                echo '<row id="'. str_replace("&","&amp;",$row['IDCODE']) .'">';
                echo '<cell>' .$row['IDCODE']. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['ORDER_NUMBER']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['LINE_NUMBER']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['ORDER_LINE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['CUSTOMER_PO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['ORDERED_ITEM']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['ITEM']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['QTY']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['UOM']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['REQUEST_DATE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['PROMISE_DATE']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SHIP_TO_CUSTOMER']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['BILL_TO_CUSTOMER']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['PPC_RECEIVING_TIME']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['ISSUE_ORDER']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['CUSTOMER_REQUEST']). '</cell>';
                $T = true;
                foreach($RowTableJob as $R) {
                    if($R["JOBJACKET"] == $row["JOBJACKET"] && $R["ActiveOrder"] == 1) {
                        echo '<cell>' .str_replace("&","&amp;",$row['JOBJACKET']). '</cell>';
                        echo '<cell>' .str_replace("&","&amp;",$R['Order_Style']). '</cell>';
                        echo '<cell>' .str_replace("&","&amp;",$R['Urgent_Status']). '</cell>';
                        $T = !$T;
                        break;
                    }
                }
                if($T) {
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                    echo '<cell></cell>';
                }
                echo '</row>';
            }
        echo "</rows>";
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADPPCRECEIVINGITEM"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            echo "<?xml version='1.0' ?><data>";
            header('Content-type: text/xml');
            foreach(explode(",",$_POST["ids"]) as $K)
            {
                $ids = $K;
                $C0 = $_POST[$ids . "_c0"];
                $C1 = $_POST[$ids . "_c1"];
                $CNavi = $_POST[$ids . "_!nativeeditor_status"];

                $X = explode("-", $C1);
                $LINE_NUMBER = "";
                $ORDER_NUMBER = $X[0];
                if(count($X) == 2) {
                    $LINE_NUMBER = $X[1];
                }
                
                
                if($CNavi == "updated"){   
                    $SQLString = "INSERT INTO $table_item_remark (GLID, PPCRemark) VALUE('$C0','$C1');";
                    
                    MiNonQuery( $SQLString, _conn());
                    $SQLString = "UPDATE $table_item_remark SET PPCRemark = '$C1' WHERE GLID = '$C0';";
                    MiNonQuery( $SQLString, _conn());
                }
                echo "<action type='$CNavi' sid='$ids' tid='$ids' ></action>";            
            }
            echo "</data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT TRIM(A.GLID) AS GLID, TRIM(B.PPCRemark) AS PPCRemark FROM $table_item_info A LEFT JOIN $table_item_remark B ON A.GLID = B.GLID;", _conn());
                foreach($retval as $row) {
                    echo '<row id="'. str_replace("&","&amp;",$row['GLID']) .'">';
                    echo '<cell>' .$row['GLID']. '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['PPCRemark']). '</cell>';
                    echo '</row>';
                }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADPPCITEMPRO"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = $_POST[$ids . "_c0"];
            $C1 = $_POST[$ids . "_c1"];
            $C2 = $_POST[$ids . "_c2"];
            $C3 = $_POST[$ids . "_c3"];
            $C4 = $_POST[$ids . "_c4"];
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            if($CNavi == "inserted")
            {    
                $SQLString = "INSERT INTO $table_order_issue ( `SOLINE`, `STATUS`, `HOLDBY`, `REMARK` ) VALUES ( '$C1', '$C2', '$OrderHandler', '$C4' );";
                MiNonQuery( $SQLString, _conn());

                header('Content-type: text/xml');
                echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";            
            }
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT `ID`, `SOLINE`, `STATUS`, `HOLDBY`, `REMARK` FROM $table_order_issue ORDER BY ID DESC LIMIT 500;", _conn());
                foreach($retval as $row) {
                    echo '<row id="'. str_replace("&","&amp;",$row['ID']) .'">';
                    echo '<cell>' .$row['ID']. '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['SOLINE']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['STATUS']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['HOLDBY']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['REMARK']). '</cell>';
                    echo '</row>';
                }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "ORDERLIST"){
        header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT Num,ID,GLID,Bo,Order_Quantity,DueDay,Order_Receive_Day,Submit_Date,Print_Sheet,Print_Scrap,Finish_Scrap,
            Order_Style,Urgent_Status,Order_Check,Stock_Code_F,UPS,Stock_Size,Cut_Number,Order_Handler,PPC_Remark,Print_Machine,Request_Date,
            Promise_Date,SO,SO_Lines,ActiveOrder,DeleteBy,MLA FROM $table_list ORDER BY Num DESC LIMIT 500;", _conn());
                foreach($retval as $row) {
                    echo '<row id="'. str_replace("&","&amp;",$row['Num']) .'">';
                    foreach($row as $K=>$S) {
                        echo '<cell>' .str_replace("&","&amp;",$row[$K]). '</cell>';
                    }
                    echo '</row>';
                }
            echo "</rows>";
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "ORDERLISTDELETE"){
        header('Content-type: text/xml');
        echo "<rows>";
        $retval = MiQuery( "SELECT Num,ID,GLID,Bo,Order_Quantity,DueDay,Order_Receive_Day,Submit_Date,Print_Sheet,Print_Scrap,Finish_Scrap,
        Order_Style,Urgent_Status,Order_Check,Stock_Code_F,UPS,Stock_Size,Cut_Number,Order_Handler,PPC_Remark,Print_Machine,Request_Date,
        Promise_Date,SO,SO_Lines,ActiveOrder,DeleteBy,MLA FROM $table_list WHERE ActiveOrder <> '1' ORDER BY ID DESC LIMIT 500;", _conn());
            foreach($retval as $row) {
                echo '<row id="'. str_replace("&","&amp;",$row['Num']) .'">';
                foreach($row as $K=>$S) {
                    echo '<cell>' .str_replace("&","&amp;",$row[$K]). '</cell>';
                }
                echo '</row>';
            }
        echo "</rows>";
    }  else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADITEMOPTIONAL"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = str_replace("&amp;","&",$_POST[$ids . "_c0"]);
            $C1 = str_replace("&amp;","&",$_POST[$ids . "_c1"]);
            $C2 = str_replace("&amp;","&",$_POST[$ids . "_c2"]);
            $C3 = str_replace("&amp;","&",$_POST[$ids . "_c3"]);
            $C4 = str_replace("&amp;","&",$_POST[$ids . "_c4"]);
            $C5 = str_replace("&amp;","&",$_POST[$ids . "_c5"]);
            $C6 = str_replace("&amp;","&",$_POST[$ids . "_c6"]);
            $C7 = str_replace("&amp;","&",$_POST[$ids . "_c7"]);
            $C8 = str_replace("&amp;","&",$_POST[$ids . "_c8"]);
            $C9 = str_replace("&amp;","&",$_POST[$ids . "_c9"]);
            $C10 = str_replace("&amp;","&",$_POST[$ids . "_c10"]);
            $C10 = str_replace("'","\\'",$C10);
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            // if($CNavi == "inserted")
            // {    
            //     $SQLString = "INSERT INTO $table_request
            //                     (
            //                     IDCODE,
            //                     RBO,
            //                     TYPERBO,
            //                     REMARK_DRILL,
            //                     REMARK_1,
            //                     REMARK_2)
            //                     VALUES
            //                     (
            //                     '$ids',
            //                     '$C1',
            //                     '$C2',
            //                     '$C3',
            //                     '$C4',
            //                     '$C5');";
            //     MiNonQuery( $SQLString, _conn());
            // } else 
            if($CNavi == "updated")
            {    
                $SQLString = "UPDATE $table_optinal_pewindow SET 
                                        Production_Type = '$C1', 
                                        Machine = '$C2', 
                                        Production_Line = '$C3', 
                                        Color_Management = '$C4', 
                                        Social_Compliance = '$C5', 
                                        System_Name = '$C6', 
                                        Digital_Availability = '$C7', 
                                        Status = '$C8', 
                                        FSC_Type = '$C9', 
                                        BO = '$C10' WHERE ID = '$ids'";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "deleted")
            {    
                $SQLString = "DELETE FROM $table_optinal_pewindow WHERE ID = '$ids'";
                MiNonQuery( $SQLString, _conn());
            }
            echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT ID, Production_Type, Machine, Production_Line, Color_Management, Social_Compliance, System_Name, Digital_Availability, Status, FSC_Type, BO FROM $table_optinal_pewindow;", _conn());
            foreach($retval as $k=>$row) {
                $i++;
                echo '<row id="' .$row['ID']. '">';
                echo '<cell>' .($k + 1). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Production_Type']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Machine']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Production_Line']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Color_Management']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Social_Compliance']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['System_Name']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Digital_Availability']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['Status']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['FSC_Type']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['BO']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADTRIMOPTIONAL"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            $ids = $_POST["ids"];
            $C0 = str_replace("&amp;","&",$_POST[$ids . "_c0"]);
            $C1 = str_replace("&amp;","&",$_POST[$ids . "_c1"]);
            $C2 = str_replace("&amp;","&",$_POST[$ids . "_c2"]);
            $C3 = str_replace("&amp;","&",$_POST[$ids . "_c3"]);
            $C4 = str_replace("&amp;","&",$_POST[$ids . "_c4"]);
            $CNavi = $_POST[$ids . "_!nativeeditor_status"];
            if($CNavi == "updated")
            {    
                $SQLString = "UPDATE $table_trimcard_rbo_print SET 
                                        RBO = '$C1', 
                                        BILL_TO_CUSTOMER = '$C2', 
                                        SHIP_TO_CUSTOMER = '$C3', 
                                        FORM_TRIM = '$C4' WHERE ID = '$ids'";
                MiNonQuery( $SQLString, _conn());
            } else if($CNavi == "deleted")
            {    
                $SQLString = "DELETE FROM $table_trimcard_rbo_print WHERE ID = '$ids'";
                MiNonQuery( $SQLString, _conn());
            }
            echo "<?xml version='1.0' ?><data><action type='$CNavi' sid='$ids' tid='$ids' ></action></data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            $retval = MiQuery( "SELECT ID, BILL_TO_CUSTOMER, SHIP_TO_CUSTOMER, RBO, FORM_TRIM FROM $table_trimcard_rbo_print", _conn());
            foreach($retval as $k=>$row) {
                $i++;
                echo '<row id="' .$row['ID']. '">';
                echo '<cell>' .($k + 1). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['RBO']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['BILL_TO_CUSTOMER']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['SHIP_TO_CUSTOMER']). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row['FORM_TRIM']). '</cell>';
                echo '</row>';
            }
            echo "</rows>";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "TRIGGERTIMELINES"){
        header('Content-type: text/xml');

        $table_timelines = "access_trigger_date_timelines";
        echo "<rows>";
        $retval = MiQuery( "SELECT * FROM $table_timelines ORDER BY id DESC LIMIT 1;", _conn());
            foreach($retval as $row) {
                echo '<row id="'. str_replace("&","&amp;",$row['id']) .'">';
                foreach($row as $K=>$S) {
                    echo '<cell>' .str_replace("&","&amp;",$row[$K]). '</cell>';
                }
                echo '</row>';
            }
        echo "</rows>";
    }
    function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-\]\[\"\{\}\!\@\#\$\%\^\&\*\(\)\+\=\`\:\;\.\,\s]/', ' ', $string); // Removes special chars.
    }

    function utf8_for_xml($string)
    {
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
    }
?>