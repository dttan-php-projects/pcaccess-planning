<?php 
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time ', '300');
    set_time_limit(300);
    require("./Module/Database.php");
    header('Content-Disposition: attachment; filename="' . $_GET["EVENT"] . '_' . Date("YmdHis") . '.csv"');
    header('Content-Type: application/csv');
    header('Pragma: no-cache');
    $out = fopen("php://output", 'w');


    if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "SOLINE") {
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        $SQLString = "SELECT B.ID AS JOBJACKET, B.GLID, B.SO, B.Request_Date, B.Submit_Date, A.SO_Line, A.SO_Line AS SOLine, A.SO_Line_Qty, A.Order_Number, B.Order_Style, B.Urgent_Status, B.Print_Machine FROM au_avery_pc.access_id_lines A
                        RIGHT JOIN au_avery_pc.access_order_list B ON A.JOBJACKET = B.ID WHERE B.Submit_Date BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND B.Order_Check = 'true' AND B.ActiveOrder = 1;";
        $DataRaw = MiQuery( $SQLString, _conn());
        for($i = 0; $i < count($DataRaw) - 1; $i++) {
            $DataRaw[$i]["SOLine"] = explode("/",$DataRaw[$i]["SOLine"])[0];
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "ORDERPROCESS") {
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        $SQLString = "SELECT A.ID AS JobJacket, GLID, Bo, Order_Quantity, SO, DueDay, Request_Date, Order_Receive_Day, Submit_Date,Print_Sheet, Print_Scrap, Finish_Scrap, Order_Style, Urgent_Status, Order_Check, 
                Stock_Code_F, UPS, Stock_Size, Cut_Number, Order_Handler, FGS_Check, Color_Sum_FB, Color_By_Size, Imprint_Lot, New_Product_If, PPC_Remark, Print_Machine, Complete_Status, Complete_Date, Complete_Quality
                FROM au_avery_pc.access_order_list A LEFT JOIN au_avery_pc.access_progress_track B ON A.ID = B.ID 
                WHERE A.Submit_Date BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND A.Order_Check = 'true' AND A.ActiveOrder = 1;";
        $DataRaw = MiQuery( $SQLString, _conn());
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "ORDERSHIPMENT") {
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        $SQLString = "SELECT ID AS JobJacket, GLID, SO, Bo, Order_Quantity, Submit_Date, DueDay, Request_Date, Print_Sheet, Print_Scrap, Finish_Scrap, Order_Style, Urgent_Status, Stock_Code_F, Stock_Size, 
                    UPS, Cut_Number, Order_Handler, Imprint_Lot, New_Product_If, Complete_Date, Complete_Quality FROM au_avery_pc.access_order_list 
                    WHERE Complete_Date BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND Order_Check = 'True' AND Complete_Status='True' AND ActiveOrder = 1;";
        $DataRaw = MiQuery( $SQLString, _conn());
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "ORDERCREATED") {
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        $SQLString = "SELECT ID AS JobJacket, GLID, SO, Bo, Order_Quantity, Submit_Date, DueDay, Request_Date, Print_Sheet, Print_Scrap, Finish_Scrap, Order_Style, Urgent_Status, Stock_Code_F, Stock_Size,
                        UPS, Cut_Number, Order_Handler, Imprint_Lot, New_Product_If, Complete_Status, Complete_Date, Complete_Quality, Print_Machine FROM au_avery_pc.access_order_list 
                        WHERE Submit_Date BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND Order_Check = 'true' AND ActiveOrder = 1;";
        $DataRaw = MiQuery( $SQLString, _conn());
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "PROCESSOUTPUT") {
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        $Routing = $_GET["R"];
        $SQLString = "SELECT B.ID AS JobJacket, B.GLID, B.Bo, B.SO, B.Order_Quantity, B.DueDay, B.Request_Date, B.Order_Receive_Day, B.Submit_Date, B.Print_Sheet, B.Print_Scrap, B.Finish_Scrap, B.Order_Style, 
                        B.Urgent_Status, B.Order_Check, B.Stock_Code_F, B.UPS, B.Stock_Size, B.Cut_Number, B.Order_Handler, B.FGS_Check, B.Color_Sum_FB, B.Color_By_Size, B.Imprint_Lot, B.New_Product_If,
                        B.PPC_Remark, B.Print_Machine, A.Process_Sequence, A.Process, A.Process_Finish_Person, A.Process_Finish_Status, A.Process_Finish_Quantity, A.Process_Finish_Remark, A.Process_Finish_Date
                        FROM au_avery_pc.access_progress_track A LEFT JOIN au_avery_pc.access_order_list B ON A.ID = B.ID
                    WHERE A.Planing_Finish_Date BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND A.Process = '$Routing' AND B.ActiveOrder = 1;";
        $DataRaw = MiQuery( $SQLString, _conn());
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "ORDERRECEIVE") {
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        $SQLString = "SELECT B.ORDER_NUMBER, B.LINE_NUMBER, A.SOLINE, B.CUST_PO_NUMBER, B.ORDERED_ITEM, B.ITEM, '' AS UOM, B.REQUEST_DATE, 
                                B.PROMISE_DATE, B.SHIP_TO_CUSTOMER, B.BILL_TO_CUSTOMER, A.CREATEDDATE, (SELECT ActiveOrder FROM au_avery_pc.access_id_lines C LEFT JOIN au_avery_pc.access_order_list D ON C.JOBJACKET = D.ID WHERE C.SO_LINE = A.SOLINE LIMIT 1) AS ISSUE 
                                FROM au_avery_pc.access_ppc_receive A LEFT JOIN au_avery.vnso B ON A.ORDER_NUMBER = B.ORDER_NUMBER AND A.LINE_NUMBER = B.LINE_NUMBER
                        WHERE A.CREATEDDATE BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59';";
        $DataRaw = MiQuery( $SQLString, _conn());
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "ORDERRECEIVENON") {
        $DateFrom = $_GET["F"];
        $DateTo = $_GET["T"];
        $SQLString = "SELECT * FROM (SELECT B.ORDER_NUMBER, B.LINE_NUMBER, A.SOLINE, B.CUST_PO_NUMBER, B.ORDERED_ITEM, B.ITEM, '' AS UOM, B.REQUEST_DATE, 
                                B.PROMISE_DATE, B.SHIP_TO_CUSTOMER, B.BILL_TO_CUSTOMER, A.CREATEDDATE, (SELECT ActiveOrder FROM au_avery_pc.access_id_lines C LEFT JOIN au_avery_pc.access_order_list D ON C.JOBJACKET = D.ID WHERE C.SO_LINE = A.SOLINE LIMIT 1) AS ISSUE 
                                FROM au_avery_pc.access_ppc_receive A LEFT JOIN au_avery.vnso B ON A.ORDER_NUMBER = B.ORDER_NUMBER AND A.LINE_NUMBER = B.LINE_NUMBER
                        WHERE A.CREATEDDATE BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59') AS X WHERE ISSUE <> 1;";
        $DataRaw = MiQuery( $SQLString, _conn());
    }

    if(count($DataRaw) != 0) {
        $ArrayMain = array();
        $Index = 0;
        foreach($DataRaw[0] as $C=>$R) {
            $ArrayMain[$Index]= $C;
            $Index++;
        }
        fputcsv($out, $ArrayMain,",");
        foreach($DataRaw as $R) {
            $ArrayMain = array();
            $Index = 0;
            foreach($R as $C=>$K) {
                $ArrayMain[$Index] = $K;
                $Index++;
            }
            fputcsv($out, $ArrayMain,",");
        }
    }
    fclose($out);
?>