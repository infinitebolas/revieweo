<?php
	try {
		$db_connection = new PDO(
			"mysql:host=localhost;dbname=revieweo;charset=utf8",
			"root", ""
		);
	} catch (Exception $e) {
		echo $e->getMessage();
	}

?>