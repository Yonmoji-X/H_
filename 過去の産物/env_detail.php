<?php
session_start();
$id = $_GET["id"]; // ?id~**を受け取る
include("funcs.php");
sschk();
$pdo = db_conn();

// セッションからユーザーIDを取得
$auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// ２．データ登録SQL作成
$stmt = $pdo->prepare("SELECT * FROM m_env_table WHERE id=:id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

// ３．データ表示
if ($status == false) {
    sql_error($stmt);
} else {
    $row = $stmt->fetch();
}

// 選択肢として表示するためのデータを取得（auth_idが一致するもののみ）
$stmt_names = $pdo->prepare("SELECT name FROM m_an_table WHERE auth_id = :auth_id");
$stmt_names->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);
$stmt_names->execute();
$names = $stmt_names->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>データ更新</title>
  <link href="css/all.css" rel="stylesheet">
  <style>div{padding: 10px;font-size:16px;}</style>
</head>
<body>

<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header"><a class="navbar-brand" href="env_select.php">環境データ一覧</a></div>
    </div>
  </nav>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<form method="POST" action="env_update.php">
  <div class="jumbotron">
    <fieldset>
      <legend>製造環境の管理</legend>
      <table>
        <tr>
          <th>項目</th>
          <th>回答</th>
        </tr>
        <tr>
          <td>1. 施設設備の衛生</td>
          <td><input type="checkbox" name="env_q1" <?= $row["env_q1"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>2. 器具の衛生</td>
          <td><input type="checkbox" name="env_q2" <?= $row["env_q2"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>3. 食品や器具の取扱い</td>
          <td><input type="checkbox" name="env_q3" <?= $row["env_q3"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>4. 廃棄物の取扱い</td>
          <td><input type="checkbox" name="env_q4" <?= $row["env_q4"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>5-1. 健康管理（従業員）</td>
          <td><input type="checkbox" name="env_q5_1" <?= $row["env_q5_1"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>5-2. 服装（従業員）</td>
          <td><input type="checkbox" name="env_q5_2" <?= $row["env_q5_2"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>5-3. 手洗い（従業員）</td>
          <td><input type="checkbox" name="env_q5_3" <?= $row["env_q5_3"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>6. 使用水</td>
          <td><input type="checkbox" name="env_q6" <?= $row["env_q6"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>7. 害虫・昆虫対策</td>
          <td><input type="checkbox" name="env_q7" <?= $row["env_q7"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
          <td>8. 情報管理の提供</td>
          <td><input type="checkbox" name="env_q8" <?= $row["env_q8"] == 1 ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td>特記事項：</td>
            <td><textarea name="env_text" rows="4" cols="40"><?= htmlspecialchars($row["env_text"], ENT_QUOTES, 'UTF-8') ?></textarea></td>
        </tr>
        <tr>
          <td>サイン</td>
          <td>
            <select name="env_name">
              <?php foreach ($names as $name): ?>
                <option value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" <?= $row["env_name"] == $name ? 'selected' : '' ?>>
                  <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
      </table>
      <br>
      <input type="hidden" name="id" value="<?= htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') ?>">
      <input type="submit" value="送信">
    </fieldset>
  </div>
</form>

<!-- Main[End] -->

</body>
</html>
