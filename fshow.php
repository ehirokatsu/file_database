<?php
    require('env.inc');

    //パラメータを受け取る
    $fid = $_REQUEST['fid'];
    if ($fid == "" || !is_numeric($fid) || $fid <= 0) {
        exit();
    }
    
    $th = "";//初期化しないと画像表示できない
    if (!empty($_REQUEST['th'])) {
        $th = $_REQUEST['th'];
    }
    //MySQLに接続、データベースを選択
    $dsn = 'mysql:dbname=file1_db;host=localhost';
    $user = 'user1';
    $password = 'user1';
    
    //ファイル情報をデータベースから取得
    try {
        $dbh = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $sql = "select fext, ftype from file_t where fid=:fid";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':fid', $fid);
        $stmt->execute();
        $dbh = null;
        
    } catch (PDOException $e) {
        print('Connection failed:'.$e -> getMessage());
        die();
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        exit();
    }
    $fext = $row['fext'];
    $ftype = $row['ftype'];

    //ファイルを出力
    $fpath = "$folder_files/$fid.$fext";
    //echo $fpath;
    //exit();
    $tpath = "$folder_thumbs/$fid.$fext";
    if (is_file($fpath)) {
        header("Content-Type: $ftype");
        if ($th != "" && is_file($tpath)) {
            @readfile($tpath);
        } else {
            @readfile($fpath);
        }
    }
    
?>
