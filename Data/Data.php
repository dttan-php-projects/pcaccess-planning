<?php 
    require($_SERVER["DOCUMENT_ROOT"]."/Database.php");

    if(!isset($_GET["FROM"]) && !isset($_GET["TO"])) return;
    $DateFrom = $_GET["FROM"];
    $DateTo = $_GET["TO"];
    $datetimeFrom = date_create($DateFrom);
    $datetimeTo = date_create($DateTo);
    $interval = date_diff($datetimeFrom, $datetimeTo)->format("%a");
    $ArrayDate;
    $ArrayUser = array();
    $ArrayMain = array();
    $DateShow = array();

    header('Content-type: text/xml');
		echo '<rows><head>
			<column width="70" type="ro" align="center" sort="str">GEN ID</column>
			<column width="70" type="ro" align="center" sort="str">USER ID</column>
			<column width="150" type="ro" align="center" sort="str">Full Name</column>
			<column width="150" type="ro" align="center" sort="str">Department</column>';
            
    for($i = 0; $i <= date_diff($datetimeFrom, $datetimeTo)->format("%a"); $i++)
    {
        // echo '<column width="100" type="ro" align="center" sort="str">WH ' . date("Ymd", strtotime($DateFrom . "+" .$i . "day")) . '</column>';
        // echo '<column width="100" type="ro" align="center" sort="str">OT ' . date("Ymd", strtotime($DateFrom . "+" .$i . "day")) . '</column>';
        echo '<column width="100" type="ro" align="center" sort="str">' . date("Ymd", strtotime($DateFrom . "+" .$i . "day")) . '</column>';
        echo '<column width="100" type="ro" align="center" sort="str">#cspan</column>';
        $ArrayDate["WH" . date("Ymd", strtotime($DateFrom . "+" .$i . "day"))] = "";
        $ArrayDate["OT" . date("Ymd", strtotime($DateFrom . "+" .$i . "day"))] = "";
        array_push($DateShow, date("Ymd", strtotime($DateFrom . "+" .$i . "day")));
    }
    echo '
        <column width="70" type="ro" align="center" sort="str">Total Time</column>
        <afterInit>
            <call command="attachHeader"><param>#text_filter,#text_filter,#text_filter,#text_filter';
        foreach($DateShow as $R)
        {
            echo ",Working Hours, Over Time";
        }   
    echo ',</param></call>
        </afterInit>';
    echo "<settings>
			<colwidth>px</colwidth>
			</settings>
        </head>";
        

    $StringSQL = "  SELECT DISTINCT GENID, USERID, FullName, Department, DATE_FORMAT(STR_TO_DATE(DateReport, '%m/%d/%Y'),'%Y%m%d') AS DateReport, Working, OverTime, TotalTime 
                        FROM au_avery.attendance_hr WHERE STR_TO_DATE(DateReport, '%m/%d/%Y') BETWEEN '$DateFrom' AND '$DateTo' AND Department LIKE '%RFID%';";
    
    $retval = mysql_query( $StringSQL , $connM2 );
    $GenList = "";
    while($Rows = mysql_fetch_array($retval, MYSQL_ASSOC)) {
        if(strpos($GenList, $Rows["GENID"]) === false)
        {
            $GenList = $GenList . "-" . $Rows["GENID"];
            array_push($ArrayMain, array(
                "GENID" => $Rows["GENID"],
                "USERID" => $Rows["USERID"],
                "FullName" => $Rows["FullName"],
                "Department" => $Rows["Department"],
                "Total" => "",
                "Data" => $ArrayDate
            ));
        }
        array_push($ArrayUser,$Rows);
    }

    foreach($ArrayUser as $K => $R)
    {
        foreach($ArrayMain as $k=>$r)
        {
            if($r["GENID"] == $R["GENID"])
            {
                $ArrayMain[$k]["Data"]["WH".$R["DateReport"]] = $R["Working"];
                $ArrayMain[$k]["Data"]["OT".$R["DateReport"]] = $R["OverTime"];
                break;
            }
        }
    }

	for($i = 0; $i < count($ArrayMain); $i++)
		{
            $Show = "";
            $TotalTime = 0;

            foreach($DateShow as $R)
            {
                // if(isset($ArrayMain[$i]["Data"][$R]))
                // {
                // 	if($ArrayMain[$i]["Data"][$R] != "")
                // 	{
                // 		echo '<cell style="background:red;font-weight:bold;color:white">X</cell>';
                // 	} else
                // 	{
                        $Show = $Show . '<cell>' .$ArrayMain[$i]["Data"]["WH".$R]. '</cell>' .
                         '<cell>' .$ArrayMain[$i]["Data"]["OT".$R]. '</cell>';
                        $OTH = round($ArrayMain[$i]["Data"]["OT".$R], 0, PHP_ROUND_HALF_DOWN);
                        if($OTH < 3.75 && $OTH > 1.75)
                        {
                            $OTH = 2;
                        } else if($OTH > 3.75)
                        {
                            $OTH = 4;
                        }
                        $TotalTime = $TotalTime + round($ArrayMain[$i]["Data"]["WH".$R]) + $OTH;
                // 	}
                // }
            }
            
			echo '<row id="'. $ArrayMain[$i]['GENID'] .'">';
			echo '<cell>' .$ArrayMain[$i]['GENID']. '</cell>';
			echo '<cell>' .$ArrayMain[$i]['USERID']. '</cell>';
			echo '<cell>' .utf8convert($ArrayMain[$i]['FullName']). '</cell>';
			echo '<cell>' .str_replace("&","&amp;",$ArrayMain[$i]['Department']). '</cell>';			

            echo $Show;	
			echo '<cell>' . $TotalTime . '</cell>';			            
			echo '</row>';
		}

		
		echo "</rows>";

        function utf8convert($str) {

            if(!$str) return false;

            $utf8 = array(

                'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

                'd'=>'đ',

                'D'=>'Đ',

                'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

                'i'=>'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',

                'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

                'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

                'y'=>'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',

            );

        foreach($utf8 as $ascii=>$uni) $str = preg_replace("/($uni)/i",$ascii,$str);

        return $str;

        }
?>