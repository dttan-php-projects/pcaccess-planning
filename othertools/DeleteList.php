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

            $file_name = 'PC_Delete_Jobjacket_' . $_SERVER['REMOTE_ADDR'] . '_' . $updateBy . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
                $createArray = array( 'JOBJACKET' );
                $makeArray = array( 'JOBJACKET' => 'JOBJACKET' );
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
                $countCheck = 0;
                if ($flag == 1) {

                    // table
                        $table_list = "access_order_list";
                        $table_fgs_data = "access_fgs_data";
                        $table_lines = "access_id_lines";
                        $table_receiving = "access_order_receiving";

                    // for
                    for ($i = 2; $i <= count($allDataInSheet); $i++) {
                        // get col key
                            $jobjacket = $SheetDataKey['JOBJACKET'];
                        // get data
                            $JobJacket = filter_var(trim(strtoupper($allDataInSheet[$i][$jobjacket]) ), FILTER_SANITIZE_STRING);
                        // check empty data
                            if (empty($JobJacket) ) {
                                if ($countCheck == 5 ) {
                                    break;
                                }

                                $countCheck++;
                                continue;
                            } 

                        // Cập nhật DeleteBy
                            $message = 'ERROR ';
                            $result = MiNonQuery("UPDATE $table_list SET ActiveOrder = '', DeleteBy = '" . $updateBy . date("ymdHis") . $_SERVER['REMOTE_ADDR'] . "' WHERE ID = '$JobJacket'", _conn());
                            if ($result == true ) {
                                // Xóa trong bảng FGS
                                $result = MiNonQuery("DELETE FROM $table_fgs_data WHERE JOB_ID = '$JobJacket'", _conn());
                                if ($result == true ) {
                                    // Cập nhật lại bảng access_order_receiving: ISSUE_ORDER = 0 đối với các đơn đã xóa
                                    $SOLineData = MiQuery("SELECT ORDER_NUMBER, LINE_NUMBER FROM $table_lines WHERE JOBJACKET = '$JobJacket' AND ACTIVE = 1 ", _conn());
                                    foreach($SOLineData as $R) {
                                        $result = MiNonQuery("UPDATE $table_receiving SET ISSUE_ORDER = '0' WHERE ORDER_NUMBER = '" . $R["ORDER_NUMBER"] . "' AND LINE_NUMBER = '" . $R["LINE_NUMBER"] . "'", _conn());
                                        if ($result == false ) {
                                            $message = 'Update Receiving Err. ' . $R["ORDER_NUMBER"] . '-' . $R["LINE_NUMBER"] ;
                                            break;
                                        }
                                    } 

                                    if ($result == true ) {
                                        // Cập nhật lại trạng thái ACTIVE = 0
                                        $result = MiNonQuery("UPDATE $table_lines SET `ACTIVE`='0' WHERE JOBJACKET = '$JobJacket';",_conn() );

                                        // end 
                                        if ($result == true ) {
                                            $count++;
                                            $message = 'SUCCESS. Count: ' . $count;

                                            // save to table
                                            MiNonQuery(" INSERT INTO `access_delete_list` (`jobjacket`, `updated_by`) VALUES ('$JobJacket', '$updateBy') ", _conn());
                                        }
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
