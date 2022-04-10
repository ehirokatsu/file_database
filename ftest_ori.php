<?php
    if (isset($_SERVER['REQUEST_METHOD'])) {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $ftemp = $_FILES['yourfile']['tmp_name'];
            $fname = $_FILES['yourfile']['name'];
            $fsize = $_FILES['yourfile']['size'];
            $ftype = $_FILES['yourfile']['type'];
            $ferror = $_FILES['yourfile']['error'];

            if (!is_uploaded_file($ftemp) || $fsize > 1048576 || $ferror > 0) {
                exit('アップロードが失敗しました。');
            }
            if ($_POST['showinfo'] === "yes") {
                echo "\$_FILES['yourfile']['tmp_name'] = $ftemp<br>";
                echo "\$_FILES['yourfile']['name'] = $fname<br>";
                echo "\$_FILES['yourfile']['size'] = $fsize<br>";
                echo "\$_FILES['yourfile']['type'] = $ftype<br>";
                echo "\$_FILES['yourfile']['error'] = $ferror<br>";
                exit();
            }
            $fpath = 'files/' . basename($fname);
            if (!move_uploaded_file($ftemp, $fpath)) {
                exit('保管用ディレクトリへの移動が失敗しました。');
            
            }
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
    <input type="checkbox" id="showinfo" name="showinfo" value="yes">
    <label for="showinfo">ファイル情報の出力</label>
    <br>
    <input type="submit" value="アップロード">
    <br>
</form>


</div>
</body>
</html>

