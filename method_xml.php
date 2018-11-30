<?php

require_once 'database.php';
require_once 'var.php';


function inquiry($array = array())
{
    $db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);
    $va = $array['billKey1'];

    $va_code_unit = substr($va, 0, 3);
    $va_year = substr($va, 3, 2);
    $va_nis = substr($va, 5, 4); // fix 4digit
    $va_month_start = substr(substr($va, -8), 0, 2);
    $va_year_start = substr(substr($va, -6), 0, 2);
    $va_month_end = substr(substr($va, -4), 0, 2);
    $va_yaer_end = substr($va, -2);


    $tmpstartdate = new DateTime('20' . $va_year_start . '-' . $va_month_start . '-01');
    $tmpenddate = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');
    $intervalDate = $tmpenddate->diff($tmpstartdate);


    $query = "select * from SISWA_SMU  where siswa_nobukti_keuangan= '" . $va_year.$va_nis . "';";
    $siswa = $db->_select($query, array());
    $count_siswa = count($siswa);
    //echo   $count_siswa;
    //echo   "<hr>";
    $siswa_nama = $count_siswa > 0 ? $siswa[0]['siswa_nama_lengkap'] : "";
    $siswa_nopin = $count_siswa > 0 ? $siswa[0]['siswa_nopin'] : "";


    $query = "select * from TblControlSMU";
    $schoolname = $db->_select($query, array());
    $count_school = count($schoolname);
    //echo   $count_school;
    //echo   "<hr>";
    $nama_school = $count_school > 0 ? $schoolname[0]['control_name'] : "";



    $status_array = array
    (
        'isError' => true,
        'errorCode' => '00',
        'statusDescription' => 'Sukses'
    );


    $data_tidakditemukan= array
    (
        'isError' => true,
        'errorCode' => '99',
        'statusDescription' => 'Data Tidak Ditemukan'
    );


    $arraypayment = array();
    $arrayinfopayment = array();
    if($count_siswa>0)
    {
        $query = "
        SELECT * FROM SISWA_NewDetailSMU a 
        where siswa_jenis_bayar not in( SELECT siswa_jenis_bayar FROM SISWA_NewDetailSMU where SUBSTRING(siswa_flag , 1, 1)='y'  and siswa_nopin = a.siswa_nopin)
        and siswa_nopin='".$siswa_nopin."' 
        order by CAST(siswa_jenis_bayar AS UNSIGNED) limit 0,1;
        ";
        $invoice = $db->_select($query, array());
        $totalinvoie = count($invoice);
        //echo   $totalinvoie;
        //echo   "<hr>";

        $tglterakhirbayar = count($invoice)>0 ? $invoice[0] ['siswa_jenis_bayar'] :"";
        //echo   "from database :: ".$tglterakhirbayar." = input start :: ".$tmpstartdate->format('Ym').' | input end ::'.$tmpenddate->format('Ym');
        //echo   "<hr>";
        
        $query = "
        SELECT * FROM SISWA_NewDetailSMU  where SUBSTRING(siswa_flag , 1, 1)='b' and siswa_nopin='".$siswa_nopin."' 
        order by CAST(siswa_jenis_bayar AS UNSIGNED) desc limit 0,1;
        ";
        $datapayment = $db->_select($query, array());
        $totaldatapayment = count($datapayment);
        
        
        $datatahunbulan = $totaldatapayment>0 ?  $datapayment[0] ['siswa_jenis_bayar'] :"";
         //echo   "from database akhir bayar :: ".$datatahunbulan." >= " .$tmpenddate->format('Ym');
        //echo   "<hr>";
        
        
        
        if($tglterakhirbayar==$tmpstartdate->format('Ym') && $datatahunbulan>=$tmpenddate->format('Ym'))
        {
            
            $query = "SELECT sum(siswa_nominal) as 'totalbayar', siswa_detil_bayar FROM SISWA_NewDetailSMU where SUBSTRING(siswa_flag , 1, 1)='b' and siswa_nopin='".$siswa_nopin."' and CAST(siswa_jenis_bayar AS UNSIGNED) BETWEEN ".$tmpstartdate->format('Ym')." and ".$tmpenddate->format('Ym')." group by siswa_detil_bayar;";
            $listpayment = $db->_select($query, array());
            $totalpayment = count($listpayment);
            //echo   $totalpayment;
            //echo   "<hr>";
            if($totalpayment>0)
            {
                $no=0;
                foreach ($listpayment as $key => $value) {
                    $no++;
                    $newarray = Array
                    (
                        'billCode' => '0' . $no,
                        'billName' => $value['siswa_detil_bayar'],
                        'billShortName' => $value['siswa_detil_bayar'],
                        'billAmount' => (int)$value['totalbayar']
                    );
            
                    array_push($arrayinfopayment, $value['siswa_detil_bayar']);
            
                    array_push($arraypayment, $newarray);
                }
            }
            else{
                $status_array =$data_tidakditemukan;
            }

        }
        else
        {
            $status_array =$data_tidakditemukan;
        }
        
    }
    else
    {
        $status_array =$data_tidakditemukan;
    }



    if ($tmpstartdate > $tmpenddate) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '99',
            'statusDescription' => 'Format Bulan Dan Tahun Salah'
        );
        $arraypayment = array();
        $arrayinfopayment = array();
    }

    $arraynew = Array
    (
        'currency' => CURRENCY,
        'billInfo1' => $va,
        'billInfo2' => implode(" | ",$arrayinfopayment),
        'billInfo3' => $siswa_nama,
        'billInfo4' => $nama_school,
        'billInfo5' => $tmpstartdate->format('M y'). ' SD ' . $tmpenddate->format('M y'),
        'billInfo6' =>  $intervalDate->format('%m')+1 . ' Bulan',
        'billInfo7' => '',
        'billInfo8' => '',
        'billDetails' => $arraypayment,
        'status' => $status_array
    );


    //_pre($arraynew);

    return $arraynew;

}

//inquiry(array('billKey1'=>'12317000207171018'));
//inquiry(array('billKey1'=>'123171718104102180818'));
//inquiry(array('billKey1' => '123171718104102180718'));




function payment($array = array())
{
    $db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);

    $db_gl = new database(DB_TYPE, DB_HOST, DB_NAME2, DB_PORT, DB_USER2, DB_PASS2);

    $va = $array['billKey1'];

    $va_code_unit = substr($va, 0, 3);
    $va_year = substr($va, 3, 2);
    $va_nis = substr($va, 5, 4); // fix 4digit
    $va_month_start = substr(substr($va, -8), 0, 2);
    $va_year_start = substr(substr($va, -6), 0, 2);
    $va_month_end = substr(substr($va, -4), 0, 2);
    $va_yaer_end = substr($va, -2);


    $tmpstartdate = new DateTime('20' . $va_year_start . '-' . $va_month_start . '-01');
    $tmpenddate = new DateTime('20' . $va_yaer_end . '-' . $va_month_end . '-01');
    

    $intervalDate = $tmpenddate->diff($tmpstartdate);


    $query = "select * from SISWA_SMU  where siswa_nobukti_keuangan= '" . $va_year.$va_nis . "';";
    $siswa = $db->_select($query, array());
    $count_siswa = count($siswa);
    //echo   $count_siswa;
    //echo   "<hr>";
    $siswa_nama = $count_siswa > 0 ? $siswa[0]['siswa_nama_lengkap'] : "";
    $siswa_nopin = $count_siswa > 0 ? $siswa[0]['siswa_nopin'] : "";


    $query = "select * from TblControlSMU";
    $schoolname = $db->_select($query, array());
    $count_school = count($schoolname);
    //echo   $count_school;
    //echo   "<hr>";
    $nama_school = $count_school > 0 ? $schoolname[0]['control_name'] : "";



    $status_array = array
    (
        'isError' => true,
        'errorCode' => '00',
        'statusDescription' => 'Sukses'
    );


    $data_tidakditemukan= array
    (
        'isError' => true,
        'errorCode' => '99',
        'statusDescription' => 'Data Tidak Ditemukan'
    );


    $arraypayment = array();
    $arrayinfopayment = array();
    if($count_siswa>0)
    {
        
      $query = "
        SELECT * FROM SISWA_NewDetailSMU a 
        where siswa_jenis_bayar not in( SELECT siswa_jenis_bayar FROM SISWA_NewDetailSMU where SUBSTRING(siswa_flag , 1, 1)='y'  and siswa_nopin = a.siswa_nopin)
        and siswa_nopin='".$siswa_nopin."' 
        order by CAST(siswa_jenis_bayar AS UNSIGNED) limit 0,1;
        ";
        $invoice = $db->_select($query, array());
        $totalinvoie = count($invoice);
        //echo   $totalinvoie;
        //echo   "<hr>";

        $tglterakhirbayar = count($invoice)>0 ? $invoice[0] ['siswa_jenis_bayar'] :"";
        //echo   "from database :: ".$tglterakhirbayar." = input start :: ".$tmpstartdate->format('Ym').' | input end ::'.$tmpenddate->format('Ym');
        //echo   "<hr>";
        
               $query = "
        SELECT * FROM SISWA_NewDetailSMU  where SUBSTRING(siswa_flag , 1, 1)='b' and siswa_nopin='".$siswa_nopin."' 
        order by CAST(siswa_jenis_bayar AS UNSIGNED) desc limit 0,1;
        ";
        $datapayment = $db->_select($query, array());
        $totaldatapayment = count($datapayment);
        
        
        $datatahunbulan = $totaldatapayment>0 ?  $datapayment[0] ['siswa_jenis_bayar'] :"";
         //echo   "from database akhir bayar :: ".$datatahunbulan." >= " .$tmpenddate->format('Ym');
        //echo   "<hr>";
        
        
        
        if($tglterakhirbayar==$tmpstartdate->format('Ym') && $datatahunbulan>=$tmpenddate->format('Ym'))
        
        {
            $query = "SELECT sum(siswa_nominal) as 'totalbayar', siswa_detil_bayar FROM SISWA_NewDetailSMU where SUBSTRING(siswa_flag , 1, 1)='b' and siswa_nopin='".$siswa_nopin."' and CAST(siswa_jenis_bayar AS UNSIGNED) BETWEEN ".$tmpstartdate->format('Ym')." and ".$tmpenddate->format('Ym')." group by siswa_detil_bayar;";
            $listpayment = $db->_select($query, array());
            $totalpayment = count($listpayment);
            //echo   $totalpayment;
            //echo   "<hr>";
            if($totalpayment>0)
            {
                $no=0;

                $grandtotal = 0;
                foreach ($listpayment as $key => $value) {
                    $no++;
                    $grandtotal+=(int)$value['totalbayar'];
                    $newarray = Array
                    (
                        'billCode' => '0' . $no,
                        'billName' => $value['siswa_detil_bayar'],
                        'billShortName' => $value['siswa_detil_bayar'],
                        'billAmount' => (int)$value['totalbayar']
                    );
                    array_push($arrayinfopayment, $value['siswa_detil_bayar']);
                    array_push($arraypayment, $newarray);
                }

                $totalbulan = $intervalDate->format('%m') ;
                $updatedate = $tmpstartdate;
                for($i=0;$i<=$totalbulan;$i++)
                {
                    
                    //echo  $i;
                    //echo  "<hr>";
                    $newdate = $i==0 ? $updatedate : $updatedate->add(new DateInterval("P1M"));
                    $new_siswa_tahunbulanbayar  =   $newdate->format('Ym');
                    $update_table = 'SISWA_NewDetailSMU';
                    $update_data = array(
                        'siswa_flag' => 'ybbbb'
                    );
                    $update_cond = array(
                        'siswa_jenis_bayar'=>$new_siswa_tahunbulanbayar,
                        'siswa_nopin' => $siswa_nopin,
                    );
                    $db->_update($update_table, $update_data, $update_cond);
                }
                $kodetime=time();
                $insert_table = 'SISWA_PayInvoiceSMU';
                $insert_data = array(
                    'pay_invoice_id' => 'SMA-' . $va_year.$va_nis,
                    'pay_siswa_nobukti' => $siswa_nopin,
                    'pay_tahun_ajaran' => $va_year,
                    'pay_jenis' => implode(",",$arrayinfopayment),
                    'pay_nominal' => $grandtotal,
                    'pay_mu_id' => 'IDR',
                    'pay_tanggal_bayar' => date("Y-m-d H:i:s"),
                    'pay_timestamp' => date("Y-m-d H:i:s"),
                    'pay_nobukti' => $kodetime ,
                    'pay_kuitansi_id' => $va,
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


                $insert_table = 'SISWA_NewPaymentSMU';
                $insert_data = array(
                    'siswa_nopin' => $siswa_nopin,
                    'siswa_tahun_ajaran' => '20' . $va_year,
                    'siswa_va' => $va,
                    'siswa_tangal_bayar' => date('Y-m-d H:i:s')
                );
                $db->_insert($insert_table, $insert_data);



                /**
                 * 
                 txn_code : PAYH2H~17~POKOK~SMU~12317000107170917
txn_cabang_code : 123
txn_id : 17181041:abel Glori
txn_currency_code :IDR
txn_currency_rate : 0
txn_date : now()
txn_input : now()
txn_desc : Jurnal H2H~12317000107170917 17181041
txn_flag : bbbbbbbbbb

                 * 
                 */
                $insert_table = 'GL_TxnHead';
                $insert_data = array(
                    'txn_code'=>"PAYH2H~".$va_year."~POKOK~SMU~".$va,
                    'txn_cabang_code'=>$va_code_unit,
                    'txn_id'=>$siswa_nopin.':'.$siswa_nama,
                    'txn_currency_code'=>'IDR',
                    'txn_currency_rate'=>0,
                    'txn_date'=>date('Y-m-d'),
                    'txn_input'=>date('Y-m-d H:i:s'),
                    'txn_desc'=>'Jurnal H2H~'.$va.' '.$siswa_nopin,
                    'txn_flag'=>'bbbbbbbbbb'
                );
                $db_gl->_insert($insert_table, $insert_data);


                $insert_table = 'GL_TxnDetail';
                $insert_data = array(
                    'txn_code'=>"PAYH2H~".$va_year."~POKOK~SMU~".$va,
                    'txn_gl_code'=>"11310",
                    'txn_seksi_code'=>"YPL",
                    'txn_amount_db'=>$grandtotal,
                    'txn_amount_cr'=>0,
                    'txn_desc'=>$siswa_nopin,
                    'txn_flag'=>'bbbbbbbbbb'
                );
                $db_gl->_insert($insert_table, $insert_data);



                $insert_table = 'GL_TxnDetail';
                $insert_data = array(
                    'txn_code'=>"PAYH2H~".$va_year."~POKOK~SMU~".$va,
                    'txn_gl_code'=>"12203",
                    'txn_seksi_code'=>"YPL",
                    'txn_amount_cr'=>$grandtotal,
                    'txn_amount_db'=>0,
                    'txn_desc'=>$siswa_nopin,
                    'txn_flag'=>'bbbbbbbbbb'
                );
                $db_gl->_insert($insert_table, $insert_data);






            }
            else{
                $status_array =$data_tidakditemukan;
            }

        }
        else
        {
            $status_array =$data_tidakditemukan;
        }
        
    }
    else
    {
        $status_array =$data_tidakditemukan;
    }



    if ($tmpstartdate > $tmpenddate) {
        $status_array = array
        (
            'isError' => true,
            'errorCode' => '99',
            'statusDescription' => 'Format Bulan Dan Tahun Salah'
        );
        $arraypayment = array();
        $arrayinfopayment = array();
    }

    $arraynew = Array
    (
        'currency' => CURRENCY,
        'billInfo1' => $va,
        'billInfo2' => implode(" | ",$arrayinfopayment),
        'billInfo3' => $siswa_nama,
        'billInfo4' => $nama_school,
        'billInfo5' => $tmpstartdate->format('M y'). ' SD ' . $tmpenddate->format('M y'),
        'billInfo6' =>  $intervalDate->format('%m')+1 . ' Bulan',
        'billInfo7' => '',
        'billInfo8' => '',
        'status' => $status_array
    );


    //_pre($arraynew);

    return $arraynew;

}
//payment(array('billKey1' => '12317000108170118'));


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
