<?php
	//set_time_limit (300);
	$target_dir = "Files/";
	$target_file = $target_dir . basename($_FILES["FileToUpload"]["name"]);
	$uploadOk = 1;
	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if file already exists

	// Check file size
    
	if ($_FILES["FileToUpload"]["size"] > 50000000) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	if( $FileType != "txt") {
		echo "Sorry, only txt files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
       
        require("../Module/Database.php");

        $ArrayTitle = array();
        $Turn = 0;
        $StringSO = "";
		$file_handle = fopen($_FILES["FileToUpload"]["tmp_name"], "r");
        while (!feof($file_handle)) {
            $StringText = fgets($file_handle);
            $StringText = str_replace("\r","",$StringText);

            if($Turn == 0 && strpos($StringText,"GLID") !== false)
            {
                $ArrayTitle = explode("\t",$StringText);
                $Turn = 1;
            }
            
        }
        $j = 1;
        $X = 1;
        if($Turn == 1)
        {
            $file_handle = fopen($_FILES["FileToUpload"]["tmp_name"], "r");
            while (!feof($file_handle)) {
                $StringText = fgets($file_handle);
                $StringText = str_replace("\r","",$StringText);
                $Rows = explode("\t",$StringText);
                if(count($Rows) == count($ArrayTitle))
                {
                    $Values = "";
                    if($Rows[0] == "GLID") continue;
                    if (empty($Rows[0]) || ($Rows[0] == ' ') || ($Rows[0] == '') ) continue;
                    
                    for($i = 1; $i < count($ArrayTitle) - 1; $i++)
                    {
                        if(strlen($ArrayTitle[$i]) > 1)
                        {
                            // Trường hợp đổi tên cột hiển thị cho người dùng
                            // Tên cũ: SPM_Remark - mới: Inactive_Reason
                            // Tên cũ: RemarkPage - mới: StringCut_ComboTag
                            


                            $Values = $Values . "," . $ArrayTitle[$i] . " = '".str_replace("\"","",$Rows[$i])."'";
                        }
                    }
                    $sql = "UPDATE access_item_information SET " . substr($Values,1) . " WHERE GLID = '" . $Rows[0] . "';";
                    echo $sql . "<br/>";
                    $X++;
                    MiNonQuery($sql, _conn());
                    if($Rows[0] == "")
                    {
                        $j++;
                    } else
                    {
                        $j = 1;
                    }
                }

                if($j > 10)
                {
                    break;
                }
            }
        }
        echo $X;
        


	}
?> 