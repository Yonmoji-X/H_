<?php
session_start();
$id = $_GET["id"]; // ?id~**を受け取る
include("funcs.php");
sschk();
$pdo = db_conn();
// var_dump($_SESSION);

// セッションからユーザーIDを取得
$auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$sql = "SELECT * FROM H_recod_table WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();




// ３．データ表示
if ($status == false) {
    sql_error($stmt);
} else {
    $row = $stmt->fetch();
}

$values = "";
$members = [];
while ($row = $stmt_ref->fetch(PDO::FETCH_ASSOC)) {
    $members[] = [
        'm_id' => $row['id'],
        'm_name' => $row['name']
    ];
}

// 全データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
?>


<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>チェック項目更新</title>
  <link href="css/all.css" rel="stylesheet">
  <style>div{padding: 10px;font-size:16px;}</style>
</head>
<body>

<!-- Head[Start] -->
<header>
    <?= include("menu.php");?>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<form method="POST" action="tmplt_update.php"> <!-- 更新用のPHPファイルを指定 -->
    <div class="jumbotron">
        <fieldset>
            <legend>製造環境の管理</legend>
            <table>
                <tr>
                    <th>項目</th>
                    <th>設定</th>
                </tr>
                <tr>
                    <td>1.項目名</td>
                    <td><input name="title" value="<?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?>"></td>
                </tr>
                <tr>
                    <td>2.管理者/従業員</td>
                    <td>
                    <select name="admin_or_emp">
                            <option value="1" <?= $row['admin_or_emp'] == 1 ? 'selected' : '' ?>>管理者</option>
                            <option value="0" <?= $row['admin_or_emp'] == 0 ? 'selected' : '' ?>>従業員</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>3.出勤/退勤</td>
                    <td>
                        <select name="work_in_or_out">
                            <option value="1" <?= $row['work_in_or_out'] == 1 ? 'selected' : '' ?>>出勤時</option>
                            <option value="0" <?= $row['work_in_or_out'] == 0 ? 'selected' : '' ?>>退勤時</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>4. [チェック欄]の有無</td>
                    <td><input type="checkbox" name="check_exist" <?= $row['check_exist'] == 1 ? 'checked' : '' ?>></td>
                </tr>
                <tr>
                    <td>5. [テキスト記入欄]の有無</td>
                    <td><input type="checkbox" name="text_exist" <?= $row['text_exist'] == 1 ? 'checked' : '' ?>></td>
                </tr>
                <tr>
                    <td>6. [温度入力欄]の有無</td>
                    <td><input type="checkbox" name="temp_exist" <?= $row['temp_exist'] == 1 ? 'checked' : '' ?>></td>
                </tr>
                <tr>
                    <td>7. [写真投稿欄]の有無</td>
                    <td><input type="checkbox" name="photo_exist" <?= $row['photo_exist'] == 1 ? 'checked' : '' ?>></td>
                </tr>
            </table>
            <br>

            <input type="hidden" name="auth_id" value="<?= htmlspecialchars($auth_id, ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="submit" value="更新">
        </fieldset>
    </div>
</form>
<!-- Main[End] -->

</body>
</html>
