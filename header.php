<?php
//0. SESSION開始！！
session_start();

//１．関数群の読み込み
include("funcs.php");

//LOGINチェック → funcs.phpへ関数化しましょう！
sschk();


//２．データ登録SQL作成
$pdo = db_conn();
$sql = "SELECT * FROM m_an_table";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

//３．データ表示
$values = "";
if($status==false) {
  sql_error($stmt);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
$json = json_encode($values,JSON_UNESCAPED_UNICODE);

?>

<!-- <header> -->
  <!-- <nav class="navbar navbar-default">
    <div class="container-fluid">
    <div class="navbar-header">
          <a class="navbar-brand" href="env_index.php">製造環境の管理</a>
          <a class="navbar-brand" href="env_select.php">環境管理データ一覧</a>
        <a class="navbar-brand" href="index.php">データ登録</a><var>
        <a class="navbar-brand" href="logout.php">ログアウト</a></var>
    </div>
  </nav> -->
<!-- </header> -->

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
        <a class="navbar-brand" href="env_index.php">環境の登録</a>
        <a class="navbar-brand" href="env_select.php">環境管理データ一覧</a>
        <!-- <a class="navbar-brand" href="index.php">データ登録</a> -->
        <a class="navbar-brand" href="logout.php">ログアウト</a>
        <?= $_SESSION["name"]?>さん、こんにちは！
            <!-- <a class="navbar-brand" href="select.php">アンケート一覧</a>　
            <a class="navbar-brand" href="user.php">ユーザー登録</a>　
            <a class="navbar-brand" href="logout.php">ログアウト</a> -->
        </div>
    </div>
</nav>
