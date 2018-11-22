<?php
// Pull in the NuSOAP code
require_once('./nusoap/lib/nusoap.php');
// Create the client instance
$get_method = isset($_GET['m']) ? $_GET['m'] : "inquiry";



/*
 * [0] => reverseResponse reverse(reverse $parameters)
    [1] => paymentResponse payment(payment $parameters)
    [2] => inquiryResponse inquiry(inquiry $parameters)
    [3] => echoTestResponse echoTest(echoTest $parameters)
 * */
$array_method = array('inquiry','payment','reverse','test');
$get_method = isset($_GET['m']) ? $_GET['m'] : "";
$array_key = array_keys($array_method,$get_method);
$method = count($array_key)>0 ? $array_method[$array_key[0]] : "inquiry";


$param = array();
if($method=="inquiry")
{
    $param = array('InquiryRequest'=>array
    (

            'language' => urlencode('02'),
            'trxDateTime' => urlencode(time()),
            'transmissionDateTime' => urlencode(time()),
            'companyCode' => urlencode(1001),
            'channelID' => 2,
            'billKey1' => '123171718104109170211',
            'billKey2' => null,
            'billKey3' => null,
            'reference1' => null,
            'reference2' => null,
            'reference3' => null


    ));

}
else if($method=="payment")
{
    $param = array('PaymentRequest'=>array
    (

            'language' => urlencode('02'),
            'trxDateTime' => urlencode(time()),
            'transmissionDateTime' => urlencode(time()),
            'companyCode' => urlencode(1001),
            'channelID' => urlencode(2),
            'billKey1' => urlencode(1110091000009),
            'billKey2' => null,
            'billKey3' => null,
            'paidBills' => array ('string'=>'01'),
            'paymentAmount' => urlencode(353000),
            'currency' => urlencode(360),
            'transactionID' => urlencode(2673099),
            'reference1' => null,
            'reference2' => null,
            'reference3' => null

    ));
}
else if($method=="reverse")
{
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
        'billKey1' => urlencode(1110091000009),
        'billKey2' => null,
        'billKey3' => null,
        'paymentAmount' => urlencode(353000),
        'currency' => urlencode(360),
        'transactionID' => urlencode(2673099),
        'reference1' => null,
        'reference2' => null,
        'reference3' => null

    ));
}
else if($method=="test")
{
    $param = array('TestEcho'=>array
    (

    ));
}





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





$result = $client->call($method, $param);


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

