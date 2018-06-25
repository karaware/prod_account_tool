<?php
    if($dbname == "asterisk_ncom"){
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
    }else{
        try{
            $sql = "INSERT INTO inventory_test.gw_accounts_sb (name,defaultuser,secret,fd_number,ipaddr,setvar,prefix,代表組,setting_date,customer_name) VALUES ($name,$originnum,$secret,$originfdn,$prodline,$setvar,$prefix_result,$representation,cast(NOW() AS DATE),$customer);";
            $stmh = $pdo -> prepare($sql);

/*
            $stmh -> bindValue(':name',$name,PDO::PARAM_INT);
            $stmh -> bindValue(':originnum',$originnum, PDO::PARAM_INT);
            $stmh -> bindValue(':secret',$secret, PDO::PARAM_STR);
            $stmh -> bindValue(':originfdn',$originfdn, PDO::PARAM_INT);
            $stmh -> bindValue(':prodline',$prodline, PDO::PARAM_STR);
            $stmh -> bindValue(':setvar',$setvar, PDO::PARAM_STR);
            $stmh -> bindValue(':prefix_result',$prefix_result, PDO::PARAM_INT);
*/
            $stmh -> execute();
        }catch(PDOException $Exception){
            print("登録できませんでした。確認してください。<br>" . $Exception -> getMessage());
        }
        $pdo = null;
    }
