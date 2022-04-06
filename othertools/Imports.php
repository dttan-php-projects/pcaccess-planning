<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ('../Module/Database.php');
    require_once ("../ToolTechnical/vendor/autoload.php"); 

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
    $table_automail = "au_avery.vnso";
    $table = "access_soline_received";
    $table_receiving = "access_order_receiving";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
    $message = "Not Submit";
    if (isset($_POST["submit"])) {

        $allowedFileType = ['application/vnd.ms-excel', 'application/octet-stream', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        
        if (in_array($_FILES["file"]["type"], $allowedFileType)) {

            $file_name = 'PC_New_Receiving_' . $_SERVER['REMOTE_ADDR'] . '_' . $updateBy . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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


                $createArray = array( 'ORDER_NUMBER', 'LINE_NUMBER', 'UOM', 'ISSUE', 'CUSTOMER_REQUEST' );
                $makeArray = array( 'ORDER_NUMBER' => 'ORDER_NUMBER', 'LINE_NUMBER' => 'LINE_NUMBER', 'UOM' => 'UOM', 'ISSUE' => 'ISSUE', 'CUSTOMER_REQUEST' => 'CUSTOMER_REQUEST' );
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

            // echo "createArray: "; print_r($createArray); echo " <br/>\n";
            // echo "makeArray: "; print_r($makeArray); echo " <br/>\n";
            // echo "SheetDataKey: "; print_r($SheetDataKey); echo " <br/>\n"; echo "flag: $flag"; exit();
            
            // load data
                $count = 0;
                if ($flag == 1) {
                    
                    $countCheck = 0;
                    for ($i = 2; $i <= count($allDataInSheet); $i++) {
                        // get col key
                            $order_number = $SheetDataKey['ORDER_NUMBER']; 
                            $line_number = $SheetDataKey['LINE_NUMBER']; 
                            $UOM = $SheetDataKey['UOM']; 
                            $ISSUE = $SheetDataKey['ISSUE']; 
                            $CUSTOMER_REQUEST = $SheetDataKey['CUSTOMER_REQUEST']; 

                        // get data 
                            $OrderNumber = filter_var(trim($allDataInSheet[$i][$order_number]), FILTER_SANITIZE_STRING);
                            $LineNumber = filter_var(trim($allDataInSheet[$i][$line_number]), FILTER_SANITIZE_STRING);
                            $UOM = filter_var(trim($allDataInSheet[$i][$UOM]), FILTER_SANITIZE_STRING);
                            $ISSUE = filter_var(trim($allDataInSheet[$i][$ISSUE]), FILTER_SANITIZE_STRING);
                            $CUSTOMER_REQUEST = filter_var(trim($allDataInSheet[$i][$CUSTOMER_REQUEST]), FILTER_SANITIZE_STRING);
                            $SOLINE = $OrderNumber . '-' . $LineNumber;
                        
                        // check empty data
                            if (empty($OrderNumber) || empty($LineNumber) ) {
                                $countCheck++;
                                if ($countCheck==2) break;
                                continue;
                            } 

                        // check automail
                            $status = 0;
                            $automailData = MiQuery( "SELECT * FROM `vnso` WHERE `ORDER_NUMBER`='$OrderNumber' AND `LINE_NUMBER`='$LineNumber' ORDER BY `ID` DESC LIMIT 1;", _conn('au_avery'));
                            if (!empty($automailData) ) {

                                $automailItem = $automailData[0];

                                $CUSTOMERPO = str_replace("'","\\'",$automailItem['CUST_PO_NUMBER'] );
                                $ORDERED_ITEM = str_replace("'","\\'",$automailItem['ORDERED_ITEM'] );
                                $ITEM = str_replace("'","\\'",$automailItem['ITEM'] );
                                $QTY = str_replace("'","\\'",$automailItem['QTY'] );
                                $REQUEST_DATE = (!empty($automailItem['REQUEST_DATE'])) ? date('Y-m-d',strtotime($automailItem['REQUEST_DATE'])) : '';

                                // check PD null or empty, 1 space
                                $PROMISE_DATE = $automailItem['PROMISE_DATE'];

                                // check PD
                                if (!empty($PROMISE_DATE)  ) {
                                    if ($PROMISE_DATE == ' ' ) {
                                        $PROMISE_DATE = '';
                                    } else {
                                        if ((strpos($PROMISE_DATE, '1970') !==false ) ) {
                                            $PROMISE_DATE = '';
                                        } else {
                                            $PROMISE_DATE = date('Y-m-d',strtotime($PROMISE_DATE));
                                        }
                                    }
                                    
                                } else {
                                    $PROMISE_DATE = '';
                                }
                                
                                $SHIP_TO_CUSTOMER = str_replace("'","\\'",$automailItem['SHIP_TO_CUSTOMER'] );
                                $BILL_TO_CUSTOMER = str_replace("'","\\'",$automailItem['BILL_TO_CUSTOMER'] );

                                // Đoạn này làm giống như cái cũ (Update Data Receiving)
                                $Result = MiNonQuery( "UPDATE $table_receiving SET ACTIVE = 0 WHERE ORDER_NUMBER = '$OrderNumber' AND LINE_NUMBER = '$LineNumber';", _conn() );

                                $Result = MiNonQuery( "INSERT INTO $table_receiving
                                                            ( ORDER_NUMBER, LINE_NUMBER, ORDER_LINE, CUSTOMER_PO, ORDERED_ITEM, ITEM, QTY, UOM, REQUEST_DATE, PROMISE_DATE, SHIP_TO_CUSTOMER, BILL_TO_CUSTOMER, PPC_RECEIVING_TIME, ISSUE_ORDER, CUSTOMER_REQUEST)
                                                        VALUES
                                                            ( '$OrderNumber', '$LineNumber', '$SOLINE', '$CUSTOMERPO', '$ORDERED_ITEM', '$ITEM', '$QTY', '$UOM', '$REQUEST_DATE', '$PROMISE_DATE', '$SHIP_TO_CUSTOMER', '$BILL_TO_CUSTOMER', NOW(), '$ISSUE', '$CUSTOMER_REQUEST');", 
                                                        _conn() 
                                                    );
                                
                                // status
                                    $status = ($Result == true ) ? 1 : 0;
                            }

                            // check exist
                                $receivingCheck = MiQuery( "SELECT * FROM $table WHERE `ORDER_NUMBER`='$OrderNumber' AND `LINE_NUMBER`='$LineNumber' ORDER BY `updated_date` DESC LIMIT 1;", _conn());
                                if (!empty($receivingCheck) ) {
                                    $sql = "UPDATE $table SET `so_line`='$SOLINE', `status`='$status', `UOM`='$UOM', `ISSUE`='$ISSUE', `CUSTOMER_REQUEST`='$CUSTOMER_REQUEST', `updated_by`='$updateBy', `updated_date`=NOW() WHERE ORDER_NUMBER = '$OrderNumber' AND LINE_NUMBER = '$LineNumber';";
                                } else {
                                    $sql = "INSERT INTO $table
                                                ( `ORDER_NUMBER`, `LINE_NUMBER`, `so_line`, `status`, `UOM`, `ISSUE`, `CUSTOMER_REQUEST`, `updated_by` )
                                            VALUES
                                                ( '$OrderNumber', '$LineNumber', '$SOLINE', '$status', '$UOM', '$ISSUE', '$CUSTOMER_REQUEST', '$updateBy' );";
                                }

                                // results
                                $Result = MiNonQuery( $sql, _conn() );
                                if ($Result == false ) {
                                    // write log error file 
                                        $fp = fopen('LogError.txt', 'a');//opens file in append mode  
                                        $content = "Import 1 Error on row number $i. $sql ";
                                        $txt = "\n======================== IMPORT RECEIVING DATA " . date('Y-m-d H:i:s') . " ======================== \n" . $content;
                                        fwrite($fp, $txt);
                                        fclose($fp);
                                    // break
                                        break;
                                } else {
                                    $count++;
                                    $message = "Import Data Success. Updated $count Rows Success. ";

                                }
                                

                    }
                } else {
                    $message = "Invalid File Type. Upload Excel File (*)";
                }


            // write log error file 
                $fp = fopen('LogError.txt', 'a');//opens file in append mode  
                $content = "Import 0 Error. Updated $count Rows Success ";
                $txt = "\n======================== IMPORT RECEIVING DATA " . date('Y-m-d H:i:s') . " ======================== \n" . $content;
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
