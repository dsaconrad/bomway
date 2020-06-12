<?php
if($_POST)
{
	$apikey_Mailchip = "0fe36f016ead75c4c9e0c7a6728048e7-us17";
	$subscriber_List = "4a1be9d5ed";
	$memberID 		= "thebomway@gmail.com";
	
	
	//check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
	
		//exit script outputting json data
		$output = json_encode(
		array(
			'type'=>'error', 
			'text' => 'Request must come from Ajax'
		));
		
		die($output);
    } 	
	//check $_POST vars are set, exit if any missing
	if(!isset($_POST["name"]) || !isset($_POST["userEmail"]))
	{
		$output = json_encode(array('type'=>'error', 'text' => 'Input fields are empty!'));
		die($output);
	}

	//Sanitize input data using PHP filter_var().
	$user_Name        = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
	$user_Email       = filter_var($_POST["userEmail"], FILTER_SANITIZE_EMAIL);
	$user_Type		  = filter_var($_POST["companyname"], FILTER_SANITIZE_STRING);
	$user_Country	  = filter_var($_POST["countryselected"], FILTER_SANITIZE_STRING);
	
	//additional php validation
	if(strlen($user_Name)<4) // If length is less than 4 it will throw an HTTP error.
	{
		$output = json_encode(array('type'=>'error', 'text' => 'Name is too short or empty!'));
		die($output);
	}
	if(!filter_var($user_Email, FILTER_VALIDATE_EMAIL)) //email validation
	{
		$output = json_encode(array('type'=>'error', 'text' => 'Please enter a valid email!'));
		die($output);
	}
	if($user_Type != "Individual" && strlen($user_Type)<3) //check emtpy company name
	{
		$output = json_encode(array('type'=>'error', 'text' => 'Please enter Company Name.'));
		die($output);
	}

	//Setting up the array 

	$postData = array(
    "email_address" => $user_Email, 
    "status" => "subscribed", 
    "merge_fields" => array(
    "FNAME"=> $user_Name,
    "COUNTRY"=> $user_Country,
	"USER_TYPE"=> $user_Type)
	);
	//Mailchimp Endpoint
	$ch = curl_init('https://us17.api.mailchimp.com/3.0/lists/'.$subscriber_List.'/members/');
		curl_setopt_array($ch, array(
		    CURLOPT_POST => TRUE,
		    CURLOPT_RETURNTRANSFER => TRUE,
		    CURLOPT_HTTPHEADER => array(
		        'Authorization: apikey '.$apikey_Mailchip,
		        'Content-Type: application/json'
		    ),
		    CURLOPT_POSTFIELDS => json_encode($postData)
		));
		// Send the request
		$response = curl_exec($ch);
		$response_JSON = json_decode($response, TRUE);

	//echo "<script>console.log('" . json_encode($response_JSON) . "');</script>";


		//Verify response and then lets see what happens.
		if ($response_JSON['status'] == 'subscribed') {
			$output = json_encode(array('type'=>'success', 'text' => 'You have been subscribed to our list. We will be in touch shortly.'));
			die($output);
		}
		else if($response_JSON['status'] == 400)
		{
			$output = json_encode(array('type'=>'error', 'text' => 'There was an error in subscribing you. Please contact ús via Email'));
			die($output);
		}


}
?>