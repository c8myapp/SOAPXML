<?php
header('Content-Type: application/json');
$array = Array
(
    'currency' => 360,
    'billInfo1' => 1,
    'billInfo2' => 1,
    'billInfo3' => 1,
    'billInfo4' => 1,
    'billInfo5' => 1,
    'billInfo6' => 1,
    'billInfo7' => 1,
    'billInfo8' => 1,
    'billDetails' => Array
    (
        array('BillDetail' => Array
        (
            'billCode' => '01',
            'billName' => 'ERIK',
            'billShortName' => 'QIU',
            'billAmount' => 13000
        )),
        array('BillDetail' => Array
        (
            'billCode' => '01',
            'billName' => 'TANTO',
            'billShortName' => 'BOP',
            'billAmount' => 13000
        ))

    ),

    'status' => Array
    (
        'isError' => true,
        'errorCode' => 00,
        'statusDescription' => '01'
    )

);
//header("Content-type: text/xml");


$data =json_encode($array);
print_r($data);