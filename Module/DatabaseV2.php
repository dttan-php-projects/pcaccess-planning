<?php
    date_default_timezone_set("Asia/Bangkok");

	function _conn($db=null){
		if ($db==null) $db = "au_avery_pc";
		$conn = mysqli_connect("147.121.59.138","planning","PELS&Auto@{2020}", $db);
		$conn->query("SET NAMES 'utf8'");

		return $conn;
	}

	function dbMiConnect246($db=null){
		if ($db==null) $db = "avery";
		$conn = mysqli_connect("147.121.59.246","planning","PELS&Auto@{2020}",$db);
		$conn->query("SET NAMES 'utf8'");

		return $conn;

	}

	function dbMiConnect252($db=null){
		if ($db==null) $db = "au_avery_pc";
		$conn = mysqli_connect("147.121.73.252","planning","PELS&Auto@{2020}",$db);
		$conn->query("SET NAMES 'utf8'");

		return $conn;

	}

	function MiQuery($Query,$conn = null) {
		if($conn == null) { $conn = _conn(); }
		$result = $conn->query($Query);		
		if(!$result) {
			echo $conn->error;
			return $conn->error;
		} else {
			return mysqli_fetch_all($result,MYSQLI_ASSOC);
		}
	}		

	function MiQueryScalar($Query,$conn = null)
	{
		if($conn == null) { $conn = _conn(); }
		$result = $conn->query($Query);	
		if(!$result) {
			echo $conn->error;
			return $conn->error;
		} else {
			$row = mysqli_fetch_assoc($result);
			if($row != null) {
				foreach($row as $K => $V){
					return $V;
				}
			} else {
				return null;
			}
		}
	}

	function MiNonQuery($Query,$conn = null)
	{
		if($conn == null) { $conn = _conn(); }
		if(!$conn->query($Query)){
			echo $conn->error  . "-" . $Query;
			return $conn->error;
		} else {
			return "OK";
		}
	}

	

	function ExecNonQuery($Query,$conn = null) {
		if($conn == null) { $conn = _conn(); }

		if(!$conn->query($Query)) {
			echo $conn->error  . "-" . $Query;
			return false;
		} else return true;
	}
	
?>