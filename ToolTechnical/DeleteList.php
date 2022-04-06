<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ('../Module/Database.php');
    require_once ("./vendor/autoload.php"); 
    $conn = _conn();
    $table = "access_item_information";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
    if (empty($updateBy) ) {
        $updateBy = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : "";
    }
    
    $message = "Not Submit";
    if (isset($_POST["submit"])) {

        $allowedFileType = ['application/vnd.ms-excel', 'application/octet-stream', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (in_array($_FILES["file"]["type"], $allowedFileType)) {

            $file_name = 'PC_Delete_MasterItem_' . $_SERVER['REMOTE_ADDR'] . '_' . $updateBy . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
                $createArray = array( 'GLID' );
                $makeArray = array( 'GLID' => 'GLID' );
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
                if ($flag == 1) {

                    for ($i = 2; $i <= count($allDataInSheet); $i++) {
                        // get col key
                            $GLID = $SheetDataKey['GLID']; 
                        // get data
                            $GLID = filter_var(trim(strtoupper($allDataInSheet[$i][$GLID]) ), FILTER_SANITIZE_STRING);
                        // check empty data
                            if (empty($GLID)  ) continue;
                        
                        // get data
                            $deleteData[] = array( 'GLID' => $GLID );
                    }
                }

                // print_r($deleteData); exit();
                
                $index = 0;
                $count = 0;
                $error_sql = '';
                $results = true;
                if (!empty($deleteData) ) {
                    foreach ($deleteData as $item ) {

                        $index++;
    
                        $GLID = trim($item['GLID']);

                        // delete
                            $sql = "SELECT `GLID` FROM $table WHERE `GLID`='$GLID' ORDER BY ID DESC LIMIT 1;";
                            $query = mysqli_query($conn, $sql);
                            if (!$query ) {
                                $error_sql .= "$index. Error: $sql ; <br />\n";
                            } else {
                                if (mysqli_num_rows($query) > 0 ) { 
                                    $results = mysqli_query($conn, "DELETE FROM $table WHERE `GLID`='$GLID'; "); 
                                    if (!$results) {
                                        $error_sql .= "$index. Error: $results ; <br />\n";
                                    } else {
                                        $count++;
                                    }
                                }   
                            }
                    }
                }

                if (!$results ) {
                    $message = "Update Material error "; 
                } else {
                    $message = "Delete $count rows success";
                }

            // close db
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
