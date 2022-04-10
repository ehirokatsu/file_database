<?php
    //共通設定を取得する
    require('env.inc');

    if (isset($_SERVER['REQUEST_METHOD'])) {
        //POST以外受け付けない
        if ($_SERVER['REQUEST_METHOD'] !== "POST") {
            exit('アップロードが失敗しました。');
        }
        //保管用ディレクトリを確保
        if (!is_dir($folder_files) && !mkdir($folder_files)) {
            exit('保管用ディレクトリを作ることができません。');
        }
        
        
        //アップロードされたファイルを特定
        $ftemp = $_FILES['yourfile']['tmp_name'];
        $fname = $_FILES['yourfile']['name'];
        $fsize = $_FILES['yourfile']['size'];
        $ftype = strtolower($_FILES['yourfile']['type']);
        $ferror = $_FILES['yourfile']['error'];

       if (!is_uploaded_file($ftemp) || $fsize > $file_maxsize || $ferror > 0) {
           exit('アップロードが失敗しました。');
       }
       
       //MIMEタイプを確認
       if ($ftype != 'application/msword'
        && $ftype != 'application/pdf'
        && $ftype != 'image/jpeg'
        && $ftype != 'image/pjpeg'
        && $ftype != 'text/html'
        && $ftype != 'text/plain') {
            exit('この種類のファイルは受付ません。');
       }
       
       //ファイル名と拡張子を取得
       $finfo = pathinfo($fname);
       $fname = $finfo['basename'];
       $fext = strtolower($finfo['extension']);
       
       //MySQLに接続、データベースを選択
       
       $dsn = 'mysql:dbname=file1_db;host=localhost';
       $user = 'user1';
       $password = 'user1';
       
       //ファイル情報をデータベースに登録
       try {
           $dbh = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
           $sql = 'insert into file_t (fext, ftype, fname, rdate)
            values (:fext, :ftype, :fname, :rdate)';
           $stmt = $dbh->prepare($sql);
           $stmt->bindValue(':fext', $fext);
           $stmt->bindValue(':ftype', $ftype);
           $stmt->bindValue(':fname', $fname);
           
           date_default_timezone_set('Asia/Tokyo');
           $today = date("Y-m-d H:i:s"); 
           $stmt->bindValue(':rdate', $today);
           
           $stmt->execute();
           $fid = $dbh->lastInsertId();
           $dbh = null;
           
       } catch (PDOException $e) {
           print('Connection failed:'.$e -> getMessage());
           die();
       }
       
       //ファイル保管用ディレクトリに移動
       $fpath = "$folder_files/$fid.$fext";
       if (!move_uploaded_file($ftemp, $fpath)) {
           exit('保管用ディレクトリへの移動が失敗しました。');
       }
       
       //ファイルが画像なら読み取る
       $img = false;
       if ($ftype === 'image/jpeg' || $ftype === 'image/pjpeg') {
           $img = @imagecreatefromjpeg($fpath);
       }
       
       //画像の読み取りが成功っし、かつサムネイル用ディレクトリが確保されるなら
       if ($img && (is_dir($folder_thumbs) || mkdir($folder_thumbs))) {
           $iw = imagesx($img);
           $ih = imagesy($img);
           $tw = $thumb_width;
           $th = $thumb_width * $ih / $iw;
           $thm = imagecreatetruecolor($tw, $th);
           imagecopyresampled($thm, $img, 0, 0, 0, 0, $tw, $th, $iw, $ih);
                  //サムネイルをファイルに保存
         if ($ftype === 'image/jpeg' || $ftype === 'image/pjpeg') {
             imagejpeg($thm, "$folder_thumbs/$fid.$fext");
          }
       }
       

       header("Location: http://{$_SERVER['SERVER_NAME']}/mmdb2/ex06/flist.php");
   }
?>
