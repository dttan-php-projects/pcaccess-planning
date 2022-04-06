<style>
	.header {
		/* title */
		background-color: #59f78d;
		text-align: center;
	}

	.border-show{
		border: 1px solid gray;
		height: 35px;
		font-size: 14px;
	}

	.padding-show {
		padding: 5px;
	}
</style>
<?php
	$current = date('d/m/Y');	
	function getBonus7Date() 
	{
		$current = date('d/m/Y');		
		$date = date("d/m/Y", strtotime(strtotime($current) . "+7 days"));
		if(date("l", strtotime($date)) == "Sunday") $date = date("Y-m-d", strtotime(strtotime($date) . "+1 days"));

		return $date;
	}

	function savePromiseDate($access_id_lines_list ) 
	{
		$current = date('d/m/Y');
		$result = true; // mặc định
		foreach ($access_id_lines_list as $value ) {
			
			$PD = '';
			$JOBJACKET = trim($value['JOBJACKET']);
			$SO_Line = trim($value['SO_Line']);
			$SO_Line_Qty = trim($value['SO_Line_Qty']);
			$ORDER_NUMBER = trim($value['ORDER_NUMBER']);
			$LINE_NUMBER = trim($value['LINE_NUMBER']);
			
			$PDList = MiQuery("SELECT * FROM au_avery_pc.access_promise_date_list WHERE JOBJACKET = '$JOBJACKET' AND SO_Line = '$SO_Line' ORDER BY printed_date DESC LIMIT 1 ; ",_conn());
			if (!empty($PDList) ) {
				// Không save thêm
				return true;
			} else {
				// Lấy dữ liệu từ Automail
				$ordersData = MiQuery("SELECT PROMISE_DATE, REQUEST_DATE FROM au_avery.vnso_total WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER' ORDER BY ID DESC LIMIT 1 ; ",_conn());

				$PROMISE_DATE = trim($ordersData[0]['PROMISE_DATE']);
				$REQUEST_DATE = trim($ordersData[0]['REQUEST_DATE']);
				if (!empty($PROMISE_DATE) && $PROMISE_DATE != '1970-01-01' ) {
					$PROMISE_DATE = date('d-m-Y', strtotime($PROMISE_DATE));
					$note = 'Automail';
				} else {
					$PROMISE_DATE = getBonus7Date();
					$note = $current . ' + 7 ngày. (Chủ nhật + 8 ngày)';
				}

				// save
				$sqlInsert = "INSERT INTO au_avery_pc.access_promise_date_list (`JOBJACKET`,`SO_Line`,`SO_Line_Qty`,`PROMISE_DATE`,`REQUEST_DATE`, `note`) VALUES ('$JOBJACKET', '$SO_Line', '$SO_Line_Qty', '$PROMISE_DATE', '$REQUEST_DATE', '$note' );";
				$result = MiNonQuery($sqlInsert, _conn() );

			}

			
		}

		return $result;

	}
	
	$page2Data = MiQuery("SELECT * FROM au_avery_pc.access_id_lines WHERE JOBJACKET = '$JobJacket' ; ",_conn());
	if (!empty($page2Data) ) {
		$savePD = savePromiseDate($page2Data);

		if ($savePD == true ) $PDList = MiQuery("SELECT * FROM au_avery_pc.access_promise_date_list WHERE JOBJACKET = '$JobJacket' ; ",_conn());

		if (!empty($PDList) ) {
	
			$htmls = '';
			$htmls .= '<table style="width:99%;height:9%;" class="padding-show">';
				$htmls .= '<thead>';
					$htmls .= '<tr >';
						$htmls .= '<th class="header border-show" style="width:50px;">Stt</th>';
						$htmls .= '<th class="header border-show" style="width:200px;">SOLine</th>';
						$htmls .= '<th class="header border-show" style="width:150px;">Số Lượng</th>';
						$htmls .= '<th class="header border-show" style="width:200px;">Promise Date</th>';
						$htmls .= '<th class="header border-show">Note</th>';
					$htmls .= '</tr>';
				$htmls .= '</thead>';
				$htmls .= '<tbody>';
					$index = 0;
					$SO_Line = '';
					$SO_Line_Qty = '';
					$PROMISE_DATE = '';
					$note = '';
					foreach ($PDList as $value ) {
						$index++;
						
						$SO_Line = trim($value['SO_Line']);
						$SO_Line_Qty = trim($value['SO_Line_Qty']);
						$PROMISE_DATE = trim($value['PROMISE_DATE']);
						$note = trim($value['Note']);
		
						$htmls .= '<tr>';
							$htmls .= '<td class="border-show" >'. $index .'</td>';
							$htmls .= '<td class="border-show">'. $SO_Line .'</td>';
							$htmls .= '<td class="border-show">'. number_format($SO_Line_Qty) .'</td>';
							$htmls .= '<td class="border-show">'. $PROMISE_DATE .'</td>';
							$htmls .= '<td class="border-show" style="height:18px;" >'. $note .'</td>';
						$htmls .= '</tr>';
					}
					
				$htmls .= '</tbody>';
			$htmls .= '</table>';
		
		
			// show data	
				echo $htmls; exit();

		}
		
	}
	