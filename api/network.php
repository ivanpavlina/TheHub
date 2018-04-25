<?php
require_once("../conf/conf.main.php");
require_once('inc/audit.php');

// Return empty if not authorized
require_once('inc/auth.php');
if (!authorize_session()) {
    return json_encode(array(), JSON_NUMERIC_CHECK);
}

require_once("lib/mysql.php");
$db = new DB(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
$db->connect();
$result = array(); 

switch ($_GET['request']) {
    case 'host_list':
        $sq = "SELECT * FROM hosts;";
        $sqRes = $db->query($sq);

        foreach ($sqRes as $val) {
            $result[] = array(
                'name' => empty($val->name)?$val->host_name:$val->name,
                'ip_address' => $val->ip_address,
                'mac_address' => $val->mac_address,
                'color' => $val->chart_color,
                'active' => $val->active
            );
        }
    break;

    case 'setup_live_traffic_data':
        $type = $db->cleanup($_GET['type']);
        //$time_period = $db->cleanup($_GET['time_period']);

        $sq = "SELECT traffic.*, hosts.host_name, hosts.name, hosts.chart_color FROM (
            SELECT DATE_FORMAT(created, '%H:%i:%s') as created, local_ip, sum(bytes_count/run_interval) / 125000 as megabytes_per_second, round(sum(packet_count/run_interval)) as packets_per_second FROM (
              SELECT created, local_ip, bytes_count, packet_count, run_interval FROM traffic WHERE type='$type' and created > date_sub(now(), interval 5 minute)
            ) tmp group by created, local_ip
          ) traffic
          LEFT JOIN hosts ON traffic.local_ip=hosts.ip_address where hosts.mac_address is not null ORDER BY created ASC";

        $sqRes = $db->query($sq);

        $result['labels'] = array();
        $result['datasets'] = array('traffic'=>array(), 'packets'=>array());

        $hostsData = array();

        foreach ($sqRes as $val){
            $ip_address = $val->local_ip;
            $name = empty($val->name) ? $val->host_name:$val->name;
            $mbps = $val->megabytes_per_second;
            $packets = $val->packets_per_second;
            $timestamp = $val->created;
            $color = $val->chart_color;

            // Create label for each timestamp
            if (!in_array($timestamp, $result['labels'])) {
                array_push($result['labels'], $timestamp);
            }

            if (!array_key_exists($ip_address, $hostsData)) {
                $hostsData[$ip_address] = array();
                $dataset =  array(
                    'label' => $name,
					'data'  => array(),
                    'fill'  => False,
                    'borderColor' => $color,
                    'ip_address' => $ip_address);

                // Create traffic and packets datasets for each IP
                $result['datasets']['packets'][] = $dataset;
                $result['datasets']['traffic'][] = $dataset;
                
            }
            $hostsData[$ip_address][$timestamp] = array('traffic'=>$mbps, 'packets'=>$packets);
        }

        foreach($hostsData as $ip_address => $obj){
            foreach($result['datasets']['traffic'] as $idx => $dobj) {
                if ($dobj['ip_address'] == $ip_address) {
                    foreach ($result['labels'] as $timestamp) {
                        if (array_key_exists($timestamp, $obj)){
                            array_push($result['datasets']['traffic'][$idx]['data'], $obj[$timestamp]['traffic']);
                        } else {
                            array_push($result['datasets']['traffic'][$idx]['data'], 0);
                        }
                    }

                }
            }
            foreach($result['datasets']['packets'] as $idx => $dobj) {
                if ($dobj['ip_address'] == $ip_address) {
                    foreach ($result['labels'] as $timestamp) {
                        if (array_key_exists($timestamp, $obj)){
                            array_push($result['datasets']['packets'][$idx]['data'], $obj[$timestamp]['packets']);
                        } else {
                            array_push($result['datasets']['packets'][$idx]['data'], 0);
                        }
                    }

                }
            }
        }
    break;

    case 'update_traffic_data':
        $type = $db->cleanup($_GET['type']);

        $sq = "SELECT time_format(traffic.created, '%H:%i:%s') as created, traffic.bytes_count/125000 as mbps, round(traffic.packet_count) as packets, hosts.ip_address, hosts.host_name, hosts.name FROM (";
        $sq .= "SELECT created, local_ip, sum(bytes_count/run_interval) as bytes_count, sum(packet_count/run_interval) as packet_count from (";
        $sq .=  "SELECT created, local_ip, bytes_count, packet_count, run_interval from traffic where type='".$type."' and created=(";
        // Get only last timestamp on update
        $sq .=   "SELECT max(created) from traffic)";
        $sq .=  ") tmp group by created, local_ip";
        $sq .= ") traffic, hosts where traffic.local_ip=hosts.ip_address";
        $sqRes = $db->query($sq);

        $result['label'] = "";
        $result['datasets'] = array('traffic'=>array(), 'packets'=>array());

        $hostsData = array();
        $hostsInfo = array();

        foreach ($sqRes as $val){
            $ip_address = $val->ip_address;
            $name = empty($val->name) ? $val->host_name:$val->name;
            $mbps = $val->mbps;
            $packets = $val->packets;
            $timestamp = $val->created;

            $result['label'] = $timestamp;
            $result['datasets']['traffic'][$ip_address] = $mbps;
            $result['datasets']['packets'][$ip_address] = $packets;
        }
    break;

    case 'setup_historic_traffic_data':
        $type = $db->cleanup($_GET['type']);
        $time_period = $db->cleanup($_GET['time']);

        $sq = "SELECT traffic.*, hosts.ip_address, hosts.host_name, hosts.name, hosts.chart_color FROM (
                 SELECT DATE_FORMAT(created, '%d-%m-%Y %H:%i:%s') as created, local_ip, avg(bytes_per_second) /125000 as megabytes_per_second, avg(packets_per_second) as packets_per_second FROM (
                   SELECT created, local_ip, sum(bytes_count/run_interval) as bytes_per_second, sum(packet_count/run_interval) as packets_per_second FROM (
                     SELECT created, local_ip, bytes_count, packet_count, run_interval FROM traffic WHERE type='$type' and created > date_sub(now(), interval $time_period minute)
                   ) tmp group by created, local_ip
                 ) t1 group by ROUND(created/$time_period)
               ) traffic 
               LEFT JOIN hosts ON traffic.local_ip=hosts.ip_address where hosts.mac_address is not null ORDER BY created ASC";

        $sqRes = $db->query($sq);
        
        $result['labels'] = array();
        $result['datasets'] = array('traffic'=>array(), 'packets'=>array());

        $hostsData = array();

        foreach ($sqRes as $val){
            $ip_address = $val->local_ip;
            $name = empty($val->name) ? $val->host_name:$val->name;
            $mbps = $val->megabytes_per_second;
            $packets = $val->packets_per_second;
            $timestamp = $val->created;
            $color = $val->chart_color;

            // Create label for each timestamp
            if (!in_array($timestamp, $result['labels'])) {
                array_push($result['labels'], $timestamp);
            }

            if (!array_key_exists($ip_address, $hostsData)) {
                $hostsData[$ip_address] = array();
                $dataset =  array(
                    'label' => $name,
					'data'  => array(),
                    'fill'  => False,
                    'borderColor' => $color,
                    'ip_address' => $ip_address);

                // Create traffic and packets datasets for each IP
                $result['datasets']['packets'][] = $dataset;
                $result['datasets']['traffic'][] = $dataset;
                
            }
            $hostsData[$ip_address][$timestamp] = array('traffic'=>$mbps, 'packets'=>$packets);
        }

        foreach($hostsData as $ip_address => $obj){
            foreach($result['datasets']['traffic'] as $idx => $dobj) {
                if ($dobj['ip_address'] == $ip_address) {
                    foreach ($result['labels'] as $timestamp) {
                        if (array_key_exists($timestamp, $obj)){
                            array_push($result['datasets']['traffic'][$idx]['data'], $obj[$timestamp]['traffic']);
                        } else {
                            array_push($result['datasets']['traffic'][$idx]['data'], 0);
                        }
                    }

                }
            }
            foreach($result['datasets']['packets'] as $idx => $dobj) {
                if ($dobj['ip_address'] == $ip_address) {
                    foreach ($result['labels'] as $timestamp) {
                        if (array_key_exists($timestamp, $obj)){
                            array_push($result['datasets']['packets'][$idx]['data'], $obj[$timestamp]['packets']);
                        } else {
                            array_push($result['datasets']['packets'][$idx]['data'], 0);
                        }
                    }

                }
            }
        }
    break;


}

echo json_encode($result, JSON_NUMERIC_CHECK);

?>
