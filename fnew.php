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
        
        
        //一時ディレクトリ格納時のパス名
        $ftemp = $_FILES['yourfile']['tmp_name'];
        
        //元ファイル名
        $fname = $_FILES['yourfile']['name'];
        
        //ファイルサイズ
        $fsize = $_FILES['yourfile']['size'];
            
        //MIMEタイプ(小文字に揃える)
        $ftype = strtolower($_FILES['yourfile']['type']);

        //エラーコード（成功：０、エラー：正の整数）
        $ferror = $_FILES['yourfile']['error'];

        //ファイルがPOSTで受信していない、サイズオーバー、エラー発生時
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
        
        
        //ファイル情報をデータベースに登録
        try {
        
            //データベースに接続する
            $dbh = new PDO($dsn, $username, $password);
            
            //エラーはCatch内で処理する
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            //サーバサイドのプリペアドステートメントを有効にする
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            //ファイル情報をINSERTするSQLを設定
            $sql = 'insert into file_t (fext, ftype, fname, rdate)
             values (:fext, :ftype, :fname, :rdate)';
            $stmt = $dbh->prepare($sql);
            
            //拡張子
            $stmt->bindValue(':fext', $fext, PDO::PARAM_STR);
            
            //MIMEタイプ
            $stmt->bindValue(':ftype', $ftype, PDO::PARAM_STR);
            
            //元ファイル名
            $stmt->bindValue(':fname', $fname, PDO::PARAM_STR);
            
            //現在時刻を取得して使用する
            date_default_timezone_set('Asia/Tokyo');
            $today = date("Y-m-d H:i:s"); 
            $stmt->bindValue(':rdate', $today, PDO::PARAM_STR);
            
            //SQL文を実行する
            $stmt->execute();
            
            //保存用ファイル名としてIDを使用する
            $fid = $dbh->lastInsertId();
            
            //データベース接続を解除する
            $dbh = null;
            
        } catch (PDOException $e) {
            print('Connection failed:'.$e -> getMessage());
            die();
        }
        
        //画像データを名前変更してファイル保管用ディレクトリに移動させる
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
        
            //元画像の横幅
            $iw = imagesx($img);
            
            //元画像の縦幅
            $ih = imagesy($img);
            
            //サムネイル画像の横幅
            $tw = $thumb_width;
            
            //サムネイル画像の縦幅（元画像から計算する）
            $th = $thumb_width * $ih / $iw;
            
            //サムネイル用の空画像データを作成
            $thm = imagecreatetruecolor($tw, $th);
            
            //元画像データからサムネイル画像データを作成する
            imagecopyresampled($thm, $img, 0, 0, 0, 0, $tw, $th, $iw, $ih);
            
            //サムネイル画像をサムネイル用フォルダに保存する
            if ($ftype === 'image/jpeg' || $ftype === 'image/pjpeg') {
                imagejpeg($thm, "$folder_thumbs/$fid.$fext");
            }
        }
        

        header("Location: http://{$_SERVER['SERVER_NAME']}/mmdb2/ex06/flist.php");
   }
?>
