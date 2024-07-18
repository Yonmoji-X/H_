<?php
// 0. SESSION開始
session_start();

// 1. 関数の読み込み（funcs.php に含まれるものと仮定）
include("funcs.php");

// 2. POSTデータの取得
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

// 3. DB接続
$pdo = db_conn();

// 4. データ登録SQL作成
$sql = "INSERT INTO m_env_table (env_q1, env_q2, env_q3, env_q4, env_q5_1, env_q5_2, env_q5_3, env_q6, env_q7, env_q8, env_text, env_name, indate)
        VALUES (:env_q1, :env_q2, :env_q3, :env_q4, :env_q5_1, :env_q5_2, :env_q5_3, :env_q6, :env_q7, :env_q8, :env_text, :env_name, sysdate())";

$stmt = $pdo->prepare($sql);
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

$status = $stmt->execute();

// 5. データ登録処理後の表示
if ($status === false) {
    sql_error($stmt); // エラー処理を行う関数
} else {
    redirect("env_index.php"); // リダイレクトする関数
}
?>
