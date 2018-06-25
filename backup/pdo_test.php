<?php

//MySQLのログイン情報
$dbname =  'inventory_test';
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

$name = "000785719810";
//$name = "000783309876";
$originnum = "0785719894";
//$originnum = "0783309876";
$secret = "q4l3o2o0g424arao";
$originfdn = "0120000000";
$prodline = "prodline";
$setvar = "OUTNUM=0120000000";
$prefix_result = "999";
$represent = "2";
$customer = "prodlight";
$date = "2018-06-22";


try{
//Ncomで成功したsqlをSB用に修正
//$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES ($name,$originnum,'$secret','$originfdn','$prodline','$setvar',$prefix_result,$represent,'$date','$customer');";
// 行けた $sql = "INSERT INTO inventory_test.gw_accounts_sb (fd_number,prefix,setting_date,customer_name) VALUES (:originfdn,:prefix_result,cast(NOW() AS DATE),:customer);";
//$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,prefix,代表組,setting_date,customer_name) VALUES (:name,:originnum,:secret,:originfdn,:prodline,:prefix_result,:represent,cast(NOW() AS DATE),:customer);";
$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,represent,setting_date,customer_name) VALUES (:name,:originnum,:secret,:originfdn,:prodline,:setvar,:prefix_result,:represent,cast(NOW() AS DATE),:customer);";
//$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES (:name,:originnum,:secret,:originfdn,:prodline,:setvar,:prefix_result,:represent,cast(NOW() AS DATE),:customer);";
//$sql = "INSERT INTO inventory_test.gw_accounts_ncom (name,secret,fd_number,ipaddr,defaultuser,setvar,prefix,setting_date,customer_name) VALUES (:name,:secret,:originfdn,:prodline,:originnum,:setvar,:prefix_result,cast(NOW() AS DATE),:customer);";
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
$stmh -> bindValue(':represent',$represent, PDO::PARAM_STR);
$stmh -> execute();
}catch(PDOException $Exception){
print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
}
$pdo = null;



/*
try{
//$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES ('$name','$originnum','$secret','$originfdn','$prodline','$setvar','$prefix_result','$represent',cast(now() as date),'$customer');";
//$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES (:name,:originnum,:secret,:originfdn,:prodline,:setvar,:prefix_result,:represent,cast(now() as date),:customer);";
//$sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES ($name,$originnum,:secret,:originfdn,:prodline,:setvar,:prefix_result,:represent,cast(now() as date),:customer);";
$sql = "INSERT INTO inventory_test.gw_accounts_sb_copy (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES (:name,:originnum,:secret,:originfdn,:prodline,:setvar,:prefix_result,:represent,cast(now() as date),:customer);";

$stmh = $pdo -> prepare($sql);
/*
$stmh -> bindValue(':name',$name, PDO::PARAM_STR);
$stmh -> bindValue(':originnum',$originnum, PDO::PARAM_STR);
$stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
$stmh -> bindValue(':originfdn',$originfdn, PDO::PARAM_STR);
$stmh -> bindValue(':prodline',$prodline, PDO::PARAM_STR);
$stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
$stmh -> bindValue(':prefix_result',$prefix_result, PDO::PARAM_STR);
$stmh -> bindValue(':represent',$represent, PDO::PARAM_STR);
$stmh -> bindValue(':customer',$customer, PDO::PARAM_STR);
*/
/*
$param = array(
  ':name' => $name, 
  ':originnum' => $originnum,
  ':secret' => $secret,
  ':originfdn' => $originfdn,
  ':prodline' => $prodline,
  ':setvar' => $setvar,
  ':prefix_result' => $prefix_result,
  ':represent' => $represent,
  ':customer' => $customer
);

$stmh -> execute($param);
}catch(PDOException $Exception){
print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
}
$pdo = null;

*/
