<?php

$servername = 'localhost';
$username='root';
$password='password';
$dbname='tender1';

session_start();

if ($_POST['action'] == 'get'){
    $connect = mysqli_connect($serverName, $username, $password, $dbname);
    mysqli_set_charset($connect, 'utf8');
    if (!$connect){
        die('die die die');
    }
    // разным пользователям - разная выдача
    if ($_SESSION['usertype'] == 'client'){
        $sql = 'SELECT id, name, createdTime, price, isDone FROM tenders';
    } else {
        $sql = 'SELECT id, name, createdTime, price, isDone FROM tenders WHERE isDone LIKE 0';
    }
    
    $result = mysqli_query($connect, $sql);
    
    if (mysqli_num_rows($result) > 0){
        $dataArr = array();
        while($row = mysqli_fetch_array($result)){
            $thisRow = array('id' => $row['id'], 'name' => $row['name'], 'createdTime'=> $row['createdTime'], 'price' => $row['price'], 'isDone'=>$row['isDone']);
            array_push($dataArr, $thisRow);
        }
        respond_with_data('dataSelected', $dataArr);
    }else {
        throw_error('noResult');
    }
    
    mysqli_close($connect);
}
elseif ($_POST['action'] == 'add'){
    if (!(isset($_SESSION['usertype'])) || ($_SESSION['usertype'] != 'client')){
        throw_error('Only authorized client can add new tenders, sorry');
        return;
    }
    
    $name = test_name_input($_POST['name']);
    $price = test_price_input($_POST['price']);
    
    $connect = mysqli_connect($serverName, $username, $password, $dbname);
    mysqli_set_charset($connect, 'utf8');
    
    $sql = "INSERT INTO tenders (name, createdBy, price, isDone) VALUES ('$name', '1', '$price', '0')";
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
elseif (($_POST['action'] == 'markcompleted')){
    
    if  (!(isset($_SESSION['login']))){
        throw_error('Unauthorized users cannot bid, sorry');
        return;
    }
    
    $connect = mysqli_connect($serverName, $username, $password, $dbname);
    $tenderId = $_POST['id'];
    $userName = $_SESSION['login'];
    $userType = $_SESSION['usertype'];
    
    if ($_SESSION['usertype'] == 'client'){
        throw_error('Clients cannot bid, sorry');
        return;
    } elseif ($_SESSION['usertype'] == 'bidder'){
        
        $sql = "SELECT price FROM tenders WHERE id='$tenderId' LIMIT 1";
        if ($result = mysqli_query($connect, $sql) or die ('error price fetching')){
            while ($row = mysqli_fetch_assoc($result)){
                $price = $row['price'];
            }
        }
        else {
            echo 'price fetch error';
        }
        
        $sql = "SELECT balance FROM users WHERE login='$userName' LIMIT 1";
        if ($result = mysqli_query($connect, $sql) or die ('error balance fetching')){
            while ($row = mysqli_fetch_assoc($result)){
                $balance = $row['balance'];
            }
        }
        else {
            echo 'balance updating error';
        }
        $newBalance = $balance + $price;
        $sql = "UPDATE users SET balance='$newBalance' WHERE login='$userName'";
        mysqli_query($connect, $sql) or die ('error balance set');
        $_SESSION['balance'] = $newBalance;
        
        $sql = "UPDATE tenders SET isDone=1 WHERE id='$tenderId'";
        mysqli_query($connect, $sql) or die ('error done set');
        
        $dataArr = array('responseType'=>'tenderCompleted');
        $dataJSON = json_encode( $dataArr, JSON_UNESCAPED_UNICODE);
        echo $dataJSON;
        
        mysqli_close($connect);
    } else {
        throw_error('Cannot authorize you and therefore cannot mark tender as completed, sorry');
    }
}
elseif ($_POST['action'] == 'truncate'){
    $connect = mysqli_connect($serverName, $username, $password, $dbname);
    $sql = "TRUNCATE TABLE tenders";
    if (mysqli_query($connect, $sql)){
        $dataArr = array('responseType'=>'tableTruncated');
        $dataJSON = json_encode($dataArr, JSON_UNESCAPED_UNICODE);
        echo $dataJSON;
    }
    else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    mysqli_close($connect);
}

function test_name_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function test_price_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function throw_error($message){
    $dataArr = array('responseType'=>'error', 'message'=>$message);
    $dataJSON = json_encode($dataArr, JSON_UNESCAPED_UNICODE);
    echo $dataJSON;
    return;
}

function respond_with_data($responseType, $dataArr){
    $newArr = array('responseType'=>$responseType, 'data'=>$dataArr);
    $dataJSON = json_encode($newArr, JSON_UNESCAPED_UNICODE);
    echo $dataJSON;
}
?>