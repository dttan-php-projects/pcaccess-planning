<?php 
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    set_time_limit(300);

    require("./Module/DatabaseV2.php");
    $table_lines = "access_id_lines";
    $table_receiving = "access_order_receiving";

    $retval = MiQuery( "SELECT ID FROM (SELECT A.ID, (SELECT JOBJACKET FROM $table_lines WHERE ORDER_NUMBER = A.ORDER_NUMBER AND LINE_NUMBER = A.LINE_NUMBER ORDER BY ID DESC LIMIT 1) AS ISSUE 
    FROM $table_receiving A WHERE ACTIVE = 1 AND ISSUE_ORDER != 1) T WHERE ISSUE IS NOT NULL LIMIT 10000" , _conn()); 
    $ArrMain = array();
    foreach($retval as $K) {
        array_push($ArrMain, $K["ID"]);
    }

    $retval = MiNonQuery( "UPDATE $table_receiving SET ISSUE_ORDER = '1' WHERE ID IN (" . implode(",",$ArrMain) . ")" , _conn());

?>