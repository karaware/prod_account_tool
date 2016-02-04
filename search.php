<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ▼jQuery本体 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <!-- ▼バリデーション -->
    <script type="text/javascript" src="check.js"></script>
    <script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/languages/jquery.validationEngine-ja.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="css/validationEngine.jquery.css">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
    <title>閲覧</title>
</head>

<body>
    <!-- 1.ナビゲーションバーの設定 -->
    <nav class="navbar navbar-default navbar-inverse">
        <div class="container">
            <!-- 2.ヘッダ情報 -->
            <div class="navbar-header">
                <a href="#" class="navbar-brand"><img src="logo_qloog.gif"></a>
            </div>
            <!-- 3.リストの配置 -->
            <ul  class="nav navbar-nav">
                <li><a href="account-tool.php">アカウント追加</a></li>
                <li class="active"><a href="#">閲覧・検索</a></li>
            </ul>
            <!-- 4.ボタン -->
            <button type="button" class="btn btn-default navbar-btn">
            <span class="glyphicon glyphicon-envelope"></span>
            </button>
        </div>
    </nav>

<div class="container-fluid">
    <h2 class="page-header">sipfriendsテーブル閲覧</h2>

    <!-- ▼検索フォーム -->
    <div class="panel panel-default">
        <div class="panel-heading">&emsp;検索フォーム</div>
        <div class="panel-body">
        <form id="form_2" name="input" action="./search.php" method="POST">
            <label>データベース</label>
            <select name="gw" id="gw" class="validate[required]">
		<option value = "">▼DBを選択して下さい。</option>
                <option value = "asterisk">asterisk (SB代表1)</option>
                <option value = "asterisk_sb">asterisk_sb (SB代表2)</option>
                <option value = "asterisk_sb3">asterisk_sb3 (SB代表3)</option>
                <option value = "asterisk_ncom">asterisk_ncom (NTT-Com)</option>
            </select>&emsp;
            <label>"name"の値で検索 </label>
            <input type="text" name="search_key">
            <input type="submit" value="検索" class="submit" id = "submit">
        </form>
	</div>
    </div>
    <?php
    //MySQLのログイン情報
    //$dbname =  'test';
    $host = '192.168.25.150';
    $user = 'root';
    $pwd = 'q4l3o2o0g424arao';
    $dbname = $_REQUEST['gw'];
    //if(empty($dbname)){
        //$dbname = "asterisk";
    //}
    $search_key = $_REQUEST['search_key'];
    //DSN(Date Source Name)　([DSN接頭辞]:host=[ホスト名];dbname=[データベース名];charset=[文字コード])
    $dsn  = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';

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
    if(empty($search_key)){
        $sql = "SELECT id,name,secret,context,setvar,ipaddr FROM sipfriends ORDER BY id ASC";
        $stmh = $pdo -> prepare($sql);
        $stmh -> execute();
    }else{
        $search_key = '%' . $search_key . '%';
        $sql = "SELECT id,name,secret,context,setvar,ipaddr FROM sipfriends WHERE name LIKE :search_key ORDER BY id ASC";
        $stmh = $pdo -> prepare($sql);
        $stmh -> bindValue(':search_key',$search_key,PDO::PARAM_INT);
        $stmh -> execute();
    }

    if(!$stmh){
        $info = $pdo -> errorInfo();
        exit($info[2]);
    }
    ?>

   <div class="panel panel-primary">
    <div class="panel-heading"><h4>&emsp;<?php print('表示中データベース：' . $dbname); ?></h4></div>
    <div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed" width="200" height="50" border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr><th>id</th><th>name</th><th>setvar</th><th>secret</th><th>ipaddr</th><th>context</th></tr>
        </thead>
        <tbody>
            <?php
            while($table = $stmh->fetch(PDO::FETCH_ASSOC)){
            ?>
            <tr>
                <td><?php print(htmlspecialchars($table['id'])); ?></td>
                <td><?php print(htmlspecialchars($table['name'])); ?></td>
                <td><?php print(htmlspecialchars($table['setvar'])); ?></td>
                <td><?php print(htmlspecialchars($table['secret'])); ?></td>
                <td><?php print(htmlspecialchars($table['ipaddr'])); ?></td>
                <td><?php print(htmlspecialchars($table['context'])); ?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </div>
    </div>
    </div>
</div>
</body>
</html>

