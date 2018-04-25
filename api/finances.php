<?php

require_once("../conf/conf.main.php");
require_once('inc/audit.php');

// Return empty if not authorized
require_once('inc/auth.php');
if (!authorize_session()) {
    return json_encode(array(), JSON_NUMERIC_CHECK);
}

require("lib/mysql.php");
$result = array();

switch ($_GET['request']) {
    case 'installments_list':

        $db = new DB(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

        $sq = "select shop_name, full_amount, number_of_installments, payment_start, description from payments_installments";
        $sqRes = $db->query($sq);

        foreach($sqRes as $id => $obj) {
            $result[$id] = array();
            $result[$id]['shop_name'] = $obj->shop_name;
            $result[$id]['full_amount'] = $obj->full_amount;
            $result[$id]['number_of_installments'] = $obj->number_of_installments;
            $result[$id]['description'] = $obj->description;
            $result[$id]['installment_amount'] =  round($obj->full_amount / $obj->number_of_installments, 2);
            
            $payment_start = new DateTime($obj->payment_start, new DateTimeZone("Europe/Zagreb"));

            $result[$id]['paid_installments'] = $payment_start->diff(new DateTime())->m;
            $result[$id]['payment_start'] = $payment_start->format("d.m.Y");
            $result[$id]['payment_end'] = $payment_start->add(new DateInterval("P".$obj->number_of_installments."M"))->format("d.m.Y");
        }

        break;
}


echo json_encode($result, JSON_NUMERIC_CHECK);

?>
