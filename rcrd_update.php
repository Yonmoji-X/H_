<?php
session_start();
// POSTデータ取得
$id = $_POST['id']; // 更新するレコードのID
$recorder = $_POST['recorder'];
$check_item = isset($_POST["check_item"]) ? $_POST["check_item"] : null;
$text = isset($_POST["text"]) ? $_POST["text"] : null;
$photo = isset($_FILES["photo"]) ? get_file_content($_FILES["photo"]) : null;
$temp = isset($_POST["temp"]) ? $_POST["temp"] : null;
$auth_id = $_POST['auth_id'];

// DB接続
include("funcs.php");
sschk();
$pdo = db_conn();

// デバッグ用：アップロードされたファイル情報を確認
if (isset($_FILES["photo"])) {
    echo "<pre>";
    print_r($_FILES["photo"]);
    echo "</pre>";
}

// データ更新SQL作成
$sql = "UPDATE H_record_table SET
            recorder = :recorder,
            check_item = :check_item,
            photo = :photo,
            text = :text,
            temp = :temp
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':recorder', $recorder, PDO::PARAM_INT);
$stmt->bindValue(':check_item', $check_item, PDO::PARAM_STR);
$stmt->bindValue(':text', $text, PDO::PARAM_STR);
$stmt->bindValue(':photo', $photo, PDO::PARAM_LOB); // BLOB型として設定
$stmt->bindValue(':temp', $temp, PDO::PARAM_INT);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

$status = $stmt->execute();

// データ更新処理後のデバッグ用メッセージ
if ($status == false) {
    sql_error($stmt);
    echo "SQL Error: " . $stmt->errorInfo()[2];
} else {
    echo "Update successful!";
    redirect("rcrd_select.php");
}
?>
