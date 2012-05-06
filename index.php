<?php
	include_once "includes/connecttomysql.php";
		$tables = array();
	$fields = array();
	$query = "SHOW TABLES FROM $dbname";
	if($result = $mysqli->query($query)){
		while ($row = $result->fetch_row()){
			array_push($tables, $row[0]);
			$query2 = "SELECT * FROM ".$row[0];
			if($result2 = $mysqli->query($query2)){
				$j=0;
				while ($row2 = $result2->fetch_field()) {
					$currentfield = $result2->current_field;
					$fields[$row[0]][] = $row2->name;
					$fields[$row[0]][$row2->name][0] = $row2->type;
					$j++;
				}
			}
		}
	}
	$mysqli->close();
?>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="js/js.js"></script>
<script type="text/javascript">
	var tables = new Array();
<?php
		echo "$(function() {\n";
		foreach($tables as &$value){
		echo"tables.push(new table('$value', [";
				$notfirst = false;
				$j = 0;
			while($j < count($fields[$value])){
				if($fields[$value][$j] != ''){
					if($notfirst){
						echo ',';
					}
					echo "'".$fields[$value][$j]."'";
					$notfirst = true;
				}
				$j++;
			}
			echo "],[";
				$notfirst = false;
				$j = 0;
			while($j < count($fields[$value])){
				if($fields[$value][$j] != ''){
					if($notfirst){
						echo ',';
					}
					echo "'".$fields[$value][$fields[$value][$j]][0]."'";
					$notfirst = true;
				}
				$j++;
			}
			echo "]));\n";
		}
		echo "});\n";
	?>
</script>
</head>
<body>
<div id='selects'>
Select:
<br style="clear:both;" />
<div style="float:left; margin-right:25px;">
<?php
	echo "Tables:<br />";
	echo "<select size='10' id='tables' onchange='selectTable()'><option value='0' selected>Select a Table</option>";
	foreach($tables as &$value) {
    echo "<option value='$value'>$value</option>";
	}
	echo "</select>";
?></div>
<div  style='float:left; margin-right:25px;'>
Table Fields:
<div id="fields">
</div>
</div>
<div  style='float:left; margin-right:25px;'>
Query Fields:
<div id="queryFields">
</div>
</div>
<br style="clear:both;" />
<br style="clear:both;" />
<input type="button" onclick="addConstraints();" value="Add Constraints" />
</div>
<div id="constraints">
		<span style="border:solid black;" id="constraintQuery"></span>
	<div id="selectConstraints">
		<br style="clear:both;" />
		Constraints:
		<br style="clear:both;" />

		<div  style='float:left; margin-right:25px;'>
		Table Fields:
		<div id="constraintFields">
		</div>
		</div>
		<div  style='float:left; margin-right:25px;'>
		Query Fields:
		<div id="constraintQueryFields">
		</div>
		</div><br style="clear:both;" />
		<input type="button" onclick="editConstraints();" value="Edit Constraints" />
	</div>
	<div id="editConstraints" style="display:none;">
		
	</div>
</div>
<div id="finishQuery" style="display:none;">
	
</div>
</body>
</html>