<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require("../Module/Database.php");

    function loadData($event)
    {
        $table = "access_soline_received";
        if ($event == 'InAutomail' ) {
            $where = '`Status` = 1';
        } else if ($event == 'OutAutomail' ) {
            $where = '`Status` = 0';
        }
        $data = MiQuery( "SELECT * FROM $table WHERE $where ORDER BY updated_date DESC LIMIT 5000;", _conn());

        return $data;
    }

    

        if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "InAutomail"){
            header('Content-type: text/xml');
            echo "<rows>";
                $header = '<head>
                    <column width="60" type="ed" align="center" sort="str">No.</column>
                    <column width="110" type="ed" align="center" sort="str">Order Number</column>
                    <column width="100" type="ed" align="center" sort="str">Line Number</column>
                    <column width="80" type="ed" align="center" sort="str">Status</column>
                    <column width="110" type="ed" align="center" sort="str">UOM</column>
                    <column width="110" type="ed" align="center" sort="str">Issue</column>
                    <column width="*" type="ed" align="center" sort="str">Customer Request</column>
                    <column width="100" type="ed" align="center" sort="str">Người cập nhật</column>
                    <column width="100" type="ed" align="center" sort="str">Ngày cập nhật</column>
                </head>';

                echo $header;

                // load data
                $data = loadData($_GET["EVENT"] );
                if (!empty($data) ) {
                    $index = 0;
                    foreach ($data as $key => $row ) {
                        $index++;
                        $status = ($row['status'] == 1 ) ? 'Đã cập nhật' : 'Chưa';

                        echo '<row id="'. $key .'">';
                            echo '<cell>'. $index .'</cell>';
                            echo '<cell>'. str_replace("&","&amp;",$row['order_number']) .'</cell>';
                            echo '<cell>'. str_replace("&","&amp;",$row['line_number']) .'</cell>';
                            echo '<cell>'. $status.'</cell>';
                            echo '<cell>'. $row['UOM'].'</cell>';
                            echo '<cell>'. $row['ISSUE'].'</cell>';
                            echo '<cell>'. $row['CUSTOMER_REQUEST'].'</cell>';
                            echo '<cell>'. $row['updated_by'].'</cell>';
                            echo '<cell>'. $row['updated_date'].'</cell>';
                        echo '</row>';
                    }
                }

            echo "</rows>";
        } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "OutAutomail"){
            header('Content-type: text/xml');
            echo "<rows>";
                $header = '<head>
                    <column width="60" type="ed" align="center" sort="str">No.</column>
                    <column width="110" type="ed" align="center" sort="str">Order Number</column>
                    <column width="100" type="ed" align="center" sort="str">Line Number</column>
                    <column width="80" type="ed" align="center" sort="str">Status</column>
                    <column width="110" type="ed" align="center" sort="str">UOM</column>
                    <column width="110" type="ed" align="center" sort="str">Issue</column>
                    <column width="*" type="ed" align="center" sort="str">Customer Request</column>
                    <column width="100" type="ed" align="center" sort="str">Người cập nhật</column>
                    <column width="100" type="ed" align="center" sort="str">Ngày cập nhật</column>
                </head>';

                echo $header;

                // load data
                $data = loadData($_GET["EVENT"] );
                if (!empty($data) ) {
                    $index = 0;
                    foreach ($data as $key => $row ) {
                        $index++;
                        $status = ($row['status'] == 1 ) ? 'Đã cập nhật' : 'Chưa';

                        echo '<row id="'. $key .'">';
                            echo '<cell>'. $index .'</cell>';
                            echo '<cell>'. str_replace("&","&amp;",$row['order_number']) .'</cell>';
                            echo '<cell>'. str_replace("&","&amp;",$row['line_number']) .'</cell>';
                            echo '<cell>'. $status.'</cell>';
                            echo '<cell>'. $row['UOM'].'</cell>';
                            echo '<cell>'. $row['ISSUE'].'</cell>';
                            echo '<cell>'. $row['CUSTOMER_REQUEST'].'</cell>';
                            echo '<cell>'. $row['updated_by'].'</cell>';
                            echo '<cell>'. $row['updated_date'].'</cell>';
                        echo '</row>';
                    }
                }

            echo "</rows>";
        }
    
