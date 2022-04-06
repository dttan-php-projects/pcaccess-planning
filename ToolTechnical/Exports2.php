<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

ini_set('memory_limit', '-1'); // enabled the full memory available.
ini_set('max_execution_time', 99999999999);  // set time 10 minutes

require_once('../Module/Database.php');
require_once("./vendor/autoload.php");

function formatDate($date)
{
    $result = '';
    if (!empty($date)) {
        $detache = implode('/', $date);
        // Sau khi tách thành mảng, kiểm tra xem có đúng định dạng tháng/ngày/năm không
        if (isset($detache[0]) && isset($detache[1]) && isset($detache[2])) {
            if (checkdate($detache[0], $detache[1], $detache[2])) {
                $result = date('Y-m-d', strtotime($date));
            }
        }
    }

    return $result;
}

// database
$conn = _conn();
$table = "access_item_information";

$queryexport = ("SELECT * FROM $table ORDER BY ID DESC LIMIT 100;");

$result = mysqli_query($conn, $queryexport);
$header = '';

while ($property = mysqli_fetch_field($result)) {
    $header .= $property->name . "\t";
}

$excelData = $header . "\n";
while ($row = mysqli_fetch_row($result)) {
    $line = '';
    foreach ($row as $value) {
        if (!isset($value) || $value == "") {
            $value = "\t";
        } else {
            $value = str_replace('"', '""', $value);
            $value = '"' . $value . '"' . "\t";
        }
        $line .= $value;
    }

    $excelData .= trim($line) . "\n";
    $excelData = str_replace("\r", "", $excelData);

    if ($excelData == "") {
        $excelData = "\nNo matching records found\n";
    }
}

// set filename for excel file to be exported
$filename = 'PC_MasterData_' . date("Y_m_d__H_i_s") . '.xls';

// header: generate excel file
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$filename\""); 
header('Cache-Control: max-age=0');

// output data
echo $excelData;

mysqli_close($conn);
