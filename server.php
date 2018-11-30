<?php
// Pull in the NuSOAP code
require_once('./nusoap/lib/nusoap.php');
require_once('method_xml.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('hellowsdl2', 'urn:hellowsdl2');
// Register the data structures used by the service

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$server->wsdl->addComplexType(
    'InquiryRequest',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'language' => array('name'=>'language','type'=>'xsd:string'),
        'trxDateTime' => array('name'=>'trxDateTime','type'=>'xsd:string'),
        'transmissionDateTime' => array('name'=>'transmissionDateTime','type'=>'xsd:string'),
        'companyCode' => array('name'=>'companyCode','type'=>'xsd:string'),
        'channelID' => array('name'=>'channelID','type'=>'xsd:string'),
        'billKey1' => array('name'=>'billKey1','type'=>'xsd:string'),
        'billKey2' => array('name'=>'billKey2','type'=>'xsd:string'),
        'billKey3' => array('name'=>'billKey3','type'=>'xsd:string'),
        'reference1' => array('name'=>'reference1','type'=>'xsd:string'),
        'reference2' => array('name'=>'reference2','type'=>'xsd:string'),
        'reference3' => array('name'=>'reference3','type'=>'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'InquiryResponse',
    'complexType',
    'struct',
    'sequence',
    '',

    array(
        'currency' => array('name' => 'currency', 'type' => 'xsd:string'),
        'billInfo1' => array('name' => 'billInfo1', 'type' => 'xsd:string'),
        'billInfo1' => array('name' => 'billInfo1', 'type' => 'xsd:string'),
        'billInfo2' => array('name' => 'billInfo2', 'type' => 'xsd:string'),
        'billInfo3' => array('name' => 'billInfo3', 'type' => 'xsd:string'),
        'billInfo4' => array('name' => 'billInfo4', 'type' => 'xsd:string'),
        'billInfo5' => array('name' => 'billInfo5', 'type' => 'xsd:string'),
        'billInfo6' => array('name' => 'billInfo6', 'type' => 'xsd:string'),
        'billInfo7' => array('name' => 'billInfo7', 'type' => 'xsd:string'),
        'billInfo8' => array('name' => 'billInfo8', 'type' => 'xsd:string'),
        'billInfo9' => array('name' => 'billInfo9', 'type' => 'xsd:string'),
        'billInfo10' => array('name' => 'billInfo10', 'type' => 'xsd:string'),
        'billInfo11' => array('name' => 'billInfo11', 'type' => 'xsd:string'),
        'billInfo12' => array('name' => 'billInfo12', 'type' => 'xsd:string'),
        'billInfo13' => array('name' => 'billInfo13', 'type' => 'xsd:string'),
        'billInfo14' => array('name' => 'billInfo14', 'type' => 'xsd:string'),
        'billInfo15' => array('name' => 'billInfo15', 'type' => 'xsd:string'),
        'billInfo16' => array('name' => 'billInfo16', 'type' => 'xsd:string'),
        'billInfo17' => array('name' => 'billInfo17', 'type' => 'xsd:string'),
        'billInfo18' => array('name' => 'billInfo18', 'type' => 'xsd:string'),
        'billInfo19' => array('name' => 'billInfo19', 'type' => 'xsd:string'),
        'billInfo20' => array('name' => 'billInfo20', 'type' => 'xsd:string'),
        'billInfo21' => array('name' => 'billInfo21', 'type' => 'xsd:string'),
        'billInfo22' => array('name' => 'billInfo22', 'type' => 'xsd:string'),
        'billInfo23' => array('name' => 'billInfo23', 'type' => 'xsd:string'),
        'billInfo24' => array('name' => 'billInfo24', 'type' => 'xsd:string'),
        'billInfo25' => array('name' => 'billInfo25', 'type' => 'xsd:string'),
        'billDetails' => array('name' => 'BillDetail', 'type' => 'tns:ArrayOfBillDetail'),
        'status' => array('name' => 'status', 'type' => 'tns:status')
    )
);


$server->wsdl->addComplexType(
    'status',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'isError' => array('name'=>'isError','type'=>'xsd:boolean'),
        'errorCode' => array('name'=>'errorCode','type'=>'xsd:string'),
        'statusDescription' => array('name'=>'statusDescription','type'=>'xsd:string')
    )
);





$server->wsdl->addComplexType(
    'ArrayOfBillDetail',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(
        'ArrayOfBillDetail' => array(
            'name' => 'ArrayOfBillDetail', 'type' => 'tns:BillDetail'
        )
    ),
    array(
        array(
            'ref'=>'tns:BillDetail',
            'wsdl:arrayType'=>'tns:BillDetail[]'
        )
    ),
    'tns:BillDetail'
);


$server->wsdl->addComplexType(
    'BillDetail',
    'element',
    'struct',
    'all',
    '',
    array(
        'billCode' => array('name'=>'billCode','type'=>'xsd:string'),
        'billName' => array('name'=>'billName','type'=>'xsd:string'),
        'billShortName' => array('name'=>'billShortName','type'=>'xsd:string'),
        'billAmount' => array('name'=>'billAmount','type'=>'xsd:string'),
        'reference1' => array('name'=>'reference1','type'=>'xsd:string'),
        'reference2' => array('name'=>'reference2','type'=>'xsd:string'),
        'reference3' => array('name'=>'reference3','type'=>'xsd:string')
    )
);






// Register the method to expose
$server->register('inquiry',                    // method name
    array('InquiryRequest' => 'tns:InquiryRequest'),          // input parameters
    array('InquiryResponse' => 'tns:InquiryResponse'),    // output parameters
    'urn:hellowsdl2',                         // namespace
    'urn:hellowsdl2#hello',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Greet a person entering the sweepstakes'        // documentation
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$server->wsdl->addComplexType(
    'PaymentRequest',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'language' => array('name'=>'language','type'=>'xsd:string'),
        'trxDateTime' => array('name'=>'trxDateTime','type'=>'xsd:string'),
        'transmissionDateTime' => array('name'=>'transmissionDateTime','type'=>'xsd:string'),
        'companyCode' => array('name'=>'companyCode','type'=>'xsd:string'),
        'channelID' => array('name'=>'channelID','type'=>'xsd:string'),
        'terminalID' => array('name'=>'terminalID','type'=>'xsd:string'),
        'billKey1' => array('name'=>'billKey1','type'=>'xsd:string'),
        'billKey2' => array('name'=>'billKey2','type'=>'xsd:string'),
        'billKey3' => array('name'=>'billKey3','type'=>'xsd:string'),
        'paidBills' => array('name'=>'billKey3','type'=>'tns:ArrayOfString'),
        'paymentAmount' => array('name'=>'paymentAmount','type'=>'xsd:string'),
        'currency' => array('name'=>'currency','type'=>'xsd:string'),
        'transactionID' => array('name'=>'transactionID','type'=>'xsd:string'),
        'reference1' => array('name'=>'reference1','type'=>'xsd:string'),
        'reference2' => array('name'=>'reference2','type'=>'xsd:string'),
        'reference3' => array('name'=>'reference3','type'=>'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'ArrayOfString',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'string' => array('name'=>'string','type'=>'xsd:string')
    )
);






$server->wsdl->addComplexType(
    'PaymentResponse',
    'complexType',
    'struct',
    'sequence',
    '',

    array(
        'currency' => array('name' => 'currency', 'type' => 'xsd:string'),
        'billInfo1' => array('name' => 'billInfo1', 'type' => 'xsd:string'),
        'billInfo1' => array('name' => 'billInfo1', 'type' => 'xsd:string'),
        'billInfo2' => array('name' => 'billInfo2', 'type' => 'xsd:string'),
        'billInfo3' => array('name' => 'billInfo3', 'type' => 'xsd:string'),
        'billInfo4' => array('name' => 'billInfo4', 'type' => 'xsd:string'),
        'billInfo5' => array('name' => 'billInfo5', 'type' => 'xsd:string'),
        'billInfo6' => array('name' => 'billInfo6', 'type' => 'xsd:string'),
        'billInfo7' => array('name' => 'billInfo7', 'type' => 'xsd:string'),
        'billInfo8' => array('name' => 'billInfo8', 'type' => 'xsd:string'),
        'billInfo9' => array('name' => 'billInfo9', 'type' => 'xsd:string'),
        'billInfo10' => array('name' => 'billInfo10', 'type' => 'xsd:string'),
        'billInfo11' => array('name' => 'billInfo11', 'type' => 'xsd:string'),
        'billInfo12' => array('name' => 'billInfo12', 'type' => 'xsd:string'),
        'billInfo13' => array('name' => 'billInfo13', 'type' => 'xsd:string'),
        'billInfo14' => array('name' => 'billInfo14', 'type' => 'xsd:string'),
        'billInfo15' => array('name' => 'billInfo15', 'type' => 'xsd:string'),
        'billInfo16' => array('name' => 'billInfo16', 'type' => 'xsd:string'),
        'billInfo17' => array('name' => 'billInfo17', 'type' => 'xsd:string'),
        'billInfo18' => array('name' => 'billInfo18', 'type' => 'xsd:string'),
        'billInfo19' => array('name' => 'billInfo19', 'type' => 'xsd:string'),
        'billInfo20' => array('name' => 'billInfo20', 'type' => 'xsd:string'),
        'billInfo21' => array('name' => 'billInfo21', 'type' => 'xsd:string'),
        'billInfo22' => array('name' => 'billInfo22', 'type' => 'xsd:string'),
        'billInfo23' => array('name' => 'billInfo23', 'type' => 'xsd:string'),
        'billInfo24' => array('name' => 'billInfo24', 'type' => 'xsd:string'),
        'billInfo25' => array('name' => 'billInfo25', 'type' => 'xsd:string'),
        'status' => array('name' => 'status', 'type' => 'tns:status')
    )
);




// Register the method to expose/
$server->register('payment',                                    // method name
    array('PaymentRequest' => 'tns:PaymentRequest'),            // input parameters
    array('PaymentResponse' => 'tns:PaymentResponse'),          // output parameters
    'urn:hellowsdl2',                                           // namespace
    'urn:hellowsdl2#hello',                                     // soapaction
    'rpc',                                                      // style
    'encoded',                                                  // use
    'Greet a person entering the sweepstakes'                   // documentation
);



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$server->wsdl->addComplexType(
    'ReversalRequest',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'language' => array('name'=>'language','type'=>'xsd:string'),
        'trxDateTime' => array('name'=>'trxDateTime','type'=>'xsd:string'),
        'origTrxDateTime' => array('name'=>'origTrxDateTime','type'=>'xsd:string'),
        'transmissionDateTime' => array('name'=>'transmissionDateTime','type'=>'xsd:string'),
        'origTransmissionDateTime' => array('name'=>'origTransmissionDateTime','type'=>'xsd:string'),
        'companyCode' => array('name'=>'companyCode','type'=>'xsd:string'),
        'channelID' => array('name'=>'channelID','type'=>'xsd:string'),
        'terminalID' => array('name'=>'terminalID','type'=>'xsd:string'),
        'billKey1' => array('name'=>'billKey1','type'=>'xsd:string'),
        'billKey2' => array('name'=>'billKey2','type'=>'xsd:string'),
        'billKey3' => array('name'=>'billKey3','type'=>'xsd:string'),
        'paymentAmount' => array('name'=>'paymentAmount','type'=>'xsd:string'),
        'currency' => array('name'=>'currency','type'=>'xsd:string'),
        'transactionID' => array('name'=>'transactionID','type'=>'xsd:string'),
        'reference1' => array('name'=>'reference1','type'=>'xsd:string'),
        'reference2' => array('name'=>'reference2','type'=>'xsd:string'),
        'reference3' => array('name'=>'reference3','type'=>'xsd:string')
    )
);




$server->wsdl->addComplexType(
    'ReversalResponse',
    'complexType',
    'struct',
    'sequence',
    '',

    array(
        'status' => array('name' => 'status', 'type' => 'tns:status')
    )
);


// Register the method to expose/
$server->register('reverse',                                    // method name
    array('ReversalRequest' => 'tns:ReversalRequest'),            // input parameters
    array('ReversalResponse' => 'tns:ReversalResponse'),          // output parameters
    'urn:hellowsdl2',                                           // namespace
    'urn:hellowsdl2#hello',                                     // soapaction
    'rpc',                                                      // style
    'encoded',                                                  // use
    'Greet a person entering the sweepstakes'                   // documentation
);



$post = file_get_contents('php://input');
$server->service($post);

?>