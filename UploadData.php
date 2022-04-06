<?php
    set_time_limit (3000);
	$target_dir = "Files/";
	$target_file = $target_dir . basename($_FILES["FileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if file already exists
    // Check file size
    $UserName = "";
    if(isset($_GET["USERNAME"])) $UserName = $_GET["USERNAME"];
	if ($_FILES["FileToUpload"]["size"] > 50000000) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// Allow certain file formats
	// if($imageFileType != "xlsx" && $imageFileType != "xls" ) {
	if( $imageFileType != "xlsx" ) {
		echo "Sorry, only xlsx files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {

        if(LoadExcel($_FILES["FileToUpload"]["tmp_name"], $UserName) == 1)
        {
            echo '<a style="font-size:30; color: red; font-weight:bold">Đã Upload thành công</a>';
        }

        if (move_uploaded_file($_FILES["FileToUpload"]["tmp_name"], "Excel/". $_SERVER['REMOTE_ADDR'] . $UserName . "-" . date('YmdHis') . ".xlsx")) {
            header('Location: PPCReceiving.php');
            //echo '<a style="font-size:30; color: red; font-weight:bold">Đã Upload thành công</a>"';
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }



    function LoadExcel($NameFile, $UserName)
    {
        require_once ("./Module/PHPExcel/IOFactory.php");
        require_once ("./Module/Database.php");
        $table_receiving = "access_order_receiving";
        
        try {
            $inputFileType = PHPExcel_IOFactory::identify($NameFile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($NameFile);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($NameFile,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $ProductLinePIC = "XXX";
        $sheet = $objPHPExcel->getSheet(0); 
        $LastRows = $sheet->getHighestRow(); 

        for($i = 2; $i <= $LastRows; $i++)
        {
            $OrderNumber = str_replace("'","\\'",$sheet->getCell("B" . $i)->getValue());
            $LineNumber = str_replace("'","\\'",$sheet->getCell("C" . $i)->getValue());
            $SOLINE = str_replace("'","\\'",$sheet->getCell("D" . $i)->getValue());
            $CUSTOMERPO = str_replace("'","\\'",$sheet->getCell("E" . $i)->getValue());
            $ORDERED_ITEM = str_replace("'","\\'",$sheet->getCell("F" . $i)->getValue());
            $ITEM = str_replace("'","\\'",$sheet->getCell("G" . $i)->getValue());
            $QTY = str_replace("'","\\'",$sheet->getCell("H" . $i)->getValue());
            $UOM = str_replace("'","\\'",$sheet->getCell("I" . $i)->getValue());
            $REQUEST_DATEX = str_replace("'","\\'",$sheet->getCell("J" . $i)->getFormattedValue());
            $REQUEST_DATEXX = str_replace("'","\\'",$sheet->getCell("J" . $i)->getValue());
            $PROMISE_DATEX = str_replace("'","\\'",$sheet->getCell("K" . $i)->getFormattedValue());
            $PROMISE_DATEXX = str_replace("'","\\'",$sheet->getCell("K" . $i)->getValue());
            $SHIP_TO_CUSTOMER = str_replace("'","\\'",$sheet->getCell("L" . $i)->getValue());
            $BILL_TO_CUSTOMER = str_replace("'","\\'",$sheet->getCell("M" . $i)->getValue());
            $PPC_RECEIVING = str_replace("'","\\'",$sheet->getCell("N" . $i)->getValue());
            $ISSUE = str_replace("'","\\'",$sheet->getCell("O" . $i)->getValue());
            $CUSTOMER_REQUEST = str_replace("'","\\'",$sheet->getCell("P" . $i)->getValue());
         
            if($OrderNumber == "") break;
            $REQUEST_DATE = \PHPExcel_Style_NumberFormat::toFormattedString($REQUEST_DATEXX, 'YYYY-MM-DD');
            if(strpos($REQUEST_DATE, "-") === false || strpos($REQUEST_DATE, "20") === false) {
                if(strpos($REQUEST_DATEX, "-") !== false || strpos($REQUEST_DATEX, "/") !== false) $REQUEST_DATE = date('Y-m-d',strtotime($REQUEST_DATEX));
                else $REQUEST_DATE = \PHPExcel_Style_NumberFormat::toFormattedString($REQUEST_DATEX, 'YYYY-MM-DD');
            }

            $PROMISE_DATE = \PHPExcel_Style_NumberFormat::toFormattedString($PROMISE_DATEXX, 'YYYY-MM-DD');
            if(strpos($REQUEST_DATE, "-") === false || strpos($REQUEST_DATE, "20") === false) {
                if(strpos($PROMISE_DATEX, "-") !== false || strpos($PROMISE_DATEX, "/") !== false) $REQUEST_DATE = date('Y-m-d',strtotime($PROMISE_DATEX));
                else $REQUEST_DATE = \PHPExcel_Style_NumberFormat::toFormattedString($PROMISE_DATEX, 'YYYY-MM-DD');
            }

            $Result = MiNonQuery( "UPDATE $table_receiving SET ACTIVE = 0 WHERE ORDER_NUMBER = '$OrderNumber' AND LINE_NUMBER = '$LineNumber';", _conn() );

            $Result = MiNonQuery( "INSERT INTO $table_receiving
                                    (
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
                                        '$OrderNumber',
                                        '$LineNumber',
                                        '$SOLINE',
                                        '$CUSTOMERPO',
                                        '$ORDERED_ITEM',
                                        '$ITEM',
                                        '$QTY',
                                        '$UOM',
                                        '$REQUEST_DATE',
                                        '$PROMISE_DATE',
                                        '$SHIP_TO_CUSTOMER',
                                        '$BILL_TO_CUSTOMER',
                                        NOW(),
                                        '$ISSUE',
                                        '$CUSTOMER_REQUEST');", _conn() );
                echo $Result . "\n";
            }
        return 1;
    }

?> 