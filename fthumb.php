<?php

    //パラメータがNULLではない場合
    if (isset($_REQUEST['fn']) && isset($_REQUEST['tw'])) {

        //元画像データ名
        $fname = $_REQUEST['fn'];
        
        //ディレクトリ、ファイル名、拡張子に分割する
        $finfo = pathinfo($fname);
        
        //元画像データ名
        $fname = $finfo['basename'];
        
        //元画像データの拡張子を小文字にする
        $fext = strtolower($finfo['extension']);

        //ファイル名検査（空白、文字列以外はNG）
        if ($fname === "" || !is_string($fname)) {
            exit('ファイル名が指定されていません。');
        }
        
        //元画像データの相対パス
        $fpath = "files/$fname";
        
        //ファイルの種類検査（JPG,PNG以外NG）
        if ($fext != 'jpeg' && $fext != 'jpe' && $fext != 'jpg' && $fext != 'png') {
            exit('この種類のファイルは扱えません：' . $fext);
        }
        
        //パラメータtw(サムネイルの横幅)を受け取る
        $tw = $_REQUEST['tw'];
        
        //サムネイル画像の横幅検査（空白、数値以外はNG）
        if ($tw === "" || !is_numeric($tw)) {
            exit('サムネイル幅が数値ではありません。');
        }
        if ($tw < 50) {
            exit('サムネイル幅は50以上でなければなりません。');
        }
        
        //アップロードされた画像ファイルの画像ID格納用
        $img = null;
        
        //アップロードしたファイルがJPGの場合
        if ($fext == 'jpeg' || $fext == 'jpe' || $fext == 'jpg') {
        
            //画像データを読み取り画像IDを取得する
            $img = imagecreatefromjpeg($fpath);
            
        //アップロードしたファイルがPNGの場合
        } elseif ($fext == 'png') {
        
            //画像データを読み取り画像IDを取得する
            $img = imagecreatefrompng($fpath);
        }
        
        
        if (!$img) {
            exit('画像ファイル読み取り時にエラーが発生しました：' . $fname);
        }
        
        //元画像の横幅
        $w = imagesx($img);
        
        //元画像の縦幅
        $h = imagesy($img);
        
        //サムネイル画像の縦幅（元画像の比率から計算する）
        $th = $tw * $h / $w;
        
        //サムネイル用の空画像データを作成
        $thm = imagecreatetruecolor($tw, $th);
        
        //元画像データからサムネイル画像データを作成する
        imagecopyresampled($thm, $img, 0, 0, 0, 0, $tw, $th, $w, $h);

        //アップロード画像がJPGの場合
        if ($fext == 'jpeg' || $fext == 'jpe' || $fext == 'jpg') {
        
            //ブラウザに画像を出力する
            header("Content-Type: image/jpeg");
            imagejpeg($thm);
            
        //アップロード画像がPNGの場合
        } elseif ($fext == 'png') {
        
            //ブラウザに画像を出力する
            header("Content-Type: image/png");
            imagepng($thm);
        }
   
    }

?>
