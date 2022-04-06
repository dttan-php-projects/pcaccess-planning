<?php 
	require_once ("Database.php");
	$table = "intranet_menu_2";
	if(isset($_GET["PAGE"]) && !isset($_GET["OPEN"])) {
		$Pages = $_GET["PAGE"];
		$row = MiQueryScalar("SELECT REDIRECT FROM $table WHERE CODEPAGE = '$Pages' LIMIT 1", _conn("au_avery") );
		if($row != "") {
			header('Location: /auto/planning/f1/' . $row);
			$Turn = 1;
		}
		if($Turn == 0) header('Location: /Index.php');
	} else if(isset($_GET["PAGE"]) && !isset($_GET["OPEN"])) {
		$Pages = $_GET["PAGE"];
		$row = MiQueryScalar("SELECT REDIRECT FROM $table WHERE CODEPAGE = '$Pages' LIMIT 1", _conn("au_avery") );
		if($row != "") {
			header('Location: /auto/planning/f1/' . $row);
			$Turn = 1;
		}
		if($Turn == 0) header('Location: /Index.php');
	}
?>