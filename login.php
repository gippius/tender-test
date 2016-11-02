<?php
$servername = 'localhost';
$username='root';
$password='password';
$dbname='tender1';

session_start();

if ($_POST['action'] == 'login'){
    $connect = mysqli_connect($serverName, $username, $password, $dbname);
    mysqli_set_charset( $connect, 'utf8');
    if (!$connect){
        die("Connection failed: " . mysql_connect_error());
    }
    $login=$_POST['login'];
    $password=$_POST['password'];
    $sql = "SELECT name, login, password, type, balance FROM users
    WHERE login='$login' AND password='$password'";
    $result = mysqli_query($connect, $sql);
    if (mysqli_num_rows($result) > 0){
        $dataArr = array();
        while($row = mysqli_fetch_assoc($result)){
            $thisRow = array('name'=>$row['name'], 'login' => $row['login'], 'usertype'=>$row['type'],
            'balance'=>$row['balance']);
            array_push($dataArr, $thisRow);
            $_SESSION['username'] = $row['name'];
            $_SESSION['login'] = $row['login'];
            $_SESSION['usertype'] = $row['type'];
            $_SESSION['balance'] = $row['balance'];
            
        }
        respond_with_data('loggedIn', $dataArr);
    }else {
        throw_error('No valid session found');
    }
    mysqli_close($connect);
}
elseif ($_POST['action'] == 'sessioncheck'){
    
    if ((isset($_SESSION['username'])) && (isset($_SESSION['login'])) && (isset($_SESSION['usertype'])) && (isset($_SESSION['balance']))){
        $dataArr = array();
            $thisRow = array('name'=>$_SESSION['username'],'login'=>$_SESSION['login'],'usertype'=>$_SESSION['usertype'],'balance'=>$_SESSION['balance']);
            array_push($dataArr, $thisRow);
        
        respond_with_data('loggedIn', $dataArr);
    }
    else {
        throw_error('sessionNotFound');
    }
}
elseif ($_POST['action'] == 'logout'){
    $pastLogin = $_SESSION['login'];
    $_SESSION = array();
    $newArr = array('responseType'=>'loggedOut', 'data'=>'Successfully logged out, ' . $pastLogin);
    $dataJSON = json_encode($newArr, JSON_UNESCAPED_UNICODE);
    echo $dataJSON;
}

function throw_error($message){
    $dataArr = array('responseType'=>'error', 'message'=>$message);
    $dataJSON = json_encode($dataArr, JSON_UNESCAPED_UNICODE);
    echo $dataJSON;
}

function respond_with_data($responseType, $dataArr){
    $newArr = array('responseType'=>$responseType, 'data'=>$dataArr);
    $dataJSON = json_encode($newArr, JSON_UNESCAPED_UNICODE);
    echo $dataJSON;
}
?>