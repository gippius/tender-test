
// запрос в базу за таймстемпом последнего обновления




<?php



function dbCheckForUpdate(){
  $servername = 'localhost';
$username='root';
$password='password';
$dbname='tender1';

$connect = mysqli_connect($serverName, $username, $password, $dbname);
mysqli_set_charset( $connect, 'utf8');

if (!$connect){
    die("Connection filed: " . mysql_connect_error());
}

$sql = 'SELECT createdTime, doneTime FROM tenders';
$result = mysqli_query($connect, $sql);

while($row = mysqli_fetch_assoc($result)){
    $thisCreatedTime = $row['createdTime'];
    $thisDoneTime = $row['doneTime'];

    $dataArr = array('lastChangeTime' => $thisCreatedTime);


      } 


      $dataJSON = json_encode($dataArr, JSON_UNESCAPED_UNICODE);
      
mysqli_close($connect);
return $dataJSON;

}


date_default_timezone_set("Europe/Moscow");
header("Content-Type: text/event-stream\n\n");

$counter = rand(1, 10);
while (1) {
  // Every second, sent a "ping" event.
  $lastUpdateDB = dbCheckForUpdate();
  echo "event: ping\n";
  $curDate = date(DATE_ISO8601);
  echo 'data: {"time": "' . $curDate . '"}';
  echo 'data: {"text": "' . $lastUpdateDB. '"}';
  echo "\n\n";
  
  
  
  ob_end_flush();
  flush();
  sleep(1);
}
?>