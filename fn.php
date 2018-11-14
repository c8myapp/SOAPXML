<?php
function _pre($array = array())
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function inquiry($array = array())
{
    $array2 = $myJSON = json_encode($array);



    $stringxml = "<inquiryResult> 
				<currency>360</currency>
				<billInfo1>Rahmad Hidayatullah</billInfo1>
				<billInfo2>1</billInfo2>
				<billInfo3>Teknik Informatika</billInfo3>
				<billInfo4>Sains dan Teknologi</billInfo4>
				<billInfo5>Daftar Ulang</billInfo5>
				<billInfo6>2010</billInfo6>
				<billInfo7>Ganjil</billInfo7>
				<billInfo8>Reguler</billInfo8>
				<billDetails>
					<BillDetail>
						<billCode>01</billCode>
						<billName>BOP</billName>
						<billShortName>BOP</billShortName>
						<billAmount>13000</billAmount>
					</BillDetail>
					<BillDetail>
						<billCode>02</billCode>
						<billName>DKM</billName>
						<billShortName>DKM</billShortName>
						<billAmount>340000</billAmount>
					</BillDetail>
				</billDetails>
				<status>
					<isError>false</isError>
					<errorCode>00</errorCode>
					<statusDescription>Sukses</statusDescription>
				</status>
			</inquiryResult>";


    $arraynew = Array
    (
        'currency' => '360',
        'billInfo1' => $array2,
        'billInfo2' => '1',
        'billInfo3' => 'Teknik Informatika',
        'billInfo4' => 'Sains dan Teknologi',
        'billInfo5' => 'Daftar Ulang',
        'billInfo6' => '2010',
        'billInfo7' => 'Ganjil',
        'billInfo8' => 'Reguler',
        'billDetails' => Array
        (

            0 => Array
            (
                'billCode' => '01',
                'billName' => 'BOP',
                'billShortName' => 'BOP',
                'billAmount' => '13000'
            ),
            1 => Array
            (
                'billCode' => '02',
                'billName' => 'DKM',
                'billShortName' => 'DKM',
                'billAmount' => '340000'
            )


        ),

        'status' => Array
        (
            'isError' => true,
            'errorCode' => '00',
            'statusDescription' => 'Sukses`'
        )

    );

    $xml = simplexml_load_string($stringxml);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
    return $arraynew;
}


function payment($array = array())
{

    $array2 = $myJSON = json_encode($array);


    $stringxml = "<paymentResult>
				<billInfo1>".$array2."</billInfo1>
				<billInfo2>1</billInfo2>
				<billInfo3>Teknik Informatika</billInfo3>
				<billInfo4>2400155831384</billInfo4>
				<billInfo5>Sains dan Teknologi</billInfo5>
				<billInfo6>2010</billInfo6>
				<billInfo7>Ganjil</billInfo7>
				<billInfo8>Reguler</billInfo8>
				<billInfo9>1110091000009</billInfo9>
				<billInfo10>100</billInfo10>
				<billInfo11>Daftar Ulang</billInfo11>
				<status>
					<isError>false</isError>
					<errorCode>00</errorCode>
					<statusDescription>Sukses</statusDescription>
				</status>
			</paymentResult>";

    $xml = simplexml_load_string($stringxml);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
    return $array;
}