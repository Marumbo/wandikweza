<?php
//database connection details
$hostname="localhost";
$username="maru2";
$password="2Greshom";
$dbname="wandikweza";


// tables in database
$usertable="client_info";
$menu = "menu_1";
$malaria ="malaria";
$family_planning = "family_planning";
$maternal_child_health = "maternal_child_health";


$con = mysqli_connect($hostname,$username,$password,$dbname) or die("Service is down");

$sessionId = "kdooijsoijd1234";
$text = "";//"*123*1*23june*23may*m";

if ($con)
{
    $menu_init = 1;
// add session id and menu number 1 to db
//initialization of session id and menu_number to zeo
    $insertSessionAndMenuInit = "INSERT INTO $menu(`session_id`,`menu_number`) VALUES ('$sessionId','$menu_init')";
    mysqli_query($con ,$insertSessionAndMenuInit);

}
else
{
    ///echo "Failed to connect to MySQL: " . mysqli_connect_error();
    $response =  "END Service down Database contact admin";
}





$textExplodeinit = explode("*",$text);
$countOfArray = count($textExplodeinit);
echo "count of array".$countOfArray."\r\n";

$userResponse = $textExplodeinit[$countOfArray-1];

$menu_details= mysqli_query($con, "SELECT menu_number FROM $menu WHERE `session_id` = '$sessionId'");

$menu_numDb = $menu_details -> fetch_assoc();

$menu_num = $menu_numDb['menu_number'];
echo "menu number".$menu_num."\r\n";
// get menu where session number is equal
// check user menu if # or M if not continue as user input
//have as a function for aesthetics!
function checkResponse ($userResponse, $menu_num, $con,$menu,$sessionId,$countOfArray)
{
if ($userResponse == "#" && $countOfArray > 1)
{
$menu_num -= 1;
mysqli_query($con, "UPDATE $menu SET `menu_number` = $menu_num WHERE `session_id` = '$sessionId'");

}
else if ($userResponse == "m" || $userResponse == "M")
{
mysqli_query($con, "UPDATE $menu SET `menu_number` = 1 WHERE `session_id` = '$sessionId'");
}
else if ($userResponse != "" && $countOfArray > 1 ) {

$menu_num += 1;
mysqli_query($con, "UPDATE $menu SET `menu_number` = $menu_num WHERE `session_id` = '$sessionId'");
}

else if ($userResponse != "" && $countOfArray == 1)
{
mysqli_query($con, "UPDATE $menu SET `menu_number` = $menu_num WHERE `session_id` = '$sessionId'");
}

else if ($userResponse == "")
{
mysqli_query($con, "UPDATE $menu SET `menu_number` = $menu_num WHERE `session_id` = '$sessionId' ");
}

$menu_details = mysqli_query($con, "SELECT `menu_number` FROM $menu WHERE `session_id` = '$sessionId' ");
$menu_numDb = mysqli_fetch_assoc($menu_details);
$menu_numdb = $menu_numDb['menu_number'];

return $menu_numdb;
}


$menu_number = checkResponse ($userResponse, $menu_num, $con,$menu,$sessionId,$countOfArray);

$textExplodeHash = array_diff($textExplodeinit,array("#"));
foreach($textExplodeHash as $k => $v) {

    if((empty($v) || $v == "")&& $k != 0)
        unset($textExplodeHash[$k]);

}
$textExplode =array_values($textExplodeHash);
$countOfArray = count($textExplode);
echo "Count of array 2".$countOfArray."\r\n";
echo "menu number after funciton".$menu_number."\r\n";
echo "last value".$textExplode[$countOfArray-1]."\r\n";
echo "last value".$textExplode[$countOfArray-2]."\r\n";
echo "last value".$textExplode[$countOfArray-3]."\r\n";

