var query;
var selectedFields = 0;
var selectedConstraints = 0;
var queryFields=new Array();
var queryConstraints=new Array();
$(function(){
$('#constraints').hide();
});

function table(tableName,fieldNames,fieldTypes)
{
this.tableName=tableName;
this.fieldNames=fieldNames;
this.fieldTypes=fieldTypes;
}
Array.prototype.isTable = function(value){
	for(var j=0;j<this.length;j++){
		if (this[j].tableName==value){
			return j;
		}
	}
	return false;
}
Array.prototype.isID = function(value1,value2){
	for(var j = 0; j < this[value1].fieldNames.length;j++){
		if (this[value1].fieldNames[j]==value2){
			if(this[value1].fieldTypes[j]=='3'){
				return true;
			}
		}
	}
	return false;
}
Array.prototype.setField = function(value1,value2){
	for(var j=0;j<this.length;j++){
		if (this[j]==value1){
			this[j]=value2;
			return true;
		}
	}
	return false;
}
function selectTable(){
	selectedFields = 0;
	selectedConstraints = 0;
	queryFields.length = 0;
	queryConstraints.length = 0;
	var thisTable = tables.isTable($('#tables').val());
	$('#fields').html('');
	$('#queryFields').html('');
	for(var j = 0; j < tables[thisTable].fieldNames.length; j++){
		$('#fields').append('<a style="cursor:pointer;color:red;" onclick="addThisField(\''+tables[thisTable].fieldNames[j]+'\')">'+tables[thisTable].fieldNames[j]+'</a><br />');
	}
	$('#constraintFields').html('');
	$('#constraintQueryFields').html('');
	for(var j = 0; j < tables[thisTable].fieldNames.length; j++){
		$('#constraintFields').append('<a style="cursor:pointer;color:red;" onclick="addThisConstraintField(\''+tables[thisTable].fieldNames[j]+'\')">'+tables[thisTable].fieldNames[j]+'</a><br />');
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
			for(var j=0; j< queryFields.length; j++){
				if(queryFields[j]!=''){
					if (!first){
						query=query+", ";
					}
					query = query + '`' + queryFields[j] + '`';
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
	for(var j=0; j< queryConstraints.length; j++){
		if(queryConstraints[j]!=''){
			$('#editConstraints').append('<input type="button" onclick="$(\'#'+queryConstraints[j]+'hidden\').val(\'constant\');$(\'#'+queryConstraints[j]+'constant\').attr(\'disabled\', \'disabled\');$(\'#'+queryConstraints[j]+'constantBox\').show();$(\'#'+queryConstraints[j]+'post\').removeAttr(\'disabled\');$(\'#'+queryConstraints[j]+'get\').removeAttr(\'disabled\');" id="'+queryConstraints[j]+'constant" value="Constant" /><input type="button" onclick="$(\'#'+queryConstraints[j]+'hidden\').val(\'post\');$(\'#'+queryConstraints[j]+'constantBox\').hide();$(\'#'+queryConstraints[j]+'post\').attr(\'disabled\', \'disabled\');$(\'#'+queryConstraints[j]+'get\').removeAttr(\'disabled\');$(\'#'+queryConstraints[j]+'constant\').removeAttr(\'disabled\');" id="'+queryConstraints[j]+'post" value="Post" /><input type="button" id="'+queryConstraints[j]+'get" value="Get" onclick="$(\'#'+queryConstraints[j]+'hidden\').val(\'get\');$(\'#'+queryConstraints[j]+'constantBox\').hide();$(\'#'+queryConstraints[j]+'get\').attr(\'disabled\', \'disabled\');$(\'#'+queryConstraints[j]+'constant\').removeAttr(\'disabled\');$(\'#'+queryConstraints[j]+'post\').removeAttr(\'disabled\');" /> '+queryConstraints[j]+'<span id="'+queryConstraints[j]+'constantBox" style="display:none;"> = <input type="text" id="'+queryConstraints[j]+'constantBoxText" /></span><input type="hidden" id="'+queryConstraints[j]+'hidden" value="" /><br />')
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
			for(var j=0; j< queryConstraints.length; j++){
				if(queryFields[j]!=''){
					var constraintType = '#'+queryConstraints[j]+'hidden';
					var constraintBox = '#'+queryConstraints[j]+'constantBoxText';
					var isIDField = tables.isID(tables.isTable($('#tables').val()),queryConstraints[j]);
					console.log(isIDField);
					if($(constraintType).val()=='constant'){
						if (first){
							if(isIDField){
								query+=" WHERE "+queryConstraints[j]+" = "+$(constraintBox).val()+'"';
							}else{
								query+=" WHERE "+queryConstraints[j]+" = '"+$(constraintBox).val()+"'";
							}
						}else{
							if(isIDField){
								query+=" and `"+queryConstraints[j]+"` = "+$(constraintBox).val()+'"';
							}else{
								query+=" and `"+queryConstraints[j]+"` = '"+$(constraintBox).val()+"'";
							}
						}
					}else if($(constraintType).val()=='post'){
						if(first){
							if(isIDField){
								query+=" WHERE "+queryConstraints[j]+" = \".$_POST['"+queryConstraints[j]+ "'].\"";
							}else{
								query+=" WHERE "+queryConstraints[j]+" = '\".$_POST['"+queryConstraints[j]+ "'].\"'";
							}
						}else{
							if(isIDField){
								query+=" AND "+queryConstraints[j]+" = \".$_POST['"+queryConstraints[j]+ "'].\"";
							} else {
								query+=" AND "+queryConstraints[j]+" = '\".$_POST['"+queryConstraints[j]+ "'].\"'";
							
							}
						}
					}else if($(constraintType).val()=='get'){
						if(first){
							if(isIDField){
								query+=" WHERE "+queryConstraints[j]+" = \".$_GET['"+queryConstraints[j]+ "'].\"";
							} else {
								query+=" WHERE "+queryConstraints[j]+" = '\".$_GET['"+queryConstraints[j]+ "'].\"'";
							}
						}else{
							if(isIDField){
								query+=" AND "+queryConstraints[j]+" = \".$_GET['"+queryConstraints[j]+ "'].\"";
							} else {
								query+=" AND "+queryConstraints[j]+" = '\".$_GET['"+queryConstraints[j]+ "'].\"'";
							}
						}
					}
					first = false;
				}
			}
		}
		var preQuery = '&lt;?php\n\tinclude_once "includes/connecttomysql.php";\n\t$sql;\n\t$format = strtolower($_GET[\'format\']) == \'json\' ? \'json\' : \'xml\';\n\t';
		var postQuery = '\n\t$result = mysql_query($sql);\n\n\t  /* create one master array of the records */\n\t  $dbResults = array();\n\t  if(mysql_num_rows($result)) {\n\t\twhile($dbResult = mysql_fetch_assoc($result)) {\n\t\t  $dbResults[] = array(\'dbResult\'=&gt;$dbResult);\n\t\t}\n\t  }\n\n\t  /* output in necessary format */\n\t  if($format == \'json\') {\n\t\theader(\'Content-type: application/json\');\n\t\techo json_encode(array(\'dbResults\'=&gt;$dbResults));\n\t  }\n\t  else {\n\t\theader(\'Content-type: text/xml\');\n\t\techo \'&lt;results&gt;\';\n\t\tforeach($dbResults as $index =&gt; $dbResult) {\n\t\t  if(is_array($dbResult)) {\n\t\t\tforeach($dbResult as $key =&gt; $value) {\n\t\t\t  echo \'&lt;\',$key,\'&gt;\';\n\t\t\t  if(is_array($value)) {\n\t\t\t\tforeach($value as $tag =&gt; $val) {\n\t\t\t\t  echo \'&lt;\',$tag,\'&gt;\',htmlentities($val),\'&lt;/\',$tag,\'&gt;\';\n\t\t\t\t}\n\t\t\t  }\n\t\t\t  echo \'&lt;/\',$key,\'&gt;\';\n\t\t\t}\n\t\t  }\n\t\t}\n\t\techo \'&lt;/results&gt;\';\n\t  }\n\n\t  /* disconnect from the db */\n\t  @mysql_close($link);\n  ?&gt;';
		$('#finishQuery').append('<div style="width 902px;margin:0 auto;"><textarea style="width:900px;height:600px;">'+preQuery+'\n\tif(isset($_GET["method"])){\n\t\tswitch($_GET["method"]){\n\t\t\tcase "get'+$('#tables').val()+'":\n\t\t\t\t$sql="'+query+'";\n\t\t\t\tbreak;\n\t\t}\n\t}\n'+postQuery+'</textarea></div>');
}