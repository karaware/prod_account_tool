<?php

//MySQLのログイン情報
//$dbname =  $_REQUEST['gw'];//本番環境
$dbname =  "asterisk_ncom_test";
//$host = '192.168.25.150';//本番環境
$host = 'localhost';
$user = 'root';
$pwd = 'q4l3o2o0g424arao';


//アカウント情報を変数に入れる
$name         = $_REQUEST['name'];
$callerid     = $_REQUEST['name'];
$secret       = $_REQUEST['secret'];
$setvar       = $_REQUEST['setvar'];
//2018/06/20追記
$customer     = $_REQUEST['customer'];
$frominternal = 'from-internal';

//ウミガメの場合
if($dbname == 'asterisk_ncom_u'){
    $frominternal = 'from-internal-umigame';
    $dbname = 'asterisk_ncom';
}

//DSN(Date Source Name)　([DSN接頭辞]:host=[ホスト名];dbname=[データベース名];charset=[文字コード])
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
//test用
    case "asterisk_ncom_test":
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
$setvar = "OUTNUM=" . $_REQUEST['setvar'];


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
    print("データベースに接続しました,,,<br>");
    }catch (PDOExpention $Exception){
    die('データベースに接続できませんでした。<br>' . $Exception->getMessage());
}

//データベースにデータを登録
try{
    //トランザクションを開始する。オートコミットがオフになる。10行目のcomitと17行目のrollBackとセット　（※今回はINSERT文一行だけのため、このセットはなくても良い)
    $pdo->beginTransaction();
    //SQL内の可変値にはプレースホルダを利用してSQLと外部の値を区別して安全にSQLを処理する（SQLインジェクションへの対策）
    $sql = "INSERT INTO sipfriends (name,callerid,context,insecure,type,host,secret,allow,nat,setvar) VALUES(:name,:callerid,:frominternal,'very','friend','dynamic',:secret,'all','yes',:setvar)";
    //ステートメントハンドラを格納する変数$stmhに格納
    //プリペアードステートメントを利用してSQLを安全に処理する
    //prepareメソッドを利用し、作成したSQLを引数に設定して実行するとSQLを解析してキャッシュするので2度目以降の実行が早くなる
    $stmh = $pdo -> prepare($sql);
    //bindValueメソッドでSQL内のプレースホルダに変数内の値を結びつける(バインドする)
    $stmh -> bindValue(':name',$name,PDO::PARAM_INT);
    $stmh -> bindValue(':callerid',$callerid,PDO::PARAM_INT);
    $stmh -> bindValue(':frominternal',$frominternal, PDO::PARAM_STR);
    $stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
    $stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
    //ステートメントハンドラに格納されたSQLを実行する
    $stmh -> execute();
    //変更をコミットする
    $pdo -> commit();
    print("DB名：" . $dbname . " のテーブル名：sipfriends にデータを" . $stmh -> rowCount() . "件、登録しました。<br><br>");
    //以下、登録結果表示のための処理
    //セレクト文を変数$sqlに格納
    $sql = "select * from sipfriends order by id desc limit 1";
    $stmh = $pdo -> prepare($sql);
    $stmh -> execute();
    //ステートメントハンドラからfetchメソッドを利用して結果のレコードに存在するすべてのカラムに入力された内容を返し、それを配列$rowに格納する
    $row = $stmh-> fetch(PDO::FETCH_ASSOC);
}catch(PDOException $Exception){
    //変更をロールバックする
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


<!--

    <table class="table table-bordered table-condensed" width="450" height="50" border="1" cellspacing="0" cellpadding="8">
    <thead>
    <tr><th>id</th><th>name</th><th>callerid</th><th>context</th><th>insecure</th><th>type</th><th>host</th><th>secret</th><th>allow</th><th>nat</th><th>setvar</th></tr>
    </thead>
    <tbody>
    <tr>
        <td><?php// print(htmlspecialchars($row['id'])) ?></td>
        <td><?php// print(htmlspecialchars($row['name'])) ?></td>
        <td><?php// print(htmlspecialchars($row['callerid'])) ?></td>
        <td><?php// print(htmlspecialchars($row['context'])) ?></td>
        <td><?php// print(htmlspecialchars($row['insecure'])) ?></td>
        <td><?php// print(htmlspecialchars($row['type'])) ?></td>
        <td><?php// print(htmlspecialchars($row['host'])) ?></td>
        <td><?php// print(htmlspecialchars($row['secret'])) ?></td>
        <td><?php// print(htmlspecialchars($row['allow'])) ?></td>
        <td><?php// print(htmlspecialchars($row['nat'])) ?></td>
        <td><?php// print(htmlspecialchars($row['setvar'])) ?></td>
    </tr>
    </tbody>
    </table>

-->


    <table class="table table-bordered table-condensed" width="450" height="50" border="1" cellspacing="0" cellpadding="8">
    <thead>
    <tr><th>id</th><th>name</th><th>secret</th><th>setvar</th></tr>
    </thead>
    <tbody>
    <tr>
        <td><?php print(htmlspecialchars($row['id'])) ?></td>
        <td><?php print(htmlspecialchars($row['name'])) ?></td>
        <td><?php print(htmlspecialchars($row['secret'])) ?></td>
        <td><?php print(htmlspecialchars($row['setvar'])) ?></td>
    </tr>
    </tbody>
    </table>


<!--
    <div class="panel panel-default">
    <div class="panel-heading"><h2 class="panel-title">PEER Details</h2></div>
    <div class="panel-body">
    <?php
/*
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
*/
    ?>
    </div>
    </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><h2 class="panel-title">Register String</h2></div>
        <div class="panel-body">
            <?php
/*
            if($dbname == "asterisk"){
                $db_ip = "202.78.218.73";
                print($name . ":" . $secret . "@" . $db_ip . "/" . $num ."<br><br>");
                $db_ip = "202.78.218.74";
                print($name . ":" . $secret . "@" . $db_ip . "/" . $num ."<br>");
            }else{
            print($name . ":" . $secret . "@" . $db_ip . "/" . $num ."<br>");
            }
*/
            ?>
        </div>
    </div>
-->

    <div class="panel panel-default">
        <div class="panel-heading"><h2 class="panel-title">Customer Name</h2></div>
        <div class="panel-body">
            <?php print($customer); ?>
        </div>
    </div>


        <br /><a href="account-tool.php">戻る</a></p>
</div>
</div>

<!-- ▼追加履歴用テーブル：historyに履歴を追加 -->
<?php
//MySQLのログイン情報
$dbname =  'gwat';
$host = 'localhost';
$user = 'root';
$pwd = 'q4l3o2o0g424arao';
//DSN(Date Source Name)　([DSN接頭辞]:host=[ホスト名];dbname=[データベース名];charset=[文字コード])
$dsn  = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';
$register_time = date('Y-n-j H:i:s');

//データベースに接続
    try{
        $pdo = new PDO($dsn,$user,$pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    }catch (PDOExpention $Exception){
        die('データベースに接続できませんでした。<br>' . $Exception->getMessage());
    }

//履歴追加    
    try{
        $sql = "INSERT INTO history (dbname,name,setvar,secret,user,register_time) VALUES(:dbname,:name,:setvar,:secret,:user,:register_time)";
        $stmh = $pdo -> prepare($sql);
        $stmh -> bindValue(':dbname',$dbname, PDO::PARAM_STR);
        $stmh -> bindValue(':name',$name,PDO::PARAM_INT);
        $stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
        $stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
        $stmh -> bindValue(':user',$user, PDO::PARAM_STR);
        $stmh -> bindValue(':register_time',$register_time, PDO::PARAM_STR);
        $stmh -> execute();
    }catch(PDOException $Exception){
        print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
    }
    $pdo = null;

?>

</body>
</html>






