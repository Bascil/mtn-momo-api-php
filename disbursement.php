<?

//put your API Key and Secret in these two variables.
define('USER_ID', ''); // Disbursement User ID
define('API_KEY', ''); // Disbursement API Key
define('DISBURSEMENT_SUBSCRIPTION_KEY', ''); // Disbursement Subscription KeyKey


//Test here
//echo disburse();

//When called this function will request an Access Token 
function get_accesstoken(){ 

    $credentials = base64_encode(USER_ID.':'.API_KEY);

    $ch = curl_init("https://sandbox.momodeveloper.mtn.com/disbursement/token/");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Basic '.$credentials,
            'Ocp-Apim-Subscription-Key: '.DISBURSEMENT_SUBSCRIPTION_KEY
        )
    );
    
    $response = curl_exec($ch); 
    $response = json_decode($response);

    $access_token = $response->access_token;
    // The above $access_token expires after an hour, find a way to cache it to minimize requests to the server
    if(!$access_token){
        throw new Exception("Invalid access token generated");
        return FALSE;
    }
    return $access_token;

  }

  // request payment from customer
  function disburse(){

    $access_token = get_accesstoken();
    $endpoint_url = 'https://sandbox.momodeveloper.mtn.com/disbursement/v1_0/transfer';
  
    # Parameters 
    $data = array( 
          "amount" => "3000", 
          "currency" => "EUR", //default for sandbox
          "externalId" => "123456", //reference number

          "payee" => array(

              "partyIdType" => "MSISDN",
              "partyId"     => "46733123453"  //user phone number, these are test numbers)  
          ),

          "payerMessage"=> "Funds Transfer",
          "payeeNote"=> "We have transfered funds"


        );

    $data_string = json_encode($data);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $endpoint_url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    //curl_setopt($curl, CURLOPT_TIMEOUT, 50);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',  //optional
            'Authorization: Bearer '.$access_token, //optional
            //'X-Callback-Url: https://anzilasandbox.ngrok.io', //optional, not required for sandbox
            'X-Reference-Id: '.get_uuid(),
            'X-Target-Environment: sandbox',
            'Ocp-Apim-Subscription-Key: '.DISBURSEMENT_SUBSCRIPTION_KEY,

        )
    );
  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $curl_response = curl_exec($curl); //will respond with HTTP 202 Accepted 
    // close curl resource to free up system resources 
    curl_close($curl);
}
  

function get_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

