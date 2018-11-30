<?php

require_once 'database.php';
require_once 'var.php';





function _pre($array = array())
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}


function inquiry($array = array())
{
    $db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);
    $va = $array['billKey1'];

    $va_code_unit = substr($va, 0, 3);
    $va_year = substr($va, 3, 2);
    $va_nis = substr($va, 5, strlen($va) - 13);
    $va_month_start = substr(substr($va, -8), 0, 2);
    $va_yaer_start = substr(substr($va, -6), 0, 2);
    $va_month_end = substr(substr($va, -4), 0, 2);
    $va_yaer_end = substr($va, -2);


    $tmpstartdate = new DateTime('20' . $va_yaer_start . '-' . $va_month_start . '-01');
    $tmpenddate = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');
    $tmpstartdate2 = new DateTime('20' . $va_yaer_start . '-' . $va_month_start . '-01');
    $tmpenddate2 = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');

    $query = "select * from SISWA_SMU  where siswa_nopin= '" . $va_nis . "';";
    $siswa = $db->_select($query, array());
    $count_siswa = count($siswa);
    $nama_siswa = $count_siswa > 0 ? $siswa[0]['siswa_nama_lengkap'] : "";


    $prevmonth = (int)$tmpstartdate->format('m') - 1;
    $query = "SELECT * FROM SISWA_PayInvoiceSMU where pay_siswa_nobukti = '" . $va_nis . "' and pay_tahun_ajaran='20" . $va_year . "'  and MONTH(pay_timestamp)='0" . $prevmonth . "' order by pay_timestamp desc limit 0,1;";
    $invoice = $db->_select($query, array());
    $totalinvoice = count($invoice);

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


    $query = "select sum(a.siswa_nominal) as total,a.siswa_detil_bayar,siswa_tahun_ajaran,siswa_nama_bayar from SISWA_UangSekolahDetilSMU a
join TblUangSekolahDetilSMU b on a.siswa_detil_bayar = b.siswa_detil_bayar 
where siswa_nopin='" . $va_nis . "' and siswa_tahun_ajaran>= " . $tmpstartdate2->format('Y') . " and siswa_tahun_ajaran <=" . $tmpenddate->format('Y') . "
            and siswa_bulan_ajaran in (" . $wherein . ") group by siswa_detil_bayar";
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
            'billAmount' => (int)$value['total']
        );

        array_push($penjelasanarray, $value['siswa_nama_bayar']);

        array_push($arraypayemnt, $newarray);
    }

    $arraypayemnt2 = $totalinvoice > 0 ? $arraypayemnt : array();


    $status_array = array();

    if ($tmpstartdate2 > $tmpenddate2) {
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
        'currency' => CURRENCY,
        'billInfo1' => $va,
        'billInfo2' => implode(' | ', $penjelasanarray),
        'billInfo3' => $nama_siswa,
        'billInfo4' => $nama_school,
        'billInfo5' => $tmpenddate2->format('M') . " " . $tmpenddate2->format('y'),
        'billInfo6' => $totalbulan . ' Bulan',
        'billInfo7' => '',
        'billInfo8' => '',
        'billDetails' => $arraypayemnt2,

        'status' => $status_array

    );


    //_pre($arraynew);

    return $arraynew;

}




function payment($array = array())
{
    $db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);
    $va = $array['billKey1'];

    $va_code_unit = substr($va, 0, 3);
    $va_year = substr($va, 3, 2);
    $va_nis = substr($va, 5, strlen($va) - 13);
    $va_month_start = substr(substr($va, -8), 0, 2);
    $va_yaer_start = substr(substr($va, -6), 0, 2);
    $va_month_end = substr(substr($va, -4), 0, 2);
    $va_yaer_end = substr($va, -2);


    $tmpstartdate = new DateTime('20' . $va_yaer_start . '-' . $va_month_start . '-01');
    $tmpenddate = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');
    $tmpstartdate2 = new DateTime('20' . $va_yaer_start . '-' . $va_month_start . '-01');
    $tmpenddate2 = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');

    $query = "select * from SISWA_SMU  where siswa_nopin= '" . $va_nis . "';";
    $siswa = $db->_select($query, array());
    $count_siswa = count($siswa);
    $nama_siswa = $count_siswa > 0 ? $siswa[0]['siswa_nama_lengkap'] : "";


    $prevmonth = (int)$tmpstartdate->format('m') - 1;
    $query = "SELECT * FROM SISWA_PayInvoiceSMU where pay_siswa_nobukti = '" . $va_nis . "' and pay_tahun_ajaran='20" . $va_year . "'  and MONTH(pay_timestamp)='0" . $prevmonth . "' order by pay_timestamp desc limit 0,1;";
    $invoice = $db->_select($query, array());
    $totalinvoice = count($invoice);

    $array_month = array();
    $totalbulan = 0;
    $noo=0;
    for ($i = -1; $i < 100; $i++) {
        $noo++;
        if ($i == -1) {
            //echo '<br>';
            //echo $tmpstartdate->format('m');
            $totalbulan++;



            $query="select siswa_detil_bayar, siswa_nominal from SISWA_UangSekolahDetilSMU where siswa_nopin='".$va_nis."' and siswa_tahun_ajaran='".(int)$tmpstartdate->format('Y')."' and siswa_bulan_ajaran='".(int)$tmpstartdate->format('m')."'";
            $payment_do = $db->_select($query, array());

            if(count($payment_do)>0)
            {

                $insert_table = 'SISWA_PayInvoiceSMU';
                $insert_data = array(
                    'pay_invoice_id'=>'SMA-'.time().'0'.$noo,
                    'pay_siswa_nobukti'=>$va_nis,
                    'pay_tahun_ajaran'=>(int)$tmpstartdate->format('Y'),
                    'pay_jenis'=>$payment_do[0]['siswa_detil_bayar'],
                    'pay_nominal'=>$payment_do[0]['siswa_nominal'],
                    'pay_mu_id'=>'IDR',
                    'pay_tanggal_bayar'=>date("Y-m-d H:i:s"),
                    'pay_timestamp'=>date("Y-m-d H:i:s"),
                    'pay_nobukti'=>time().'0'.$noo,
                    'pay_kuitansi_id'=>time().'0'.$noo,
                    'pay_jenis_kuitansi'=>'T',
                    'pay_method_id'=>'A',
                    'pay_desc'=>'I',
                    'pay_bayar_via'=>'I',
                    'pay_cetak'=>'0',
                    'pay_kuitansi_sementara'=>'I',
                    'pay_not_active'=>'I',
                    'pay_not_active_desc'=>'I',
                    'pay_flag'=>'I'
                );
                $db->_insert($insert_table, $insert_data);
            }



            array_push($array_month, (int)$tmpstartdate->format('m'));
        } else {

            $tmpstartdate->add(new DateInterval("P1M"));
            $start_month_plus1 = $tmpstartdate->format('m');
            if ($tmpstartdate <= $tmpenddate) {





                $query="select siswa_detil_bayar, siswa_nominal from SISWA_UangSekolahDetilSMU where siswa_nopin='".$va_nis."' and siswa_tahun_ajaran='".(int)$tmpstartdate->format('Y')."' and siswa_bulan_ajaran='".(int)$tmpstartdate->format('m')."'";
                $payment_do = $db->_select($query, array());

                if(count($payment_do)>0)
                {

                    $insert_table = 'SISWA_PayInvoiceSMU';
                    $insert_data = array(
                        'pay_invoice_id'=>'SMA-'.time().'0'.$noo,
                        'pay_siswa_nobukti'=>$va_nis,
                        'pay_tahun_ajaran'=>(int)$tmpstartdate->format('Y'),
                        'pay_jenis'=>$payment_do[0]['siswa_detil_bayar'],
                        'pay_nominal'=>$payment_do[0]['siswa_nominal'],
                        'pay_mu_id'=>'IDR',
                        'pay_tanggal_bayar'=>date("Y-m-d H:i:s"),
                        'pay_timestamp'=>date("Y-m-d H:i:s"),
                        'pay_nobukti'=>time().'0'.$noo,
                        'pay_kuitansi_id'=>time().'0'.$noo,
                        'pay_jenis_kuitansi'=>'T',
                        'pay_method_id'=>'A',
                        'pay_desc'=>'I',
                        'pay_bayar_via'=>'I',
                        'pay_cetak'=>'0',
                        'pay_kuitansi_sementara'=>'I',
                        'pay_not_active'=>'I',
                        'pay_not_active_desc'=>'I',
                        'pay_flag'=>'I'
                    );
                    $db->_insert($insert_table, $insert_data);
                }



                $totalbulan++;
                array_push($array_month, (int)$start_month_plus1);
            } else {
                break;
            }
        }
    }
    $wherein = "'" . implode("','", $array_month) . "'";


    $query = "select sum(a.siswa_nominal) as total,a.siswa_detil_bayar,siswa_tahun_ajaran,siswa_nama_bayar from SISWA_UangSekolahDetilSMU a
join TblUangSekolahDetilSMU b on a.siswa_detil_bayar = b.siswa_detil_bayar 
where siswa_nopin='" . $va_nis . "' and siswa_tahun_ajaran>= " . $tmpstartdate2->format('Y') . " and siswa_tahun_ajaran <=" . $tmpenddate->format('Y') . "
            and siswa_bulan_ajaran in (" . $wherein . ") group by siswa_detil_bayar";
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
            'billAmount' => (int)$value['total']
        );

        array_push($penjelasanarray, $value['siswa_nama_bayar']);

        array_push($arraypayemnt, $newarray);
    }

    $arraypayemnt2 = $totalinvoice > 0 ? $arraypayemnt : array();


    $status_array = array();

    if ($tmpstartdate2 > $tmpenddate2) {
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
        'billInfo2' => implode(' | ', $penjelasanarray),
        'billInfo3' => $nama_siswa,
        'billInfo4' => $nama_school,
        'billInfo5' => $tmpenddate2->format('M') . " " . $tmpenddate2->format('y'),
        'billInfo6' => $totalbulan . ' Bulan',
        'billInfo7' => '',
        'billInfo8' => '',


        'status' => $status_array

    );



    return $arraynew;

}




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
