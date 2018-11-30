<?
require_once 'database.php';
require_once 'var.php';


$db = new database(DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_USER, DB_PASS);
?>

<html>
<head></head>
<style>
    a {
        text-decoration: none;
        color: blue;
        font-size: 40px;
    }

    a:hover {
        font-size: 44px;
        font-weight: bold;
    }
</style>
<body>

<table align="">
    <tr>
        <td><a href="inquiry.php" target="inquiry">Inquiry</a></td>
        <td style="padding-left:150px;" rowspan="100">
            <table>

                <tr>
                    <td>
                        <table style="border-collapse:collapse;text-align:center; width: 400px;" border="1">
                            <tr>
                                <td>NIS</td>
                                <td>TAHUN</td>
                                <td>BULAN</td>
                            </tr>
                            <?php

                            foreach ($tmp_data as $k => $v) {
                                if ($k < 10) {

                                    ?>

                                    <tr>
                                        <td><?= $v['nis'] ?></td>
                                        <td><?= $v['year'] ?></td>
                                        <td><?= $v['month'] ?></td>
                                    </tr>

                                    <?php
                                }
                            }
                            ?>
                        </table>


                    </td>
                    <td>
                        <table style="border-collapse:collapse;text-align:center; width: 400px;" border="1">
                            <tr>
                                <td>NIS</td>
                                <td>TAHUN</td>
                                <td>BULAN</td>
                            </tr>
                            <?php

                            foreach ($tmp_data as $k => $v) {
                                if ($k > 9 && $k<=20) {

                                    ?>

                                    <tr>
                                        <td><?= $v['nis'] ?></td>
                                        <td><?= $v['year'] ?></td>
                                        <td><?= $v['month'] ?></td>
                                    </tr>

                                    <?php
                                }
                            }
                            ?>
                        </table>


                    </td>

                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td><a href="payment.php" target="payment">Payment</a></td>
    </tr>
    <tr>
        <td><a href="cleardata.php">Clear Data</a></td>
    </tr>
    <tr>
        <td style="color:grey;font-size:30px;">Reversal</td>
    </tr>
</table>
<hr>
<h1>PAYMENT SUCCESS</h1>

<?php
$query = "select * from SISWA_NewPaymentSMU;";
$LogPayment= $db->_select($query, array());

?>
<table style="border-collapse:collapse;text-align:center; width: 1000px;" border="1">
    <tr>
        <th>NIS</th>
        <th>Tahun Ajaran</th>
        <th>VA</th>
        <th>Tanggal Bayar</th>
    </tr>
    <?php

    foreach ($LogPayment as $k =>$v)
    {
        ?>
        <tr>
            <td><?=$v['siswa_nopin']?></td>
            <td><?=$v['siswa_tahun_ajaran']?></td>
            <td><?=$v['siswa_va']?></td>
            <td><?=$v['siswa_tangal_bayar']?></td>
        </tr>

        <?php
    }

    ?>

</table>

</body>
</html>