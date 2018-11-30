<?php
require_once('./var.php');
require_once('./nusoap/lib/nusoap.php');

    $param = array('ReversalRequest'=>array
    (

        'language' => urlencode('02'),
        'trxDateTime' => urlencode(time()),
        'origTrxDateTime' => urlencode(time()),
        'transmissionDateTime' => urlencode(time()),
        'origTransmissionDateTime' => urlencode(time()),
        'companyCode' => urlencode(1001),
        'channelID' => urlencode(2),
        'terminalID' => urlencode(2),
        'billKey1' => $_POST['bill'],
        'billKey2' => null,
        'billKey3' => null,
        'paymentAmount' => urlencode(353000),
        'currency' => urlencode(360),
        'transactionID' => urlencode(2673099),
        'reference1' => null,
        'reference2' => null,
        'reference3' => null

    ));

$client = new nusoap_client(LINK_WSDL, true);
// Check for an error
$err = $client->getError();
if ($err) {
    // Display the error
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    // At this point, you know the call that follows will fail
}
// Call the SOAP method
$person = array();





$result = $client->call('reverse', $param);


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

