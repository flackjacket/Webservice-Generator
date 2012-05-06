<?php
	$dbname = 'flackwebservice';
	$host = "flackwebservice.db.9314520.hostedresource.com";
	$user = "flackwebservice";
	$pass = "Wtbpa55word!";
	$mysqli = new mysqli($host, $user, $pass, $dbname);

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s<br />", mysqli_connect_error());
		exit();
	}  

	$tables = array();
	$fields = array();
	$query = "SHOW TABLES FROM $dbname";
	if($result = $mysqli->query($query)){
		while ($row = $result->fetch_row()){
			array_push($tables, $row[0]);
			$query2 = "SELECT * FROM ".$row[0];
			if($result2 = $mysqli->query($query2)){
				$i=0;
				while ($row2 = $result2->fetch_field()) {
					$currentfield = $result2->current_field;
					$fields[$row[0]][] = $row2->name;
					$fields[$row[0]][$row2->name][0] = $row2->type;
					$i++;
				}
			}
		}
	}
	
echo "$(function() {/n";
		foreach($tables as &$value){
		echo"tables.push(new table('$value', [";
				$notfirst = false;
				$i = 0;
			while($i < count($fields[$value])){
				if($fields[$value][$i] != ''){
					if($notfirst){
						echo ',';
					}
					echo "'".$fields[$value][$i]."'";
					$notfirst = true;
				}
				$i++;
			}
			echo "],[";
				$notfirst = false;
				$i = 0;
			while($i < count($fields[$value])){
				if($fields[$value][$i] != ''){
					if($notfirst){
						echo ',';
					}
					echo "'".$fields[$value][$fields[$value][$i]][0]."'";
					$notfirst = true;
				}
				$i++;
			}
			echo "]));/n";
		}
		echo "});/n";
?>