<?php
$servername = 'localhost';
$username='root';
$password='password';
$dbname='tender1';

session_start();

if ($_POST['action'] == 'check'){
        $connect = mysqli_connect($serverName, $username, $password, $dbname);
        mysqli_set_charset( $connect, 'utf8');
        if (!$connect){
            die("Connection failed: " . mysql_connect_error());
        }
        $login=$_POST['login'];
        $password=$_POST['password'];
        $sql = "SELECT login, password FROM users 
        WHERE login='$login' AND password='$password'";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) > 0){
            $dataArr = array();
            while($row = mysqli_fetch_assoc($result)){
            $thisRow = array('login' => $row['login'], 'password' => $row['password']);
            array_push($dataArr, $thisRow);
            $_SESSION['username'] = $row['login'];
            } 
        }else {
                $dataArr = array('responseType' => 'noResult');
            } 
            $newArr = array('responseType'=>'loginChecked', 'data'=>$dataArr);
            $dataJSON = json_encode($newArr, JSON_UNESCAPED_UNICODE);
            echo $dataJSON;


        mysqli_close($connect);
    }
    elseif ($_POST['action'] == 'logout'){
       $_SESSION = array();

            $newArr = array('responseType'=>'logOut', 'data'=>'Successfully logged out!');
            $dataJSON = json_encode($newArr, JSON_UNESCAPED_UNICODE);
            echo $dataJSON;
    }
?>