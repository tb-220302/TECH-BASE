<?php
//変数の初期化
  $name = "";
  $comment = "";
  $pass = "";
  
//データベース設定
$dsn='データベース名';//nameはハイフンを抜いて末尾にdb
$user='ユーザーネーム';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

//テーブルの作成
$sql = "CREATE TABLE IF NOT EXISTS tb3"
  ." ("
  . "id INT AUTO_INCREMENT PRIMARY KEY,"
  . "name char(32),"
  . "comment TEXT,"
  . "date datetime,"
  . "pass char(16)"
  .");";
$stmt = $pdo->query($sql);
	
//編集番号指定フォーム
if(isset($_POST["edit"]) && isset($_POST["pass2"])){
    $edit_number=$_POST["edit"];
    $pass=$_POST["pass2"];
    $sql = 'SELECT * FROM tb3 WHERE id=:id';
	$stmt = $pdo->prepare($sql);//SQLの準備
	$stmt->bindParam(':id',$edit_number,PDO::PARAM_INT);//差し替えるパラメータ
	$stmt->execute();//SQLを実行
	$results = $stmt->fetchAll();//投稿データ配列の作成
	if($results[0]['pass'] != ""){//パスワードが空文字でないならば(パスワードが存在するならば)
        if($results[0]['pass'] == $pass){ //パスワードが一致するならば
            $name = $results[0]['name'];
            $comment = $results[0]['comment'];
            $pass = $results[0]['pass'];
        }else{
            echo "パスワードが違います";
        }
    }
}
	
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1-2</title>
    </head>
    <body>
        <form action="mission_5-1-2.php"method="post"> <!--新規投稿フォーム-->
            <input type="hidden" name="edit_number" value="<?php if($edit_number){echo $edit_number;}?>">
            <br>
            <input type="text" name="name" placeholder="名前" value="<?php if($name!==""){echo $name;}?>">
            <input type="text" name="comment" placeholder="コメント" value="<?php if($comment!==""){echo $comment;}?>">
            <input type="text" name="pass1" placeholder="パスワード" value="<?php if($pass!==""){echo $pass;}?>">
            <input type="submit" value="送信">
        </form>
        <form action="mission_5-1-2.php"method="post"><!--編集番号送信フォーム-->
            <input type="number" name="edit" placeholder="編集番号">
            <input type="text" name="pass2" placeholder="パスワード">
            <input type="submit" value="編集">
        </form>
        <form action="mission_5-1-2.php"method="post"><!--投稿削除フォーム-->
            <input type="number" name="delete_number" placeholder="投稿削除番号">
            <input type="text" name="pass3" placeholder="パスワード">
            <input type="submit" value="削除">
        </form>
    <?php
    if(isset($_POST["name"]) && isset($_POST["comment"])){
        if($_POST["edit_number"]!=0){
            $edit_number=$_POST["edit_number"];
            $name=$_POST["name"];
            $comment=$_POST["comment"];
            $pass=$_POST["pass1"];
            $sql="UPDATE tb3 SET name=:name,comment=:comment,pass=:pass WHERE id=:id";
            $stmt=$pdo->prepare($sql);
            //bindParam→変数を文字列としてパラメータに入れる
            //PDO::PARAM_STR→変数の値を文字列として扱う
            $stmt->bindParam(':name',$name,PDO::PARAM_STR);
            $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
            $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
            $stmt->bindParam(':id',$edit_number,PDO::PARAM_INT);
            $stmt->execute();//prepareに入っているSQL文を実行
            echo "編集しました！<br>";
        }else{
            //新規投稿。INSERT文（データを入力）
            $name=$_POST["name"];
            $comment=$_POST["comment"];
            $pass=$_POST["pass1"];
            $date = date("Y/m/d H:i:s");
            $sql = $pdo -> prepare("INSERT INTO tb3 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> execute();//repareに入っているSQL文を実行
            echo "投稿しました！<br>";
        }
    }
    
//削除フォーム
if(isset($_POST["delete_number"])){
    $delete_number=$_POST["delete_number"];
    $pass=$_POST["pass3"];
    $sql = "SELECT * FROM tb3 WHERE id=:id";//データを抽出
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $delete_number, PDO::PARAM_INT);
	$stmt->execute();
	$results=$stmt->fetchAll();
	if($results[0]['pass']!=""){//パスワードが存在するなら
	    if($results[0]['pass']==$pass){//パスワードが一致するなら
	        $sql = "delete from tb3 where id=:id";//データを差し替える
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':id', $delete_number, PDO::PARAM_INT);
	        $stmt->execute();
	        echo "削除しました！<br>";
	    }else{
	        echo "パスワードが違います。<br>";
	    }
	}
}
    
//SELECT文（データを抽出し、表示する）
$sql = 'SELECT * FROM tb3';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
if(!empty($results[0]['id'])){ //最初の投稿が存在すれば
	 foreach ($results as $row){
	   echo $row['id'].',';
	   echo $row['name'].',';
	   echo $row['comment'].',';
	   echo $row['date']."<br>";
	        
	  }
}
        
    
	?>
    </body>
</html>