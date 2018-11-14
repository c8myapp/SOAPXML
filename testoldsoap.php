<?php


$client = new SoapClient('http://localhost:50614/Service.asmx?WSDL');

$answer = $client->__soapCall('inquiry', array());

echo "<pre>";
print_r($answer);
echo "</pre>";