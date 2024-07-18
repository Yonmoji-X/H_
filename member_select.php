<?php
// 0. SESSION開始！！
session_start();

// 1. 関数群の読み込み
include("funcs.php");

// LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// 2. データ登録SQL作成
$pdo = db_conn();
$auth_id = $_SESSION['user_id']; // セッションからユーザーIDを取得

// auth_idが一致するデータのみを取得するクエリに変更
$sql = "SELECT * FROM H_member_table WHERE auth_id = :auth_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT); // バインドパラメータにユーザーIDを設定
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
<title>衛生管理</title>
<link rel="stylesheet" href="css/range.css">
<!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
<style>div{padding: 10px;font-size:16px;}</style>
</head>
<body id="main">
<!-- Head[Start] -->
<header>
<?= include("menu.php");?>

</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<div>
    <div class="container jumbotron">
      <table>
        <tr>
          <th>ID</th>
          <th>名前</th>
          <?php if ($_SESSION["kanri_flg"] == "1") { ?>
          <th>操作</th>
          <?php } ?>
        </tr>
        <?php foreach ($values as $v) { ?>
        <tr>
          <td><?= h($v["id"]) ?></td>
          <td><a href="detail.php?id=<?= h($v["id"]) ?>"><?= h($v["name"]) ?></a></td>
          <?php if ($_SESSION["kanri_flg"] == "1") { ?>
          <td>
            <a href="member_delete.php?id=<?= h($v["id"]) ?>">[削除]</a>
            <a href="member_detail.php?id=<?= h($v["id"]) ?>">[編集]</a>
          </td>
          <?php } ?>
        </tr>
        <?php } ?>
      </table>
    </div>
</div>
<!-- Main[End] -->

<script>
  // JSONデータを正しくエスケープする
  const json = `<?= json_encode($values, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS) ?>`;
  console.log(JSON.parse(json));
</script>
</body>
</html>
