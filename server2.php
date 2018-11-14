<?php
// Pull in the NuSOAP code
require_once('./nusoap/lib/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('hellowsdl2', 'urn:hellowsdl2');
// Register the data structures used by the service
$server->wsdl->addComplexType(
    'Person',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'firstname' => array('name' => 'firstname', 'type' => 'xsd:string'),
        'age' => array('name' => 'age', 'type' => 'xsd:int'),
        'gender' => array('name' => 'gender', 'type' => 'xsd:string')
    )
);
$server->wsdl->addComplexType(
    'SweepstakesGreeting',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'greeting' => array('name' => 'greeting', 'type' => 'xsd:string'),
        'winner' => array('name' => 'winner', 'type' => 'xsd:boolean')
    )
);
// Register the method to expose
$server->register('hello',                    // method name
    array('person' => 'tns:Person'),          // input parameters
    array('return' => 'tns:SweepstakesGreeting'),    // output parameters
    'urn:hellowsdl2',                         // namespace
    'urn:hellowsdl2#hello',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Greet a person entering the sweepstakes'        // documentation
);
// Define the method as a PHP function
function hello($person) {
    $greeting = 'Hello, ' . $person['firstname'] .
        '. It is nice to meet a ' . $person['age'] .
        ' year old ' . $person['gender'] . '.';

    $winner = $person['firstname'] == 'Scott';
    return array(
        'greeting' => $greeting,
        'winner' => $winner
    );
}

//run the service
$post = file_get_contents('php://input');
$server->service($post);

?>