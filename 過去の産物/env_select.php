<?php
// 0. SESSION開始！！
session_start();

// 1. 関数群の読み込み
include("funcs.php");

// LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// セッションからユーザーのauth_idを取得
$auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// 2. データ登録SQL作成
$pdo = db_conn();
$sql = "SELECT * FROM m_env_table WHERE auth_id = :auth_id";
$stmt = $pdo->prepare($sql);

// プレースホルダーに値をバインド
$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);

$status = $stmt->execute();

// 3. データ表示
$values = "";
if ($status == false) {
    sql_error($stmt);
}

// 全データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
$json = json_encode($values, JSON_UNESCAPED_UNICODE);

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>フリーアンケート表示</title>
<!-- <link rel="stylesheet" href="css/range.css"> -->
<!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
<link href="./css/all.css" rel="stylesheet">
<style>div{padding: 10px;font-size:16px;}</style>
</head>
<body id="main">
<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <?= htmlspecialchars($_SESSION["name"], ENT_QUOTES, 'UTF-8') ?>さん、こんにちは！
        <a class="navbar-brand" href="env_index.php">環境データ登録</a><var>
        <a class="navbar-brand" href="member_select.php">所属メンバー一覧</a><var>
        <a class="navbar-brand" href="logout.php">ログアウト</a></var>
      </div>
    </div>
  </nav>
</header>
<!-- Head[End] -->


<!-- Main[Start] -->
<div>
    <div class="container jumbotron">

      <table>
        <tr>
          <th>ID</th>
          <th>記入者</th>
          <th>施設設備の衛生</th>
          <th>器具の衛生</th>
          <th>食品や器具の取扱い</th>
          <th>廃棄物の取扱い</th>
          <th>健康管理（従業員）</th>
          <th>服装（従業員）</th>
          <th>手洗い（従業員）</th>
          <th>使用水</th>
          <th>害虫・昆虫対策</th>
          <th>情報管理の提供</th>
          <?php if($_SESSION["kanri_flg"] == "1"){ ?>
          <th>削除</th>
          <th>編集</th>
          <?php } ?>
        </tr>
      <?php foreach($values as $v){ ?>
        <tr>
          <td><?= htmlspecialchars($v["id"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($v["env_name"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= $v["env_q1"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q2"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q3"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q4"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q5_1"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q5_2"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q5_3"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q6"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q7"] == 1 ? "◯" : "×" ?></td>
          <td><?= $v["env_q8"] == 1 ? "◯" : "×" ?></td>

          <?php if($_SESSION["kanri_flg"] == "1"){ ?>
          <td><a href="delete.php?id=<?= htmlspecialchars($v["id"], ENT_QUOTES, 'UTF-8') ?>">削除</a></td>
          <td><a href="env_detail.php?id=<?= htmlspecialchars($v["id"], ENT_QUOTES, 'UTF-8') ?>">編集</a></td>
          <?php } ?>
        </tr>
      <?php } ?>
      </table>

  </div>
</div>
<!-- Main[End] -->


<script>
  // JSON データをデバッグしてみる
  const jsonString = '<?= $json ?>';
  console.log(jsonString); // ここで JSON の構造を確認します

  try {
    const data = JSON.parse(jsonString);
    console.log(data);
  } catch (e) {
    console.error('Error parsing JSON:', e);
  }
</script>
</body>
</html>
