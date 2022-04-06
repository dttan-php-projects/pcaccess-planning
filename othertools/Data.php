<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require("../Module/Database.php");

    function loadData($event)
    {
        $table = "access_delete_list";
        $data = MiQuery( "SELECT * FROM $table ORDER BY updated_date DESC LIMIT 5000;", _conn());

        return $data;
    }

    

        if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LoadData"){
            header('Content-type: text/xml');
            echo "<rows>";
                $header = '<head>
                    <column width="60" type="ed" align="center" sort="str">No.</column>
                    <column width="*" type="ed" align="center" sort="str">JobJacket</column>
                    <column width="200" type="ed" align="center" sort="str">Người cập nhật</column>
                    <column width="200" type="ed" align="center" sort="str">Ngày cập nhật</column>
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
                            echo '<cell>'. $row['jobjacket'].'</cell>';
                            echo '<cell>'. $row['updated_by'].'</cell>';
                            echo '<cell>'. $row['updated_date'].'</cell>';
                        echo '</row>';
                    }
                }

            echo "</rows>";
        } 
    
