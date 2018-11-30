<?php

require_once 'database.php';

define('CURRENCY', '360');


define('DB_TYPE', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'db_sms');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DEBUG', TRUE);

function _pre($array = array())
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function inquiry($array = array())
{
    $db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);
    $post_param = $myJSON = json_encode($array);


    $va = $array['billKey1'];

    $code_unit = substr($va, 0, 3);
    $year = substr($va, 3, 2);
    $nis = substr($va, 5, strlen($va) - 13);
    $month_start = substr(substr($va, -8), 0, 2);
    $yaer_start = substr(substr($va, -6), 0, 2);
    $month_end = substr(substr($va, -4), 0, 2);
    $yaer_end = substr($va, -2);


    $yaer_start_onemonth = date("y", strtotime("-1 month", strtotime($yaer_start . '-' . $month_start . '-01')));
    $month_start_onemonth = date("m", strtotime("-1 month", strtotime($yaer_start . '-' . $month_start . '-01')));


    $realyear = '20' . $year;


    $query = "select * from siswa_smu  where siswa_nopin= '" . $nis . "';";
    $siswa = $db->_select($query, array());
    $count_siswa = count($siswa);
    $nama_siswa = $count_siswa > 0 ? $siswa[0]['siswa_nama_lengkap'] : "";


    $asd = $query = "SELECT * FROM siswa_payinvoicesmu where pay_siswa_nobukti = '" . $nis . "' and pay_tahun_ajaran='" . $realyear . "'  and MONTH(pay_timestamp)='" . $month_start_onemonth . "' and year(pay_timestamp)='20" . $yaer_start_onemonth . "' ;";
    $invoice = $db->_select($query, array());
    $totalinvoice = count($invoice);

    $tanngal_akhir_bayar = $totalinvoice > 0 ? date("ym", strtotime($invoice[0]['pay_timestamp'])) : "";




    $yearmonthstart = $yaer_start . $month_start;
    $yearmonthend = $yaer_end . $month_end;

    $validate = $yearmonthend - $yearmonthstart;

    $banyak_tahun = $yaer_end - $yaer_start;


    $dikalibulan = 1;
    if ($banyak_tahun == 0) {
        if($validate>0)
        {
            $dikalibulan=$validate+1;
        }

    } else {

        $banyak_bulan = 12 - (int)$month_start;
        $banyak_bulan_add = (($banyak_tahun - 1) * 12) + (int)$month_end+1;
        $dikalibulan = $banyak_bulan + $banyak_bulan_add;
    }


    $query = "select * from siswa_uangsekolahdetilsmu where siswa_nopin='" . $nis . "' ";

    $payment = $db->_select($query, array());

    $arraypayemnt = array();
    $no = 0;
    $tahun_ajaran = 0;
    foreach ($payment as $key => $value) {

        $no++;
        $newarray = Array
        (
            'billCode' => '0' . $no,
            'billName' => $value['siswa_detil_bayar'],
            'billShortName' => $value['siswa_detil_bayar'],
            'billAmount' => (int)$value['siswa_nominal'] * $dikalibulan
        );
        array_push($arraypayemnt, $newarray);

        $tahun_ajaran = $value['siswa_tahun_ajaran'];
    }

    $arraypayemnt2 = $totalinvoice > 0 ? $arraypayemnt : array();


    $status_array = array();
    if ($validate < 0) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '99',
            'statusDescription' => 'Format Bulan Dan Tahun Salah'
        );
    } else if ($totalinvoice <= 0) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '99',
            'statusDescription' => 'Data Tidak Ditemukan'
        );
    } else {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '00',
            'statusDescription' => 'Sukses'
        );
    }


    $arraynew = Array
    (
        'currency' => $asd,
        'billInfo1' => $nama_siswa,
        'billInfo2' => '1',
        'billInfo3' => $code_unit . ' - ' . $year . ' - ' . $nis . ' - ' . $month_start . ' - ' . $yaer_start . ' - ' . $month_end . ' - ' . $yaer_end . ' | ' . $va,
        'billInfo4' => 'Sains dan Teknologi',
        'billInfo5' => 'Pembayaran Uang Sekolah',
        'billInfo6' => $tahun_ajaran,
        'billInfo7' => 'Ganjil',
        'billInfo8' => 'Rguler',
        'billDetails' => $arraypayemnt2,

        'status' => $status_array

    );
//    $arraynew = Array
//    (
//        'currency' => '360',
//        'billInfo1' => $code_unit . ' - ' . $year . ' - ' . $nis . ' - ' . $month_start . ' - ' . $yaer_start . ' - ' . $month_end . ' - ' . $yaer_end . ' | ' . $va,
//        'billInfo2' => '1',
//        'billInfo3' => 'Teknik Informatika',
//        'billInfo4' => 'Sains dan Teknologi',
//        'billInfo5' => 'Pembayaran Uang Sekolah',
//        'billInfo6' => '2010',
//        'billInfo7' => 'Ganjil',
//        'billInfo8' => 'Reguler',
//        'billDetails' => Array
//        (
//
//            0 => Array
//            (
//                'billCode' => '01',
//                'billName' => 'BOP',
//                'billShortName' => 'BOP',
//                'billAmount' => '13000'
//            ),
//            1 => Array
//            (
//                'billCode' => '02',
//                'billName' => 'DKM',
//                'billShortName' => 'DKM',
//                'billAmount' => '340000'
//            )
//
//
//        ),
//
//        'status' => Array
//        (
//            'isError' => true,
//            'errorCode' => '00',
//            'statusDescription' => 'Sukses`'
//        )
//
//    );

    return $arraynew;
}


function payment($array = array())
{

    $array2 = $myJSON = json_encode($array);


    $stringxml = "<paymentResult>
				<billInfo1>" . $array2 . "</billInfo1>
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