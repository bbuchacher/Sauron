<?php 
require 'vendor/autoload.php';
$next = $_GET[next];

$api = $_POST[api];
$api_key = $_POST[api_key];
$query = $_POST[query];
$query =  strval($query);


$searchfields = array();

$service_banner = $_POST[service_banner];
$host_country = $_POST[host_country];
$host_geolocation = $_POST[host_geolocation];
$host_ip = $_POST[host_ip];
$service_port = $_POST[service_port];
$service_protocol = $_POST[service_protocol];
$rapid7_hash = $_POST[rapid7_hash];
$service_type = $_POST[service_type];

$query = "'". $query . "'";

if($service_banner != NULL){
    $service_banner = 'service_banner';
    array_push($searchfields, $service_banner);
};
if($host_country != NULL){
    $host_country = 'host_country';
    array_push($searchfields, $host_country);
};
if($host_geolocation != NULL){
    $host_geolocation = 'host_geolocation';
    array_push($searchfields, $host_geolocation);
};
if($host_ip != NULL){
    $host_ip = 'host_ip';
    array_push($searchfields, $host_ip);
};
if($service_port != NULL){
    $service_port = 'service_port';
    array_push($searchfields, $service_port);
};
if($service_protocol != NULL){
    $service_protocol = 'host_ip';
    array_push($searchfields, $service_protocol);
};
if($rapid7_hash != NULL){
    $rapid7_hash = 'rapid7_hash';
    array_push($searchfields, $rapid7_hash);
};
if($service_type != NULL){
    $service_type = 'service_type';
    array_push($searchfields, $service_type);
};

$fieldcount = count($searchfields);

if($fieldcount == '0') {
    array_push($searchfields ,'service_port','host_ip','host_country','service_name','service_protocol','service_banner','service_banner','host_geolocation','service_type','rapid7_hash');
};

$es = new Elasticsearch\Client(
    array(
        'hosts' => array(
            '192.168.1.1:9200',
            '192.168.1.2:9200',
            '192.168.1.3:9200',
            '192.168.1.4:9200'
        )
    )
);


// Search:
//Horibly Hackish way to get around the lack of documentation with ElasticSearch PHP API and setting a custom endpoint. 

function scan_elasticsearch($scan_token,$api,$api_key){
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://192.168.1.13:9200/_search/scroll?scroll=1m',
    CURLOPT_POSTFIELDS => $scan_token
));
$results = curl_exec($curl);
$results = json_decode($results, true);
$scan_token = $results['_scroll_id'];
$count = count($results['hits']['hits']);

$count = $count - 1;
if ($api == 1) {
    //Validate api_key 
    include('lib/config.php');
    $api_key = mysql_real_escape_string($api_key); 
    $query = "SELECT api_key FROM users WHERE api_key= '$api_key' LIMIT 1"; 
    $result = mysql_query($query) or die(mysql_error());

    if ($row = mysql_fetch_array($result)){
      echo json_encode($results['hits']['hits']);
    }
    else{
        print "Invalid or Missing API Key";
    }
    
} else {
include('lib/header.php');  
echo "<div class='container' style='overflow: auto'>";
echo "<h3>Total Results : ".$results['hits']['total']."</h3>";
echo "<table class='table table-striped'><tr>";
echo "<th>IP</th><th>PORT</th><th>COUNTRY</th><th>PROTOCOL</th><th>SERVICE</th></tr>";
foreach (range('0', $count) as $i) {
    echo "<tr><td>".$results['hits']['hits'][$i]['_source']['host_ip'] . "</td> "; 
    echo "<td>".$results['hits']['hits'][$i]['_source']['service_port'] . "</td> "; 
    echo "<td>".$results['hits']['hits'][$i]['_source']['host_country'] . "</td> "; 
    echo "<td>".$results['hits']['hits'][$i]['_source']['service_protocol'] . "</td> "; 
    echo "<td>".$results['hits']['hits'][$i]['_source']['service_type'] . "</td> "; 
};
echo "</table>";
echo "</div>";

echo '<ul class="pager">';
echo '<li><a href="/search.php?next='. $scan_token . '">Next</a></li>';
echo '</ul>';

include('lib/footer.php');
};
};

function getscan_token($es,$searchfields,$query,$api,$api_key){
$params = array(
    'index' => 'critical_io',
    'search_type'=> 'scan',
    'scroll' => '2m',
    'type'  => 'scan_data',
    'from'  => '0',
    'size'  => '25',
    'body' => array(
        'query' => array(
            'multi_match' => array(
                'query' => $query,
                'fields' => $searchfields
                )
            )
        )
    );


$results = $es->search($params);
$scan_token = $results['_scroll_id'];
scan_elasticsearch($scan_token,$api,$api_key);
}
if (isset($next)) {
    $scan_token = $next;
    scan_elasticsearch($scan_token,$api,$api_key);

}

else{
    getscan_token($es,$searchfields,$query,$api,$api_key);
}







?>