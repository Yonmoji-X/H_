<?php
//1. POSTデータ取得
$auth_id   = $_POST["auth_id"];
$gene_id  = $_POST["gene_id"];

//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$stmt = $pdo->prepare("INSERT INTO H_share_table(auth_id,gene_id)VALUES(:auth_id, :gene_id)");

$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);        //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':gene_id', $gene_id, PDO::PARAM_INT);        //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute(); //実行


//４．データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect("shr_index.php");
}
?>
