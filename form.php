<?php

//MySQLのログイン情報
$dbname =  $_REQUEST['gw'];
$host = '202.78.218.212';
$user = 'root';
$pwd = '1qazxsw2';

//アカウント情報を変数に入れる
$name         = $_REQUEST['name'];
$callerid     = $_REQUEST['name'];
$secret       = $_REQUEST['secret'];
$setvar       = $_REQUEST['setvar'];
$frominternal = 'from-internal';

//ウミガメの場合
if($dbname == 'asterisk_ncom_u'){
    $frominternal = 'from-internal-umigame';
    $dbname = 'asterisk_ncom';
}

$dsn  = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';

//GWアドレスを変数に格納
switch($dbname){
    case "asterisk":
	$db_ip = "202.78.218.73";
	break;

    case "asterisk_sb":
	$db_ip = "202.78.218.75";
	break;

    case "asterisk_sb3":
	$db_ip = "202.78.218.81";
	break;

    case "asterisk_ncom":
	$db_ip = "202.78.218.85";
	break;
}

//$nameに格納されている下番から'00'を抜き、$numに格納する
$num = substr($name, 2);

//バリデーション(文字数と内容チェック)
$error = array();
if((strlen($name)  !== 12)){
    $error[] = "電話番号の文字数を確認してください。" ;
}
if(!preg_match('/^[0-9]+$/', $name)){
    $error[] = "電話番号に数字以外が入っています。";
}

if(strlen($setvar) > 11 || strlen($setvar) < 10){
    $error[] = "表示番号の文字数を確認してください。" ;
}
if(!preg_match('/^[0-9]+$/', $setvar)){
    $error[] = "表示番号に数字以外が入っています。";
}

if(strlen($secret) !== 16){
    $error[] = "パスワードの文字数を確認してください。" ;
}
if(!preg_match('/^[a-zA-Z0-9]+$/',$secret)){
    $error[] = "パスワードに英文字以外が入っています。";
}

//バリデーションエラー結果表示
$message = implode("",$error);
if(count($error) > 0){
    exit($message);
}

//setvarの先頭に"outnum="を追加
$setvar = "outnum=" . $_REQUEST['setvar'];
	
	
//データベースに接続
try{
    $pdo = new PDO($dsn,$user,$pwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    print("データベースに接続しました,,,<br>");
    }catch (PDOExpention $Exception){
	die('データベースに接続できませんでした。<br>' . $Exception->getMessage());
}

//データベースにデータを登録
try{
    $pdo->beginTransaction();
    $sql = "INSERT INTO sipfriends (name,callerid,context,insecure,type,host,secret,allow,nat,setvar) VALUES(:name,:callerid,:frominternal,'very','friend','dynamic',:secret,'all','yes',:setvar)";
    $stmh = $pdo -> prepare($sql);
    $stmh -> bindValue(':name',$name,PDO::PARAM_INT);
    $stmh -> bindValue(':callerid',$callerid,PDO::PARAM_INT);
    $stmh -> bindValue(':frominternal',$frominternal, PDO::PARAM_STR);
    $stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
    $stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
    $stmh -> execute();
    $pdo -> commit();
    print("データを" . $stmh -> rowCount() . "件、登録しました。<br><br>");
    $sql = "select * from sipfriends order by id desc limit 1";
    $stmh = $pdo -> prepare($sql);
    $stmh -> execute();
    $row = $stmh-> fetch(PDO::FETCH_ASSOC);
}catch(PDOException $Exception){
    $pdo->rollBack();
    print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
}
$pdo = null;
?>


<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
    <title>アドレス登録</title>
</head>

<body>
<div class="container-fluid">
<div class="table-responsive">
    <h3>【登録結果】</h3>
    <table class="table table-bordered table-condensed" width="450" height="50" border="1" cellspacing="0" cellpadding="8">
	<thead>
	<tr><th>id</th><th>name</th><th>callerid</th><th>context</th><th>insecure</th><th>type</th><th>host</th><th>secret</th><th>allow</th><th>nat</th><th>setvar</th></tr>
	</thead>
	<tbody>
	<tr>
	<td><?=htmlspecialchars($row['id'])?></td>
	<td><?=htmlspecialchars($row['name'])?></td>
	<td><?=htmlspecialchars($row['callerid'])?></td>
	<td><?=htmlspecialchars($row['context'])?></td>
	<td><?=htmlspecialchars($row['insecure'])?></td>
	<td><?=htmlspecialchars($row['type'])?></td>
	<td><?=htmlspecialchars($row['host'])?></td>
	<td><?=htmlspecialchars($row['secret'])?></td>
	<td><?=htmlspecialchars($row['allow'])?></td>
	<td><?=htmlspecialchars($row['nat'])?></td>
	<td><?=htmlspecialchars($row['setvar'])?></td>
	</tr>
	</tbody>
    </table>

    <div class="panel panel-default">
    <div class="panel-heading"><h2 class="panel-title">PEER Details</h2></div>
    <div class="panel-body">
	<?php
	print("username=" . $name);
	print("<br>type=friend");
	print("<br>tos=none");
	print("<br>sendrpid=no");
	print("<br>secret=" . $secret);
	print("<br>register=yes");
	print("<br>qualify=no");
	print("<br>notransfer=no");
	print("<br>nat=yes");
	print("<br>insecure=very");
	print("<br>host=" . $db_ip);
	print("<br>fromuser=" . $name);
	print("<br>fromdomain=" . $db_ip);
	print("<br>dtmfmode=inband");
	print("<br>disallow=all");
	print("<br>context=from-trunk");
	print("<br>canreinvite=no");
	print("<br>auth=plaintext");
	print("<br>allow=ulaw&alaw&gsm");

	//SB回線　代表組１の場合	
	if($dbname == "asterisk"){
	    $db_ip = "202.78.218.74";
	    print("<br>");
	    print("<br>username=" . $name);
	    print("<br>type=friend");
	    print("<br>tos=none");
	    print("<br>sendrpid=no");
	    print("<br>secret=" . $secret);
	    print("<br>register=yes");
	    print("<br>qualify=no");
	    print("<br>notransfer=no");
	    print("<br>nat=yes");
	    print("<br>insecure=very");
	    print("<br>host=" . $db_ip);
	    print("<br>fromuser=" . $name);
	    print("<br>fromdomain=" . $db_ip);
	    print("<br>dtmfmode=inband");
	    print("<br>disallow=all");
	    print("<br>context=from-trunk");
	    print("<br>canreinvite=no");
	    print("<br>auth=plaintext");
	    print("<br>allow=ulaw&alaw&gsm");
	}
	?>
    </div>
    </div>
    </div>

    <div class="panel panel-default">
    	<div class="panel-heading"><h2 class="panel-title">Rejister String</h2></div>
    	<div class="panel-body">
	    <p><? echo $name; ?>:<? echo $secret; ?>@<? echo $db_ip; ?>/<? echo $num; ?></p>
    	</div>
    	</div>

    	<br /><a href="account-tool.html">戻る</a></p>
</div>
</div>
</body>
</html>

























