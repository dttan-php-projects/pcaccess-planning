<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require("./Module/Database.php");

    // @tandoan - 20200910: remark KHONG KIM LOAI (mail: Request "KHONG KIM LOAI" tren Job Jacket. DK: packing instr co KHONG KIM LOAI
	function khongKimLoaiRemark($ArrLine, $RBO, $SHIPTO) 
	{
        $table_vnso = "vnso";
        $result = '';
        $PackingInstrStr = '';
        $SOLineCheck = getSOLineCheck($ArrLine);

        $RBO = html_entity_decode($RBO, ENT_QUOTES);
        if (strpos(strtoupper($RBO), 'ADIDAS') !==false || strpos(strtoupper($RBO), 'REEBOK') !==false ) {
            $result = 'KHONG KIM LOAI';
        } else {

            if (strpos(strtoupper($SHIPTO), 'WORLDON') !==false || strpos(strtoupper($SHIPTO), 'CRYTAL MARTIN') !==false ) {
                $result = 'KHONG KIM LOAI';
            } else {
                if (!empty($SOLineCheck) ) {
                    $SOLineCheck = implode("','",$SOLineCheck);
                    $VNData = MiQuery("SELECT PACKING_INSTRUCTIONS FROM $table_vnso WHERE CONCAT(ORDER_NUMBER, '-', LINE_NUMBER) IN ('".$SOLineCheck."') ;", _conn("au_avery") );
                    // if (empty($VNData) ) {
                    //     $VNData = MiQuery("SELECT PACKING_INSTRUCTIONS FROM au_avery.vnso_total WHERE CONCAT(ORDER_NUMBER, '-', LINE_NUMBER) IN ('".$SOLineCheck."') ;", _conn() );
                    // }
        
                    if (!empty($VNData ) ) {
                        foreach ($VNData as $item ) { $PackingInstrStr .= $item['PACKING_INSTRUCTIONS']; }
                    }
        
                    if (strpos(strtoupper($PackingInstrStr), 'KHONG KIM LOAI') !== false ) {
                        $result = 'KHONG KIM LOAI';
                    }
                } 
            }
            
        }
        
		return $result;
    }

    function urgentRemark($RBO, $SHIPTO) 
    {   
        $remark = '';
        if (strpos(strtoupper($RBO), 'NIKE') !== false ) {
            if (strpos(strtoupper($SHIPTO), 'MAY TINH LOI') !== false ) {
                $remark = "Quấn giấy, buộc thun";
            }
        }
        
        return $remark;

    }

    function getSOLineCheck($ArrLine) {
        $results = array();
        if (!empty($ArrLine ) ) {
            foreach ($ArrLine as $value ) {
                $results[] = $value->SOLINE;
            }
        }

        return $results;
    }

    $table_fgs_data = "access_fgs_data";
    $table_accessory = "access_item_accessory";
    $table_information = "access_item_information";
    $table_progress_track = "access_progress_track";
    $table_list = "access_order_list";
    $table_lines = "access_id_lines";

    $table_inventory_list = "access_inventory_list";
    $table_receiving = "access_order_receiving";
    $table_item_remark = "access_item_remark";

    $table_vnso = "au_avery.vnso";
    $table_soview_text = "au_avery.oe_soview_text";

    // @TanDoan - 20211108
    $PMDigitalArr = array(
        'INDIGO', 'C800P', 'G-U', 'C1000I', 'NON G-U', 'WS6800', 'ABG', 'INKJET', 'KM1', 
        '1', '4', '5', '6', '2,3,7', 'ALL'
    );

    

    $OrderHandler = "";
    if(!isset($_COOKIE["ZeroIntranet"])) $OrderHandler = "Guest"; 
    else $OrderHandler = $_COOKIE["ZeroIntranet"];
    if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "UPDATEDATA") UpdateData($_POST["MAIN"]); 
    else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "CREATEDJOB") CreatedJob($_POST["ORDER"], $_POST["GLID"], $_POST["PL"], $_POST["CODE"], $_POST["REMARK"], $_POST["BO"], $_POST["SHIPTO"] ); 
    else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "GETFGSDATA") {
        $GLID = $_POST["GLID"];
        $FGSQty = MiQueryScalar("SELECT SUM(FGS_IN_OUT) AS FGS FROM $table_fgs_data WHERE GLID = '$GLID';",_conn());
        if($FGSQty == "") $FGSQty = "0";
        echo $FGSQty;
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "UPDATEROUTING") {
        UpdateRouting($_POST["MAIN"], $_POST["JobJacket"]);
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "SAVEACCESSORY") {
        $GLID = trim($_POST["GLID"]);
        if (!empty($GLID) && $GLID !== " " ) {
            $DataItem = explode("|",$_POST["DATA"]);
            $retval = MiNonQuery( "DELETE FROM $table_accessory WHERE GLID = '$GLID';" , _conn());
            foreach($DataItem as $Item) {
                $DataRow = explode("^^",$Item);
                if(count($DataRow) == 3) {
                    $Process = $DataRow[0];
                    $Accessory = $DataRow[1];
                    $Remark = $DataRow[2];
                    $StringInsert = "INSERT INTO $table_accessory (GLID,Process,Accessory,Remark) VALUES('$GLID','$Process','$Accessory','$Remark')";
                    $retval = MiNonQuery( $StringInsert , _conn());
                }
            }
        }
        
        echo "OK";
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "DELETEITEM") {
        $GLID = $_POST["GLID"];
        $retval = MiNonQuery( "DELETE FROM $table_information WHERE GLID = '$GLID';" , _conn());
        $retval = MiNonQuery( "DELETE FROM $table_accessory WHERE GLID = '$GLID';" , _conn());
        echo "OK";
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "UPDATEDATAJJ") {
        $MAIN = $_POST["MAIN"];
        $SOLine = $_POST["SOLine"];
        $Lot = $_POST["Lot"];
        UpdateDataJobJacket($MAIN,$SOLine,$Lot);
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "REROUTING") {
        $PM = $_POST["PM"];
        $GLID = $_POST["GLID"];
        $DueDate = date("Y-m-d H:i:s",strtotime(str_replace('/', '-', $_POST["DUEDATE"])));
        $Qty = $_POST["QTY"];
        $JJ = $_POST["JOBJACKET"];
        $Result = MiNonQuery("DELETE FROM $table_progress_track WHERE ID = '$JJ'", _conn());
        $PM = strtoupper(trim($PM));
        // Tìm trong danh sách máy Digital
        if (in_array($PM, $PMDigitalArr) ) {
            CalculateProcess("Digital", $GLID, $DueDate, $Qty, $JJ);
        } else {
            CalculateProcess("Offset", $GLID, $DueDate, $Qty, $JJ);
        }
        // if($PM == "INDIGO" || $PM == "C800P"  || $PM == "G-U" || $PM == "C1000I" || $PM == "NON G-U" || $PM == "WS6800" || $PM == "ABG" || $PM = 'INKJET' || $PM == "KM1" || $PM == "1" || $PM == "4" || $PM == "5" || $PM == "6" || $PM == "2,3,7" || $PM == "ALL"  ) CalculateProcess("Digital", $GLID, $DueDate, $Qty, $JJ);
        // else CalculateProcess("Offset", $GLID, $DueDate, $Qty, $JJ);
        echo "OK";
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "REROUTINGPROCESS") {
        $PM = $_POST["PM"];
        $GLID = $_POST["GLID"];
        $DueDate = date("Y-m-d H:i:s",strtotime(str_replace('/', '-', $_POST["DUEDATE"])));
        $Qty = $_POST["QTY"];
        $CODE = $_POST["CODE"];
        $PM = strtoupper(trim($PM));
        // Tìm trong danh sách máy Digital
        if (in_array($PM, $PMDigitalArr) ) {
            ReCalculateProcess("Digital", $GLID, $DueDate, $Qty, $CODE);
        } else {
            ReCalculateProcess("Offset", $GLID, $DueDate, $Qty, $CODE);
        }

        // if($PM == "INDIGO" || $PM == "C800P"  || $PM == "G-U" || $PM == "C1000I" || $PM == "NON G-U" || $PM == "WS6800" || $PM == "ABG" || $PM = 'INKJET' || $PM == "KM1" || $PM == "1" || $PM == "4" || $PM == "5" || $PM == "6" || $PM == "2,3,7" || $PM == "ALL"   ) ReCalculateProcess("Digital", $GLID, $DueDate, $Qty, $CODE);
        // else ReCalculateProcess("Offset", $GLID, $DueDate, $Qty, $CODE);
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "DELETEINVENTORY") {
        $StockCode = $_POST["StockCode"];
        MiNonQuery("DELETE FROM $table_inventory_list WHERE StockCode = '$StockCode'", _conn());
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "DELETEJJ") {
        $JobJacket = $_POST["JJ"];
        // Cập nhật DeleteBy
            MiNonQuery("UPDATE $table_list SET ActiveOrder = '', DeleteBy = '" . $OrderHandler . date("ymdHis") . $_SERVER['REMOTE_ADDR'] . "' WHERE ID = '$JobJacket'", _conn());
        
        // Xóa trong bảng FGS
            MiNonQuery("DELETE FROM $table_fgs_data WHERE JOB_ID = '$JobJacket'", _conn());
        
        // Cập nhật lại bảng access_order_receiving: ISSUE_ORDER = 0 đối với các đơn đã xóa
            $Result = MiQuery("SELECT ORDER_NUMBER, LINE_NUMBER FROM $table_lines WHERE JOBJACKET = '$JobJacket' AND ACTIVE = 1 ", _conn());
            foreach($Result as $R) {
                MiNonQuery("UPDATE $table_receiving SET ISSUE_ORDER = '0' WHERE ORDER_NUMBER = '" . $R["ORDER_NUMBER"] . "' AND LINE_NUMBER = '" . $R["LINE_NUMBER"] . "'", _conn());
            } 

        // Cập nhật lại trạng thái ACTIVE = 0
            MiNonQuery("UPDATE $table_lines SET `ACTIVE`='0' WHERE JOBJACKET = '$JobJacket';",_conn() );

    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "GETDUEDATE") {
        $PD = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST["PD"])));
        $CRD = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_POST["CRD"])));
        $MLA = $_POST["MLA"];

        // @TanDoan - 20210505: Tai sao lại tính DueDate thêm lần nữa tại đây???
        $DueDate = getDueDate($CRD, "", $MLA );
        echo date("d/m/Y", strtotime($DueDate));
        // // $DueDate = $CRD;
        // // // @Tandoan: xử lý theo yêu cầu từ Team Offset (Thuong.Pham trao đổi trực tiếp) 20201111
        // // // if($MLA == "MLA") $DueDate = $CRD; // @Thuong yêu cầu bỏ
        // // if(date("l", strtotime($DueDate)) == "Sunday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days")); // cũ -2
        // // else if(date("l", strtotime($DueDate)) == "Saturday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days")); // cũ -2
        // // else if(date("l", strtotime($DueDate)) == "Monday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-2 days")); // chị Hanh note 
        // // else if(date("l", strtotime($DueDate)) == "Tuesday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days")); // cũ -3
        // // else $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days"));
        // // $Am = 0;
        // // if(date("dmY", strtotime($DueDate)) == "01012020") $Am = 1;
        // // echo date("d/m/Y", strtotime($DueDate . " -" . $Am . " days"));

    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "UPDATEINVENTORY") {
        $DataCode = json_decode($_POST["MAIN"]);
        $StrSQL = "";
        $Values = "";
        $Para = "";
        $StockCode = "";
        foreach($DataCode as $K=>$V) {
            $Para = $Para . "," . $K;
            if($K == "Active") $Values = $Values . "," .$V . ""; 
            else $Values = $Values . ",'" .$V . "'"; 
            if($K == "StockCode") $StockCode = $V;
        }

        MiNonQuery("DELETE FROM $table_inventory_list WHERE StockCode = '$StockCode'", _conn());
        $StrSQL = "INSERT INTO $table_inventory_list (" . substr($Para,1) . ") VALUES (" . substr($Values,1) . ")";
        MiNonQuery($StrSQL, _conn());
        echo $StrSQL;
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "GETSOLINEINFOR") {
        $SOLine = $_POST["SOLINE"];
        $S = explode("-",$SOLine);
        $ORDER_NUMBER = $S[0];
        $ArrayMain = array();

        if(count($S) == 2) {
            $LINE_NUMBER = $S[1];
            $ArrMain = array();
            $ArrMain["ORDER_NUMBER"] = $ORDER_NUMBER;
            $ArrMain["LINE_NUMBER"] = $LINE_NUMBER;
            $ArrMain["SOLINE"] = $ORDER_NUMBER . "-" . $LINE_NUMBER;
            $ArrMain["CUST_PO_NUMBER"] = "";
            $ArrMain["ORDERED_ITEM"] = "";
            $ArrMain["ITEM"] = "";
            $ArrMain["QTY"] = "";
            $ArrMain["UOM"] = "";
            $ArrMain["REQUEST_DATE"] = "";
            $ArrMain["PROMISE_DATE"] = "";
            $ArrMain["SHIP_TO_CUSTOMER"] = "";
            $ArrMain["BILL_TO_CUSTOMER"] = "";
            $ArrMain["PPC"] = Date("Y-m-d H:i:s");
            $ArrMain["CUSTOMER"] = "";
            $ArrayMain []= $ArrMain;

            $DataVNSO = MiQuery("SELECT ORDER_NUMBER, LINE_NUMBER, CUST_PO_NUMBER, ORDERED_ITEM, ITEM, QTY, '' AS UOM, 
                             REQUEST_DATE, PROMISE_DATE, SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, (SELECT PPCRemark FROM $table_item_remark WHERE GLID = ITEM LIMIT 1) AS C FROM $table_vnso A WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER'", _conn());

            $DataOEText = MiQuery("SELECT ORDER_NUMBER, LINE_NUMBER, CUSTOMER_PO AS CUST_PO_NUMBER, QTY, CONTENT, ITEM, UOM, REQUEST_DATE, PROMISE_DATE, SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, (SELECT PPCRemark FROM $table_item_remark WHERE GLID = ITEM LIMIT 1) AS C
                            FROM $table_soview_text WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER'", _conn());

        } else if(count($S) == 3) {
            $StartR = $S[1];
            $EndR = $S[2];
            for($i = $StartR; $i <= $EndR; $i++) {
                $ArrMain = array();
                $ArrMain["ORDER_NUMBER"] = $ORDER_NUMBER;
                $ArrMain["LINE_NUMBER"] = $i;
                $ArrMain["SOLINE"] = $ORDER_NUMBER . "-" . $i;
                $ArrMain["CUST_PO_NUMBER"] = "";
                $ArrMain["ORDERED_ITEM"] = "";
                $ArrMain["ITEM"] = "";
                $ArrMain["QTY"] = "";
                $ArrMain["UOM"] = "";
                $ArrMain["REQUEST_DATE"] = "";
                $ArrMain["PROMISE_DATE"] = "";
                $ArrMain["SHIP_TO_CUSTOMER"] = "";
                $ArrMain["BILL_TO_CUSTOMER"] = "";
                $ArrMain["PPC"] = Date("Y-m-d H:i:s");
                $ArrMain["CUSTOMER"] = "";
                $ArrayMain []= $ArrMain;
            }

            $DataVNSO = MiQuery("SELECT ORDER_NUMBER, LINE_NUMBER, CUST_PO_NUMBER, ORDERED_ITEM, ITEM, QTY, '' AS UOM, 
                             REQUEST_DATE, PROMISE_DATE, SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, (SELECT PPCRemark FROM $table_item_remark WHERE GLID = ITEM LIMIT 1) AS C FROM $table_vnso A WHERE ORDER_NUMBER = '$ORDER_NUMBER'", _conn());

            $DataOEText = MiQuery("SELECT ORDER_NUMBER, LINE_NUMBER, CUSTOMER_PO AS CUST_PO_NUMBER, QTY, CONTENT, ITEM, UOM, REQUEST_DATE, PROMISE_DATE, SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, (SELECT PPCRemark FROM $table_item_remark WHERE GLID = ITEM LIMIT 1) AS C 
                            FROM $table_soview_text WHERE ORDER_NUMBER = '$ORDER_NUMBER'", _conn());

        }
        
        if(count($DataVNSO) > 0) {
            foreach($DataVNSO as $R) {
                foreach($ArrayMain  as $k=>$r) {
                    if($R["ORDER_NUMBER"] == $r["ORDER_NUMBER"] && $R["LINE_NUMBER"] == $r["LINE_NUMBER"]) {
                        $ArrayMain[$k]["CUST_PO_NUMBER"] = $R["CUST_PO_NUMBER"];
                        $ArrayMain[$k]["ORDERED_ITEM"] = $R["ORDERED_ITEM"];
                        $ArrayMain[$k]["ITEM"] = $R["ITEM"];
                        $ArrayMain[$k]["QTY"] = $R["QTY"];
                        $ArrayMain[$k]["REQUEST_DATE"] = $R["REQUEST_DATE"];
                        $ArrayMain[$k]["PROMISE_DATE"] = $R["PROMISE_DATE"];
                        $ArrayMain[$k]["SHIP_TO_CUSTOMER"] = $R["SHIP_TO_CUSTOMER"];
                        $ArrayMain[$k]["BILL_TO_CUSTOMER"] = $R["BILL_TO_CUSTOMER"];
                        $ArrayMain[$k]["CUSTOMER"] = $R["C"];
                    }
                }
            }
        } 

        if(count($DataOEText) > 0) {
            foreach($DataOEText as $R) {
                foreach($ArrayMain  as $k=>$r) {
                    if($R["ORDER_NUMBER"] == $r["ORDER_NUMBER"] && $R["LINE_NUMBER"] == $r["LINE_NUMBER"]) {
                        if($ArrayMain[$k]["CUST_PO_NUMBER"] == "") $ArrayMain[$k]["CUST_PO_NUMBER"] = $R["CUST_PO_NUMBER"];
                        if($ArrayMain[$k]["ITEM"] == "") $ArrayMain[$k]["ITEM"] = $R["ITEM"];
                        if($ArrayMain[$k]["QTY"] == "") $ArrayMain[$k]["QTY"] = $R["QTY"];
                        if($ArrayMain[$k]["REQUEST_DATE"] == "") $ArrayMain[$k]["REQUEST_DATE"] = $R["REQUEST_DATE"];
                        if($ArrayMain[$k]["PROMISE_DATE"] == "") $ArrayMain[$k]["PROMISE_DATE"] = $R["PROMISE_DATE"];
                        if($ArrayMain[$k]["SHIP_TO_CUSTOMER"] == "") $ArrayMain[$k]["SHIP_TO_CUSTOMER"] = $R["SHIP_TO_CUSTOMER"];
                        if($ArrayMain[$k]["BILL_TO_CUSTOMER"] == "") $ArrayMain[$k]["BILL_TO_CUSTOMER"] = $R["BILL_TO_CUSTOMER"];
                        $ArrayMain[$k]["UOM"] = $R["UOM"];
                        $ArrayMain[$k]["CUSTOMER"] = $R["C"];
                    }
                }
            }
        }
        
        foreach($ArrayMain  as $k=>$r) {
            if(isset($r["REQUEST_DATE"]) && $r["REQUEST_DATE"] != "") $ArrayMain[$k]["REQUEST_DATE"] = date("Y-m-d", strtotime($r["REQUEST_DATE"]));
            if(isset($r["PROMISE_DATE"]) && $r["PROMISE_DATE"] != "") $ArrayMain[$k]["PROMISE_DATE"] = date("Y-m-d", strtotime($r["PROMISE_DATE"]));
        }
        echo json_encode($ArrayMain);
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "SAVETRIGGERTIMELINES") {
        
        $table_timelines = "access_trigger_date_timelines";

        $data = json_decode($_POST["data"], true);
        // $data = '{"idSave":"1","mla_timelines":"3","non_mla_timelines":"1"}';
        // $data = json_decode($data, true);
        $idSave = (int)$data['idSave'];
        $mla_timelines = (int)$data['mla_timelines'];
        $non_mla_timelines = (int)$data['non_mla_timelines'];
        $updated_by = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : '';
        $updated_date = date('Y-m-d H:i:s');

        if (!empty($mla_timelines) && !empty($non_mla_timelines) ) {
            $Result = MiQuery("SELECT id FROM $table_timelines WHERE id = '$idSave' ", _conn());
            if (!empty($Result) ) {
                $StrSQL = "UPDATE $table_timelines SET mla_timelines='$mla_timelines', non_mla_timelines='$non_mla_timelines', updated_by='$updated_by', updated_date='$updated_date' WHERE id = '$idSave' ; ";
            } else {
                $StrSQL = "INSERT INTO $table_timelines (mla_timelines, non_mla_timelines, updated_by) VALUES ('$mla_timelines', '$non_mla_timelines', '$updated_by'); ";
            }
        }

        $result = MiNonQuery($StrSQL, _conn());
        if ($result ) {
            $results = array(
                'status' => true,
                'message' => 'Save Data Success'
            );
        } else {
            $results = array(
                'status' => false,
                'message' => 'Save Data Error'
            );
        }
        
        echo json_encode($results,JSON_UNESCAPED_UNICODE); exit();

    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "GETDUEDATE") {
        $PD = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_GET["PD"])));
        $CRD = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $_GET["CRD"])));
        $MLA = $_GET["MLA"];

        // @TanDoan - 20210505: Tai sao lại tính DueDate thêm lần nữa tại đây???
        $DueDate = getDueDate($CRD, "", $MLA );
        echo date("d/m/Y", strtotime($DueDate));
        // // $DueDate = $CRD;
        // // // @Tandoan: xử lý theo yêu cầu từ Team Offset (Thuong.Pham trao đổi trực tiếp) 20201111
        // // // if($MLA == "MLA") $DueDate = $CRD; // @Thuong yêu cầu bỏ
        // // if(date("l", strtotime($DueDate)) == "Sunday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days")); // cũ -2
        // // else if(date("l", strtotime($DueDate)) == "Saturday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days")); // cũ -2
        // // else if(date("l", strtotime($DueDate)) == "Monday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-2 days")); // chị Hanh note 
        // // else if(date("l", strtotime($DueDate)) == "Tuesday") $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days")); // cũ -3
        // // else $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days"));
        // // $Am = 0;
        // // if(date("dmY", strtotime($DueDate)) == "01012020") $Am = 1;
        // // echo date("d/m/Y", strtotime($DueDate . " -" . $Am . " days"));

    }





    function UpdateRouting($DataLine,$JobJacket) {
        $SQLInsert = "";
        $ArrLine = json_decode($DataLine);
        
        $table_progress_track = "access_progress_track";

        $Result = MiNonQuery("DELETE FROM $table_progress_track WHERE ID = '$JobJacket'", _conn());
        if(count($ArrLine) > 0) {
            foreach($ArrLine as $R) {
                $Seq = $R->SEQ;
                $Process = $R->Process;
                $PFD = $R->PFD;
                $SQLInsert = $SQLInsert . ",('$JobJacket','$Seq','$Process','$PFD')";
            }
            $SQLInsert = substr($SQLInsert,1);
            $Result = MiNonQuery("INSERT INTO $table_progress_track (ID,Process_Sequence,Process,Planing_Finish_Date) VALUES " . $SQLInsert, _conn());
            if($Result) echo $DataLine;
        } else if($Result) echo $DataLine;
    }
    
    function CreatedJob($DataLine,$GLID,$ProductLine, $CodeProcess, $Remark,$BO, $SHIPTO) {
        $OrderHandler = "";
        if(!isset($_COOKIE["ZeroIntranet"])) return; 
        else $OrderHandler = $_COOKIE["ZeroIntranet"];
        $IDS = $OrderHandler.date("ymdHis").round(microtime(true) * 1000);
        $ArrLine = json_decode($DataLine);

        $table_information = "access_item_information";
        $table_list = "access_order_list";
        $table_lines = "access_id_lines";
        $table_receiving = "access_order_receiving";
        $table_mla = "access_mla";
        $table_timelines = "access_trigger_date_timelines";

        // @tandoan: get SOLine Arr
            $remarkKKL = khongKimLoaiRemark($ArrLine,$BO, $SHIPTO);
            $Remark .= " " . $remarkKKL;
        // @TanDoan: Thêm remark mới
            $urgentRemark = urgentRemark($BO, $SHIPTO);
            if (!empty($urgentRemark) ) {
                $Remark .= " " . $urgentRemark;
            }
    
        
        $Qty = 0;
        $Order = "";
        $ReceiveDate = date("d/m/Y H:i:s");
        $CRD = date("Y-m-d H:i:s", strtotime("2050-01-01 00:00:00"));
        $PD = date("Y-m-d H:i:s", strtotime("2050-01-01 00:00:00"));
        $CusRequest = "";
        foreach($ArrLine as $KeyR => $R) {
            
            // if(date("Y-m-d H:i:s",strtotime($R->CRD)) < $CRD) $CRD = date("Y-m-d H:i:s",strtotime($R->CRD));
            // if(date("Y-m-d H:i:s",strtotime($R->PD)) < $PD) $PD = date("Y-m-d H:i:s",strtotime($R->PD));


            // @TanDoan - 20210311: Xử lý lấy ngày CRD nhỏ nhất
                if ($KeyR == 0 ) {
                    if(date("Y-m-d H:i:s",strtotime($R->CRD)) < $CRD) $CRD = date("Y-m-d H:i:s",strtotime($R->CRD));
                    // if(date("Y-m-d H:i:s",strtotime($R->PD)) < $PD) $PD = date("Y-m-d H:i:s",strtotime($R->PD));
                    if (!empty($R->PD) ) {
                        $PD = date("Y-m-d H:i:s",strtotime($R->PD));
                    } else {
                        $PD = '';
                    }
                } else {
                    $CRDCheck = date("Y-m-d H:i:s",strtotime($R->CRD));
                    $PDCheck = !empty($R->PD) ? date("Y-m-d H:i:s",strtotime($R->PD)) : '';

                    $CRD = ( strtotime($CRDCheck) < strtotime($CRD) ) ? $CRDCheck : $CRD;
                    
                    if (!empty($PD) && !empty($PDCheck) ) {
                        $PD = ( strtotime($PDCheck) < strtotime($PD) ) ? $PDCheck : $PD;
                    } else {
                        $PD = '';
                    }

                }
            

            $Qty += $R->QTY;
            if(strpos($Order, explode("-",$R->SOLINE)[0]) === false) $Order .= explode("-",$R->SOLINE)[0] . "/";
        }

        // $BO = htmlspecialchars($BO, ENT_QUOTES, 'UTF-8');

        $MLA = MiQueryScalar("SELECT MLA FROM $table_mla WHERE RBO = '" . str_replace("'","\\'",$BO) . "' LIMIT 1;", _conn());
        if($MLA != "") $MLA = "MLA";
        else $MLA = "";

        $NewProX = MiQueryScalar( "SELECT COUNT(1) AS GLID FROM $table_list WHERE GLID = '$GLID' AND ActiveOrder = 1 LIMIT 1;", _conn());
        if($NewProX == "0") $NewProX = "1";
        else $NewProX = "";
        // $DueDate = $MLA != "" ? $CRD : $PD;
        $DueDate =  $CRD;

        /* --------------------------------------------------------------------------------------------------------------------------------------
            
            SU DUNG TOOL UPDATE SỐ LƯỢNG CẦN TRỪ ĐỐI VỚI ĐƠN HÀNG MLA, NON MLA

            @TanDoan - 20210414: Thay đổi lần 4: mặc định
            - Trừ 1 ngày. 
            - Đơn MLA: -2 ngày
            - Sau khi tính DueDate, nếu DueDate là chủ nhật thì trừ thêm 1 ngày
        -------------------------------------------------------------------------------------------------------------------------------------- */

            $DueDate = getDueDate($DueDate, $BO);

        
        $CRD = date("d/m/Y", strtotime($CRD));
        $PD = !empty($PD) ? date("d/m/Y", strtotime($PD)) : '';

        // Xử lý PD nếu trống: Nếu trống thì cộng ngày làm lệnh thực tế + 7, chủ nhật thì cộng thêm 1

        $DueDateShow = date("d/m/Y", strtotime($DueDate));

        /*  - Vẫn giữ logic cũ: Load GLID là lưu và set Active = 0
            - Xử lý lại: Cách thêm nội dung và check Active
        */

        // Create JobJacket Number
            $ID = createJJNO();
        // get Master data
            $dataInfo = MiQuery( "SELECT * FROM $table_information WHERE GLID = '$GLID';", _conn() );
            $dataInfo = $dataInfo[0];

            $Buying_Office = isset($dataInfo["Buying_Office"]) ? htmlspecialchars($dataInfo["Buying_Office"],ENT_QUOTES, 'UTF-8') : "";
            $Order_Style = ($ProductLine == "Offset") ? "Offset" : "Digital";
            
            $Urgent_Status = "Normal";
            $Order_Check = "1";

            $Stock_Code_F = "";
            if (!empty($dataInfo) ) {
                $Stock_Code_F = ($ProductLine == "Offset") ? $dataInfo["Stock_Code"] : $dataInfo["Digital_Stock_Code_F"];
            }

            $UPS = "";
            if (!empty($dataInfo) ) {
                $UPS = ($ProductLine == "Offset") ? $dataInfo["Offset_UPS"] : $dataInfo["Digital_UPS"];
            }

            $Stock_Size = "";
            if (!empty($dataInfo) ) {
                $Stock_Size = ($ProductLine == "Offset") ? $dataInfo["Sheet_Size"] : $dataInfo["Digital_Sheet_Size"];
            }

            $Cut_Number = "";
            if (!empty($dataInfo) ) {
                $Cut_Number = ($ProductLine == "Offset") ? $dataInfo["Offset_Cut_No"] : $dataInfo["Digital_Cut_No"];
            }

            $FGS_Check = "0";

            $Print_Machine = "";
            if (!empty($dataInfo) ) {
                $Print_Machine = ($ProductLine == "Offset") ? $dataInfo["Suited_Machine"] : $dataInfo["Digital_Machine"];
            }

            // get query string
            // Mặc định gán ACTIVE=0 để khi người dùng SAVE thì mới cập nhật lại trạng thái ACTIVE = 1
            $SqlString = "INSERT INTO $table_list (
                `GLID`, `Bo`, `Order_Quantity`, `DueDay`, `Order_Receive_Day`, `Submit_Date`, `Order_Style`, `Urgent_Status`, `Order_Check`, `Stock_Code_F`, `UPS`, `Stock_Size`, 
                `Cut_Number`, `Order_Handler`, `FGS_Check`, `New_Product_If`, `Print_Machine`, `Request_Date`, `Promise_Date`, `SO`, `SO_Lines`, `PPC_Remark`, `IDS`
            ) VALUES (
                '$GLID', '$Buying_Office', '$Qty', '$DueDateShow', NOW(), '', '$Order_Style', '$Urgent_Status', '$Order_Check', '$Stock_Code_F', '$UPS', '$Stock_Size',
                '$Cut_Number', '$OrderHandler', '$FGS_Check', '$NewProX', '$Print_Machine', '$CRD', '$PD', '$Order', '', '$Remark', '$IDS'
            ); ";
            // query excute
            $Result = MiNonQuery($SqlString,_conn());
            if($Result) $Result = MiNonQuery("UPDATE $table_list SET ID = CONCAT('D',RIGHT(Num,6)), Submit_Date = NOW() WHERE ID IS NULL",_conn());
            if($Result) $Result = MiQueryScalar("SELECT ID FROM au_avery_pc.access_order_list WHERE IDS = '$IDS' LIMIT 1",_conn());
        
        // Kiêm tra đã insert thành công chưa
            if($Result) {
                // Đoạn cập nhật lại thông tin Last_Order_Time trong bảng infomation
                    MiNonQuery("UPDATE $table_information SET Last_Order_Time = NOW() WHERE GLID = '$GLID'",_conn());
                // Thêm nội dung vào bảng access_id_lines
                    $S = "";
                    $SQLInsert = "";
                    $SOLCheck = "|";
                    $current = date('d/m/Y');	
                    foreach($ArrLine as $R) {
                        
                        if(strpos($SOLCheck, "|" . $R->SOLINE . "|") !== false) continue;
                        else $SOLCheck .= $R->SOLINE . "|";
                        $Qty += $R->QTY;
                        $SQLInsert .= ",('$Result','" . $R->SOLINE . "','" . $R->QTY . "','" . explode("-",$R->SOLINE)[0] . "','" . explode("-",$R->SOLINE)[1] . "', '0')";
                        MiNonQuery("UPDATE $table_receiving SET ISSUE_ORDER = '1' WHERE ORDER_NUMBER = '" . explode("-",$R->SOLINE)[0] . "' AND LINE_NUMBER = '" . explode("-",$R->SOLINE)[1] . "'",_conn());
                    }

                    // @Tandoan: 20200921 - Xử lý sửa đơn không có SO#
                    $checkActiveBU = 0;
                    if (empty($SQLInsert) ) {
                        $SQLInsert = ",('$Result','','','','','0')";
                        $checkActiveBU = 1;
                    }

                    $JJ = $Result;
                    $SQLInsert = substr($SQLInsert, 1);
                    
                    // Thêm vào bảng access_id_lines
                    $ResultInsert = "";
                    if(strlen($SQLInsert) > 5) $ResultInsert = MiNonQuery("INSERT INTO $table_lines (`JOBJACKET`,`SO_Line`,`SO_Line_Qty`,`ORDER_NUMBER`,`LINE_NUMBER`, `ACTIVE`) VALUES $SQLInsert",_conn());
                    if($ResultInsert) echo $Result;
                    // Tính toán 
                    if(strpos($CRD,"2050") === false) CalculateProcess($ProductLine, $GLID, $DueDate, $Qty, $JJ);
            }
            
    }

    function CalculateProcess($ProductLine, $GLID, $DueDate, $Qty, $JJ) {
        
        $table_information = "access_item_information";
        $table_progress_track = "access_progress_track";
        $table_process = "access_item_process";

        if($ProductLine == "Offset") $ResultTable = MiQuery("SELECT Buying_Office, Process, Varnish_F, Offset_UPS AS UPS FROM $table_information WHERE GLID = '$GLID';", _conn()); 
        else $ResultTable = MiQuery("SELECT Buying_Office, Process, Varnish_F, Digital_UPS AS UPS FROM $table_information WHERE GLID = '$GLID';", _conn());
        
        $RBO = "";

        $CodeProcess = "";
        $SpecVarnish = "";
        $PrintSheet = "";
        if(!empty($ResultTable) && count($ResultTable) != 0) {
            $ResultTable = $ResultTable[0];
            
            $RBO = $ResultTable["Buying_Office"];

            $CodeProcess = $ResultTable["Process"];
            $SpecVarnish = $ResultTable["Varnish_F"];
            
            $PrintSheet = $ResultTable["UPS"] != 0 ? ceil($Qty/$ResultTable["UPS"]) : "0";
            if($ResultTable["Process"] == "") return;
        }

        $TempCode = $CodeProcess;
        for($i = 1; $i < 20; $i++){
            $TempCode = str_replace(str_pad($i, 2, '0', STR_PAD_LEFT),",",$TempCode);
            $CodeProcess = str_replace(str_pad($i, 2, '0', STR_PAD_LEFT),"','",$CodeProcess);
        }
        if(strlen($CodeProcess) < 4 && strpos($CodeProcess,"PS")) $CodeProcess = "'PS'";
        else $CodeProcess = "'" . substr($CodeProcess,3) . "'";
        
        $TempCode = explode(",",substr($TempCode,1));
        $TurnOn = 0;
        $ArrMain = array();
        $RawResult = Miquery( "SELECT ID,Code,Process, Process_Ability, Ability_Unit, `<=500` AS X, `501-2000` AS Y, `2001-5000` AS Z, `>5000` AS W, '' AS PFD FROM $table_process WHERE Code IN ($CodeProcess);", _conn());
        foreach($TempCode as $K=>$R) {
            foreach($RawResult as $r) {
                if($R == $r["Code"]) {
                    $r["Seq"] = $K;
                    $ArrMain[]= $r;
                    break;
                }
            }
        }
        
        // @TanDoan - 2021-07-08: Tính Trigger Date mới nhất
            $ArrMain = getTriggerDate($ArrMain, $DueDate, $Qty, $PrintSheet);


        $i = 1;
        $SQLInsert = array();
        foreach($ArrMain as $R) {
            $Seq = $R["Seq"];
            $Process = $R["Process"];
            $PFD = $R["PFD"];
            $SQLInsert []= "('$JJ','$Seq','$Process','$PFD')";
        }
        $Result = MiNonQuery("DELETE FROM $table_progress_track WHERE ID = '$JJ'", _conn());
        $Result = MiNonQuery("INSERT INTO $table_progress_track (ID,Process_Sequence,Process,Planing_Finish_Date) VALUES " . implode(",",$SQLInsert), _conn());
    }

    function ReCalculateProcess($ProductLine, $GLID, $DueDate, $Qty, $Process) {
        
        $table_information = "access_item_information";
        $table_process = "access_item_process";

        if($ProductLine == "Offset") $ResultTable = MiQuery("SELECT Buying_Office, Process, Varnish_F, Offset_UPS AS UPS FROM $table_information WHERE GLID = '$GLID';", _conn()); 
        else $ResultTable = MiQuery("SELECT Buying_Office, Process, Varnish_F, Digital_UPS AS UPS FROM $table_information WHERE GLID = '$GLID';", _conn());
        
        $RBO = "";
        $SpecVarnish = "";
        $PrintSheet = "";
        $Qty = str_replace(",","",trim($Qty));
        if(!empty($ResultTable) && count($ResultTable) != 0) {
            $ResultTable = $ResultTable[0];

            $RBO = $ResultTable["Buying_Office"];

            $CodeProcess = $ResultTable["Process"];
            $SpecVarnish = $ResultTable["Varnish_F"];
            $PrintSheet = ceil($Qty/$ResultTable["UPS"]);
        }
        
        $CodeProcess = "'" . str_replace(",","','", $Process) . "'";
        $TempCode = explode(",",$Process);
        $TurnOn = 0;
        $ArrMain = array();
        $RawResult = Miquery( "SELECT ID,Code,Process, Process_Ability, Ability_Unit, `<=500` AS X, `501-2000` AS Y, `2001-5000` AS Z, `>5000` AS W, '' AS PFD FROM $table_process WHERE Code IN ($CodeProcess);", _conn());
        foreach($TempCode as $K=>$R) {
            foreach($RawResult as $r) {
                if($R == $r["Code"]) {
                    $r["Seq"] = $K;
                    $ArrMain[]= $r;
                    break;
                }
            }
        }
        
        
        // @TanDoan - 2021-07-08: Tính Trigger Date mới nhất
            $ArrMain = getTriggerDate($ArrMain, $DueDate, $Qty, $PrintSheet);

        $StringMain = "<rows>";
        foreach($ArrMain as $row) {
            $StringMain .= '<row id="'. $row['Seq'] .'">';
            $StringMain .= '<cell style="background: gray">X</cell>';
            $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Seq']). '</cell>';
            $StringMain .= '<cell>' .str_replace("&","&amp;",$row['Process']). '</cell>';
            $StringMain .= '<cell>' .str_replace("&","&amp;",$row['PFD']). '</cell>';
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
        echo $StringMain;
    }

    function UpdateData($DataGLID) {
        $table_information = "access_item_information";
        $DataGLID = json_decode($DataGLID);
        $Result = MiQueryScalar("SELECT GLID FROM $table_information WHERE GLID = '" . $DataGLID->GLID . "' LIMIT 1", _conn());
        $StrSQL = array();
        if($Result != "") {
            foreach($DataGLID as $K=>$V) {
                $V = str_replace("'","''",$V);
                if($K == "PE_Receive_Date" || $K == "Setup_Date" || $K == "Ready_Date" || $K == "Last_Revise_Date" || $K == "Last_Order_Time"){
                    if($V == "" || strpos($V, "1970") !== false ) $StrSQL []= $K . " = null"; 
                    else {
                        $V = date('Y-m-d H:i:s', strtotime($V));
                        $StrSQL []= $K . " = '" .$V . "'";
                    }
                } else $StrSQL []= $K . " = '" .$V . "'";
                // // else if($K == "ProductionLength" || $K == "ProductionWidth"){ // @TanDoan: Xử lý làm tròn 3 chữ số
                // //     if (!empty($V) && (is_float($V) || is_int($V)) ) {
                // //         $V = round($V,3);
                // //         $StrSQL []= $K . " = '" .$V . "'";
                // //     }
                // // } 
            }
            $StrSQL = "UPDATE $table_information SET " . implode(",",$StrSQL) . " WHERE GLID = '" . $DataGLID->GLID . "'";
        } else {
            $Values = "";
            $Para = "";
            foreach($DataGLID as $K=>$V) {
                $V = str_replace("'","\\'",$V);
                if(strpos($V,"00000") !== false) $V = "1970-01-01 00:00:00";
                $Para = $Para . "," . $K;
                if($K == "PE_Receive_Date" || $K == "Setup_Date" || $K == "Ready_Date" || $K == "Last_Revise_Date" || $K == "Last_Order_Time"){
                    if($V == "" || strpos($V, "1970") !== false ) $Values = $Values . ",null"; 
                    else {
                        $V = date('Y-m-d H:i:s', strtotime($V));
                        $Values = $Values . ",'" .$V . "'";
                    }
                } else $Values = $Values . ",'" .$V . "'"; 
            }
            $StrSQL = "INSERT INTO $table_information (" . substr($Para,1) . ") VALUES (" . substr($Values,1) . ")";
        }
        $Result = MiNonQuery($StrSQL, _conn());
        echo $Result;
    }

    function UpdateDataJobJacket($DataJobJacket, $SOLine, $Lot) {
        
        global $OrderHandler;
        $DataJJ = json_decode($DataJobJacket);
        $StrSQL = "";
        $IDJob = "";
        $Qty = "";

        $table_fgs_data = "access_fgs_data";
        $table_progress_track = "access_progress_track";
        $table_list = "access_order_list";
        $table_lines = "access_id_lines";
        $table_lot_remark = "access_lot_remark";

        foreach($DataJJ as $K=>$V) {
            $V = str_replace("'","\\'",$V);
            if($K == "Request_Date" || $K == "Promise_Date"){
                $V = date('d/m/Y', strtotime($V));
                $StrSQL = $StrSQL . "," . $K . " = '" .$V . "'";
            } else if($K == "ActualPCS" || $K == "OverRate" || $K == "ScrapRate")
            {

            } else if($K == "Submit_Date") $StrSQL = $StrSQL . "," . $K . " = NOW()";
            else $StrSQL = $StrSQL . "," . $K . " = '" .$V . "'";
        }

        if($DataJJ->Order_Style == "FGS-OUT"){
            $Result = MiNonQuery("DELETE FROM $table_fgs_data WHERE JOB_ID = '". $DataJJ->ID ."'", _conn());
            if(!$Result) return;
            $Result = MiNonQuery("INSERT INTO $table_fgs_data (GLID,FGS_IN_OUT,JOB_ID,Record_Date,Record_Name) VALUES('". $DataJJ->GLID ."','-". $DataJJ->Order_Quantity ."','". $DataJJ->ID ."',NOW(),'". $DataJJ->Order_Handler ."')", _conn());
            if(!$Result) return;
            $Result = MiNonQuery("DELETE FROM $table_progress_track WHERE ID = '". $DataJJ->ID ."'", _conn());
            if(!$Result) return;
        }

        // cập nhật lại thông tin của bảng access_order_list
            $Result = MiNonQuery("UPDATE $table_list SET ActiveOrder = '1', Submit_Date = NOW()" . $StrSQL . " WHERE ID = '" . $DataJJ->ID . "'; ", _conn());
            if(!$Result) return;

        // Cập nhật lại thông tin của bảng access_id_lines
            $SQLUpdate = array();
            $ArrSOL = array();
            $RemarkSO = "";

            if (!empty($SOLine) ) {

                // Xóa trong bảng SOLine trước sau đó insert vào sau.
                    MiNonQuery("DELETE FROM $table_lines WHERE `JOBJACKET` = '". $DataJJ->ID ."'", _conn());
                // Tạm thời vẫn giữ code hiện tại (UPDATE đang không cần thiết do đã xóa rồi)
                foreach(explode("|",$SOLine) as $R) {
                    $DataSL = explode("*",$R);
                    if(count($DataSL) == 3){
                        // get data
                            $JOBJACKET = $DataJJ->ID;
                            $SO_Line = $DataSL[0];
                            $SO_Line_Qty = $DataSL[1];

                            $SOLineArr = explode("-",$DataSL[0]);
                            $ORDER_NUMBER = $SOLineArr[0];
                            $LINE_NUMBER = $SOLineArr[1];
                            $REMARK = $DataSL[2];

                        // check trường hợp không có SOLine trong bảng lưu thì thêm mới: Trường hợp nhãn cặp
                            $CheckExistSO = MiQueryScalar("SELECT `JOBJACKET` FROM $table_lines WHERE `JOBJACKET`='$JOBJACKET' AND `ORDER_NUMBER`='$ORDER_NUMBER' AND `LINE_NUMBER`='$LINE_NUMBER' LIMIT 1;", _conn());
                            if($CheckExistSO != "") { // update
                                // sql string 
                                $SQLLines =  "UPDATE $table_lines SET `ACTIVE`='1', `SO_Line_Qty`='$SO_Line_Qty', `ORDER_NUMBER`='$ORDER_NUMBER', `LINE_NUMBER`='$LINE_NUMBER', `REMARK`='$REMARK' WHERE JOBJACKET = '$JOBJACKET' AND `SO_Line`='$SO_Line';";
                            } else { // insert
                                $SQLLines = "INSERT INTO $table_lines 
                                                (`JOBJACKET`,`SO_Line`,`SO_Line_Qty`,`ORDER_NUMBER`,`LINE_NUMBER`, `ACTIVE`) 
                                            VALUES
                                                ('$JOBJACKET', '$SO_Line', '$SO_Line_Qty', '$ORDER_NUMBER', '$LINE_NUMBER', '1' ) ;";
                            }
                        
                        // excute
                            $ResultUpdate = MiNonQuery($SQLLines,_conn() );
                        // check 
                            if(!$ResultUpdate) return;
                    }
                }
            } else { // Trường hợp không có SO#
                // sql string 
                    $SQLLines = "UPDATE $table_lines SET `ACTIVE`='1' WHERE JOBJACKET = '". $DataJJ->ID ."';";
                // excute
                    $ResultUpdate = MiNonQuery($SQLLines,_conn() );
                // check 
                    if(!$ResultUpdate) return;
            }
        
        // Cập nhật khác (giữ nguyên)
            $Result = MiNonQuery("DELETE FROM $table_lot_remark WHERE JOBJACKET = '". $DataJJ->ID ."'", _conn());
            if(!$Result) return;
            $SQLUpdate = array();
            foreach(explode("|",$Lot) as $R){
                $DataSL = explode("*",$R);
                if(count($DataSL) == 2) $SQLUpdate []= "('".$DataJJ->ID."','" . $DataSL[0] . "','" . $DataSL[1] . "','" . $OrderHandler . "')";
            }
            $Result = MiNonQuery("INSERT INTO $table_lot_remark (JOBJACKET, LotName, Qty, CreatedBy) VALUES " . implode(",",$SQLUpdate), _conn());
            
            if($Result) echo "OK";

    }

    function createJJNO() 
    {
        $Result = "";
        $table = "access_order_list";
        $Number = MiQueryScalar("SELECT `Num` FROM $table ORDER BY `Num` DESC LIMIT 1",_conn());
        
        
        if (strpos($Number, 'D') !== false ) {
            $Number = (int)str_replace('D', '', $Number);
        }

        // Tăng lên 1 đơn vị
        if (is_int($Number) ) {
            $Number +=1;
            $Result = "D" . $Number;
        }
        

        return (empty($Result) || $Result==null || (substr($Result, 0, 1) !== "D" ) ) ? "" : $Result;
    }

    function checkDeleteOrder($JobJacket) 
    {
        $table = "access_order_list";
        $Result = MiQueryScalar("SELECT `DeleteBy` FROM $table WHERE `ID`='$JobJacket' LIMIT 1",_conn());
    
        return ($Result==true) ? true : false;

    }



    /* --------------------------------------------------------------------------------------------------------------------------------------
        @TanDoan - 20210414: Sử dụng tool cập nhật số lượng cần trừ đối với đơn MLA và NON MLA
    -------------------------------------------------------------------------------------------------------------------------------------- */
    function getDueDate_Off($DueDate, $BO ) 
    {
        // table
            $table_mla = "access_mla";
            $table_timelines = "access_trigger_date_timelines";


            $MLA = MiQueryScalar("SELECT MLA FROM $table_mla WHERE RBO = '" . str_replace("'","\\'",$BO) . "' LIMIT 1;", _conn());
            if($MLA != "") $MLA = "MLA";
            else $MLA = "";

        // Lấy dữ liệu từ bảng timelines
            $mla_timelines = 2;
            $non_mla_timelines = 1;
            $timelines = MiQuery( "SELECT * FROM $table_timelines ORDER BY `id` DESC LIMIT 1;", _conn());
            if (!empty($timelines) ) {
                $timeline = $timelines[0];
                $mla_timelines = $timeline['mla_timelines'];
                $non_mla_timelines = $timeline['non_mla_timelines'];
            }

        // DueDate
            $DueDate = ($MLA != "" ) ? date("Y-m-d H:i:s", strtotime($DueDate . "-" .$mla_timelines. " days")) : date("Y-m-d H:i:s", strtotime($DueDate . "-" .$non_mla_timelines. " days")) ; 

        // Tết dương lịch - 1 ngày
            $Am = 0;
            $NewYearArr = array('20220101', '20230101', '20240101', '20250101');
            foreach ($NewYearArr as $NewYear ) {
                if(date("Ymd", strtotime($DueDate)) == $NewYear) $Am = 1;
            }
            
            $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-" . $Am . " days"));

        // chủ nhật 
            if(date("l", strtotime($DueDate)) == "Sunday") {
                $DueDate = date("Y-m-d H:i:s", strtotime($DueDate . "-1 days"));
            } 

        // results
            return $DueDate;


    }

    function getDueDate($DueDate, $BO, $MLA="" ) 
    {
        // table
            $table_mla = "access_mla";
            $table_timelines = "access_trigger_date_timelines";

            if ($MLA == "" ) {
                if ($BO == "" ) {
                    $MLA = "";
                } else {
                    $MLA = MiQueryScalar("SELECT MLA FROM $table_mla WHERE RBO = '" . str_replace("'","\\'",$BO) . "' LIMIT 1;", _conn());
                    if($MLA != "") $MLA = "MLA";
                    else $MLA = "";
                }
                
            }
            

        // Tết dương lịch
            $NewYearArr = array('20220101', '20230101', '20240101', '20250101');

        // Tết Âm lịch 2022
            $LunaNewYear2022 = MiQuery("SELECT `holiday_date` FROM `holidays` WHERE `holiday_name_group`='LunaNewYear2022' ORDER BY `holiday_date` ASC; ", _conn('au_avery') );

        // Các ngày Lễ khác ngày Tết Dương lịch & Âm lịch cho năm 2022 & (2023 chưa cập nhật)
            $allHolidays = MiQuery("SELECT `holiday_date` FROM `holidays` WHERE NOT `holiday_name_group` like '%NewYear%' AND (`holiday_date` LIKE '2022%' OR `holiday_date` LIKE '2023%') ORDER BY `holiday_date` ASC; ", _conn('au_avery') );

        // Lấy dữ liệu từ bảng timelines
            $mla_timelines = 2;
            $non_mla_timelines = 1;
            $timelines = MiQuery( "SELECT * FROM $table_timelines ORDER BY `id` DESC LIMIT 1;", _conn());
            if (!empty($timelines) ) {
                $timeline = $timelines[0];
                $mla_timelines = $timeline['mla_timelines'];
                $non_mla_timelines = $timeline['non_mla_timelines'];
            }

        // DueDate
            $DueDateOK = ($MLA != "" ) ? date("Y-m-d H:i:s", strtotime($DueDate . "-" .$mla_timelines. " days")) : date("Y-m-d H:i:s", strtotime($DueDate . "-" .$non_mla_timelines. " days")) ; 


        // Kiểm tra trong khoảng thời gian được trừ có ngày chủ nhật thì trừ từng ngày chủ nhật một
            $hieu_so = abs(strtotime($DueDate) - strtotime($DueDateOK));  
            $countY = floor($hieu_so / (365*60*60*24));  
            $countM = floor(($hieu_so - $countY * 365*60*60*24) / (30*60*60*24));  
            $countD = floor(($hieu_so - $countY * 365*60*60*24 - $countM*30*60*60*24)/ (60*60*24));  
                
            $DateCheck = $DueDate;
            for ($i=1;$i<=$countD; $i++ ) {
                $DateCheck = date("Y-m-d", strtotime($DateCheck . "-1 days") );
                // chủ nhật 
                if(date("l", strtotime($DateCheck)) == "Sunday") {
                    $DueDateOK = date("Y-m-d H:i:s", strtotime($DueDateOK . "-1 days"));
                }

                // Tết dương lịch - 1 ngày
                    $Am = 0;
                    foreach ($NewYearArr as $NewYear ) {
                        if(date("Ymd", strtotime($DueDateOK)) == $NewYear) {
                            $DueDateOK = date("Y-m-d H:i:s", strtotime($DueDateOK . "-1 days"));
                            break;
                        } 
                    }  
                    
            }

        // Tết dương lịch - 1 ngày
            $Am = 0;
            foreach ($NewYearArr as $NewYear ) {
                if(date("Ymd", strtotime($DueDateOK)) == $NewYear) $Am = 1;
            }
            
            $DueDateOK = date("Y-m-d H:i:s", strtotime($DueDateOK . "-" . $Am . " days"));

        // Tết âm lịch 2022
            if (!empty($LunaNewYear2022) ) {
                
                // Lấy dữ liệu Tết âm lịch bỏ vô mảng & xác định ngày đầu tiên nghỉ Lễ
                $LunaNewYear2022Ok = array();
                $BeginLunar = $DueDateOK;
                foreach ($LunaNewYear2022 as $keyY => $year ) {
                    if ($keyY == 0 ) $BeginLunar = $year['holiday_date'];
                    $LunaNewYear2022Ok[] = $year['holiday_date'];
                } 

                $checkLuna = false;
                foreach ($LunaNewYear2022Ok as $LunaNewYear ) {
                    if(date("Y-m-d", strtotime($DueDateOK)) == $LunaNewYear) {
                        $checkLuna = true;
                        break;
                    } 
                }

                // Nếu có trong ngày tết, trừ bỏ các ngày tết đi
                if ($checkLuna == true ) {
                    $DueDateOK = date("Y-m-d H:i:s", strtotime($BeginLunar . "-1 days"));
                }
            }

        // Các ngày Lễ khác của năm 2022 (2023 chưa thêm vào)
            if (!empty($allHolidays) ) {
                foreach ($allHolidays as $holiday ) {
                    $holiday_date = $holiday['holiday_date'];
                    if(date("Y-m-d", strtotime($DueDateOK)) == $holiday_date) {
                        $DueDateOK = date("Y-m-d H:i:s", strtotime($DueDateOK . "-1 days"));
                        break;
                    } 
                }
            }

        // chủ nhật 
            if(date("l", strtotime($DueDateOK)) == "Sunday") {
                $DueDateOK = date("Y-m-d H:i:s", strtotime($DueDateOK . "-1 days"));
            } 


        // results
            return $DueDateOK;


    }

    // Updated: 2021-07-09. Mail mới nhất: "Công thức tính trigger date"
    function getTriggerDate($ArrMain, $DueDate, $Qty, $PrintSheet )
    {
        // init 
            $LeadTime = 0;

        // Tết dương lịch
            $NewYearArr = array('20220101', '20230101', '20240101', '20250101');

        // Tết Âm lịch 2022
            $LunaNewYear2022 = MiQuery("SELECT `holiday_date` FROM `holidays` WHERE `holiday_name_group`='LunaNewYear2022' ORDER BY `holiday_date` ASC; ", _conn('au_avery') );

        // Các ngày Lễ khác ngày Tết Dương lịch & Âm lịch cho năm 2022 & 2023
            $allHolidays = MiQuery("SELECT `holiday_date` FROM `holidays` WHERE NOT `holiday_name_group` like '%NewYear%' AND (`holiday_date` LIKE '2022%' OR `holiday_date` LIKE '2023%') ORDER BY `holiday_date` ASC; ", _conn('au_avery') );
        
        // Mặc định: DueDay = DueDate
            $DueDay = Date("Y-m-d H:i:s",strtotime($DueDate));

        // Đếm công đoạn
            $count = count($ArrMain);

        /* --------------------------------------------------------------------------------------------------
            * TÍNH CÔNG ĐOẠN CUỐI CÙNG TRƯỚC. 
            ==> Đóng gói cho LeadTime mặc định bằng DueDate (Trường hợp trong vòng lặp không áp dụng cách này)

        -------------------------------------------------------------------------------------------------- */ 

            // Lấy dữ liệu công đoạn cuối cùng
                $Ability_Unit_Check = $ArrMain[$count-1]["Ability_Unit"];
                $Process_Ability_Check = $ArrMain[$count-1]["Process_Ability"];
                $Process_Check = $ArrMain[$count-1]["Process"];

            // Tính leadtime mặc định
                if($Ability_Unit_Check == "pcs/day") {
                    $LeadTime = $Process_Ability_Check != "0" ? (int)$Qty/$Process_Ability_Check : 0; 
                } 
                else if($Ability_Unit_Check == "sheet/day") {
                    $LeadTime = $Process_Ability_Check != "0" ? $PrintSheet/$Process_Ability_Check : 0;
                }  
                else {
                    $LeadTime = 1;
                } 

            // làm tròn chẵn
                $LeadTime = ceil($LeadTime);

            // tính LeadTime các công đoạn đặc biệt
                if($Process_Check == "Special varnishing" ) {
                    $LeadTime = 2;
                } else if($Process_Check == "Lamination 2 ply" ) {
                    $LeadTime = 2;
                } else if($Process_Check == "Lamination 3 ply" ) {
                    $LeadTime = 2;
                } else if($Process_Check == "Lamination 4 ply" ) {
                    $LeadTime = 2;
                } else if($Process_Check == "Lamination 5 ply" ) {
                    $LeadTime = 2;
                } else if($Process_Check == "Drying time 72h" ) {
                    $LeadTime = 2;
                } else if($Process_Check == "String" ) {
                    $LeadTime = 3;
                } else if($Process_Check == "Varnish Soft Touch CD" ) {
                    $LeadTime = 4;
                } else if($Process_Check == "Paper Cutting" ) {
                    $LeadTime = 0;
                } else if($Process_Check == "Varnishing" ) {
                    $LeadTime = 0;
                } else if($Process_Check == "Plate making" ) {
                    $LeadTime = 0;
                } else if($Process_Check == "Make layout" ) {
                    $LeadTime = 0;
                } else if($Process_Check == "Pack & Sort" ) { // Trong vòng lặp k áp dụng
                    $LeadTime = 0;
                }
            
            // Tính DueDay này
                $DueDay = Date("Y-m-d H:i:s",strtotime($DueDay . "-" . $LeadTime . " days"));

            // Các ngày Lễ khác của năm 2022 (2023 chưa thêm vào)
                if (!empty($allHolidays) ) {
                    foreach ($allHolidays as $holiday ) {
                        $holiday_date = $holiday['holiday_date'];
                        if(date("Y-m-d", strtotime($DueDay)) == $holiday_date) {
                            $DueDay = date("Y-m-d H:i:s", strtotime($DueDay . "-1 days"));
                            break;
                        } 
                    }
                }

            // Neu CN: - 1 ngay
                if(date('w', strtotime($DueDay)) == 0) $DueDay = Date("Y-m-d H:i:s",strtotime($DueDay . "-1 days"));

            // Lưu công đoạn này vào mảng đầu tiên
                $count = count($ArrMain);
                $ArrMain[$count-1]["PFD"] = $DueDay;


            // // test
            //     echo "Process: " . $ArrMain[$count-1]["Process"] . " --- ";    
            //     echo "Date: " . $ArrMain[$count-1]["PFD"] . " <br/>\n ";
                
        /* -------------------------------------------------------------------------------------------------- */


        /* --------------------------------------------------------------------------------------------------
            * TÍNH CÁC CÔNG ĐOẠN CÒN LẠI. 
            Xử lý theo đúng các mail đã yêu cầu trước đó
        -------------------------------------------------------------------------------------------------- */

            for($i = $count - 2; $i > -1; $i--) {

                // Lấy dữ liệu công đoạn cuối cùng
                    $Ability_Unit_Check = $ArrMain[$i]["Ability_Unit"];
                    $Process_Ability_Check = $ArrMain[$i]["Process_Ability"];
                    $Process_Check = $ArrMain[$i]["Process"];

                // Tính leadtime mặc định
                    if($Ability_Unit_Check == "pcs/day") {
                        $LeadTime = $Process_Ability_Check != "0" ? (int)$Qty/$Process_Ability_Check : 0; 
                    } 
                    else if($Ability_Unit_Check == "sheet/day") {
                        $LeadTime = $Process_Ability_Check != "0" ? $PrintSheet/$Process_Ability_Check : 0;
                    }  
                    else {
                        $LeadTime = 1;
                    } 

                // làm tròn chẵn
                    $LeadTime = ceil($LeadTime);

                // tính LeadTime các công đoạn đặc biệt
                    if($Process_Check == "Special varnishing" ) {
                        $LeadTime = 2;
                    } else if($Process_Check == "Lamination 2 ply" ) {
                        $LeadTime = 2;
                    } else if($Process_Check == "Lamination 3 ply" ) {
                        $LeadTime = 2;
                    } else if($Process_Check == "Lamination 4 ply" ) {
                        $LeadTime = 2;
                    } else if($Process_Check == "Lamination 5 ply" ) {
                        $LeadTime = 2;
                    } else if($Process_Check == "Drying time 72h" ) {
                        $LeadTime = 2;
                    } else if($Process_Check == "String" ) {
                        $LeadTime = 3;
                    } else if($Process_Check == "Varnish Soft Touch CD" ) {
                        $LeadTime = 4;
                    } else if($Process_Check == "Paper Cutting" ) {
                        $LeadTime = 0;
                    } else if($Process_Check == "Varnishing" ) {
                        $LeadTime = 0;
                    } else if($Process_Check == "Plate making" ) {
                        $LeadTime = 0;
                    } else if($Process_Check == "Make layout" ) {
                        $LeadTime = 0;
                    }


                // Set DueDay Temp để sử dụng tính toán ngày chủ nhật nằm trong khoảng ngày
                    $DueDayTmp = Date("Y-m-d H:i:s",strtotime($DueDay . "-" . $LeadTime . " days"));

                // Kiểm tra trong khoảng thời gian được trừ có ngày chủ nhật thì trừ từng ngày chủ nhật một
                    $hieu_so = abs(strtotime($DueDay) - strtotime($DueDayTmp));  
                    $countY = floor($hieu_so / (365*60*60*24));  
                    $countM = floor(($hieu_so - $countY * 365*60*60*24) / (30*60*60*24));  
                    $countD = floor(($hieu_so - $countY * 365*60*60*24 - $countM*30*60*60*24)/ (60*60*24));  
                        
                    $DateCheck = $DueDay;
                    for ($j=1;$j<=$countD; $j++ ) {
                        
                        // chủ nhật 
                        if(date("l", strtotime($DateCheck)) == "Sunday") {
                            $DueDayTmp = date("Y-m-d H:i:s", strtotime($DueDayTmp . "-1 days"));
                            $LeadTime = $LeadTime + 1;
                        }

                        // Tết dương lịch - 1 ngày
                            foreach ($NewYearArr as $NewYear ) {
                                if(date("Ymd", strtotime($DueDayTmp)) == $NewYear) {
                                    $DueDayTmp = date("Y-m-d H:i:s", strtotime($DueDayTmp . "-1 days"));
                                    $LeadTime = $LeadTime + 1;
                                    break;
                                } 
                            }

                        // check 
                            $DateCheck = date("Y-m-d", strtotime($DateCheck . "-1 days") );
                            
                    }


                // set DueDay của công đoạn hiện tại đang tính
                    $DueDay = $DueDayTmp;

                // Tết âm lịch 2022
                    if (!empty($LunaNewYear2022) ) {
                        
                        // Lấy dữ liệu Tết âm lịch bỏ vô mảng & xác định ngày đầu tiên nghỉ Lễ
                        $LunaNewYear2022Ok = array();
                        $BeginLunar = $DueDay;
                        foreach ($LunaNewYear2022 as $keyY => $year ) {
                            if ($keyY == 0 ) $BeginLunar = $year['holiday_date'];
                            $LunaNewYear2022Ok[] = $year['holiday_date'];
                        } 

                        $checkLuna = false;
                        foreach ($LunaNewYear2022Ok as $LunaNewYear ) {
                            if(date("Y-m-d", strtotime($DueDay)) == $LunaNewYear) {
                                $checkLuna = true;
                                break;
                            } 
                        }

                        // Nếu có trong ngày tết, trừ bỏ các ngày tết đi
                        if ($checkLuna == true ) {
                            $DueDay = date("Y-m-d H:i:s", strtotime($BeginLunar . "-1 days"));
                        }
                    }

                // Các ngày Lễ khác của năm 2022 (2023 chưa thêm vào)
                    if (!empty($allHolidays) ) {
                        foreach ($allHolidays as $holiday ) {
                            $holiday_date = $holiday['holiday_date'];
                            if(date("Y-m-d", strtotime($DueDay)) == $holiday_date) {
                                $DueDay = date("Y-m-d H:i:s", strtotime($DueDay . "-1 days"));
                                break;
                            } 
                        }
                    }


                // Neu CN: - 1 ngay
                    if(date('w', strtotime($DueDay)) == 0) $DueDay = Date("Y-m-d H:i:s",strtotime($DueDay . "-1 days"));


                // Tính các công đoạn - QUAN TRỌNG
                    $ArrMain[$i]["PFD"] = $DueDay; 

                // // test
                //     echo "Process: " . $ArrMain[$i]["Process"] . " ---  ";    
                //     echo "Date: " . $ArrMain[$i]["PFD"] . " <br/>\n";
                    

            }


        // result
            return $ArrMain;


    }


    





?>