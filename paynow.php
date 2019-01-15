<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_POST = json_decode(file_get_contents('php://input'), true);
//pull in the database
require 'dbconfig.php';
require 'Paystack.php';

$paystack = new Paystack('sk_test_af1b02672c3124527de88d9b4ab4df6c72dd23c9');

// Capture Post Data that is coming from the form
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$amount = $_POST['amount'];

//  Connect to the Database using PDO
$dsn = "mysql:host=$host;dbname=$db";
//Create PDO Connection with the dbconfig data
$conn = new PDO($dsn, $username, $password);

//  Check to see if the user is in the database already
$usercheck = "SELECT * FROM paytest WHERE email=?";
// prepare the Query
$usercheckquery = $conn->prepare($usercheck);
//Execute the Query
$usercheckquery->execute(array("$email"));
//Fetch the Result
$usercheckquery->rowCount();
if ($usercheckquery->rowCount() > 0) {
    echo json_encode("user_exists");
} else {
    // Insert the user into the database
    $enteruser = "INSERT into paytest (name, email, phone) VALUES (:name,:email, :phone)";
    //  Prepare Query
    $enteruserquery = $conn->prepare($enteruser);
    //  Execute the Query
    $enteruserquery->execute(
        array(
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        )
    );

    //  Fetch Result
    $enteruserquery->rowCount();
    // Check to see if the query executed successfully
    if ($enteruserquery->rowCount() > 0) {
        
        $trx = $paystack->transaction->initialize(
            [
                'amount'    =>  $amount,
                'firstname' => $name,
                'email'     =>  $email,
                'metadata'  =>  json_encode([
                    'custom_fields' => [
                        'display_name' => 'Mobile Number',
                        'variable_name' => 'mobile_number',
                        'value'=> $phone
                    ]
                ])
            ]
        );

        if(!$trx->status) {
            exit($trx->message);
        }

        header('Location: ' . $trx->data->authorization_url);

        // echo json_encode("success");
    }


}