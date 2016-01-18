<!DOCTYPE html>

<html>

<head>

<script src='Scripts/jquery-2.1.4.min.js'></script>
<script src='Scripts/jquery.datetimepicker.js'></script>

<link rel="stylesheet" type="text/css" href="Scripts/jquery.datetimepicker.css" />

<title>QuickCalendar Entry</title>

<script>

$(document).ready(function() { $('.DateTimePicker').datetimepicker({ format:'Y-m-d H:i', step: 30, theme:'dark', timepicker: true }); });

</script>

</head>

<body>

<?php 

$DBUser = /* Your MySQL User name */
$DBPass = /* Your MySQL Password */

try {
$wcpdo = new PDO("mysql:host=localhost;dbname=WebCal", $DB_User, $DB_Pass);
$wcpdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { echo 'MySQL WebCalendar DB - Error: ' . $e->getMessage(); }

/* Queries. Modify 'User' to reflect your WebCalendar User ID */

$query_Calendar_LastLogID = "SELECT MAX(cal_entry_id) AS CEID, MAX(cal_log_id) AS CLID FROM webcal_entry_log;";
$query_Calendar_AddEntry = "INSERT INTO webcal_entry VALUES (:NextCEID,Null,Null,'User',:Start_Ymd,:Start_His,:Date_Ymd,:Date_His,:Duration,:Start_Ymd,:Start_His,5,'E','P',:Title,Null,Null,Null,:Title);";
$query_Calendar_AddEntryLog = "INSERT INTO webcal_entry_log VALUES (:NextCLID,:NextCEID,'User','User','C',:Date_Ymd,:Date_His,Null);";
$query_Calendar_AddEntryUser = "INSERT INTO webcal_entry_user VALUES (:NextCEID,'User','A',Null,0);";

$stmt_WCLastID = $wcpdo -> prepare($query_Calendar_LastLogID);
$stmt_WCAddEntry = $wcpdo -> prepare($query_Calendar_AddEntry);
$stmt_WCAddEntryLog = $wcpdo -> prepare($query_Calendar_AddEntryLog);
$stmt_WCAddEntryUser = $wcpdo -> prepare($query_Calendar_AddEntryUser);

if(isset($_POST['QuickCalendar'])) {

	$stmt_WCLastID -> execute();
	$row_WCLastID = $stmt_WCLastID -> fetch(PDO::FETCH_ASSOC);

	$NextCEID = $row_WCLastID['CEID'] + 1;
	$NextCLID = $row_WCLastID['CLID'] + 1;
	$Date_Ymd = date("Ymd");
	$Date_His = date("His");
	$QuickStart = strtotime($_POST['QuickStart']);
	$QuickEnd = strtotime($_POST['QuickEnd']);
	$Start_Ymd = gmdate("Ymd", $QuickStart);
	$Start_His = gmdate("His", $QuickStart);
	$Duration = abs($QuickEnd - $QuickStart) / 60;

	$stmt_WCAddEntryUser -> bindParam(':NextCEID', $NextCEID, PDO::PARAM_INT, 8);
	$stmt_WCAddEntryUser -> execute();

	$stmt_WCAddEntryLog -> bindParam(':NextCEID', $NextCEID, PDO::PARAM_INT, 8);
	$stmt_WCAddEntryLog -> bindParam(':NextCLID', $NextCLID, PDO::PARAM_INT, 8);
	$stmt_WCAddEntryLog -> bindParam(':Date_Ymd', $Date_Ymd, PDO::PARAM_STR, 19);
	$stmt_WCAddEntryLog -> bindParam(':Date_His', $Date_His, PDO::PARAM_STR, 19);
	$stmt_WCAddEntryLog -> execute();

	$stmt_WCAddEntry -> bindParam(':NextCEID', $NextCEID, PDO::PARAM_INT, 8);
	$stmt_WCAddEntry -> bindParam(':Date_Ymd', $Date_Ymd, PDO::PARAM_STR, 19);
	$stmt_WCAddEntry -> bindParam(':Date_His', $Date_His, PDO::PARAM_STR, 19);
	$stmt_WCAddEntry -> bindParam(':Duration', $Duration, PDO::PARAM_INT, 5);
	$stmt_WCAddEntry -> bindParam(':Start_Ymd', $Start_Ymd, PDO::PARAM_STR, 19);
	$stmt_WCAddEntry -> bindParam(':Start_His', $Start_His, PDO::PARAM_STR, 19);
	$stmt_WCAddEntry -> bindParam(':Title', $_POST['QuickTitle'], PDO::PARAM_STR, 255);
	$stmt_WCAddEntry -> execute();

	echo "<div class='Notice'>Calendar entry " . $NextCEID . " added!</div>";

}

?>

<div id='QuickCalendar'>
<span>Quick Calendar Entry</span>
<form action='QuickCal.php' method='post'>
<div class='table'>
<div class='tr'>
	<span class='td'><input name='QuickStart' class='DateTimePicker' type='text' style='width: 100px;' /></span>
	<span class='td'><input name='QuickEnd' class='DateTimePicker' type='text' style='width: 100px;' /></span>
</div>
</div>
<span><input name='QuickTitle' type='text' value='' style='width: 175px;' /><button name='QuickCalendar' class='UButton'>Go!</button></span>
</form></div>

</body>

</html>
