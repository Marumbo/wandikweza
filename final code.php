<?php

require_once('AfricasTalkingGateway.php');
require('con_details.php');



$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];


checkForMenu($textExplode,$countOfArray);
$adminNumber = '+265996282948';


$first_name ="";
$last_name = "";
$email_address = "";

$textExplode = explode("*",$text);
$countOfArray = count($textExplode);


// connect to db
$con = mysqli_connect($hostname,$username,$password,$dbname) or die("Service is down");
// Check connection
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


//home screen and first name entry
else if ($textExplode[0] == "" ) {
    // start with con api rules
    $response = "CON Welcome to the Before I Sleep competition \n";
    $response .= "1 : Register for Competition \n";
    $response .= "2 : About this competition \n";
    $response .= "3 : About this app \n";
    $response .= "4 : Exit \n";
}

else if ($textExplode[0] == "1" && $countOfArray== 1) {
    $checkNumber = mysqli_query($con,"SELECT * FROM $usertable WHERE phoneNumber = $phoneNumber");

// check if number is registered already if registred end session with message if not go to home

// ToDO check entries of names and emails to ensure lenght maybe?

    if (mysqli_num_rows($checkNumber)>= 1 && $phoneNumber != $adminNumber)
    {
        $response = "END Welcome to Before I sleep. \n";
        $response .= "This number has already been registered.\n";
        $response .= "Thank you \n";
        $response .= "--------------------------- \n";
        $response .= "Developed by Marumbo Sichinga\n";
        $response .= " Marumbok@gmail.com, +265 996 282 948 \n";
        $response .= " And is powered by Africa's talking  \n";
    }

    else{

        // after home screen last name dont forget CON
        $response = "CON Please enter your first name: \n";
        $response .= " * Back \n";
        $response .= " # Main menu";

    }
}

else if ($textExplode[0] =="1" && $countOfArray == 2) {
    // third step is to get email address
    $response = "CON Please enter your surname: \n";
    $response .= " * Back \n";
    $response .= " # Main menu";


}
else if ($textExplode[0] =="1" && $countOfArray == 3) {
    // third step is to get email address
    $response = "CON Please enter your email: \n";
    $response .= " * Back \n";
    $response .= " # Main menu";

}
else if ($textExplode[0] =="1" && $countOfArray == 4) {
    // competition question
    $response = "CON When and where is Suffix launching his album? \n";
    $response .= " * Back \n";

    $response .= " # Main menu";

}
else if ($textExplode [0] == "1" && $countOfArray == 5) {
    // last screen to Thank them and send sms of thank you. submit data to db
    $emailExplode = explode("*",$text);
    $first_name =$emailExplode[1];
    $last_name =$emailExplode[2];
    $email =$emailExplode[3];
    $answer = $emailExplode[4];





    $insertContestant = "INSERT INTO $usertable(`first_name`,`last_name`,`email`,`phoneNumber`,`answer`) 
      VALUES ('$first_name','$last_name','$email','$phoneNumber','$answer')";

    $recipients = $phoneNumber;

    $message = "Thank you,{$first_name}, for participating. See you at the launch, CD'S will be available at 2,000mwk.";


    $gateway  = new AfricasTalkingGateway($userSMS, $apiKey,"sandbox"); // test environment requires flagging sandbox

    if(mysqli_query($con,$insertContestant))
    {
        try
        {
            // Thats it, hit send and we'll take care of the rest.
            $results = $gateway->sendMessage($recipients, $message,$sender);
            $response = "END Thank you for participating, see you at the launch \n";
            $response .= "Winners will be announced at the launch";


        }
        catch ( AfricasTalkingGatewayException $e )
        {
            // $response = "Encountered an error while sending SMS: ".$e->getMessage();
            $response = "END Thank you for participating, see you at the launch \n";
        }



    } else {

        $response = "END You entered this competition already!";

    }


}
else if ($textExplode[0] =="2"){
    $response = "CON Welcome to the Before I Sleep competition \n";
    $response .= "Enter your details \n";
    $response .= "To stand a chance of Winning 'Before i sleep'  \n";
    $response .= "The album by Suffix and other prizes \n";
    $response .= " * Back \n";
    $response .= " # Main menu";


}
else if ($textExplode[0] =="3"){
    $response = " CON This application was developed by Marumbo Sichinga\n";
    $response .= "Marumbok@gmail.com, +265 996 282 948 \n";
    $response .= "And is powered by Africa's talking  \n";
    $response .= " * Back \n";
    $response .= " # Main menu";


}
else if($textExplode[0] == "4"){
    $response = "END Thank you, see you at the launch \n";
}

else{
    $response = "END Invalid option try again \n";
}



header('Content-type: text/plain');
echo $response;





