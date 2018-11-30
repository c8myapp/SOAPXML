<?php
$d1 = new DateTime('2011-09-01');
$d2 = new DateTime('2012-12-01');
$end_date_month = (int)$d2->format('Ymd');

$array_month = array();
for ($i = -1; $i < 100; $i++) {

    if ($i == -1) {
        array_push($array_month,(int)$d1->format('m'));
    } else {

        $d1->add(new DateInterval("P1M"));
        $start_month_plus1 = $d1->format('m');
        if ($d1 <= $d2) {
            array_push($array_month,(int)$start_month_plus1);
        } else {
            break;
        }
    }
}

print_r($array_month);

