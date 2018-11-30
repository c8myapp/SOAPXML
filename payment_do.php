<?php

require_once('./nusoap/lib/nusoap.php');
$param = array('InquiryRequest'=>array
(

    'language' => urlencode('02'),
    'trxDateTime' => urlencode(time()),
    'transmissionDateTime' => urlencode(time()),
    'companyCode' => urlencode(1001),
    'channelID' => 2,
    'billKey1' => $_POST['bill'],
    'billKey2' => null,
    'billKey3' => null,
    'reference1' => null,
    'reference2' => null,
    'reference3' => null


));

$client = new nusoap_client('http://localhost/h2hns/server.php?wsdl', true);
// Check for an error
$err = $client->getError();
if ($err) {
    // Display the error
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    // At this point, you know the call that follows will fail
}
// Call the SOAP method
$person = array();





$result = $client->call('inquiry', $param);


if ($client->fault) {
    echo '<h2>Fault</h2><pre>';
    print_r($result);
    echo '</pre>';
} else {
    // Check for errors
    $err = $client->getError();
    if ($err) {
        // Display the error
        echo '<h2>Error</h2><pre>' . $err . '</pre>';
    } else {
        // Display the result
        echo '<h2>Result</h2><pre>';
        print_r($result);
        echo '</pre>';
    }
}


// Display the request and response
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';

