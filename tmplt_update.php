<?php
session_start();
// POSTデータ取得
$id = $_POST['id']; // 更新するレコードのID

// $admin_or_emp = isset($_POST['admin_or_emp']) ? 1 : 0;
// $work_in_or_out = isset($_POST['work_in_or_out']) ? 1 : 0;
$admin_or_emp = $_POST['admin_or_emp'];
$work_in_or_out = $_POST['work_in_or_out'];
$title = $_POST['title'];
$check_exist = isset($_POST['check_exist']) ? 1 : 0;
$photo_exist = isset($_POST['photo_exist']) ? 1 : 0;
$text_exist = isset($_POST['text_exist']) ? 1 : 0;
$temp_exist = isset($_POST['temp_exist']) ? 1 : 0;
$auth_id = $_POST['auth_id'];

// DB接続
include("funcs.php");
sschk();
$pdo = db_conn();

// データ更新SQL作成
$sql = "UPDATE H_template_table SET
            admin_or_emp = :admin_or_emp,
            work_in_or_out = :work_in_or_out,
            title = :title,
            check_exist = :check_exist,
            photo_exist = :photo_exist,
            text_exist = :text_exist,
            temp_exist = :temp_exist,
            auth_id = :auth_id
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':admin_or_emp', $admin_or_emp, PDO::PARAM_INT);
$stmt->bindValue(':work_in_or_out', $work_in_or_out, PDO::PARAM_INT);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':check_exist', $check_exist, PDO::PARAM_INT);
$stmt->bindValue(':photo_exist', $photo_exist, PDO::PARAM_INT);
$stmt->bindValue(':text_exist', $text_exist, PDO::PARAM_INT);
$stmt->bindValue(':temp_exist', $temp_exist, PDO::PARAM_INT);
$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

$status = $stmt->execute();

// データ更新処理後
if ($status == false) {
    sql_error($stmt);
} else {
    redirect("tmplt_select.php");
}
?>
