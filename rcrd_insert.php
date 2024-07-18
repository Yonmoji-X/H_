<?php
session_start();
include("funcs.php");
sschk();

// POSTデータ取得
$admin_or_emp = $_POST['admin_or_emp'];
$work_in_or_out = $_POST['work_in_or_out'];

// データベース接続
$pdo = db_conn();

// ファイルのアップロードがあるかチェック
function get_file_content($file) {
    if (isset($file) && $file['error'] == UPLOAD_ERR_OK) {
        return file_get_contents($file['tmp_name']);
    }
    return null;
}

foreach ($_POST as $key => $value) {
    // タイトル項目かチェック
    if (strpos($key, 'title_') === 0) {
        $index = str_replace('title_', '', $key);
        $title = $value;
        $check_item = isset($_POST["check_item_$index"]) ? $_POST["check_item_$index"] : null;
        $text = isset($_POST["text_$index"]) ? $_POST["text_$index"] : null;
        $photo = isset($_FILES["photo_$index"]) ? get_file_content($_FILES["photo_$index"]) : null;
        $temp = isset($_POST["temp_$index"]) ? $_POST["temp_$index"] : null;

        // デバッグ情報
        // echo '<pre>';
        // echo "index: $index\n";
        // echo "title: $title\n";
        // echo "check_item: $check_item\n";
        // echo "text: $text\n";
        // echo "photo: " . ($photo !== null ? 'データあり' : 'データなし') . "\n";
        // echo "temp: $temp\n";
        // echo '</pre>';

        // データ挿入SQL作成
        $sql = "INSERT INTO H_record_table(admin_or_emp, work_in_or_out, title, check_item, text, photo, temp)
                VALUES(:admin_or_emp, :work_in_or_out, :title, :check_item, :text, :photo, :temp)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':admin_or_emp', $admin_or_emp, PDO::PARAM_INT);
        $stmt->bindValue(':work_in_or_out', $work_in_or_out, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':check_item', $check_item, PDO::PARAM_STR);
        $stmt->bindValue(':text', $text, PDO::PARAM_STR);
        $stmt->bindValue(':photo', $photo, PDO::PARAM_LOB); // BLOB型として設定
        $stmt->bindValue(':temp', $temp, PDO::PARAM_INT);
        $status = $stmt->execute();

        if ($status == false) {
            $error = $stmt->errorInfo();
            echo "SQLエラー: " . $error[2];
            exit;
        }
    }
}

redirect('rcrd_select.php');
?>
