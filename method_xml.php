<?php

require_once 'database.php';
require_once 'var.php';


function inquiry($array = array())
{
    $db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);
    $va = $array['billKey1'];

    $va_code_unit = substr($va, 0, 3);
    $va_year = substr($va, 3, 2);
    $va_nis = substr($va, 5, strlen($va) - 13); // fix 4digit
    $va_month_start = substr(substr($va, -8), 0, 2);
    $va_year_start = substr(substr($va, -6), 0, 2);
    $va_month_end = substr(substr($va, -4), 0, 2);
    $va_yaer_end = substr($va, -2);


    $tmpstartdate = new DateTime('20' . $va_year_start . '-' . $va_month_start . '-01');
    $tmpenddate = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');
    $tmpstartdate2 = new DateTime('20' . $va_year_start . '-' . $va_month_start . '-01');
    $tmpenddate2 = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');

    $query = "select * from SISWA_SMU  where siswa_nopin= '" . $va_nis . "';";
    $siswa = $db->_select($query, array());
    $count_siswa = count($siswa);
    $nama_siswa = $count_siswa > 0 ? $siswa[0]['siswa_nama_lengkap'] : "";

    //echo "<hr>";
    $prevmonth = (int)$tmpstartdate->format('m') - 1;
    //$query = "SELECT * FROM SISWA_UangSekolahDetailSMU where siswa_nopin = '" . $va_nis . "' and siswa_tahun_ajaran='20" . $va_year_start . "'  and siswa_jenis_bayar='" . $prevmonth . "' order by CAST(siswa_jenis_bayar AS UNSIGNED) desc limit 0,1;";
    $query = "SELECT * FROM SISWA_UangSekolahDetailSMU where siswa_nopin = '" . $va_nis . "' and siswa_tahun_ajaran='20" . $va_year_start . "'  order by CAST(siswa_jenis_bayar AS UNSIGNED) desc limit 0,1;";
    $invoice = $db->_select($query, array());
    $totalinvoice = count($invoice);

    //echo "<hr>";
    $tglterakhirbaray = $invoice[0] ['siswa_tahun_ajaran'] . substr('00' . $invoice[0]['siswa_jenis_bayar'], -2);
    //echo "<hr>";


    $now = mktime(0, 0, 0, $va_month_start, 1, '20' . $va_year_start);

    $tglmaubayar = date("Ym", strtotime("-1 months", $now));

    $tahunakhirsekolah = $va_year + 1;


    $inputtahunakhirbayar = '20' . $va_yaer_end . $va_month_end;

    $tahunakhirbayar = '20' . $tahunakhirsekolah . "06";


    $array_month = array();
    $totalbulan = 0;
    for ($i = -1; $i < 100; $i++) {

        if ($i == -1) {
            //echo '<br>';
            //echo $tmpstartdate->format('m');
            $totalbulan++;
            array_push($array_month, (int)$tmpstartdate->format('m'));
        } else {

            $tmpstartdate->add(new DateInterval("P1M"));
            $start_month_plus1 = $tmpstartdate->format('m');
            if ($tmpstartdate <= $tmpenddate) {
                //echo '<br>';
                //echo $start_month_plus1;
                $totalbulan++;
                array_push($array_month, (int)$start_month_plus1);
            } else {
                break;
            }
        }
    }
    $wherein = "'" . implode("','", $array_month) . "'";


    $query = "select * from SISWA_UangSekolahDetilSMU where siswa_nopin='" . $va_nis . "'";
    $payment = $db->_select($query, array());
    $no = 0;
    $penjelasanarray = array();
    $arraypayemnt = array();
    foreach ($payment as $key => $value) {
        $no++;
        $newarray = Array
        (
            'billCode' => '0' . $no,
            'billName' => $value['siswa_detil_bayar'],
            'billShortName' => $value['siswa_detil_bayar'],
            'billAmount' => (int)$value['siswa_nominal'] * $totalbulan
        );

        //array_push($penjelasanarray, $value['siswa_nama_bayar']);

        array_push($arraypayemnt, $newarray);
    }

    $arraypayemnt2 = $arraypayemnt;


    $status_array = array();
    //echo $tmptglmaubayar = $tglmaubayar;
    //echo "<br>";
    //echo $tglterakhirbaray;
    if ($tahunakhirbayar < $inputtahunakhirbayar) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '991',
            'statusDescription' => 'tanggal terakhir bayar ' . $tahunakhirbayar
        );
        $arraypayemnt2 = array();
    } else if ($tglmaubayar != $tglterakhirbaray) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '991',
            'statusDescription' => 'Data Tidak Ditemukan'
        );
        $arraypayemnt2 = array();
    } else if ($tmpstartdate2 > $tmpenddate2) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '99',
            'statusDescription' => 'Format Bulan Dan Tahun Salah'
        );
        $arraypayemnt2 = array();
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


    /*
     * 1. No Bayar
2. Pembayaran
3. Nama Siswa
4. Nama Sekolah
5. Bulan
6. Jumlah Bulan
     * */


    $query = "select * from TblControlSMU";
    $schoolname = $db->_select($query, array());
    $count_school = count($schoolname);
    $nama_school = $count_school > 0 ? $schoolname[0]['control_name'] : "";

    $arraynew = Array
    (
        'currency' => CURRENCY,
        'billInfo1' => $va,
        'billInfo2' => "Uang Sekolah",
        'billInfo3' => $nama_siswa,
        'billInfo4' => $nama_school,
        'billInfo5' => $tmpstartdate2->format('M') . " " . $tmpstartdate2->format('y') . ' SD ' . $tmpenddate2->format('M') . " " . $tmpenddate2->format('y'),
        'billInfo6' => $totalbulan . ' Bulan',
        'billInfo7' => '',
        'billInfo8' => '',
        'billDetails' => $arraypayemnt2,

        'status' => $status_array

    );


    //_pre($arraynew);

    return $arraynew;

}

//inquiry(array('billKey1'=>'123171718104110170118'));
//inquiry(array('billKey1'=>'123171718104102180818'));
//inquiry(array('billKey1' => '123171718104102180718'));
function payment($array = array())
{
    $db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);
    $va = $array['billKey1'];

    $va_code_unit = substr($va, 0, 3);
    $va_year = substr($va, 3, 2);
    $va_nis = substr($va, 5, strlen($va) - 13);
    $va_month_start = substr(substr($va, -8), 0, 2);
    $va_year_start = substr(substr($va, -6), 0, 2);
    $va_month_end = substr(substr($va, -4), 0, 2);
    $va_yaer_end = substr($va, -2);


    $tmpstartdate = new DateTime('20' . $va_year_start . '-' . $va_month_start . '-01');
    $tmpenddate = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');
    $tmpstartdate2 = new DateTime('20' . $va_year_start . '-' . $va_month_start . '-01');
    $tmpenddate2 = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');

    $query = "select * from SISWA_SMU  where siswa_nopin= '" . $va_nis . "';";
    $siswa = $db->_select($query, array());
    $count_siswa = count($siswa);
    $nama_siswa = $count_siswa > 0 ? $siswa[0]['siswa_nama_lengkap'] : "";

    $prevmonth = (int)$tmpstartdate->format('m') - 1;
    $query = "SELECT * FROM SISWA_UangSekolahDetailSMU where siswa_nopin = '" . $va_nis . "' and siswa_tahun_ajaran='20" . $va_year_start . "'   order by CAST(siswa_jenis_bayar AS UNSIGNED) desc limit 0,1;";
    $invoice = $db->_select($query, array());
    $totalinvoice = count($invoice);


    $tglterakhirbaray = $invoice[0] ['siswa_tahun_ajaran'] . substr('00' . $invoice[0]['siswa_jenis_bayar'], -2);
    //echo "<hr>";


    $now = mktime(0, 0, 0, $va_month_start, 1, '20' . $va_year_start);

    $tglmaubayar = date("Ym", strtotime("-1 months", $now));

    $tahunakhirsekolah = $va_year + 1;


    $inputtahunakhirbayar = '20' . $va_yaer_end . $va_month_end;

    $tahunakhirbayar = '20' . $tahunakhirsekolah . "06";


    $array_month = array();
    $totalbulan = 0;
    for ($i = -1; $i < 100; $i++) {

        if ($i == -1) {
            //echo '<br>';
            //echo $tmpstartdate->format('m');
            $totalbulan++;
            array_push($array_month, (int)$tmpstartdate->format('m'));
        } else {

            $tmpstartdate->add(new DateInterval("P1M"));
            $start_month_plus1 = $tmpstartdate->format('m');
            if ($tmpstartdate <= $tmpenddate) {
                //echo '<br>';
                //echo $start_month_plus1;
                $totalbulan++;
                array_push($array_month, (int)$start_month_plus1);
            } else {
                break;
            }
        }
    }
    $wherein = "'" . implode("','", $array_month) . "'";


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $checkpayment = 1;
    foreach ($array_month as $k) {
        $query = "select * from SISWA_UangSekolahDetailSMU where siswa_tahun_ajaran='20" . $va_year_start . "' and siswa_jenis_bayar='" . $k . "' and siswa_nopin ='" . $va_nis . "'";
        $invoice = $db->_select($query, array());
        $totalinvoice2 = count($invoice);

        if ($totalinvoice2 > 0) {
            $checkpayment = 0;
            break;
        }

    }


    $kodetime = time();

    $noo = 0;
    if ($checkpayment > 0 && $tahunakhirbayar >= $inputtahunakhirbayar && $tglmaubayar == $tglterakhirbaray) {


        $insert_table = 'SISWA_NewPayemntSmu';
        $insert_data = array(
            'siswa_nopin' => $va_nis,
            'siswa_tahun_ajaran' => '20' . $va_year,
            'siswa_va' => $va,
            'siswa_tangal_bayar' => date('Y-m-d H:i:s')
        );
        $db->_insert($insert_table, $insert_data);


        $query = "select * from SISWA_UangSekolahDetilSMU where siswa_nopin='" . $va_nis . "'";
        $bill = $db->_select($query, array());

        $countbill = count($bill);
        $siswa_detil_bayar = $countbill > 0 ? $bill[0]['siswa_detil_bayar'] : '';
        $siswa_nominal = $countbill > 0 ? $bill[0]['siswa_nominal'] : '';

        $checkmonth = 0;
        $yearinsert = $va_year_start;
        foreach ($array_month as $k) {

            if ($checkmonth == 0) {
                $checkmonth = $k;
            } else {
                if ($checkmonth > $k) {
                    $yearinsert = $va_year_start + 1;
                }
            }

            $noo++;
            $insert_table = 'SISWA_PayInvoiceSMU';
            $insert_data = array(
                'pay_invoice_id' => 'SMA-' . $kodetime . '0' . $noo,
                'pay_siswa_nobukti' => $va_nis,
                'pay_tahun_ajaran' => $yearinsert,
                'pay_jenis' => $siswa_detil_bayar,
                'pay_nominal' => $siswa_nominal,
                'pay_mu_id' => 'IDR',
                'pay_tanggal_bayar' => date("Y-m-d H:i:s"),
                'pay_timestamp' => date("Y-m-d H:i:s"),
                'pay_nobukti' => $kodetime . '0' . $noo,
                'pay_kuitansi_id' => $kodetime . '0' . $noo,
                'pay_jenis_kuitansi' => 'T',
                'pay_method_id' => 'A',
                'pay_desc' => 'I',
                'pay_bayar_via' => 'I',
                'pay_cetak' => '0',
                'pay_kuitansi_sementara' => 'I',
                'pay_not_active' => 'I',
                'pay_not_active_desc' => 'I',
                'pay_flag' => 'I'
            );
            $db->_insert($insert_table, $insert_data);


            $insert_table = 'SISWA_UangSekolahDetailSMU';
            $insert_data = array(
                'siswa_nopin' => $va_nis,
                'siswa_tahun_ajaran' => '20' . $yearinsert,
                'siswa_invoice_id' => $kodetime . '0' . $noo,
                'siswa_jenis_bayar' => $k,
                'siswa_detil_bayar' => $siswa_detil_bayar,
                'siswa_nominal' => $siswa_nominal,
                'siswa_mu_id' => 'IDR',
                'siswa_timestamp' => date('Y-m-d H:i:s'),
                'siswa_detil_urut' => '0',
                'siswa_flag' => 'bbbbb',
                'siswa_keterangan_lain' => ''
            );
            $db->_insert($insert_table, $insert_data);
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///

    $query = "select * from SISWA_UangSekolahDetilSMU where siswa_nopin='" . $va_nis . "'";

    $payment = $db->_select($query, array());
    $no = 0;
    $penjelasanarray = array();
    $arraypayemnt = array();
    foreach ($payment as $key => $value) {
        $no++;
        $newarray = Array
        (
            'billCode' => '0' . $no,
            'billName' => $value['siswa_detil_bayar'],
            'billShortName' => $value['siswa_detil_bayar'],
            'billAmount' => (int)$value['siswa_nominal'] * $noo
        );

        //array_push($penjelasanarray, $value['siswa_nama_bayar']);

        array_push($arraypayemnt, $newarray);
    }

    $arraypayemnt2 = $arraypayemnt;


    $status_array = array();

    //echo $tmptglmaubayar = $tglmaubayar;
    //echo "<br>";
    //echo $tglterakhirbaray;
    if ($tahunakhirbayar < $inputtahunakhirbayar) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '991',
            'statusDescription' => 'tanggal terakhir bayar ' . $tahunakhirbayar
        );
        $arraypayemnt2 = array();
    } else if ($tglmaubayar != $tglterakhirbaray) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '991',
            'statusDescription' => 'Data Tidak Ditemukan'
        );
        $arraypayemnt2 = array();
    } else if ($checkpayment === 0) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '99',
            'statusDescription' => 'Ada data telah di input'
        );
    } else if ($tmpstartdate2 > $tmpenddate2) {
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


    /*
     * 1. No Bayar
2. Pembayaran
3. Nama Siswa
4. Nama Sekolah
5. Bulan
6. Jumlah Bulan
     * */


    $query = "select * from TblControlSMU";
    $schoolname = $db->_select($query, array());
    $count_school = count($schoolname);
    $nama_school = $count_school > 0 ? $schoolname[0]['control_name'] : "";

    $arraynew = Array
    (

        'billInfo1' => $va,
        'billInfo2' => "Uang Sekolah",
        'billInfo3' => $nama_siswa,
        'billInfo4' => $nama_school,
        'billInfo5' => $tmpenddate2->format('M') . " " . $tmpenddate2->format('y'),
        'billInfo6' => $totalbulan . ' Bulan',
        'billInfo7' => '',
        'billInfo8' => '',


        'status' => $status_array

    );

    //_pre($arraynew);
    return $arraynew;

}


//payment(array('billKey1' => '123171718104110170118'));


function reverse($array = array())
{
    $status_array = array
    (
        'isError' => true,
        'errorCode' => '00',
        'statusDescription' => 'Sukses'
    );

    $arraynew = Array
    (


        'status' => $status_array

    );
    return $arraynew;


}
