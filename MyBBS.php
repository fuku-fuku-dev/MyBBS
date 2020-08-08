<?php
    
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $db_name = 'fukuokappf_db';

    $database = mysqli_connect($host, $username, $password, $db_name);
    

    if ($database == false) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }

    $charset = 'utf8';
    mysqli_set_charset($database, $charset);
    date_default_timezone_set('Asia/Tokyo');
    
    
    if (isset($_POST['poster']) && (isset($_POST['content']))) {
        
        
        $sql = 'INSERT INTO MyBBS (poster, content) VALUES(?, ?)';
        
        $statement = mysqli_prepare($database, $sql);
        
        mysqli_stmt_bind_param($statement, 'ss', $_POST['poster'], $_POST['content']);
        
        mysqli_stmt_execute($statement);
        
        mysqli_stmt_close($statement);
        
    }
    
    if ($_POST['submit_content_delete']) {
        
        $sql = 'DELETE FROM MyBBS WHERE id=?';
        $statement = mysqli_prepare($database, $sql);                
        mysqli_stmt_bind_param($statement, 'i', $_POST['content_id']);  
        mysqli_stmt_execute($statement);                             
        mysqli_stmt_close($statement);                               
    }
    
    if (isset($_POST['content_edit'])) {

        $sql = 'UPDATE MyBBS SET content = ? WHERE id=?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'si', $_POST['content_edit'], $_POST['content_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    
    $sql = 'SELECT * FROM MyBBS ORDER BY created_at DESC';

    $result = mysqli_query($database, $sql);
    
?>
<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "utf-8">
        <title>MyBBS</title>
        <link rel = "stylesheet" href = "bookshelf.css">
    </head>
    <body>
        <a href = "bookshelf_mini.php"><h1>MyBBS</h1></a>
        <h2>投稿一覧</h2>
        <ul>
<?php
            if ($result) {
                while ($record = mysqli_fetch_assoc($result)) {
                    $id = $record['id'];
                    $poster = $record['poster'];
                    $created_at = $record['created_at'];
                    $content = $record['content'];
                    $tmp = strtotime('+9 hour' , strtotime($created_at));
                    $time2 = date('Y-m-d H:i:s',$tmp);
?>
                    <li><?php print htmlspecialchars($poster, ENT_QUOTES, 'UTF-8'); ?>/<?php print htmlspecialchars($time2, ENT_QUOTES, 'UTF-8'); ?><br><?php print htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?>
                        <form action="MyBBS.php" method="post">
                            <input type="hidden" name="content_id" value="<?php print h($id); ?>">
                            <div class="content_edit">
                              <textarea name = "content_edit" placeholder = "本文を書く" required></textarea>
                              <input type = "submit" name = "submit_content_edit" value = "編集する">
                            </div>
                        </form>
                        <form action="MyBBS.php" method="post">
                            <input type="hidden" name="content_id" value="<?php print h($id); ?>">
                            <div class="content_delete">
                              <input type="submit" name="submit_content_delete" value="削除する">
                            </div>
                        </form>
                    </li>
<?php
                }
                mysqli_free_result($result);
            }
            
?>


        </ul>
        <h2>投稿する</h2>
        <form action = "MyBBS.php" method = "post">
            <input type = "text" name = "poster" placeholder = "投稿者を入力" required><br>
            <textarea name = "content" placeholder = "本文を書く" required></textarea>
            <input type = "submit" name = "post" value = "投稿">
        </form>
    </body>
</html>