<?php
    //データベース接続
    $dsn = 'mysql:dbname=file1_db;host=localhost';
    $username = "user1";
    $password = "user1";

    //アップロードできる上限サイズ
    $file_maxsize = 1048576;
    $disp_maxsize = '1MB';
    
    //保管用ディレクトリ
    $folder_root = 'd:/mmdb2/file1_db';
    $folder_files = "$folder_root/files.orig";
    $folder_thumbs = "$folder_root/files.thumbs";
    
    //サムネイルの横幅
    $thumb_width = 100;
?>
