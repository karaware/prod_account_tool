<?php

//MySQLのログイン情報
//$dbname =  $_REQUEST['gw'];//本番環境
//$dbname =  "asterisk_ncom_test";
$dbname =  "asterisk_sb_test";
//$host = '192.168.25.150';//本番環境
$host = 'localhost';
$user = 'root';
$pwd = 'XXXXXX';


//アカウント情報を変数に入れる
$name         = $_REQUEST['name'];
$callerid     = $_REQUEST['name'];
$secret       = $_REQUEST['secret'];
$setvar       = $_REQUEST['setvar'];
//2018/06/20追記
$customer     = $_REQUEST['customer'];
$frominternal = 'from-internal';
$cut2         = 2;
$cut7         = 7;
$prodline     = "プロディライト回線サーバ";

$originnum    = substr( $name , $cut2 , strlen($name)-$cut2 );
//$originfdn    = substr( $setvar , $cut7 , strlen($setvar)-$cut7 );
$originfdn    = $setvar;

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

    case "asterisk_sb_test":
    $db_ip = "202.78.218.75";
    break;
}


//代表組番号$dbnameから判断して変数に格納
switch($dbname){
    case "asterisk":
    $groups = 1;
    break;

    case "asterisk_sb":
    $groups = 2;
    break;

    case "asterisk_sb3":
    $groups = 3;
    break;

    case "asterisk_ncom":
    $groups = "-";
    break;
//test用
    case "asterisk_sb_test":
    $groups = 2;
    break;

}
//$groups = 2;


//print_r($groups);
//exit();


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


<?php
//空きプレフィックスを取得
require_once("prefix_check.php");
$prefix_result = get_prefix($dbname);
//print_r($result);

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


<!-- 結果表示  --> 
    <table class="table table-bordered table-condensed" width="450" height="50" border="1" cellspacing="0" cellpadding="8">
    <thead>
    <tr><th>id</th><th>name</th><th>secret</th><th>setvar</th><th>prefix</th></tr>
    </thead>
    <tbody>
    <tr>
        <td><?php print(htmlspecialchars($row['id'])) ?></td>
        <td><?php print(htmlspecialchars($row['name'])) ?></td>
        <td><?php print(htmlspecialchars($row['secret'])) ?></td>
        <td><?php print(htmlspecialchars($row['setvar'])) ?></td>
        <td><?php print($prefix_result) ?></td>
    </tr>
    </tbody>
    </table>

    <!-- 登録先GW IPアドレス -->
    <div class="panel panel-default">
        <div class="panel-heading"><h2 class="panel-title">Registered GW IP address</h2></div>
        <div class="panel-body">
            <?php print($db_ip);
            if($db_ip == "202.78.218.73"){
                print("<br>202.78.218.74");
            }
            ?>
        </div>
    </div>

    <!-- お客様名表示 -->
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
$gwat_dbname =  'gwat';
$host = 'localhost';
$user = 'root';
$pwd = 'XXXXXX';
//DSN(Date Source Name)　([DSN接頭辞]:host=[ホスト名];dbname=[データベース名];charset=[文字コード])
$dsn  = 'mysql:host=' . $host . ';dbname=' . $gwat_dbname . ';charset=utf8';
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
        $stmh -> bindValue(':dbname',$gwat_dbname, PDO::PARAM_STR);
        $stmh -> bindValue(':name',$name,PDO::PARAM_INT);
        $stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
        $stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
        $stmh -> bindValue(':user',$user, PDO::PARAM_STR);
        $stmh -> bindValue(':register_time',$register_time, PDO::PARAM_STR);
        $stmh -> execute();
    }catch(PDOException $Exception){
        print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
    }

//InventryDBに追加
    //N-Com番号の場合
    if($dbname == "asterisk_ncom_test"){
        try{
            $sql = "INSERT INTO inventory_test.gw_accounts_ncom (name,secret,fd_number,ipaddr,defaultuser,setvar,prefix,setting_date,customer_name) VALUES (:name,:secret,:originfdn,:prodline,:originnum,:setvar,:prefix_result,cast(NOW() AS DATE),:customer);";
            //$sql = "INSERT INTO inventory.gw_accounts_ncom (name,secret,fd_number,ipaddr,defaultuser,setvar,prefix,setting_date,customer_name) VALUES (:name,:secret,:originfdn,:prodline,:originnum,:setvar,:prefix_result,cast(NOW() AS DATE),:customer);";
            $stmh = $pdo -> prepare($sql);
            $stmh -> bindValue(':name',$name,PDO::PARAM_INT);
            $stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
            $stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
            $stmh -> bindValue(':customer',$customer, PDO::PARAM_STR);
            $stmh -> bindValue(':originnum',$originnum, PDO::PARAM_INT);
            $stmh -> bindValue(':originfdn',$originfdn, PDO::PARAM_INT);
            $stmh -> bindValue(':prefix_result',$prefix_result, PDO::PARAM_INT);
            $stmh -> bindValue(':prodline',$prodline, PDO::PARAM_STR);
            $stmh -> execute();
        }catch(PDOException $Exception){
            print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
        }
        $pdo = null;
    //SB番号の場合
    }else{
        try{
            //$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES ($name,$originnum,'$secret',$originfdn,'$prodline','$setvar',$prefix_result,$groups,cast(now() as date),'$customer');";
            $sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,groups,setting_date,customer_name) VALUES (:name,:originnum,:secret,:originfdn,:prodline,:setvar,:prefix_result,:groups,cast(now() as date),:customer);";
            $stmh = $pdo -> prepare($sql);
            $stmh -> bindValue(':name',$name, PDO::PARAM_INT);
            $stmh -> bindValue(':originnum',$originnum, PDO::PARAM_INT);
            $stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
            $stmh -> bindValue(':originfdn',$originfdn, PDO::PARAM_INT);
            $stmh -> bindValue(':prodline',$prodline, PDO::PARAM_STR);
            $stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
            $stmh -> bindValue(':prefix_result',$prefix_result, PDO::PARAM_INT);
            $stmh -> bindValue(':groups',$groups, PDO::PARAM_INT);
            $stmh -> bindValue(':customer',$customer, PDO::PARAM_STR);
            $stmh -> execute();
        }catch(PDOException $Exception){
            print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
        }
        $pdo = null;
    }


?>

</body>
</html>






