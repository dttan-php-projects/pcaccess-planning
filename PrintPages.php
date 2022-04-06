<?php 
	ini_set('max_execution_time', 3000); //300 seconds = 5 minutes
	require("./Module/Database.php");
	require_once("./Module/QRcode/qrcode.class.php");
	include('./Module/code128.class.php');
	if(!isset($_GET["JJ"])) {
		return;
	}

	function urgentRemarkShow($PPC_Remark) 
	{
		$remarkCheck = array(
			"Quấn giấy, buộc thun"
		);

		$remark = '';
		if (!empty($PPC_Remark) ) {
			foreach ($remarkCheck as $value ) {
				if (stripos($PPC_Remark, $value ) !== false ) {
					$remark = $value;
					$PPC_Remark = str_replace($value, ' ', $PPC_Remark );
					break;
				}	
			}
		}

		return $remark;

	}

	
	function imagesUniqloShow($rbo, $Order_Style) 
	{
		// init 
			$isUQ = false;
			$isFGSOut = false;
		
		// check empty
			if (empty($rbo) || empty($Order_Style) ) return array('isUQ' => false, 'isFGSOut' => false );

		// check RBO
		if (stripos($rbo, 'UNIQLO') !== false ) {
			
			$isUQ = true; // UNIQLO true

			if ($Order_Style == 'FGS-OUT' ) {
				$isFGSOut = true; // FGS Out true
			}
		}

		return array('isUQ' => $isUQ, 'isFGSOut' => $isFGSOut );
	}

	// mail: Add thông tin lên JJ
	function quanGiayRemark($RBO, $ShipTo, $BillTo )
	{
		$remark = '';
		$RBOArr = array( 'PUMA', 'RYOHIN', 'UNIQLO', 'EAGLE' );
		$ShipToArr = array( 'ELITE', 'TMI' );
		$BillToArr = array( 'ECLAT TEXTILE' );
		foreach ($RBOArr as $value ) {
			if (strpos(strtoupper($RBO), $value) !== false ) {
				$remark = '<span style="letter-spacing: 1px;font-size:22px;">Quấn giấy</span>';
				break;
			}
		}

		foreach ($ShipToArr as $value ) {
			if (strpos(strtoupper($ShipTo), $value) !== false ) {
				$remark = '<span style="letter-spacing: 1px;font-size:22px;">Quấn giấy</span>';
				break;
			}
		}

		foreach ($BillToArr as $value ) {
			if (strpos(strtoupper($BillTo), $value) !== false ) {
				$remark = '<span style="letter-spacing: 1px;font-size:22px;">Quấn giấy</span>';
				break;
			}
		}

		return $remark;

	}

	function fruICRemark($SO_Line) 
	{	
		$remark = '';

		if (!empty($SO_Line) ) {
			$SO_Line_Arr = explode('-', $SO_Line);
			$ORDER_NUMBER = $SO_Line_Arr[0];
			$LINE_NUMBER = $SO_Line_Arr[1];
			$sql = "SELECT ORDER_TYPE_NAME FROM au_avery.vnso_total WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER'; ";
			$result = MiQuery($sql, _conn('au_avery'));

			$order_type_name = (!empty($result) ) ? $result[0]['ORDER_TYPE_NAME'] : '';
			if (!empty($order_type_name) ) {
				if (stripos($order_type_name, 'BNH') !==false ) {
					$remark = 'FRU IC';
				}
			}
		}
		

		return $remark;
	}

	// Nếu đơn là đơn được in bù có Ship to WORLDO thì xử lý đơn Bù cũng có viền đen dọc (phải)
	function checkTurnWorldonBu($JobJacketInBu )
	{
		// init
			$ArrSOCheckInBu = array();
			$TurnWorldon = false;

		// table
			$table_lines = "au_avery_pc.access_id_lines";
			$table_receiving = "au_avery_pc.access_order_receiving";

		// Lấy thông tin data từ ID cần in bù này
			$Sql = "SELECT A.JOBJACKET, A.SO_Line, A.SO_Line_Qty, A.ORDER_NUMBER, A.LINE_NUMBER, A.REMARK, A.CreatedDate, A.ID, (B.PPC_RECEIVING_TIME) AS D FROM au_avery_pc.access_id_lines A LEFT JOIN au_avery_pc.access_order_receiving B ON A.ORDER_NUMBER = B.ORDER_NUMBER AND A.LINE_NUMBER = B.LINE_NUMBER WHERE A.JOBJACKET = '$JobJacketInBu' AND A.ACTIVE='1' GROUP BY A.JOBJACKET, A.SO_Line, A.SO_Line_Qty, A.ORDER_NUMBER, A.LINE_NUMBER, A.REMARK, A.CreatedDate, A.ID ORDER BY ORDER_NUMBER, CONVERT(A.LINE_NUMBER, UNSIGNED);";
			$RowTableBu = MiQuery($Sql, _conn());

		// Lấy list ORDER_NUMBER
			foreach($RowTableBu as $R) $ArrSOCheckInBu []= $R["ORDER_NUMBER"];

		// Loại bỏ phần tử trùng
			$ArrSOCheckInBu = array_unique($ArrSOCheckInBu);
		// Lấy Ship to
			$RShipInBu = MiQuery("SELECT SHIP_TO_CUSTOMER FROM $table_receiving WHERE ORDER_NUMBER IN ('" . implode("','", $ArrSOCheckInBu) . "') GROUP BY SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER;", _conn());
		
		// Kiểm tra xem có WORLDON không
		foreach($RShipInBu as $R) {
			// echo "SHIP_TO_CUSTOMER: " . $R["SHIP_TO_CUSTOMER"];
			if(strpos($R["SHIP_TO_CUSTOMER"],"WORLDON") !== false) {
				$TurnWorldon = true;
				break;
			}
		}

		return $TurnWorldon;

	}


	$table_order_list = "access_order_list";
	$table_lot_remark = "access_lot_remark";
	$table_lines = "access_id_lines";
	$table_receiving = "access_order_receiving";
	$table_information = "access_item_information";
	$table_customer_request = "access_customer_request";
	$table_rbo_request = "access_rbo_request";
	$table_item_accessory = "access_item_accessory";

	$table_progress_track = "access_progress_track";
	$table_process = "access_item_process";
	$table_mla = "access_mla";
	$table_item_remark = "access_item_remark";

	$table_oe_special_remark = "au_avery_oe.oe_special_remark";

	$WithOutChecklist = true;
	if(isset($_GET["CL"])) $WithOutChecklist = false;
	
	$JobJacket = $_GET["JJ"];

	$qrcode = new QRcode("$JobJacket", 'M');
	$qrcode->displayPNG(200, array(255,255,255), array(0,0,0), "Images/QR_$JobJacket.png", 0);

	$DataJob = MiQuery("SELECT 	Num,
								ID,
								GLID,
								Bo,
								Order_Quantity,
								DueDay,
								Order_Receive_Day,
								Submit_Date,
								Print_Sheet,
								Print_Scrap,
								Finish_Scrap,
								Order_Style,
								Urgent_Status,
								Order_Check,
								Stock_Code_F,
								UPS,
								Stock_Size,
								Cut_Number,
								Order_Handler,
								FGS_Check,
								Color_Sum_FB,
								Color_By_Size,
								Imprint_Lot,
								New_Product_If,
								PPC_Remark,
								Print_Machine,
								Complete_Status,
								Complete_Date,
								Complete_Quality,
								Request_Date,
								Promise_Date,
								SO,
								SO_Lines,
								IDS
							FROM $table_order_list WHERE ID = '$JobJacket';", _conn());
	$ID = "";
	$GLID = "";
	$Bo = "";
	$Quantity = "";
	$Dueday = "";
	$OrderReceive = "";
	$Submit_date = "";
	$Print_Sheet = "";
	$Print_Scrap = "";
	$Finish_Scrap = "";
	$Order_Style = "";
	$Urgent_Status = "";
	$Order_Check = "";
	$Stock_Code_F = "";
	$UPS = "";
	$Stock_Size = "";
	$Cut_Number = "";
	$Order_Handler = "";
	$FGS_Check = "";
	$Color_Sum_FB = "";
	$Color_By_Size = "";
	$Imprint_Lot = "";
	$New_Product = "";
	$PPC_Remark = "";
	$SO = "";
	$Print_Machine = "";
	$SOLines = "";
	if(!empty($DataJob)) {

		$D = $DataJob[0];
		$ID = $D["ID"];
		$GLID = $D["GLID"];
		$Bo = htmlspecialchars_decode($D["Bo"], ENT_QUOTES);
		$Quantity = trim($D["Order_Quantity"]);
		$Dueday = $D["DueDay"];
		$OrderReceive = $D["Order_Receive_Day"];
		$Submit_date = $D["Submit_Date"];
		$Print_Sheet = $D["Print_Sheet"];
		$Print_Scrap = $D["Print_Scrap"];
		$Finish_Scrap = $D["Finish_Scrap"];
		$Order_Style = $D["Order_Style"];
		$Urgent_Status = $D["Urgent_Status"];
		$Order_Check = $D["Order_Check"];
		$Stock_Code_F = $D["Stock_Code_F"];
		$UPS = $D["UPS"];
		$Stock_Size = $D["Stock_Size"];
		$Cut_Number = $D["Cut_Number"];
		$Order_Handler = $D["Order_Handler"];
		$FGS_Check = $D["FGS_Check"];
		$Color_Sum_FB = $D["Color_Sum_FB"];
		$Color_By_Size = $D["Color_By_Size"];
		$Imprint_Lot = $D["Imprint_Lot"];
		$New_Product = $D["New_Product_If"];
		$PPC_Remark = $D["PPC_Remark"];
		$SO = $D["SO"];
		$Print_Machine = $D["Print_Machine"];
		$SOLines = $D["SO_Lines"];

	}



	$Finish_Scrap = $Finish_Scrap == "" ? 0 : $Finish_Scrap;
	$Print_Sheet = $Print_Sheet == "" ? 0 : $Print_Sheet;
	$Print_Scrap = $Print_Scrap == "" ? 0 : $Print_Scrap;


	$SO = substr($SO,0,8);

	// // @tandoan - 20200910: Lấy danh sách SO để remark Packing Instr. Thêm Khong kim loại vào Remark
		$remarkKKL = '';
		if (stripos($PPC_Remark, 'KHONG KIM LOAI' ) !==false ) {
			// $remarkKKL = '<div style="width:150px;border:1px solid blue; background-color:black; color:white; text-align:center; font-weight:bold;font-size:15px;">KHONG KIM LOAI</div>';
			$remarkKKL = 'KHONG KIM LOAI';
			$PPC_Remark = str_replace('KHONG KIM LOAI', ' ', $PPC_Remark );
		}

	// $PPC_Remark .= $remarkKKL;
	

	$PPC_Remark .= "\n<table style='width:99%;position:relative;height:90%;'>";
	$PPC_RemarkV2 = "<table style='font-size:10pt'>";

	$ArrTableLot = array();
	for($i = 0; $i < 40; $i++) {
		$ArrTableLot[]= "<tr >";
	}
	$RowTable = MiQuery( "SELECT LotName, Qty FROM $table_lot_remark WHERE JobJacket = '$JobJacket';", _conn());
	if(count($RowTable) != 0) {
		if(count($RowTable) < 49) {
			for($K = 0; $K < 8; $K++) {
				for($i = $K; $i < count($RowTable); $i+=8) $ArrTableLot[$K] .= "<td style='min-heigth:30px;min-width:100px;text-align:left !important;'>" . $RowTable[$i]["LotName"] . " " . $RowTable[$i]["Qty"] . "</td>";
				$PPC_Remark .= $ArrTableLot[$K] . "</tr>";
			}
		} else {
			foreach($ArrTableLot as $K=>$R) {
				for($i = $K; $i < count($RowTable); $i+=40) $ArrTableLot[$K] .= "<td style='min-width:100px;text-align:left !important;'>" . $RowTable[$i]["LotName"] . " " . $RowTable[$i]["Qty"] . "</td>";
				$PPC_RemarkV2 .= $ArrTableLot[$K] . "</tr>";
			}
		}
	} 

	$PPC_Remark .= "</table>";
	$PPC_RemarkV2 .= "</table>";

	$TableSOLine = "<table  style='margin-top: 30px; padding: auto; font-weight:bold' border=1  cellpadding='0' cellspacing='0'>
	<tr>
		<td colspan='4' style='font-size: 20pt; font-weight: bold;'>ID: $JobJacket</td>
	</tr>
	<tr>
		<td colspan='4' style='font-size: 20pt; font-weight: bold;'>GLID: $GLID</td>
	</tr>
	<tr>
		<td colspan='4'>Print Date: $Submit_date</td>
	</tr>
	<tr></tr>
	<tr style='height: 40px;font-size: 12pt;background: yellow'>
		<td>No</td>
		<td>ID</td>
		<td>SOLine</td>
		<td>Line Qty</td>
	</tr>";

	$SOL = "";
	$RowTable = MiQuery("SELECT A.JOBJACKET,
					A.SO_Line,
					A.SO_Line_Qty,
					A.ORDER_NUMBER,
					A.LINE_NUMBER,
					A.REMARK,
					A.CreatedDate,
					A.ID,
					(B.PPC_RECEIVING_TIME) AS D
				FROM $table_lines A LEFT JOIN $table_receiving B ON A.ORDER_NUMBER = B.ORDER_NUMBER AND A.LINE_NUMBER = B.LINE_NUMBER 
				WHERE A.JOBJACKET = '$JobJacket' AND A.ACTIVE = 1 GROUP BY A.JOBJACKET,
					A.SO_Line,
					A.SO_Line_Qty,
					A.ORDER_NUMBER,
					A.LINE_NUMBER,
					A.REMARK,
					A.CreatedDate,
					A.ID ORDER BY ORDER_NUMBER, CONVERT(A.LINE_NUMBER, UNSIGNED);", _conn());
	$i = 1;
	$ArrSOL = array();
	while($i < 19){
		$ArrSOL[] = "";
		$i++;
	}

	$MaxTimeOrder = strtotime("1970-01-01");
	$MinTimeOrder = strtotime("2050-01-01");
	$Turn = false;

	$ArrSOCheck = array();

	// @tandoan: get SO_Line for BuildStock
		$SO_Line_Check = isset($RowTable[0]['SO_Line']) ? $RowTable[0]['SO_Line'] : '';
	
	// Check nếu SOLine trống thì không hiển thị Page_2.php
		$page2 = (!empty($SO_Line_Check) ) ? $page2 = true : $page2 = false;

	// @TanDoan - 20210316: remark "FRU". Đây là remark dành cho đơn hàng  (FRU IC LONG HAU)
		$fruICRemark = fruICRemark($SO_Line_Check);

	foreach($RowTable as $R) {
		$ArrSOCheck []= $R["ORDER_NUMBER"];
		if($MaxTimeOrder < strtotime($R["D"])) $MaxTimeOrder = strtotime($R["D"]);
		if($MinTimeOrder > strtotime($R["D"])) $MinTimeOrder = strtotime($R["D"]);

		$Turn = true;
	}

	$ArrSOCheck = array_unique($ArrSOCheck);

	$ShowReceivingDate = "";
	
	if($Turn) {
		$MaxTimeOrder = date("Y-m-d H:i", $MaxTimeOrder);
		$MinTimeOrder = date("Y-m-d H:i", $MinTimeOrder);
		if($MaxTimeOrder == $MinTimeOrder) $ShowReceivingDate = $MinTimeOrder;
		else $ShowReceivingDate = $MinTimeOrder . " <br/> " . $MaxTimeOrder;
	}
	$ArrTableSoLine = array();
	$ArrTableSoLine2 = array();
	for($i = 0; $i < 30; $i++) {
		$ArrTableSoLine[]= "<tr style='height: 22px;font-size: 9pt'>";
		$ArrTableSoLine2[]= "<tr style='height: 22px;font-size: 9pt'>";
	}


	$TableSOLine2 = "<table  style='margin-top: 30px; padding: auto; font-weight:bold' border=1  cellpadding='0' cellspacing='0'>
	<tr style='height: 40px;font-size: 10pt;background: yellow'>
		<td>No 1</td>
		<td>SOLine</td>
		<td>Line Qty</td>
		<td></td>
		<td>No 2</td>
		<td>SOLine</td>
		<td>Line Qty</td>
		<td></td>
		<td>No 3</td>
		<td>SOLine</td>
		<td>Line Qty</td>
		<td></td>
		<td>No 4</td>
		<td>SOLine</td>
		<td>Line Qty</td>
		<td></td>
		<td>No 5</td>
		<td>SOLine</td>
		<td>Line Qty</td>
		<td></td>
		<td>No 6</td>
		<td>SOLine</td>
		<td>Line Qty</td>
	</tr>";

	$TableSOLine3 = $TableSOLine2;

	if(count($RowTable) < 180) {
		foreach($ArrTableSoLine as $K=>$R) {
			for($i = $K; $i < count($RowTable); $i+=30) $ArrTableSoLine[$K] .= "<td>" . ($i + 1) . "</td><td style='padding:2px'>" . $RowTable[$i]["SO_Line"] . "</td><td> " . $RowTable[$i]["SO_Line_Qty"] . "</td><td style='width:10px;border: none;'></td>";
			$TableSOLine2 .= $ArrTableSoLine[$K] . "</tr>";
		}
	} else {
		foreach($ArrTableSoLine as $K=>$R) {
			for($i = $K; $i < 180; $i+=30) $ArrTableSoLine[$K] .= "<td>" . ($i + 1) . "</td><td style='padding:2px'>" . $RowTable[$i]["SO_Line"] . "</td><td> " . $RowTable[$i]["SO_Line_Qty"] . "</td><td style='width:10px;border: none;'></td>";
			$TableSOLine2 .= $ArrTableSoLine[$K] . "</tr>";
		}

		foreach($ArrTableSoLine2 as $K=>$R) {
			for($i = $K + 180; $i < count($RowTable); $i+=30) $ArrTableSoLine2[$K] .= "<td>" . ($i + 1) . "</td><td style='padding:2px'>" . $RowTable[$i]["SO_Line"] . "</td><td> " . $RowTable[$i]["SO_Line_Qty"] . "</td><td style='width:10px;border: none;'></td>";
			$TableSOLine3 .= $ArrTableSoLine2[$K] . "</tr>";
		}
	
	}

	
	

	$TurnSO = true;

	if(!empty($RowTable) && count($RowTable) < 19){
		$TurnSO = false;
		foreach($RowTable as $K=>$result) {
			$TableSOLine = $TableSOLine . "<tr style='height: 30px;font-size: 10pt'>
			<td style='text-align: left;padding-left: 5px;width: 10%'>" . ($K + 1) . "</td>
			<td style='text-align: left;padding-left: 5px;width: 15%'>" . ($result["JOBJACKET"] == null ? "" : $result["JOBJACKET"]) . "</td>
			<td style='text-align: left;padding-left: 5px;width: 45%'>" . ($result["SO_Line"] == null ? "" : $result["SO_Line"]) . "</td>
			<td style='text-align: left;padding-left: 5px;width: 30%''>" . ($result["SO_Line_Qty"] == null ? "" : $result["SO_Line_Qty"]) . "</td></tr>";
			$ArrSOL[$K] = ($K+1) . ". " . $result["SO_Line"] . "|Qty:" . $result["SO_Line_Qty"];
		}
	}
	$TableSOLine = $TableSOLine . "</table>";

	function ConvertS($S) {
		$A = $S;
		for($i=0;30 - strlen($S) > $i; $i++) $A .= "_";
		return $A;
	}
	
	$SOL = "<table style='text-align:left;height: 100%;'>";
	for($j = 0; $j < 6; $j++){
		$A1 = $ArrSOL[$j] != "" ? ConvertS($ArrSOL[$j]) : "";
		$A2 = $ArrSOL[$j + 6] != "" ? ConvertS($ArrSOL[$j + 6]) : "";
		$A3 = $ArrSOL[$j + 12] != "" ? ConvertS($ArrSOL[$j + 12]) : "";
		$SOL .= "<tr><td style='text-align: left;'>" . $A1 . "</td><td>" . $A2 . "</td><td>" . $A3 . "</td></tr>";
	}
	if($TurnSO && count($RowTable) > 10) $SOL .= "<tr><td style='font-size:15pt'>Vui lòng xem danh sách SO Line ở tờ sau</td><td cospan=3>$SOLines</td></tr></table>"; 
	else $SOL .= "<tr><td cospan=3>$SOLines</td></tr></table>";
	

	$ItemCode = "";
	$ProductWidth = "";
	$ProductLength = "";
	$Color_F = "";
	$Color_B = "";
	$Imprint_F = "";
	$Imprint_B = "";
	$Variable_F = "";
	$Variable_B = "";
	$Varnish_F = "";
	$Varnish_B = "";
	$UV_F = "";
	$UV_B = "";
	$DieCut = "";
	$Color_Management = "";
	$OS_Sample = "";
	$CS_Sample = "";
	$DS_Sample = "";
	$Hot_Folder = "";
	$SubContract = "";
	$Special_Ins = "";
	$FSC = "";
	$RemarkPage = "";
	$DieCut_Machine = "";
	$RowItem = MiQuery("SELECT GLID, Item_Code, ProductionWidth, ProductionLength, Color_F, Color_B, Imprint_F, Imprint_B,
	Variable_F, Variable_B, Varnish_F, Varnish_B, UV_F, UV_B, DieCut_No, Digital_DieCut_No,Color_Management, OS_Sample, 
	CS_Sample, DS_Sample, Hot_Folder, SubContract_detail,Special_Instruction, FSC,StringCut_ComboTag, DieCut_Machine, Special_Drying_Time FROM $table_information WHERE GLID = '$GLID';", _conn());
	if(!empty($RowItem)) {
		$R = $RowItem[0];
		$ItemCode = $R["Item_Code"];
		$ProductWidth = $R["ProductionWidth"];
		$ProductLength = $R["ProductionLength"];
		$Color_F = $R["Color_F"];
		$Color_B = $R["Color_B"];
		$Imprint_F = $R["Imprint_F"];
		$Imprint_B = $R["Imprint_B"];
		$Variable_F = $R["Variable_F"];
		$Variable_B = $R["Variable_B"];
		$Varnish_F = $R["Varnish_F"];
		$Varnish_B = $R["Varnish_B"];
		$UV_F = $R["UV_F"];
		$UV_B = $R["UV_B"];
		$RemarkPage = $R["StringCut_ComboTag"];
		$DieCut_Machine = $R["DieCut_Machine"];

		$Special_Drying_Time = $R['Special_Drying_Time'];
		
		// if($PrintMachine == "INDIGO" || PrintMachine == "C800P" || PrintMachine == "G-U" || PrintMachine == "C1000i" || PrintMachine == "NON G-U" || PrintMachine == "KM1" || PrintMachine == "1" || PrintMachine == "4" || PrintMachine == "5" || PrintMachine == "6" || PrintMachine == "2,3,7" || PrintMachine.toUpperCase() == "ALL" ) {
		if($Print_Machine == "G-U" || $Print_Machine == "NON G-U" || $Print_Machine == "INDIGO" || $Print_Machine == "C800P" || $Print_Machine == "C1000i" || $Print_Machine == "WS6800" || $Print_Machine == "KM1" || $Print_Machine == "1" || $Print_Machine == "4" || $Print_Machine == "5" || $Print_Machine == "6" || $Print_Machine == "2,3,7"  || strtoupper($Print_Machine) == "ALL") {
		
			$DieCut = $R["Digital_DieCut_No"] == null ? "" : $R["Digital_DieCut_No"];
		} else {
			$DieCut = $R["DieCut_No"] == null ? "" : $R["DieCut_No"];
		}
		$Color_Management = $R["Color_Management"];
		$OS_Sample = $R["OS_Sample"];
		$CS_Sample = $R["CS_Sample"];
		$DS_Sample = $R["DS_Sample"];
		$Hot_Folder = $R["Hot_Folder"];
		$SubContract = $R["SubContract_detail"];
		$Special_Ins = explode("\n\n\n",$R["Special_Instruction"])[0];
		$FSC = $R["FSC"];
	}

	$Accessory = "<table style='width:98%;height:90%;margin: auto; padding: auto; font-weight:bold' border=1  cellpadding='0' cellspacing='0'>" .
		"<tr style='background:yellow; height: 20px'><td style='width:20%'>Process</td>
		<td style='width:50%'>Accessory</td><td style='width:30%'>Remark</td></tr>";
	$TurnAccessory = 0;

	$RowAcc = MiQuery("SELECT GLID, Process, Accessory, Remark FROM $table_item_accessory WHERE GLID = '$GLID';", _conn());
	if(!empty($RowAcc)){
		foreach($RowAcc as $result){
			$Accessory = $Accessory . "<tr>" . 
			"<td style='width:20%'>" . ($result["Process"] == null ? "" : $result["Process"]) . "</td>" .
			"<td style='width:50%'>" . ($result["Accessory"] == null ? "" : $result["Accessory"]) . "</td>" .
			"<td style='width:30%'>" . ($result["Remark"] == null ? "" : $result["Remark"]) . "</td>" .
			"</tr>";
			$TurnAccessory = 1;
		}
	}
	$Accessory = $TurnAccessory == 0 ? "" : $Accessory . "</table>";



	$RoutingDetail = "<table style='vertical-align: top;width:100%;min-height: 70%; max-height:100%; font-size: 8pt;margin: auto; padding: auto' border=1  cellpadding='0' cellspacing='0'>" .
		"<tr style='background:yellow; height: 20px'>
		<td style='width:5%'>Seq</td>
		<td style='width:15%'>Công Đoạn</td>
		<td style='width:15%'>Planning Finish Date</td>
		<td style='width:15%'>Ngày Hoàn Thành</td>
		<td style='width:20%'>Tên Vận Hành</td>
		<td style='width:25%'>Số Lượng</td>
		</tr>";
	$TurnPro = 0;
	$Routing = MiQuery("SELECT A.ID, A.Process_Sequence, A.Process, A.Planing_Finish_Date, B.VN_vi FROM $table_progress_track A LEFT JOIN $table_process B ON A.Process = B.Process WHERE A.ID = '$JobJacket' ORDER BY  CONVERT(A.Process_Sequence, UNSIGNED INTEGER) ASC;", _conn());

	if(!empty($Routing))
	{
		foreach($Routing as $result){
			if($result["Process"] == "Make layout") continue;
			if($result["Process"] == "Lamination") $result["VN_vi"] .= " " . (substr_count($Stock_Code_F,"/") + 1) . " Lớp";
			if(($Bo == "UNIQLO" || $Bo == "SEIYU/WAL-MART JAPAN" || $GLID == "2-291801-100-QC" || $GLID == "2-374514-100-QC") && $result["Process"] == "Pack & Sort") $result["VN_vi"] .= " Kiểm tra 100%";

			$RoutingDetail = $RoutingDetail . "<tr>" . 
			"<td>" . $result["Process_Sequence"] . "</td><td>" . ($result["VN_vi"] == null ? $result["Process"] : $result["VN_vi"])  . "</td>" .
			"<td>" . ($result["Planing_Finish_Date"] == null ? "" : Date("d/m/Y", strtotime($result["Planing_Finish_Date"]))) . "</td>" .
			"<td></td>" .
			"<td></td>" .
			"<td></td>" .
			"</tr>";
			$TurnPro = 1;
		}
	}

	$RoutingDetail = $TurnPro == 0 ? "" : $RoutingDetail . "</table>";
	
	// @tandoan - 20201218: mảng trả về check isUQ và isFGSOut. mail Tram.Tong: Thêm thông tin trên jobjacket
		$imagesUQ = imagesUniqloShow($Bo, $Order_Style);
		$isUniqlo = $imagesUQ['isUQ'];
		$isFGSOut = $imagesUQ['isFGSOut'];

	if(strpos($Order_Style,"FGS-OUT") !== false) {
			// mặc định các đơn hàng không phải UNIQLO
				$RoutingDetail = "";
			// Kiểm tra nếu đơn hàng UNIQLO và FGS-OUT. Hiển thị thông tin bảng kiểm tra công đoạn
				if ($isUniqlo == true && $isFGSOut == true ) {
					$RoutingDetail = '<div style="text-align: center; width: 99%;height:99%;background: url(\'Images/img/UNIQLO_FGSOut_HandOverCheck.png\') no-repeat;background-size: contain;"></div>';
				}
	} 
	$ShipTo = "";
	$BillTo = "";
	if($Order_Style == "Offset") $Order_Style = "FG";
	$RShip = MiQuery("SELECT SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, LINE_NUMBER FROM $table_receiving WHERE ORDER_NUMBER IN ('" . implode("','", $ArrSOCheck) . "') GROUP BY SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER;", _conn());

	$TurnWorldon = false;
	foreach($RShip as $R) {
		if(strpos($R["SHIP_TO_CUSTOMER"],"WORLDON") !== false) {
			$ShipTo = $R["SHIP_TO_CUSTOMER"];
			$BillTo = $R["BILL_TO_CUSTOMER"];
			$TurnWorldon = true;
		}
	}

	// @TanDoan - 20210604: Đơn bù. mail: Re: Hàng wondon không thấy QC kiểm tờ lớn
		if ($TurnWorldon == false ) {
			$ArrSOCheckInBu = array();
			// Lấy Job ID cần in bù 
				$JobJacketInBu = substr($SOLines, -7);

			// kiểm tra lại dự vào Job ID cần in bù, Nếu Ship to là WORLDON thì đơn Bù này là WORLDON
				$TurnWorldon = checkTurnWorldonBu($JobJacketInBu);
			
		}

	if(!empty($RShip) && $ShipTo == "") {
		$ShipTo = $RShip["0"]["SHIP_TO_CUSTOMER"];
		$BillTo = $RShip["0"]["BILL_TO_CUSTOMER"];

		// @tandoan: Lấy Ship to theo line (cho chính xác đơn hàng BuildStock)
		if (isset($SO_Line_Check) && !empty($SO_Line_Check) ) {
			$SOLineC = explode('-',$SO_Line_Check);
			if (isset($SOLineC[1]) ) {
				$RShipC = $RShip;
				foreach ($RShipC as $RC ) {
					if ((int)$SOLineC[1] == (int)$RC['LINE_NUMBER'] ) {
						$ShipTo = $RC["SHIP_TO_CUSTOMER"];
						$BillTo = $RC["BILL_TO_CUSTOMER"];
						break;
					}
				}
			}
			
			
		}
		
	}

	$MLA = "";
	$MLA = MiQuery( "SELECT RBO FROM $table_mla WHERE RBO = '" . str_replace("'","\'",$Bo) . "' LIMIT 1;", _conn());
	if(empty($MLA)) $MLA = "";

	$ICINDO = "";
	if(strpos($ShipTo,"PAXAR INDONESIA") !== false) {
		$ICINDO = "IC INDO";
	}

	$DataRemark = explode("\n",$PPC_Remark);

	if($RemarkPage != "") $ICINDO .= $RemarkPage;
	
	foreach($DataRemark as $R)
	{
		if(strpos(strtoupper($R),"DI CHUNG") !== false || strpos(strtoupper($R),"GHEP NHAN") !== false || strpos(strtoupper($R),"ĐI CHUNG") !== false || strpos(strtoupper($R),"GHÉP NHÃN") !== false || strpos(strtoupper($R),"GHÉP") !== false || strpos(strtoupper($R),"GHEP") !== false || strpos(strtoupper($R),"CHUNG") !== false )
		{
			if($ICINDO != "") $ICINDO = $ICINDO . "\n" . $R;
			else $ICINDO = $R;
		}
	}

	$Drill = "";
	$RowTableRemark = MiQuery("SELECT B.RBO AS RBO,A.BILL_TO, A.SHIP_TO, A.REMARK_DRILL, A.REMARK_1, A.REMARK_2, B.REMARK_DRILL AS CREMARK_DRILL, B.REMARK_1 AS CREMARK_1, B.REMARK_2 AS CREMARK_2 
								FROM $table_customer_request A JOIN $table_rbo_request B ON A.RBO = B.TYPERBO
								UNION ALL
								SELECT A.RBO,A.BILL_TO, A.SHIP_TO, A.REMARK_DRILL, A.REMARK_1, A.REMARK_2, B.REMARK_DRILL AS CREMARK_DRILL, B.REMARK_1 AS CREMARK_1, B.REMARK_2 AS CREMARK_2 
								FROM $table_customer_request A LEFT JOIN $table_rbo_request B ON A.RBO = B.TYPERBO WHERE B.RBO IS NULL
								UNION ALL
								SELECT A.RBO,A.BILL_TO, A.SHIP_TO, A.REMARK_DRILL, A.REMARK_1, A.REMARK_2, '' AS CREMARK_DRILL, '' AS CREMARK_1, '' AS CREMARK_2 
								FROM $table_customer_request A WHERE A.RBO = '';", _conn());
	$RowTableGLID = MiQuery("SELECT Buying_Office AS BO, Hole FROM $table_information WHERE GLID = '$GLID' LIMIT 1;", _conn());
	$RowTableGLID = $RowTableGLID[0];
	$RemarkDrill = array();
	$Remark1 = array();
	$Remark2 = array();
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
			if(!in_array($X["CREMARK_DRILL"], $RemarkDrill) && (strtoupper($RowTableGLID["Hole"]) == "TRUE" || $RowTableGLID["Hole"] == "1")) $RemarkDrill []= $X["CREMARK_DRILL"];
			if(!in_array($X["CREMARK_1"], $Remark1)) $Remark1 []= $X["CREMARK_1"];
			if(!in_array($X["CREMARK_2"], $Remark2)) $Remark2 []= $X["CREMARK_2"];
		}
	}

	// @TanDoan 20201130: Đoạn xử lý remark đặc biệt khác. Nếu có remark thì hiển thị thông tin chổ remarkX (mảng remark 1 chuyển đổi thành chuỗi)
		$urgentRemark = urgentRemarkShow($PPC_Remark);
		$quanGiayRemark = quanGiayRemark($Bo, $ShipTo, $BillTo );
		if (!empty($urgentRemark) ) {
			$urgentRemark .= "<br/>\n" .$quanGiayRemark;
		} else {
			$urgentRemark = $quanGiayRemark;
		}
	// test
		// $urgentRemark = "Quấn giấy, buộc thun";

	$RemarkX = implode("  ",$Remark1);

	// @TanDoan: Nối với RemarkX
		$RemarkX .= $urgentRemark;

	$RemarkSpec = "";
	$Drill = implode("  ",$RemarkDrill);
	if(strpos(strtoupper($PPC_Remark), "LAY MAU") !== false || strpos(strtoupper($PPC_Remark), "LẤY MẪU") !== false || $Bo == "LOWE'S"){
		$RemarkSpec = "LẤY MẪU";
	}

	// @TanDoan - 20210513: mail: Combine orders
	if(strpos(strtoupper($PPC_Remark), "COMBINE ITEM") !== false){
		$PPC_Remark = str_replace("COMBINE ITEM", "<a style='font-size:18pt;font-weight:bold;'>Đơn hàng combine orders/items,trưởng cell kiểm tra</a>", $PPC_Remark);
	}

	// if(strpos(strtoupper($PPC_Remark), "GHÉP NHÃN") !== false || strpos(strtoupper($PPC_Remark), "GHEP NHAN") !== false){
	// 	$RemarkSpec = "GHÉP NHÃN";
	// } else if(strpos(strtoupper($PPC_Remark), "ĐI CHUNG") !== false || strpos(strtoupper($PPC_Remark), "DI CHUNG") !== false) {
	// 	$RemarkSpec = "ĐI CHUNG";
	// } else  

	$SOLine = $JobJacket;
	$barcode = new phpCode128($SOLine, 60, 'c:\windows\fonts\verdana.ttf', 18);
	$barcode->setBorderWidth(0);
	$barcode->setBorderSpacing(5);
	$barcode->setPixelWidth(4);
	$barcode->setEanStyle(false);
	$barcode->setShowText(false);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(5);

	$barcode->saveBarcode('Barcode//'.$SOLine.'.png');

	// @TanDoan - 20210604: barcode GLID
	$barcode_GLID = new phpCode128($GLID, 60, 'c:\windows\fonts\verdana.ttf', 18);
	$barcode_GLID->setBorderWidth(0);
	$barcode_GLID->setBorderSpacing(5);
	$barcode_GLID->setPixelWidth(4);
	$barcode_GLID->setEanStyle(false);
	$barcode_GLID->setShowText(false);
	$barcode_GLID->setAutoAdjustFontSize(true);
	$barcode_GLID->setTextSpacing(5);

	$barcode_GLID->saveBarcode('Barcode//'.$GLID.'.png');


	// // $SpecialItem = MiQueryScalar("SELECT Remark FROM $table_item_remark WHERE GLID = '$GLID' LIMIT 1;", _conn());
	// // if($SpecialItem == null) $SpecialItem = MiQueryScalar("SELECT 'ITEM' AS T FROM $table_oe_special_remark WHERE ITEM = '$GLID' LIMIT 1;", _conn());
	// // if($SpecialItem == null) $SpecialItem = "";

	// // // @TanDoan - 20220316: Đổi lại logic so sánh bằng thành chứa chữ COACH. email: "Brand protection item"
	// // if( strpos(strtoupper($Bo),"COACH") !== false ) {
	// // 	$SpecialItem = "Item Đặc Biệt";
	// // } 


	// @TanDoan - 20220318: Cập nhật theo thông tin mới
	/*
		Lấy remark: "Item Đặc Biệt" dựa vào điều kiện
		- Cột Brand_Protection
		- RBO = COACH
	*/
	$SpecialItem = "";
	$Brand_Protection = MiQueryScalar("SELECT Brand_Protection FROM $table_information WHERE GLID = '$GLID' LIMIT 1;", _conn());
	if ($Brand_Protection == "1" ) {
		$SpecialItem = "Item Đặc Biệt";
	}
	
	// @TanDoan - 20220316: Đổi lại logic so sánh bằng thành chứa chữ COACH. email: "Brand protection item"
	if( strpos(strtoupper($Bo),"COACH") !== false ) {
		$SpecialItem = "Item Đặc Biệt";
	} 

	
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item Maintain</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<script src="./Module/JS/jquery-1.10.1.min.js"></script>
 </head>
<style>
@page {
        size: A4 landscape;
        margin: 10mm 5mm 10mm 5mm;
    }
        @media print {
        html, body {
            width: 210mm;
            height: 280mm;
            font-family: "Source Sans Pro","Helvetica Neue",Helvetica;
        }
    /* ... the rest of the rules ... */
    }


html, body {
        width: 100%;      /*provides the correct work of a full-screen layout*/ 
        height: 100%;     /*provides the correct work of a full-screen layout*/
        margin: 0px;      /*hides the body's scrolls*/
		font-family: "Source Sans Pro","Helvetica Neue",Helvetica;
		background-repeat: no-repeat;
		background-size: 100%;
		font-size: 12px;
		margin: 0px;
		padding: 0px;
   	}
	
.NotCol{
	border: 1px solid black;
}

.NotMar td {
	border-top: none;
	border-bottom: none;
	border-left: none;
	border-right: none;
}

.NotBor{
	border-style: none;
}

table{
    table-layout: fixed;
}

td{
    word-wrap:break-word;
	text-align: center;
	margin: 0px;
	padding: 0px;
	/* white-space: pre-wrap;  css-3 */
}

.Left li{
	text-align: left;
}

.left {
	text-align: left;
}

.Bold{
	font-weight: bold;
}

.bold1 td{
	font-weight: bold;
}

.big1 td{
	font-size: 14px;
}

.big2{
	font-size: 20px;
}

.nopad{
	padding: 0px ;
}

.MarLeft{
	margin-left: 100px;
}

.graytd td{
	background-color: #e6e6e6;
}

.Gray{
	background-color: #cccccc;
}

.Graylight{
	background-color: #e6e6e6;
}

.HeightLine td{
	line-height: 0;
}

.rotateimg90 {
  -webkit-transform:rotate(90deg);
  -moz-transform: rotate(90deg);
  -ms-transform: rotate(90deg);
  -o-transform: rotate(90deg);
  transform: rotate(90deg);
}

table.headerTable td {
  border: 1px solid black;
}
</style>
<script>

$(document).ready(function(){	
	<?php if(isset($_GET["P"])) {
		echo "
				window.print();                                         
				setTimeout(function(){
					window.close();
				},1000);";
	} ?>
})
</script>
<body>
	<div style="height:100%; width:100%">
		<?php if($TurnWorldon) echo '<div style="width:50px;height: 50%;background: black;position: absolute;bottom: 10%;right: 0;"></div>'; ?>
		<table style="width:100%;height:100%;border-collapse: separate;" cellpadding="0" cellspacing="0" border=1>
			
			<tr class="NotMar" style="height:0">
				<td style="width: 6%"></td>
				<td style="width: 1%"></td>
				<td style="width: 5%"></td>
				<td style="width: 5%"></td>
				<td style="width: 5%"></td>
				<td style="width: 11%"></td>
				<td style="width: 8%"></td>
				<td style="width: 8%"></td>
				<td style="width: 6%"></td>
				<td style="width: 7%"></td>
				<td style="width: 9%"></td>
				<td style="width: 6%"></td>
				<td style="width: 6%"></td>
				<td style="width: 6%"></td>
				<td style="width: 6%"></td>
				<td style="width: 6%"></td>
			</tr>
		
			<tr style="height:4%">
				<td colspan="6" class="NotBor" style="white-space: inherit;">
					<img class="NotBor" style="float: left;display: inline-block;height:25%;padding-right:10px" src="Images\Logo.png">
					<div style="float: left;"><a style="font-size:16pt;font-weight: bold; padding:2px; background: black; color:white;"><?php echo $Urgent_Status; ?></a></div>
					<div style="float: left;margin-left:10px"><a style="font-size:12pt;font-weight: bold; padding:2px; background: black; color:white;"><?php echo $Drill . " " . $SpecialItem; ?></a></div>
				</td>
				<td class="NotBor" colspan="2" rowspan="2"><img style="width:90%;height:90%" src='Barcode/<?php echo $GLID; ?>.png'></td>
				<td class="NotBor" colspan="1" rowspan="2"><div style="width: 100%;height:100%;background: url('Images/QR_<?php echo $JobJacket; ?>.png') no-repeat;background-size: contain;"></div></td>
				<!-- <td class="NotBor" colspan="2" rowspan="2"><img style="width:90%;height:90%" src='Barcode/<?php echo $GLID; ?>.png'></td> -->
				<td class="NotBor" colspan="2" rowspan="2" style="font-weight:bold; font-size:20pt; line-height:0.9;margin: 0; padding:0;"><div style="vertical-align: top;"><?php echo $Order_Style; ?><br/><?php echo $Print_Machine; ?></div></td>
				<td class="NotBor" colspan="2" rowspan="2"><img style="width:90%;height:90%" src='Barcode/<?php echo $SOLine; ?>.png'></td>
				<td class="NotBor" colspan="3" rowspan="2" style="font-size:25pt;font-weight:bold;text-align:left"><?php echo $SOLine; ?></td>
				
			</tr>
			
			<tr class="NotMar" style="font-size:8pt;line-height: 0.5;">
				<td style=" text-align: left;" colspan="8">Print Date:<?php echo date("d/m/Y H:i:s");?>   <a style="color:white">___</a>      PPC name: <?php echo $Order_Handler; ?></td>
			</tr>
			
			<tr style="height:16%">
				<td colspan="16">
					<table class="headerTable" style="border-collapse: collapse;width:100%;height:100%;border:none;">
						<tr style="line-height:1">
							<td class="Bold Gray big HeightLine;" colspan=2>Basic Information</td>
							<td class="Bold Graylight">GLID</td>
							<td colspan="3" style="font-weight:bold; font-size:14pt"><?php echo $GLID; ?></td>
							<td colspan="2" class="Bold Graylight">Buying Office</td>
							<td colspan="3" style="font-weight:bold; font-size:14pt"><?php echo $Bo; ?></td>
							<td colspan="3" style="font-size:12pt;font-weight:bold"><?php if($New_Product != "0") {echo "NEW";} else {echo "OLD";} ?> Order</td>
							<td colspan="2" class="Bold Graylight">Item Code</td>
							<td colspan="4" style="font-weight:bold; font-size:14pt"><?php echo $ItemCode; ?></td>
							<td rowspan=5 style="width:30px">
							<!-- <div style="width:100%;height:100%;background:white; position: relative;"> -->
								<!-- <img style="top: 35px;position: absolute;height:40px;right: -60px;width: 150px;transform: rotate(90deg);" src='Barcode/<?php echo $SOLine;//@@@@@@@@@@@@ ?>.png'> -->
								<?php echo !empty($remarkKKL) ? '<div style="position: relative; border: 1px solid blue;background-color: black; color: white; top: 115px; position: absolute; height: 20px; right: -62px; width: 150px; transform: rotate(-90deg); padding-top: 8px; font-size: 16px;font-weight:bold;">' . $remarkKKL . '</div>' : ''; ?>
							<!-- </div> -->
							
							</td>
						</tr>
						<tr class="Bold Graylight">
							<td class="Bold Gray big HeightLine;" colspan=3>Material Code</td>
							<td class="Bold Gray big HeightLine;" colspan=3>DieCut Machine</td>
							<td>Cut No</td>
							<td>UPS</td>
							<td colspan="2">Order Qty</td>
							<td colspan="3">Order Date</td>
							<td colspan="3">PPC Nhận</td>
							<td>MLA</td>
							<td colspan="3">Due Date</td>
						</tr>
						<tr>
							<td colspan=3 style="font-size:14pt; font-weight:bold;"><?php echo $Stock_Code_F; ?></td>
							<td colspan=3 style="background-color:aqua;font-family:'Lucida Sans Unicode'; font-weight:bolder;"><?php echo $DieCut_Machine; ?></td>
							<td><?php echo $Cut_Number; ?></td>
							<td><?php echo $UPS; ?></td>
							<td colspan="2"><?php echo number_format($Quantity,0,",","."); ?></td>
							<td colspan="3"><?php echo Date("d/m/Y",strtotime($OrderReceive)); ?></td>
							<td colspan="3" rowspan=3 style="font-size:10pt;font-weight:bold"><?php echo $ShowReceivingDate ?></td>
							<td colspan="1" rowspan=3 style="font-size:16pt;font-weight:bold"><?php if($MLA == "") {echo "NO";} else {echo "YES";} ?></td>
							<td colspan="3" rowspan=3 style="font-size:16pt;font-weight:bold"><?php echo $Dueday; ?></td>
						</tr>
						<tr class="bold1 graytd">
							<td colspan="3">Kích thước con nhãn</td>
							<td colspan="2">Khổ in</td>
							<td style="font-size:8pt">SL tờ lớn</td>
							<td>SL cần</td>
							<td colspan="2"  style="font-size:8pt">Bù hao CD in</td>
							<td colspan="2" style="font-size:8pt">Bù hao CĐ khác</td>
							<td colspan="2">DieCut No</td>
						</tr>
						<tr>
							<td>
								<?php 
									// if($Print_Machine != "String only") echo number_format($ProductWidth,3,",","."); 
									if($Print_Machine != "String only") echo $ProductWidth; 
								?>
							</td>
							<td class="Gray">X</td>
							<td>
								<?php 
									// if($Print_Machine != "String only") echo number_format($ProductLength,3,",","."); 
									if($Print_Machine != "String only") echo $ProductLength; 
								?>
							</td>
							<td colspan="2"><?php echo $Stock_Size; ?></td>
							<td><?php if($Cut_Number == 0) echo "0";
								 else echo number_format(ceil(($Print_Sheet + $Finish_Scrap + $Print_Scrap)/$Cut_Number),0,",","."); ?></td>
							
							<?php 
								$new_style = 'font-weight:bold; ';
								if ($Print_Sheet < 10000 ) {
									$new_style .= 'font-size:14pt;';
								} else if ($Print_Sheet >= 10000 && $Print_Sheet < 100000 ) {
									$new_style .= 'font-size:12pt;';
								} else {
									$new_style .= 'font-size:9pt;';
								} 
								// echo "font size: $font_size -- print sheet: $Print_Sheet ";
							?>
							<td style="<?php echo $new_style; ?>"><?php echo number_format($Print_Sheet,0,",","."); ?></td>
							<td colspan="2" ><?php echo number_format($Print_Scrap,0,",","."); ?></td>
							<td colspan="2"><?php echo number_format($Finish_Scrap,0,",","."); ?></td>
							<td colspan="2"><?php echo $DieCut; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr class="bold1" style="line-height:0.8">
				<td colspan="6" class="Gray big">Color Information</td>
				<td colspan="4" class="Gray big">Accessory Information</td>
				<td colspan="6" class="Gray big">Routing</td>
			</tr>
			
			<tr style="line-height:1">
				<td rowspan="2" class="Bold Graylight">Màu cố định MT</td>
				<td colspan="3" rowspan="2"><?php echo $Color_F; ?></td>
				<td rowspan="2" class="Bold Graylight">Màu cố định MS</td>
				<td rowspan="2"><?php echo $Color_B; ?></td>
				<td colspan="4" rowspan="10">
					<?php echo $Accessory; ?>
				</td>
				<td class="left" colspan="6" rowspan="3"><?php echo $SubContract; ?></td>
			</tr>
			
				
			<tr>
		
			</tr>
			
			<tr style="line-height:1">
				<td rowspan="2" class="Bold Graylight">Barcode MT</td>
				<td colspan="3" rowspan="2"><?php echo $Imprint_F; ?></td>
				<td rowspan="2" class="Bold Graylight">Barode MS</td>
				<td rowspan="2"><?php echo $Imprint_B; ?></td>

			</tr>
			
			<tr>
				<td colspan="6" rowspan="19" style="vertical-align: TEXT-TOP;    white-space: normal;"> <?php echo $RoutingDetail; ?> </td>			
			</tr>
			
			<tr style="line-height:1">
				<td rowspan="2" class="Bold Graylight">Màu thay đổi MT</td>
				<td colspan="3" rowspan="2"><?php echo $Variable_F; ?></td>
				<td rowspan="2" class="Bold Graylight">Màu thay đổi MS</td>
				<td rowspan="2"><?php echo $Variable_B; ?></td>

			</tr>
			
			<tr>
				
			</tr>
			
			<tr style="line-height:1">
				<td rowspan="2" class="Bold Graylight">Vanish MT</td>
				<td colspan="3" rowspan="2"><?php echo $Varnish_F; ?></td>
				<td rowspan="2" class="Bold Graylight">Vanish MS</td>
				<td rowspan="2"><?php echo $Varnish_B; ?></td>
				
				</td>
			</tr>
			
			<tr>
				
			</tr>
			
			<tr>
				<td rowspan="2" class="Bold Graylight">UV MT</td>
				<td colspan="3" rowspan="2"><?php echo $UV_F; ?></td>
				<td rowspan="2" class="Bold Graylight">UV MS</td>
				<td rowspan="2"><?php echo $UV_B; ?></td>
			</tr>
			
			<tr>
		
			</tr>
			
			<tr>
				<td rowspan="4" class="Bold Graylight">SO_Line</td>
				<td colspan="9" rowspan="4" style="text-align:left;padding-left:10px"><?php echo $SOL; ?></td>
				<!-- <td colspan="9" rowspan="4" style="text-align:left;padding-left:10px"><?php //echo $SOLines; ?></td> -->
			</tr>
			
			<tr>
			
			</tr>
			
			<tr>
			
			</tr>
			
			<tr>
			
			</tr>
			
			<tr style="height:5%">
				<td colspan=10 rowspan=2>
					<table class="headerTable" style="border-collapse: collapse;width:100%;height:100%;border:none;">
						<tr style="line-height:1">
							<td colspan=3 class="Bold Graylight"> Color_Management:</td>
							<td colspan=2><?php echo $Color_Management; ?></td>
							<td colspan=4 class="Bold Graylight">FG Card No:</td>
							<td colspan="2"><?php echo $CS_Sample; ?></td>
							<td rowspan="2" class="Bold Graylight" style="font-size:7pt">HOT FOLDER</td>
							<td rowspan="2" colspan=4><?php echo $Hot_Folder; ?></td>
						</tr>
						<tr style="line-height:1">
							<td colspan="2" class="Bold Graylight">OS Sample:</td>
							<td colspan="3"><?php echo $OS_Sample; ?></td>
							<td colspan="3" class="Bold Graylight">DS Sample:</td>
							<td colspan="3"><?php echo $DS_Sample; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr></tr>
			
			<tr>
				<td class="Bold Gray">Special Instructions</td>
				<?php 
					if($RemarkSpec != "") {
						echo '<td colspan="6" style="text-align: left;padding-left:5px;">' . str_replace("\n","<br/>",$Special_Ins) . '</td>' . '<td colspan="3" style="background:black;color:white;font-size:30pt;font-weight:bold">' . $RemarkSpec . '</td>';
					} else {
						echo '<td colspan="9" style="text-align: left;padding-left:5px;">' . str_replace("\n","<br/>",$Special_Ins) . '</td>';
					}
				?>
			</tr>
			
			<tr>
				<?php 
					if(substr($FSC,0,3) == "FSC") {
						// echo '<td rowspan="5" class="Bold Gray"></td>';

						echo '<td rowspan="7" class="Bold Gray">
							<div style="width: 100%;height:50%;background: url(\'Images/QR_' . $JobJacket . '.png\') no-repeat;background-size: contain;"></div>
							<div style="width: 100%;height:10%"></div>
							<div style="width: 100%;height:10%">Remark</div>
							<div style="width: 100%;height:30%;color:white; background: black;text-align: center; font-weight: bold; font-size: 16pt;line-height:2">FSC</div>
						</td>';	
					} else {
						echo '<td rowspan="7" class="Bold Gray">
							<div style="width: 100%;height:50%;background: url(\'Images/QR_' . $JobJacket . '.png\') no-repeat;background-size: contain;"></div>
							<div style="width: 100%;height:10%"></div>
							<div style="width: 100%;height:10%">Remark</div>
							<div style="width: 100%;height:30%"></div>
						</td>';						
					}

						$CS = 7;
						
						// @tandoan: Trường hợp Remark FRU IC fix lại
						if (!empty($fruICRemark) ) {
							$fruICRemark = '<span style="width:97%;color:white;text-align:center;font-size:28pt;font-weight:bold;line-height:1.2">' . $fruICRemark. '</span>';
							$RemarkX .= !empty($RemarkX) ? "<br/>\n $fruICRemark" : "$fruICRemark" ;
						} 

						if($RemarkX == "" && $ICINDO == "") $CS = 9;
						
						echo '<td colspan="'.$CS .'" rowspan="7" style="text-align: left;border-right: none;vertical-align: top;padding-left:2px; width:90%;">' . str_replace("\n","<br/>",$PPC_Remark) . '</td>';

						echo '<td colspan="2" rowspan="7" style="text-align:left;border-left: none">';
						
							if($RemarkX != "") echo '<div style="width:100%; color: white; background:black;text-align:center;font-size:12pt;font-weight:bold;">' . $RemarkX . '</div>';
						
							// @TanDoan - 20210311: Cập nhật Chờ khô, cuối tháng 4 xóa bỏ
							$Special_Drying_Time_show =  "CHỜ KHÔ $Special_Drying_Time H";
							if($ICINDO != "") {
								if(strlen($ICINDO) > 10) echo '<div style="width:99%;color: white; background:black;text-align:center;font-size:12pt;font-weight:bold;line-height:1.2">' . $ICINDO . '</div>';
								else echo '<div style="width:99%; color: white; background:black;text-align:center;font-size:12pt;font-weight:bold;line-height:1.4">' . $ICINDO . '</div>';	
							} if (!empty($Special_Drying_Time) || $Special_Drying_Time != 0 || $Special_Drying_Time != '0' ) {
								echo '<div style="width:99%; color:white; background:black;text-align:center;font-size:12pt;font-weight:bold;">' . $Special_Drying_Time_show . '</div>';	
							} else {
								echo '<div style="width:99%;color:white; background:white;text-align:center;font-size:12pt;font-weight:bold;line-height:1.4"></div>';							
							}

							
							// if(!empty($fruICRemark) ) echo '<div style="border:2px solid blue;width:97%;color:blue;text-align:center;font-size:28pt;font-weight:bold;line-height:1.2">' . $fruICRemark . '</div>';

						
						echo '</td>';

				?>
			</tr>
			<tr>
			</tr>
			<tr>
			</tr>
			<tr>
			</tr>
			<tr>
			</tr>
			<tr>
				<td colspan="6" rowspan="2" style="font-size:12pt; font-weight: bold">
				<?php 
					if(strtoupper($Bo) == "TARGET"){
						echo "Bill To :" . $BillTo . "<br/>"; 	
						echo "Ship To :" . $ShipTo; 	
					} else echo "Ship To :" . $ShipTo;	
				?> <br/><a style="background:black;font-size:18pt;color:white"><?php echo implode(" ",$Remark2); ?></a></td>
			</tr>
			<tr>
			</tr>

		</table>
	</div>


	<div>
		<div style="height:186mm;float: left;width: 95%">
		<?php 
			// @tandoan - 20201221: hiển thị trường hợp UNIQLO. mail Tram.Tong: Thêm thông tin trên jobjacket
			if ($isUniqlo == true ) { 
				echo '<img style="position: relative; top: 10px; left:10px; height: 97%;width: 99%;margin:auto" src="Images/img/UNIQLO_Trace_ability_record.png" />';	
			} else {
				echo '<img style="position: relative; top: 10px; left:10px; height: 97%;width: 75%;margin:auto" src="Images/P3.png" />';
			}
		?>
			
		</div>
		<div style="height:186mm;background: white; border: 1px black solid;">
			<?php //echo $TableSOLine; ?>
		</div>
	</div>
	<?php 
		if($WithOutChecklist){
			echo '<div>
					<img style="height:186mm;margin:auto" src=\'Images/P1.png\' />
				</div>
				<div>
					<img style="height:186mm;margin:auto" src=\'Images/P2.png\' />
				</div>';
		}

		if(strlen($PPC_RemarkV2) > 100){
			echo '<div>
					' . "<a style='font-size:20pt;font-weight:bold;color:white;background:black'>$JobJacket</a>" . $PPC_RemarkV2 . '
				</div>';
		}

		if($TurnSO && strlen($TableSOLine2) > 2000){
			echo '<div>
					' . $TableSOLine2 . '
				</div>';
		}

		if($TurnSO && strlen($TableSOLine3) > 2000){
			echo '<div>
					' . $TableSOLine3 . '
				</div>';
		}

		// // // Page 2
		// // if ($page2 == true ) {
		// // 	echo '<div style="height:100%; width:100%;page-break-after:always;">';
		// // 	include_once ("print/page_2.php");
		// // }
		
	?>
	
</body>
</html>