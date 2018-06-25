<?php

//MySQLのログイン情報
$dbname =  'inventory_test';
$host = 'localhost';
$user = 'root';
$pwd = 'q4l3o2o0g424arao';
//DSN(Date Source Name)　([DSN接頭辞]:host=[ホスト名];dbname=[データベース名];charset=[文字コード])
$dsn  = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';

$query_array = array();
$query_array_int = 0;
$iplus = 0;

//データベースに接続
    try{
        //PDO(PHP Date Objects)による接続
        //$変数 = new PDO([DSN],[ユーザ名],[パスワード])  [DSN]は上で宣言した変数$dsnの中身([DSN接頭辞]:host=[ホスト名];dbname=[データベース名];charset=[文字コード])が入る
        $pdo = new PDO($dsn,$user,$pwd);
        //以下はエラーハンドラ
        //ERRMODEにERRMODE_EXCEPTIONを選択する(エラーを検知した時に例外を投げる。4行下のcatchへ移動する。)
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //プリペアドステートメントに関する属性。ATTR_EMULATE_PREPARESにfalseを設定する
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        //print("データベースに接続しました,,,<br>");
    }catch (PDOExpention $Exception){
        die('データベースに接続できませんでした。<br>' . $Exception->getMessage());
    }
    $stmh = $pdo->query('SELECT prefix FROM inventory_test.gw_accounts_ncom WHERE CHAR_LENGTH(prefix) < 4 AND CHAR_LENGTH(prefix) > 2 AND name NOT LIKE "%解約%" ORDER BY prefix ASC;');
    if(!$stmh){
        $info = $pdo -> errorInfo();
        exit($info[2]);
    }else{
        while($table = $stmh->fetch(PDO::FETCH_NUM)){
	    $query_array[] = $table[0];
        }
    }



    for($i = 0 ; $i < 899 ; $i++){
        $iplus = $i + 101;
        $query_array_int = intval($query_array[$i]);
        if($query_array_int == $iplus){
        }else{
            print_r($iplus);  //$iplusを戻り値として返す
            break;
        }
    }

?>


