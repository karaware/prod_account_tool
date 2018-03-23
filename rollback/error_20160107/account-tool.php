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
        <title>番号登録</title>
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
				<li class="active"><a href="#">アカウント追加</a></li>
				<li><a href="search.php">閲覧・検索</a></li>
			</ul>
			<!-- 4.ボタン -->
			<button type="button" class="btn btn-default navbar-btn">
            <span class="glyphicon glyphicon-envelope"></span>
        	</button>
		</div>
	</nav>

	<div class="container-fluid">
        <h2 class="page-header">GW DBへのアカウント追加</h2>
	<!-- ▼フォーム -->
        <form id="form_1" name="input" action="./form.php" method="POST">
            <p><b>データベースを選択してください</b></p>
            <div class="radio"><label><input type="radio" class="validate[required]" name="gw" id="radio1" value="asterisk">SB GW1(asterisk)</label></div>
            <div class="radio"><label><input type="radio" class="validate[required]" name="gw" id="radio2" value="asterisk_sb">SB GW2(asterisk_sb)</label></div>
            <div class="radio"><label><input type="radio" class="validate[required]" name="gw" id="radio3" value="asterisk_sb3">SB GW3(asterisk_sb3)</label></div>
            <div class="radio"><label><input type="radio" class="validate[required]" name="gw" id="radio4" value="asterisk_ncom">N-COM GW(asterisk_ncom)</label></div> 
            <div class="radio"><label><input type="radio" class="validate[required]" name="gw" id="radio5" value="asterisk_ncom_u">ウミガメ N-COM GW(asterisk_ncom)</label></div><br>
	    <label>"00" + 下番号を入力</label>
            <input type="text" name="name" value="00" class="validate[required,custom[phone_name],minSize[12],maxSize[12]]">
            <label>&emsp;表示番号を入力</label>
            <input type="text" name="setvar" class="validate[required,,custom[phone_outnum],minSize[10],maxSize[11]]">
            <label>&emsp;パスワードを入力</label>
            <input type="button" value="自動生成" onclick="getPassword();">
            <input type="text" name="secret" id="result" class="validate[required]">
            <br><br>
            <input type="submit" name="" id="btn_submit" value="アカウント追加" class="btn btn-primary btn-sm">
        </form>

        <!-- ▼追加履歴表示 -->

	<?php session_start(); ?>
	<?php
	//MySQLのログイン情報
	$dbname =  'gwat';
	$host = 'localhost';
	$user = 'root';
	$pwd = 'q4l3o2o0g424arao';
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
        $stmh = $pdo->query('SELECT id,dbname,name,setvar,secret,user,register_time FROM history ORDER BY id DESC');
        if(!$stmh){
            $info = $pdo -> errorInfo();
            exit($info[2]);
        }
	?>	

        <br><br><h2 class="page-header">追加履歴</h2>
	
        <div class="container-fluid">
        <div class="table-responsive">
        <table class="table table-bordered table-condensed" width="450" height="50" border="1" cellspacing="0" cellpadding="8">
            <thead>
            <tr><th>id</th><th>dbname</th><th>name</th><th>setvar</th><th>secret</th><th>user</th><th>register_time</th></tr>
            </thead>
            <tbody>
                <?php
                        while($table = $stmh->fetch(PDO::FETCH_ASSOC)){
                        $count += 1;
                        ?>
                        <tr>
				<td><?php print(htmlspecialchars($table['id'])); ?></td>
                                <td><?php print(htmlspecialchars($table['dbname'])); ?></td>
                                <td><?php print(htmlspecialchars($table['name'])); ?></td>
                                <td><?php print(htmlspecialchars($table['setvar'])); ?></td>
                                <td><?php print(htmlspecialchars($table['secret'])); ?></td>
                                <td><?php print(htmlspecialchars($table['user'])); ?></td>
                                <td><?php print(htmlspecialchars($table['register_time'])); ?></td>
                        </tr>
                        <?php
                        if($count > 9){
                            break;
                        }
                        }
                ?>
            </tbody>
        </table>
        </div>
        </div>
    </div>
    </body>
</html>

