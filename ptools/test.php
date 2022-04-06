<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ('../Module/Database.php');

    // Các ngày khác Tết Dương lịch & Âm lịch
    $sql = "SELECT `holiday_date` FROM `holidays` WHERE NOT `holiday_name_group` like '%NewYear%' AND (`holiday_date` LIKE '2022%' OR `holiday_date` LIKE '2023%') ORDER BY `holiday_date` ASC; ";
    $allHolidays = MiQuery($sql, _conn('au_avery') );

    // echo "sql: $sql <br>";
    
    // Các ngày Lễ khác của năm 2022 (2023 chưa thêm vào)
    if (!empty($allHolidays) ) {
        foreach ($allHolidays as $holiday ) {
            $holiday_date = $holiday['holiday_date'];
            echo "Holiday: $holiday_date <br>";
        }
    }