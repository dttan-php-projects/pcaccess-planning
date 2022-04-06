<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ('../Module/Database.php');

    $conn = _conn();
    $table_automail = "au_avery.vnso";
    $table = "access_soline_received";
    $table_receiving = "access_order_receiving";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";

    $rawData = MiQuery( "SELECT * FROM $table WHERE `status`=0 ORDER BY `updated_date` DESC;", _conn() );
    
    $count = 0;
    if (!empty($rawData) ) {
        foreach ($rawData as $data ) {
            $OrderNumber = trim($data['order_number']);
            $LineNumber = trim($data['line_number']);
            $SOLINE = $OrderNumber . '-' . $LineNumber;

            $UOM = trim($data['UOM']);
            $ISSUE = trim($data['ISSUE']);
            $CUSTOMER_REQUEST = trim($data['CUSTOMER_REQUEST']);

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
                $PROMISE_DATE = (!empty($automailItem['PROMISE_DATE'])) ? date('Y-m-d',strtotime($automailItem['PROMISE_DATE'])) : '';
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

            // update status
            $sql = "UPDATE $table SET `status`='$status', `updated_by`='$updateBy', `updated_date`=NOW() WHERE ORDER_NUMBER = '$OrderNumber' AND LINE_NUMBER = '$LineNumber';";
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
    }


?>
<script>
    var message = '<?php  echo $message; ?>';
    alert(message);
    window.location="./index.php";
</script>
