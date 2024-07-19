<?php
// 0. SESSION開始！！
session_start();

// 1. 関数群の読み込み
include("funcs.php");

// LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// 2. データ登録SQL作成
// $pdo = db_conn();
// $sql = "SELECT * FROM H_record_table";
// $stmt = $pdo->prepare($sql);

// セッションからユーザーのauth_idを取得
$auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// 2. データ登録SQL作成
$pdo = db_conn();
$sql = "SELECT * FROM H_record_table WHERE auth_id = :auth_id";
$stmt = $pdo->prepare($sql);

// プレースホルダーに値をバインド
$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);

$status = $stmt->execute();

// 3. データ表示
$values = "";
if ($status === false) {
    sql_error($stmt);
} else {
    $values = $stmt->fetchAll(PDO::FETCH_ASSOC); // 全データ取得
    $json = json_encode($values, JSON_UNESCAPED_UNICODE);
}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>チェック項目作成</title>
<link href="./css/all.css" rel="stylesheet">
<style>
div{padding: 10px;font-size:16px;}
img.photo { width: 100px; height: 100px; object-fit: cover; }
</style>
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
          <th>項目名</th>
          <th>管理者/従業員</th>
          <th>出勤/退勤</th>
          <th>項目①：<br>[チェック欄]</th>
          <th>項目②：<br>[テキスト記入欄]</th>
          <th>項目③：<br>[温度入力欄]</th>
          <th>項目④：<br>[写真投稿欄]</th>
          <?php if($_SESSION["kanri_flg"] == "1"){ ?>
          <th>削除</th>
          <th>編集</th>
          <?php } ?>
        </tr>
      <?php foreach($values as $v){ ?>
        <tr>
          <td><?= htmlspecialchars($v["id"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($v["title"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= $v["admin_or_emp"] == 1 ? "管理者" : "従業員" ?></td>
          <td><?= $v["work_in_or_out"] == 1 ? "出勤時" : "退勤時" ?></td>
          <td><?= htmlspecialchars($v["check_item"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($v["text"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($v["temp"], ENT_QUOTES, 'UTF-8') ?></td>
          <td>
            <?php
            if ($v["photo"] !== null) {
              $base64img = base64_encode($v["photo"]);
              echo '<img class="photo" src="data:image/jpeg;base64,' . $base64img . '" alt="Photo">';
            } else {
              echo "無";
            }
            ?>
          </td>
          <?php if($_SESSION["kanri_flg"] == "1"){ ?>
          <td><a href="tmplt_delete.php?id=<?= htmlspecialchars($v["id"], ENT_QUOTES, 'UTF-8') ?>">削除</a></td>
          <td><a href="tmplt_detail.php?id=<?= htmlspecialchars($v["id"], ENT_QUOTES, 'UTF-8') ?>">編集</a></td>
          <?php } ?>
        </tr>
      <?php } ?>
      </table>
  </div>
</div>
<!-- Main[End] -->


<script>
  // JSON データをデバッグしてみる
  const jsonString = '<?= isset($json) ? $json : '' ?>';
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
