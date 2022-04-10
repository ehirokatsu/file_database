<?php

    //POSTされた場合
    if (isset($_SERVER['REQUEST_METHOD'])) {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
        
            //一時ディレクトリ格納時のパス名
            $ftemp = $_FILES['yourfile']['tmp_name'];
            
            //元ファイル名
            $fname = $_FILES['yourfile']['name'];
            
            //ファイルサイズ
            $fsize = $_FILES['yourfile']['size'];
            
            //MIMEタイプ
            $ftype = $_FILES['yourfile']['type'];
            
            //エラーコード（成功：０、エラー：正の整数）
            $ferror = $_FILES['yourfile']['error'];

            //ファイルがPOSTで受信していない、サイズオーバー、エラー発生時
            if (!is_uploaded_file($ftemp) || $fsize > 1048576 || $ferror > 0) {
                exit('アップロードが失敗しました。');
            }
            
            //保管ディレクトリ名を作成
            $fpath = 'files/' . basename($fname);
            
            //一時ディレクトリから保管ディレクトリへファイルを移動する
            if (!move_uploaded_file($ftemp, $fpath)) {
                exit('保管用ディレクトリへの移動が失敗しました。');
            
            }
            
            //サムネイル表示するにチェックがされていた場合
            if ($_POST['mkthumb'] === 'yes') {
            
                //MIMEを小文字に揃える
                $ftype = strtolower($ftype);
                
                //アップロードされた画像ファイルの画像ID格納用
                $img = FALSE;
                
                //アップロードしたファイルがJPGの場合
                if ($ftype === 'image/jpeg' || $ftype === 'image/pjpeg') {
                
                    //画像データを読み取り画像IDを取得する
                    $img = @imagecreatefromjpeg($fpath);
                    
                }

                //アップロード画像が存在する場合
                if ($img) {
                
                    //元画像の横幅
                    $iw = imagesx($img);
                    
                    //元画像の縦幅
                    $ih = imagesy($img);
                    
                    //サムネイル画像の横幅
                    $tw = 200;
                    
                    //サムネイル画像の縦幅（元画像の比率から計算する）
                    $th = 200 * $ih / $iw;
                    
                    //サムネイル用の空画像データを作成
                    $thm = imagecreatetruecolor($tw, $th);
                    
                    //アップロード画像データからサムネイル画像データを作成する
                    imagecopyresampled($thm, $img, 0, 0, 0, 0, $tw, $th, $iw, $ih);
                }
                
                //アップロード画像がJPGの場合
                if ($ftype === 'image/jpeg' || $ftype === 'image/pjpeg') {
                
                    //サムネイル画像データをファイルに保存する
                    imagejpeg($thm, $fpath);
                    
                }
            }
            
            //保管ディレクトリを表示する
            header("Location: http://{$_SERVER['SERVER_NAME']}/mmdb2/ex06/files/");
        }
    }

?>



<!DOCTYPE html PUBLIC "-// W3C// DTD XHTML 1.0 Transitional// EN"
 "http:// www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http:// www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<title>ファイルのアップロード処理</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<br>
<br>
<div class="center">
<H1>
<font color="blue">ファイルのアップロード処理</font>
</H1>


<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
    新しいファイル：<input type="file" name="yourfile">(1MB以内)<br>
    <input type="checkbox" id="mkthumb" name="mkthumb" value="yes">
    <label for="showinfo">サムネイルに変換する</label>
    <br>
    <input type="submit" value="アップロード">
    <br>
</form>


</div>
</body>
</html>

