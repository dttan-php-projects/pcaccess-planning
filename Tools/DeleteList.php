<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ('../Module/Database.php');
    require_once ("../ToolTechnical/vendor/autoload.php"); 
    // $conn = _conn();
    $table = "access_soline_received";
    $table_receiving = "access_order_receiving";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
    if (empty($updateBy) ) {
        $updateBy = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : "";
    }
    $message = "Not Submit";
    if (isset($_POST["submit"])) {

        $allowedFileType = ['application/vnd.ms-excel', 'application/octet-stream', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (in_array($_FILES["file"]["type"], $allowedFileType)) {

            $file_name = 'PC_Delete_Receiving_' . $_SERVER['REMOTE_ADDR'] . '_' . $updateBy . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
                $createArray = array( 'ORDER_NUMBER', 'LINE_NUMBER' );
                $makeArray = array( 'ORDER_NUMBER' => 'ORDER_NUMBER', 'LINE_NUMBER' => 'LINE_NUMBER' );
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
                $count = 0;
                if ($flag == 1) {

                    for ($i = 2; $i <= count($allDataInSheet); $i++) {
                        // get col key
                            $ORDER_NUMBER = $SheetDataKey['ORDER_NUMBER']; 
                            $LINE_NUMBER = $SheetDataKey['LINE_NUMBER']; 
                        // get data
                            $order_number = filter_var(trim(strtoupper($allDataInSheet[$i][$ORDER_NUMBER]) ), FILTER_SANITIZE_STRING);
                            $line_number = filter_var(trim(strtoupper($allDataInSheet[$i][$LINE_NUMBER]) ), FILTER_SANITIZE_STRING);
                        // check empty data
                            if (empty($order_number) || empty($line_number)  ) continue;

                        // check 
                            $check = MiQuery( "SELECT * FROM $table WHERE `order_number`='$order_number' AND `line_number`='$line_number' ORDER BY `updated_date` DESC LIMIT 1;", _conn());
                            if (!empty($check) ) {
                                $Result = MiNonQuery( "DELETE FROM $table WHERE `order_number`='$order_number' AND `line_number`='$line_number';", _conn());
                                if ($Result == true ) {
                                    $Result = MiNonQuery( "DELETE FROM $table_receiving WHERE `ORDER_NUMBER`='$order_number' AND `LINE_NUMBER`='$line_number';", _conn());
                                    // $Result = MiNonQuery( "UPDATE $table_receiving SET ACTIVE = 0 WHERE ORDER_NUMBER = '$order_number' AND LINE_NUMBER = '$line_number';", _conn() );
                                    if ($Result == false ) {
                                        $message = "There was an error deleting the order: " . $order_number ."-". $line_number;
                                    }  else {
                                        $count++;
                                        $message = "Successfully Deleted $count rows";
                                    }
                                }
                            }

                        
                    }
                }
            
        } else {
            $message = "Invalid File Type. Upload Excel File.";
        }
    }


?>
<script>
    var message = '<?php  echo $message; ?>';
    alert(message);
    // window.location="./index.php";
</script>
