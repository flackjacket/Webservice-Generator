<?php
$dbname = 'flackwebservice';
mysql_connect("flackwebservice.db.9314520.hostedresource.com", "flackwebservice", "Wtbpa55word!") or die(mysql_error());
mysql_select_db($dbname) or die ("no database");
	$sql;
	$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml';
	
	if(isset($_GET["method"])){
		switch($_GET["method"]){
			case "getblarg":
				$sql="SELECT `id`, `this`, `is`, `sparta` FROM `blarg` WHERE id = ".$_GET['id']." AND this = '".$_GET['this']."'";
				break;
		}
	}

	$result = mysql_query($sql);

	  /* create one master array of the records */
	  $dbResults = array();
	  if(mysql_num_rows($result)) {
		while($dbResult = mysql_fetch_assoc($result)) {
		  $dbResults[] = array('dbResult'=>$dbResult);
		}
	  }

	  /* output in necessary format */
	  if($format == 'json') {
		header('Content-type: application/json');
		echo json_encode(array('dbResults'=>$dbResults));
	  }
	  else {
		header('Content-type: text/xml');
		echo '<results>';
		foreach($dbResults as $index => $dbResult) {
		  if(is_array($dbResult)) {
			foreach($dbResult as $key => $value) {
			  echo '<',$key,'>';
			  if(is_array($value)) {
				foreach($value as $tag => $val) {
				  echo '<',$tag,'>',htmlentities($val),'</',$tag,'>';
				}
			  }
			  echo '</',$key,'>';
			}
		  }
		}
		echo '</results>';
	  }

	  /* disconnect from the db */
	  @mysql_close($link);
  ?>