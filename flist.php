<?php

    //共通設定を取得する
    require('env.inc');


    //検索キーが指定された場合
    if (isset($_REQUEST['fkey'])) {
    
        //パラメータfkeyを取得
        $fkey = trim($_REQUEST['fkey']);
        
    } else {
    
        //指定されていなければ空文字にする
        $fkey = "";
    }


    //ファイル情報をデータベースから取得
    try {
        //データベースに接続する
        $dbh = new PDO($dsn, $username, $password);
        
        //エラーはCatch内で処理する
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        //サーバサイドのプリペアドステートメントを有効にする
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
        //一覧表示するSQL文
        $sql = "select fid, fext, ftype, fname, rdate from file_t where fname like :fkey order by fid desc";
        
        $stmt = $dbh->prepare($sql);
        
        //部分一致をプレースホルダで使用する時は％ごと置き換える。
        $key = "%$fkey%";

        $stmt->bindValue(':fkey', $key, PDO::PARAM_STR);

        $stmt->execute();
        
        //データベースを閉じる
        $dbh = null;
        
    } catch (PDOException $e) {
        print('Connection failed:'.$e -> getMessage());
        die();
    }
?>

<!DOCTYPE html PUBLIC "-// W3C// DTD XHTML 1.0 Transitional// EN"
 "http:// www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http:// www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<title>ファイルデータベース</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<br>
<br>
<div class="center">
<H1>
ファイルデータベース
</H1>


<form action="fnew.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?= $file_maxsize ?>">
    新しいファイル：<input type="file" name="yourfile">
    (<?= $file_maxsize ?>以内)<br>
    <input type="submit" value="アップロード">
    <br>
</form>
<p>実行したSQL：<?= $sql ?></p>

<form>
元のファイル名（一部分でも可）：<input type="text" name="fkey" value="<?= $fkey ?>">
<input type="submit" value="検索">
</form>

<table border="1" width="100%">
<tr>
    <th width="100"><br></th>
    <th>ファイル</th><th>ftype</th><th>fname</th><th>rdate</th>
</tr>

<?php

    if ($stmt) {
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            //各列の値を変数に取り出す
            $fid = $row['fid'];
            $fext = $row['fext'];
            $ftype = $row['ftype'];
            $fname = $row['fname'];
            $rdate = $row['rdate'];
            //ファイルのパス名を決定
            $bname = "$fid.$fext";
            $fpath = "$folder_files/$fid.$fext";
            $tpath = "$folder_thumbs/$fid.$fext";
            
            //ファイルのURLを決定
            $furl = "/mmdb2/ex06/fshow.php?fid=$fid";
            $turl = "/mmdb2/ex06/fshow.php?fid=$fid&th=y";
            //表示
            echo "<tr><td>";
            if (is_file($fpath)) {
                echo "<a href=\"$furl\">";
                if (is_file($tpath)) {
                    echo "<img src=\"$turl\" alt=\"$bname\" width=\"100\" border=\"0\">";
                } else {
                    echo "<br>";
                }
                echo "</a>";
            } else {
                echo "(removed)";
            }
            echo "</td>";
            echo "<td><a href=\"$furl\">$bname</a></td>";
            echo "<td>$ftype</td>";
            echo "<td>$fname</td>";
            echo "<td>$rdate</td>";
            echo '</tr>';
        }
    }
?>
</table>
</body>
</html>

