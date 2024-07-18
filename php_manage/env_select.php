<?php
// 0. SESSION開始！！
session_start();

// １．関数群の読み込み
include("funcs.php");

// LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// ２．データ登録SQL作成
$pdo = db_conn();
$sql = "SELECT * FROM m_env_table";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

// ３．データ表示
$values = "";
if($status==false) {
  sql_error($stmt);
}

// 全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
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
        <?= $_SESSION["name"]?>さん、こんにちは！
      <a class="navbar-brand" href="env_index.php">環境データ登録</a><var>
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
          <th>環境名</th>
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
          <?php if($_SESSION["kanri_flg"]=="1"){ ?>
          <th>削除</th>
          <?php } ?>
        </tr>
      <?php foreach($values as $v){ ?>
        <tr>
          <td><?= $v["id"] ?></td>
          <td><a href="detail.php?id=<?= $v["id"] ?>"><?= $v["env_name"] ?></a></td>
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

          <?php if($_SESSION["kanri_flg"]=="1"){ ?>
          <td><a href="delete.php?id=<?= $v["id"] ?>">削除</a></td>
          <td><a href="env_detail.php?id=<?= $v["id"] ?>">編集</a></td>
          <?php } ?>
        </tr>
      <?php } ?>
      </table>

  </div>
</div>
<!-- Main[End] -->


<script>
  const a = '<?= $json ?>';
  console.log(JSON.parse(a));
</script>
</body>
</html>
