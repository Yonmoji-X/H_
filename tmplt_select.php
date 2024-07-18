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
$sql = "SELECT * FROM H_template_table WHERE auth_id = :auth_id";
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
<title>チェック項目作成</title>
<!-- <link rel="stylesheet" href="css/range.css"> -->
<!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
<link href="./css/all.css" rel="stylesheet">
<style>div{padding: 10px;font-size:16px;}</style>
</head>
<body id="main">
<!-- Head[Start] -->
<header>
<?= include("menu.php");?>
  <!-- <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        さん、こんにちは！
        <a class="navbar-brand" href="tmplt_index.php">チェック項目設定</a><var>
        <a class="navbar-brand" href="member_select.php">所属メンバー一覧</a><var>
        <a class="navbar-brand" href="logout.php">ログアウト</a></var>
      </div>
    </div>
  </nav> -->
</header>
<!-- Head[End] -->


<!-- Main[Start] -->
<div>
    <div class="container jumbotron">

      <table>
        <tr>
          <th nowrap>ID</th>
          <th nowrap>項目名</th>
          <th nowrap>管理者/従業員</th>
          <th nowrap>出勤/退勤</th>
          <!-- <th nowrap>項目名</th> -->
          <th nowrap>項目①：<br>[チェック欄]</th>
          <th nowrap>項目②：<br>[テキスト記入欄]</th>
          <th nowrap>項目③：<br>[温度入力欄]</th>
          <th nowrap>項目④：<br>[写真投稿欄]</th>
          <?php if($_SESSION["kanri_flg"] == "1"){ ?>
          <th nowrap>削除</th>
          <th nowrap>編集</th>
          <?php } ?>
        </tr>
      <?php foreach($values as $v){ ?>
        <tr>
          <td><?= htmlspecialchars($v["id"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($v["title"], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= $v["admin_or_emp"] == 1 ? "管理者" : "従業員" ?></td>
          <td><?= $v["work_in_or_out"] == 1 ? "出勤時" : "退勤時" ?></td>
          <td><?= $v["check_exist"] == 1 ? "有" : "無" ?></td>
          <td><?= $v["text_exist"] == 1 ? "有" : "無" ?></td>
          <td><?= $v["temp_exist"] == 1 ? "有" : "無" ?></td>
          <td><?= $v["photo_exist	"] == 1 ? "有" : "無" ?></td>

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
