<?php
	include_once "connecttomysql.php";
	$sql = "SHOW TABLES FROM $dbname";
	$result = mysql_query($sql);
	$tables = array();
	$fields = array();
	while ($row = mysql_fetch_row($result)) {
		array_push($tables, $row[0]);
		$sql2 = "SHOW COLUMNS FROM ".$row[0];
		$result2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_row($result2)) {
			$fields[$row[0]][] = $row2[0];
		}
	}
	echo "<br />";
?>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
	var tables = new Array();
	var query;
	var selectedFields = 0;
	var selectedConstraints = 0;
	var queryFields=new Array();
	var queryConstraints=new Array();
	$(function(){
	$('#constraints').hide();
	});
	
	function table(tableName,fieldNames)
	{
	this.tableName=tableName;
	this.fieldNames=fieldNames;
	}
	Array.prototype.isTable = function(value){
		for(var i=0;i<this.length;i++){
			if (this[i].tableName==value){
				return i;
			}
		}
		return false;
	}
	Array.prototype.setField = function(value1,value2){
		for(var i=0;i<this.length;i++){
			if (this[i]==value1){
				this[i]=value2;
				return true;
			}
		}
		return false;
	}
	<?php
		echo "$(function() {\n";
		foreach($tables as &$value){
		echo"tables.push(new table('$value', [";
				$notfirst = false;
			foreach($fields[$value] as &$value2){
				if($notfirst){
					echo ',';
				}
				echo "'".$value2."'";
				$notfirst = true;
			}
			echo "]));\n";
		}
		echo "});\n";
	?>
	function selectTable(){
		selectedFields = 0;
		selectedConstraints = 0;
		queryFields.length = 0;
		queryConstraints.length = 0;
		var thisTable = tables.isTable($('#tables').val());
		$('#fields').html('');
		$('#queryFields').html('');
		for(var i = 0; i < tables[thisTable].fieldNames.length; i++){
			$('#fields').append('<a style="cursor:pointer;color:red;" onclick="addThisField(\''+tables[thisTable].fieldNames[i]+'\')">'+tables[thisTable].fieldNames[i]+'</a><br />');
		}
		$('#constraintFields').html('');
		$('#constraintQueryFields').html('');
		for(var i = 0; i < tables[thisTable].fieldNames.length; i++){
			$('#constraintFields').append('<a style="cursor:pointer;color:red;" onclick="addThisConstraintField(\''+tables[thisTable].fieldNames[i]+'\')">'+tables[thisTable].fieldNames[i]+'</a><br />');
		}
	}
	function addThisField(value){
			selectedFields++;
			queryFields.push(value);
			console.log
			$('#queryFields').append('<span id="query'+value+'"><a style="cursor:pointer;color:red;" onclick="removeThisField(\''+value+'\',\'field\')">'+value+'</a><br /></span>');
	}
	function addThisConstraintField(value){
		selectedConstraints++;
			queryConstraints.push(value);
		$('#constraintQueryFields').append('<span id="constraint'+value+'"><a style="cursor:pointer;color:red;" onclick="removeThisField(\''+value+'\',\'constraint\')">'+value+'</a><br /></span>');
	}
	function removeThisField(value,type){
		var thisValue = '#';
		if(type=='field'){
			thisValue+="query"+value;
		}else{
			thisValue+="constraint"+value;
		}
		$(thisValue).remove();
		if(type=='field'){
			selectedFields--;
			queryFields.setField(value,'');
		} else{
			selectedConstraints--;
			queryConstraints.setField(value,'');
		}
	}
	function addConstraints(){
		if ($('#tables').val() != 0){
			if(selectedFields==0){
				query = "SELECT * FROM " + $('#tables').val();
			} else {
				query = "SELECT ";
				var first=true;
				for(var i=0; i< queryFields.length; i++){
					if(queryFields[i]!=''){
						if (!first){
							query=query+", ";
						}
						query = query + '`' + queryFields[i] + '`';
						first = false;
					}
				}
				query =query+ " FROM `" + $('#tables').val() + '`';
			}
			$('#constraintQuery').html(query);
			$('#selects').hide();
			$('#constraints').show();
		} else {
		alert('Please select a table first.');
		}
	}
	function editConstraints(){
		$('#selectConstraints').hide()
		$('#editConstraints').show()
		for(var i=0; i< queryConstraints.length; i++){
			if(queryConstraints[i]!=''){
				$('#editConstraints').append('<input type="button" onclick="$(\'#'+queryConstraints[i]+'hidden\').val(\'constant\');$(\'#'+queryConstraints[i]+'constant\').attr(\'disabled\', \'disabled\');$(\'#'+queryConstraints[i]+'constantBox\').show();$(\'#'+queryConstraints[i]+'post\').removeAttr(\'disabled\');$(\'#'+queryConstraints[i]+'get\').removeAttr(\'disabled\');" id="'+queryConstraints[i]+'constant" value="Constant" /><input type="button" onclick="$(\'#'+queryConstraints[i]+'hidden\').val(\'post\');$(\'#'+queryConstraints[i]+'constantBox\').hide();$(\'#'+queryConstraints[i]+'post\').attr(\'disabled\', \'disabled\');$(\'#'+queryConstraints[i]+'get\').removeAttr(\'disabled\');$(\'#'+queryConstraints[i]+'constant\').removeAttr(\'disabled\');" id="'+queryConstraints[i]+'post" value="Post" /><input type="button" id="'+queryConstraints[i]+'get" value="Get" onclick="$(\'#'+queryConstraints[i]+'hidden\').val(\'get\');$(\'#'+queryConstraints[i]+'constantBox\').hide();$(\'#'+queryConstraints[i]+'get\').attr(\'disabled\', \'disabled\');$(\'#'+queryConstraints[i]+'constant\').removeAttr(\'disabled\');$(\'#'+queryConstraints[i]+'post\').removeAttr(\'disabled\');" /> '+queryConstraints[i]+'<span id="'+queryConstraints[i]+'constantBox" style="display:none;"> = <input type="text" id="'+queryConstraints[i]+'constantBoxText" /></span><input type="hidden" id="'+queryConstraints[i]+'hidden" value="" /><br />')
			}
		}
		$('#editConstraints').append('<input type="button" onclick="finishQuery();" value="Finish Query" />');
	}
	function finishQuery(){
		$('#constraints').hide();
		$('#finishQuery').show();
		if(selectedConstraints==0){
			} else {
				var first=true;
				for(var i=0; i< queryConstraints.length; i++){
					if(queryFields[i]!=''){
						var constraintType = '#'+queryConstraints[i]+'hidden';
						var constraintBox = '#'+queryConstraints[i]+'constantBoxText';
						if($(constraintType).val()=='constant'){
							if (first){
								query+=" WHERE "+queryConstraints[i]+" = `"+$(constraintBox).val()+"`";
							}else{
								query+=" and `"+queryConstraints[i]+"`";
							}
						}else if($(constraintType).val()=='post'){
							if(first){
								query+=" WHERE "+queryConstraints[i]+" = `\".$_POST['"+queryConstraints[i]+ "'].\"`";
							}else{
								query+=" AND "+queryConstraints[i]+" = `\".$_POST['"+queryConstraints[i]+ "'].\"`";
							}
						}else if($(constraintType).val()=='get'){
							if(first){
								query+=" WHERE "+queryConstraints[i]+" = `\".$_GET['"+queryConstraints[i]+ "'].\"`";
							}else{
								query+=" AND "+queryConstraints[i]+" = `\".$_GET['"+queryConstraints[i]+ "'].\"`";
							}
						}
						first = false;
					}
				}
			}
		$('#finishQuery').append('<div style="width 902px;margin:0 auto;">"<textarea style="width:900px;height:600px;">&lt;?php\n\tif(isset($_POST["method"]){\n\t\tswitch($_POST["method"]){\n\t\t\tcase "get'+$('#tables').val()+'":\n\t\t\t\t$sql="'+query+'";\n\t\t\t\tbreak;\n\t\t}\n\t}\n ?&gt;</textarea></div>');
	}
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