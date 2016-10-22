<?php

$servername = 'servername';
$username='username';
$password='password';
$dbname='dbname';


if ($_POST['action'] == 'get'){


$connect = mysqli_connect($serverName, $username, $password, $dbname);
mysqli_set_charset( $connect, 'utf8');

if (!$connect){
    die("Connection filed: " . mysql_connect_error());
}

$sql = 'SELECT id, name, createdTime, price FROM tenders';
$result = mysqli_query($connect, $sql);

if (mysqli_num_rows($result) > 0){
    $dataArr = array();

    while($row = mysqli_fetch_assoc($result)){
      $thisRow = array('id' => $row['id'], 'name' => $row['name'], 'createdTime'=> $row['createdTime'], price => $row['price']);
      array_push($dataArr, $thisRow);

      } 
}else {
        $dataArr = array('responseType' => 'noResult');

    } 

      $dataJSON = json_encode($dataArr, JSON_UNESCAPED_UNICODE);
      echo $dataJSON;

mysqli_close($connect);
    }

elseif ($_POST['action'] == 'add'){

$name = $_POST['name'];
$price = $_POST['price'];

$connect = mysqli_connect($serverName, $username, $password, $dbname);
mysqli_set_charset($connect, 'utf8');
if (!mysqli_connect){
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "INSERT INTO tenders (name, createdBy, price) VALUES ('$name', '1', '$price')";

    if (mysqli_query($connect, $sql)){


        
        $dataArr = array('responseType'=>'recordAdded');
        $dataJSON = json_encode($dataArr, JSON_UNESCAPED_UNICODE);
        echo $dataJSON;
        }
    else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connect);
    }

    mysqli_close($connect);

}
elseif ($_POST['action'] == 'remove'){
    $connect = mysqli_connect($serverName, $username, $password, $dbname);
    $id = $_POST['id'];

    $sql = "DELETE FROM tenders WHERE id = '$id'";

    if (mysqli_query($connect, $sql)){
    echo "Record deleted!";
}
else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($connect);
}
elseif ($_POST['action'] == 'truncate'){

$connect = mysqli_connect($serverName, $username, $password, $dbname);
$sql = "TRUNCATE TABLE tenders";

if (mysqli_query($connect, $sql)){
    echo "Table truncated!";
}
else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($connect);
}



?>