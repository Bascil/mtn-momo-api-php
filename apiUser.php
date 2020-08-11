<?php

//Test here
//et_api_user();

function get_api_user()
{

    $token = get_uuid();
    $apiKey = ''; //Your api key

    $post_data = array(
        'providerCallbackHost' => 'https://localhost',
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_URL, 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'X-Reference-Id: ' . $token,
            'Ocp-Apim-Subscription-Key: ' . $apiKey,
        )
    );

    $result = curl_exec($curl);
    if (!$result) {die("Connection Failure");}
    curl_close($curl);
    echo $result;

}

function get_uuid()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}