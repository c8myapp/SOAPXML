<?php
define('LINK_WSDL','http://localhost/h2hns/server.php?wsdl');


define('CURRENCY', '360');


define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_sms');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DEBUG', TRUE);





$tmp_data = array(
    array('nis'=>'17181041','year'=>'2017','month'=>'9'),
    array('nis'=>'15161002','year'=>'2017','month'=>'7'),
    array('nis'=>'15161007','year'=>'2017','month'=>'8'),
    array('nis'=>'15161157','year'=>'2017','month'=>'9'),
    array('nis'=>'15161159','year'=>'2017','month'=>'10'),
    array('nis'=>'15161124','year'=>'2017','month'=>'11'),
    array('nis'=>'15161082','year'=>'2017','month'=>'12'),
    array('nis'=>'15161162','year'=>'2017','month'=>'4'),
    array('nis'=>'15161083','year'=>'2017','month'=>'3'),
    array('nis'=>'15161086','year'=>'2017','month'=>'2'),
    array('nis'=>'15161128','year'=>'2018','month'=>'1'),
    array('nis'=>'15161129','year'=>'2018','month'=>'2'),
    array('nis'=>'15161046','year'=>'2018','month'=>'3'),
    array('nis'=>'15161047','year'=>'2018','month'=>'4'),
    array('nis'=>'15161167','year'=>'2018','month'=>'5'),
    array('nis'=>'15161051','year'=>'2018','month'=>'6'),
    array('nis'=>'15161137','year'=>'2018','month'=>'7'),
    array('nis'=>'15161020','year'=>'2018','month'=>'8'),
    array('nis'=>'15161174','year'=>'2018','month'=>'9'),
    array('nis'=>'15161140','year'=>'2018','month'=>'10')

);


function _pre($array = array())
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
