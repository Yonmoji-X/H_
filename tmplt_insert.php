<?php
// 0. SESSION開始
session_start();

// 1. 関数の読み込み（funcs.php に含まれるものと仮定）
include("funcs.php");

// 2. POSTデータの取得
// $admin_or_emp   = isset($_POST['admin_or_emp']) ? 1 : 0;
// $work_in_or_out   = isset($_POST['work_in_or_out']) ? 1 : 0;
$admin_or_emp = $_POST['admin_or_emp'];
$work_in_or_out = $_POST['work_in_or_out'];
$title = $_POST['title'];
$check_exist   = isset($_POST['check_exist']) ? 1 : 0;
$photo_exist = isset($_POST['photo_exist']) ? 1 : 0;
$text_exist = isset($_POST['text_exist']) ? 1 : 0;
$temp_exist = isset($_POST['temp_exist']) ? 1 : 0;
$env_q6   = isset($_POST['env_q6']) ? 1 : 0;
$env_q7   = isset($_POST['env_q7']) ? 1 : 0;
$env_q8   = isset($_POST['env_q8']) ? 1 : 0;
// $env_text = $_POST['env_text'];
$auth_id= $_POST["auth_id"];

// 3. DB接続
$pdo = db_conn();

// 4. データ登録SQL作成
$sql = "INSERT INTO H_template_table (admin_or_emp, work_in_or_out, check_exist, photo_exist, text_exist, temp_exist, title, indate, auth_id)
        VALUES (:admin_or_emp, :work_in_or_out, :check_exist, :photo_exist, :text_exist, :temp_exist, :title, sysdate(),:auth_id)";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':admin_or_emp', $admin_or_emp, PDO::PARAM_INT);
$stmt->bindValue(':work_in_or_out', $work_in_or_out, PDO::PARAM_INT);
$stmt->bindValue(':check_exist', $check_exist, PDO::PARAM_INT);
$stmt->bindValue(':photo_exist', $photo_exist, PDO::PARAM_INT);
$stmt->bindValue(':text_exist', $text_exist, PDO::PARAM_INT);
$stmt->bindValue(':temp_exist', $temp_exist, PDO::PARAM_INT);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);

$status = $stmt->execute();

// 5. データ登録処理後の表示
if ($status === false) {
    sql_error($stmt); // エラー処理を行う関数
} else {
    redirect("tmplt_index.php"); // リダイレクトする関数
}
?>
