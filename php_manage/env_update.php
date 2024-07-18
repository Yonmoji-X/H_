<?php
// var_dump($_POST);
session_start();
//1. POSTデータ取得
$id = $_POST['id']; // 更新するレコードのID

$env_q1   = isset($_POST['env_q1']) ? 1 : 0;
$env_q2   = isset($_POST['env_q2']) ? 1 : 0;
$env_q3   = isset($_POST['env_q3']) ? 1 : 0;
$env_q4   = isset($_POST['env_q4']) ? 1 : 0;
$env_q5_1 = isset($_POST['env_q5_1']) ? 1 : 0;
$env_q5_2 = isset($_POST['env_q5_2']) ? 1 : 0;
$env_q5_3 = isset($_POST['env_q5_3']) ? 1 : 0;
$env_q6   = isset($_POST['env_q6']) ? 1 : 0;
$env_q7   = isset($_POST['env_q7']) ? 1 : 0;
$env_q8   = isset($_POST['env_q8']) ? 1 : 0;
$env_text = $_POST['env_text'];
$env_name = $_POST['env_name'];

//2. DB接続します
include("funcs.php");
sschk();
$pdo = db_conn();

//３．データ更新SQL作成
$stmt = $pdo->prepare("UPDATE m_env_table SET
                        env_q1=:env_q1, env_q2=:env_q2, env_q3=:env_q3, env_q4=:env_q4,
                        env_q5_1=:env_q5_1, env_q5_2=:env_q5_2, env_q5_3=:env_q5_3,
                        env_q6=:env_q6, env_q7=:env_q7, env_q8=:env_q8,
                        env_text=:env_text, env_name=:env_name
                        WHERE id=:id");

$stmt->bindValue(':env_q1', $env_q1, PDO::PARAM_INT);
$stmt->bindValue(':env_q2', $env_q2, PDO::PARAM_INT);
$stmt->bindValue(':env_q3', $env_q3, PDO::PARAM_INT);
$stmt->bindValue(':env_q4', $env_q4, PDO::PARAM_INT);
$stmt->bindValue(':env_q5_1', $env_q5_1, PDO::PARAM_INT);
$stmt->bindValue(':env_q5_2', $env_q5_2, PDO::PARAM_INT);
$stmt->bindValue(':env_q5_3', $env_q5_3, PDO::PARAM_INT);
$stmt->bindValue(':env_q6', $env_q6, PDO::PARAM_INT);
$stmt->bindValue(':env_q7', $env_q7, PDO::PARAM_INT);
$stmt->bindValue(':env_q8', $env_q8, PDO::PARAM_INT);
$stmt->bindValue(':env_text', $env_text, PDO::PARAM_STR);
$stmt->bindValue(':env_name', $env_name, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

$status = $stmt->execute(); //実行

//４．データ更新処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect("env_select.php");
}
?>
