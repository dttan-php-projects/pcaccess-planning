<?php 
    require("./Module/Database.php");
    $table_digital_maxscrap = "access_digital_maxscrap";
    $table_digital_printing_scrap = "access_digital_printing_scrap";
    $table_digital_scrap_special = "access_digital_scrap_special";
    $table_offset_printing_scrap = "access_offset_printing_scrap";
    
    if(isset($_GET["GLID"]) && isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADITEMSETUP")
    {
        $GLID = $_GET["GLID"];
        $ColorMax = $_GET["COLORMAX"];
        $Side = $_GET["SIDE"];

        $MaxTurn = 0;
        $MaxTurn = MiQueryScalar( "SELECT MaxScrap FROM $table_digital_maxscrap WHERE GLID = '$GLID' LIMIT 1", _conn());
        if($MaxTurn == "") $MaxTurn = 0;
        if($ColorMax > 2){
            $ColorMax = 2;
        }

        if($MaxTurn == 0){
            $SqlString = "SELECT * FROM $table_digital_printing_scrap WHERE Color = '$ColorMax' AND Side = '$Side'";
        } else {
            $SqlString = "SELECT * FROM $table_digital_scrap_special WHERE Color = '$ColorMax' AND Side = '$Side'";
        }
        $row = MiQuery( $SqlString, _conn());
        echo json_encode($row);
    } else if(isset($_GET["GLID"]) && isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADITEMSETUPOFFSET")
    {
        $GLID = $_GET["GLID"];
        $ColorMax = $_GET["COLORMAX"];
        $Side = $_GET["SIDE"];
        
        $SqlString = "SELECT * FROM $table_offset_printing_scrap WHERE Color = '$ColorMax' AND Side = '$Side'";
        $row = MiQuery( $SqlString, _conn());
        echo json_encode($row);
    }
?>