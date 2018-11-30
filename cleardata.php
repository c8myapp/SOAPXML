<?php


require_once 'database.php';
require_once 'var.php';


$db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);



    $query_trans ="delete FROM SISWA_NewPaymentSMU";
    $data_trans = $db->_customexec($query_trans);

    $query_trans = "delete FROM SISWA_PayInvoiceSMU where pay_siswa_nobukti='17181041'";

    $data_trans = $db->_customexec($query_trans);

    $query_trans = "update SISWA_NewDetailSMU set siswa_flag='bbbbb'";
    //echo "<hr>";
    $data_trans = $db->_customexec($query_trans);



//header("Location: ./index.php")


?>