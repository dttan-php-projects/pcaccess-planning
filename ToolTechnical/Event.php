<?php 
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    require("../Module/Database.php");

    $table_fgs_data = "access_fgs_data";
    $table_accessory = "access_item_accessory";
    $table_information = "access_item_information";
    $table_progress_track = "access_progress_track";
    $table_list = "access_order_list";
    $table_lines = "access_id_lines";

    $table_inventory_list = "access_inventory_list";
    $table_receiving = "access_order_receiving";
    $table_item_remark = "access_item_remark";

    $table_vnso = "au_avery.vnso";
    $table_soview_text = "au_avery.oe_soview_text";

    $OrderHandler = "";
    if(!isset($_COOKIE["ZeroIntranet"])) $OrderHandler = "Guest"; 
    else $OrderHandler = $_COOKIE["ZeroIntranet"];

    

    