<?php

    //MySQLのログイン情報
    $dbname =  $_REQUEST['gw'];
    $host = '202.78.218.212';
    $user = 'root';
    $pwd = '1qazxsw2';
    $dsn = 'mysql:dbname='.$dbname.';host='.$host;

    try{
         $pdo=new PDO($dsn,$user,$pass);
        }catch(Exception $e){
        	echo 'error' .$e->getMesseage;
        	exit('データベースに接続できませんでした。');
        }


	//入力された単語を分解
	$kensaku = htmlspecialchars($_REQUEST['kensaku']);
	$kensaku = trim($kensaku);
	$kensaku = str_replace("　"," ",$kensaku);
	
	if(stristr($kensaku," ")){
		$word = explode(" ",$kensaku);
		$count = count($word);
	}

$stmt = $pod->prepare("select * from sipfriends where name like :kensaku")
$stmt->bindParam(':kensaku',$kensaku);
$stmt->execute();

?>
