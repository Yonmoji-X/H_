<?php
session_start();
include("funcs.php");

// LOGINチェック
sschk();

$auth_id = '';
if ($_SESSION["kanri_flg"] == "1") {
    $auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

$pdo = db_conn();
$kanri_flg = 0;

// auth_idが一致するデータのみを取得するクエリに変更
$sql = "SELECT * FROM H_user_table WHERE kanri_flg = :kanri_flg";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
    sql_error($stmt);
}

// 全データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>H_</title>
    <style>div{padding: 10px;font-size:16px;}</style>
</head>
<body>

    <!-- Head[Start] -->
    <header>
        <?= include("menu.php"); ?>
    </header>
    <!-- Head[End] -->

    <!-- Main[Start] -->
    <form method="POST" action="shr_insert.php">
        <div class="jumbotron">
            <fieldset>
                <legend>従業員登録</legend>
                <!-- 隠しフィールドにユーザーIDを追加 -->
                <input type="hidden" name="auth_id" value="<?= $auth_id ?>">
                <!-- 従業員アカウントID -->
                <label>従業員アカウントID：
                    <input type="text" id="gene_id">
                </label><br>
                <!-- 隠しフィールドに従業員IDを追加 -->
                <input type="hidden" name="gene_id" id="hidden_gene_id" value="">
                <input type="submit" value="送信">
            </fieldset>
        </div>
    </form>
    <!-- Main[End] -->

    <!-- JSONデータをJavaScriptで利用 -->
    <script>
        // PHPからJSONデータをJavaScriptに埋め込む
        var userTableData = <?= $json ?>;

        document.addEventListener('DOMContentLoaded', function() {
            var geneIdInput = document.getElementById('gene_id');
            var hiddenGeneId = document.getElementById('hidden_gene_id');

            geneIdInput.addEventListener('input', function() {
                var inputValue = geneIdInput.value;

                // `userTableData` から `lid` が `inputValue` と一致するレコードを探す
                var matchedRecord = userTableData.find(function(record) {
                    return record.lid === inputValue;
                });

                if (matchedRecord) {
                    // 一致するレコードが見つかった場合、`id` を `hidden_gene_id` の値に設定
                    hiddenGeneId.value = matchedRecord.id;
                } else {
                    // 一致するレコードが見つからない場合、隠しフィールドをクリア
                    hiddenGeneId.value = '';
                }
            });
        });
    </script>

</body>
</html>
