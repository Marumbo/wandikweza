<?php
require_once('AfricasTalkingGateway.php');
include('init_sandbox.php');
//include('variables.php');

// connect to db
$con = mysqli_connect($hostname,$username,$password,$dbname) or die("Service is down");

// Check connection
if (!$con)
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    $response =  "END Service down Database contact admin";
}

$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];

//todo use phoneNumber to id health worker for wandikweza monitoring and tracking
//todo and limit access through the number
//todo add sms to health worker too



$textExplode = explode("*",$text);
$countOfArray = count($textExplode);

$userResponse = $textExplode[$countOfArray-1];

$menu_details= mysqli_query($con, "SELECT menu_number FROM $menu");

$menu_numDb = $menu_details -> fetch_assoc();

$menu_num = $menu_numDb['menu_number'];
// get menu where session number is equal
// check user menu if # or M if not continue as user input
//have as a function for aesthetics!
function checkResponse ($userResponse, $menu_num, $con,$menu,$sessionId)
{
    $userResponse;

    if ($userResponse == "#" && $menu_num > 1)
    {
        $menu_num -= 1;
        mysqli_query($con, "UPDATE $menu SET `menu_number` = $menu_num WHERE `session_id` = $sessionId");

    }
    else if ($userResponse == "m" || $userResponse == "M")
    {
        mysqli_query($con, "UPDATE $menu SET `menu_number` = 1 WHERE `session_id` = $sessionId");
    }
    else if ($userResponse != "") {

        $menu_num += 1;
        mysqli_query($con, "UPDATE $menu SET `menu_number` = $menu_num WHERE `session_id` = $sessionId");
    }
    else if ($userResponse == "")
    {
        mysqli_query($con, "UPDATE $menu SET `menu_number` = $menu_num WHERE `session_id` = $sessionId");
    }

    $menu_details = mysqli_query($con, "SELECT `menu_number` FROM $menu WHERE `session_id` = $sessionId ");
    $menu_numDb = mysqli_fetch_assoc($menu_details);
    $menu_numdb = $menu_numDb['menu_number'];

    return $menu_numdb;
}

$menu_number = checkResponse ($userResponse, $menu_num, $con,$menu,$sessionId);


switch($menu_number) {

    case 0:
        //home screen and first name entry
        if ($textExplode[0] == "") {

            //first menu ask for text string will be empty
            // start with con api rules
            $response = "CON Welcome Wandikweza \n";
            $response .= "Please enter Client ID:";
        } else if ($textExplode[0] != "") {
            // first entry will be client id
            //proceed to check if client id in db
            $client_id = $textExplode[0];
            $check_client1 = mysqli_query($con, "SELECT `client_id` FROM $usertable WHERE `client_id` = $client_id");
            $check_client = mysqli_fetch_assoc($check_client1);

            if (mysqli_num_rows($check_client1) >= 1) {

                // fetch client name
                //update menu ini db acccording to session id
                $num = 1;
                mysqli_query($con, "UPDATE $menu SET `menu_number` = $num WHERE `session_id` = $sessionId");


                //storing details from database into variables to use later for sms
                $client_id = $check_client['client_id'];
                $first_name = $check_client['first_name'];
                $phoneNumberdb = $check_client['phoneNumber'];
                $response = "CON Client in Database";

            } else {
                //first entry for client details enters id, maybe self generated in future
                //stored in $textExplode[1]
                $response = "CON Client not in system \n";
                $response .= "Enter client id";

               // $client_id_db = $textExplode[1];

                if ($countOfArray == 2)
                {
                    // client first name stored in $textExplode[2]
                    //$client_id_db = $textExplode[1];
                    $response = "CON Enter client first name";



                } else if ($countOfArray == 3)
                {

                    //$first_name = $textExplode[$countOfArray -1];
                    // client first name stored in $textExplode[3]
                    $response = "CON Enter client last name";



                } else if ($countOfArray == 4)
                {
                    //$last_name = $textExplode[$countOfArray-1];
                    // client first name stored in $textExplode[4]
                    $response = "CON Enter client name of spouse";



                } else if ($countOfArray == 5)
                {
                    //$name_spouse = $textExplode[$countOfArray-1];
                    // client date of birth stored in $textExplode[5]
                    $response = "CON Enter client date of birth";



                } else if ($countOfArray == 6)
                {
                    //$date_of_birth = $textExplode[$countOfArray-1];
                    // client Sex stored in $textExplode[6]
                    $response = "CON Enter client Sex (Male or Female)";



                } else if ($countOfArray == 7)
                {
                    //$sex = $textExplode[$countOfArray-1];
                    // client no_of children stored in $textExplode[7]
                    $response = "CON Enter client no of children ";



                } else if ($countOfArray == 8)
                {
                    //$no_children = $textExplode[$countOfArray-1];
                    // client date of start stored in $textExplode[8]
                    // in future db generated
                    $response = "CON Enter client date of start";



                } else if ($countOfArray == 9)
                {
                    //$date_of_start = $textExplode[$countOfArray-1];
                    // client phone number stored in $textExplode[9]
                    $response = "CON Enter client phone number";



                } else if ($countOfArray == 10)
                {

                    $client_id_db = $textExplode[1];
                    $first_name = $textExplode[2];
                    $last_name = $textExplode[3];
                    $name_spouse = $textExplode[4];
                    $date_of_birth = $textExplode[5];
                    $sex = $textExplode[6];
                    $no_children = $textExplode[7];
                    $date_of_start = $textExplode[8];
                    $phoneNumberdb = $textExplode[9];
                    // when count is at ten all details entered store in db send meessage to Health worker
                    // send into db

                    $insertClient = "INSERT INTO $usertable(`client_id`,`first_name`,`last_name`,`name_spouse`,`date_of_birth`,`sex`,`no_children`,`date_of_start`,`phoneNumber`) 
                                      VALUES ('".$client_id_db."','".$first_name."','".$last_name."','".$name_spouse."','".$date_of_birth."','".$sex."','".$no_children."','".$date_of_start."','".$phoneNumberdb."')";

                    $recipients = $phoneNumber;

                    $message = "You have successfully recorded {$first_name}, into the records";


                    $gateway = new AfricasTalkingGateway($userSMS, $apiKey, "sandbox"); // test environment requires flagging sandbox

                    if (mysqli_query($con, $insertClient)) {
                        try {
                            // Thats it, hit send and we'll take care of the rest.
                            $results = $gateway->sendMessage($recipients, $message, $sender);
                            $response = "END Successfully recorded client \n";


                        } catch (AfricasTalkingGatewayException $e) {
                            // $response = "Encountered an error while sending SMS: ".$e->getMessage();
                            $response = "END Successfully recorded client but SMS was not sent \n";
                        }

                    } else {
                        $response = "END Failed to record client please try again later or contact admin";
                    }

                }


            }
        }

    break;

    case 1:
        $response = "CON Welcome to Wandikweza Main menu \n";
        $response .= "1. Family planning \n";
        $response .= "2. Maternal & Child Health \n";
        $response .= "3. Malaria \n";

    break;

    case 2:
        if($textExplode[$countOfArray-1] == 1)
        {
            //for family planning

            $response = "CON Enter date of  visit \n"; //in future will be time stamp
            $response .= "# back \n ";
            $response .= "M to go to main menu";


        }

        else if($textExplode[$countOfArray-1] == 2)
        {
            // for maternal and child health

            $response = "CON Enter Scheduled visit";
            $response .= "# back \n ";
            $response .= "M to go to main menu";


        }

        else if($textExplode[$countOfArray-1] == 3)
        {
            //for malaria
            $response = "CON Enter date of visit";  //in future will be time stamp
            $response .= "# back \n ";
            $response .= "M to go to main menu";

        }
        else
        {
            $response = "CON invalid option \n ";
            $response .= "# back \n ";
            $response .= "M to go to main menu";

        }

    break;

    case 3:
        if($textExplode[$countOfArray-2] == 1)
        {
            //for family planning

            $response = "CON Scheduled date given \n";
            $response .= "# back \n ";
            $response .= "M to go to main menu";

            //storing of previous value as date of visit
            $date_of_visit = $textExplode[$countOfArray-1];


        }

        else if($textExplode[$countOfArray-2] == 2)
        {
            // for maternal and child health

            $response = "CON Quantity ";
            $response .= "# back \n ";
            $response .= "M to go to main menu";
            //storing previous value as scheduled visit date
            $scheduled_visit = $textExplode[$countOfArray-1];


        }

        else if($textExplode[$countOfArray-2] == 3)
        {
            //for malaria
            $response = "CON Quantity";
            $response .= "# back \n ";
            $response .= "M to go to main menu";

            //storing previous entry as date of visit
            $date_of_visit = $textExplode[$countOfArray-1];

        }
        else
        {
            $response = "CON invalid option \n ";
            $response .= "# back \n ";
            $response .= "M to go to main menu";

        }
    Break;

    case 4:
        if($textExplode[$countOfArray-3] ==1)
        {


            //storing previous as scheduled date
            $scheduled_visit = $textExplode[$countOfArray-1];


            // store client id , health worker id, date of visit and scheduled vist to family plannig
            $familyPlanning = "INSERT INTO $family_planning(`client_id`,`health_worker_id`,`date_of_visit`,`scheduled_vist_date`) VALUES ('".$client_id."','".$phoneNumber."','".$date_of_visit."','".$scheduled_visit."')";

            $recipients = $phoneNumberdb;

            $message = "The next scheduled visit date for {$first_name}, is on {$scheduled_visit}";


            $gateway = new AfricasTalkingGateway($userSMS, $apiKey, "sandbox"); // test environment requires flagging sandbox

            if (mysqli_query($con, $familyPlanning)) {
                try {
                    // sending the sms
                    $results = $gateway->sendMessage($recipients, $message, $sender);
                    $response = "END Thank you for using the application";


                } catch (AfricasTalkingGatewayException $e) {
                    // $response = "Encountered an error while sending SMS: ".$e->getMessage();
                    $response = "END Reminder message sending encountered an error but info stored in database \n";
                    $response .= "Contact administrator";
                }

            } else {
                $response = "END Failed to record details in system contact admin";
            }

            $delete_menu = mysqli_query($con, "DELETE FROM $menu WHERE `session_id` = $sessionId");
        }
        else if($textExplode[$countOfArray-3] ==2)
        {
            $response = "END Thank you for using the application";

            // storing previous entry as quantity
            $quantity = $textExplode[$countOfArray-1];

            // store client id , health worker id, date of visit and scheduled vist to family plannig
            $maternalChildHealth = "INSERT INTO $maternal_child_health (`client_id`,`health_worker_id`,`scheduled_visit_date`,`quantity`) VALUES ('".$client_id."','".$phoneNumber."','".$scheduled_visit."','".$quantity."')";

            $recipients = $phoneNumberdb;

            $message = "The next scheduled visit date for {$first_name}, is on {$scheduled_visit}";


            $gateway = new AfricasTalkingGateway($userSMS, $apiKey, "sandbox"); // test environment requires flagging sandbox

            if (mysqli_query($con, $maternalChildHealth)) {
                try {
                    // sending the sms
                    $results = $gateway->sendMessage($recipients, $message, $sender);
                    $response = "END Thank you for using the application";


                } catch (AfricasTalkingGatewayException $e) {
                    // $response = "Encountered an error while sending SMS: ".$e->getMessage();
                    $response = "END Reminder message sending encountered an error but info stored in database \n";
                    $response .= "Contact administrator";
                }

            } else {
                $response = "END Failed to record details in system contact admin";
            }
            $delete_menu = mysqli_query($con, "DELETE FROM $menu WHERE `session_id` = $sessionId");

        }
        else if($textExplode[$countOfArray-3] ==3)
        {
            $response = "END Thank you for using the application";

            //storing previous entry as quantity
            $quantity = $textExplode[$countOfArray-1];


            // store client id , health worker id, date of visit and scheduled vist to family plannig
            $malariaDb = "INSERT INTO $malaria (`client_id`,`health_worker_id`,`scheduled_visit_date`,`quantity`) VALUES ('".$client_id."','".$phoneNumber."','".$scheduled_visit."','".$quantity."')";

            $recipients = $phoneNumberdb;

            $message = "The next scheduled visit date for {$first_name}, is on {$scheduled_visit}";


            $gateway = new AfricasTalkingGateway($userSMS, $apiKey, "sandbox"); // test environment requires flagging sandbox

            if (mysqli_query($con, $malariaDb)) {
                try {
                    // sending the sms
                    $results = $gateway->sendMessage($recipients, $message, $sender);
                    $response = "END Thank you for using the application";


                } catch (AfricasTalkingGatewayException $e) {
                    // $response = "Encountered an error while sending SMS: ".$e->getMessage();
                    $response = "END Reminder message sending encountered an error but info stored in database \n";
                    $response .= "Contact administrator";
                }

            } else {
                $response = "END Failed to record details in system contact admin";
            }
            $delete_menu = mysqli_query($con, "DELETE FROM $menu WHERE `session_id` = $sessionId");

        }
        else
        {
            $response = "END Proceeded from invalid option please try again";
            $delete_menu = mysqli_query($con, "DELETE FROM $menu WHERE `session_id` = $sessionId");

        }

     break;

    default:
        $response = "END Unable to process input try again or contact admin";


}

header('Content-type: text/plain');
echo $response;