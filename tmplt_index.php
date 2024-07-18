<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>データ登録</title>
    <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
    <style>div{padding: 10px;font-size:16px;}</style>
</head>
<body>

    <!-- Head[Start] -->
    <header>
    <?= include("menu.php");?>
        <!-- <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header"><a class="navbar-brand" href="tmplt_select.php">チェック項目設定</a></div>
            </div>
        </nav> -->
    </header>
    <?php
    // セッションの開始
    session_start();
    // funcs.phpを読み込む
    include("funcs.php");

    // データベース接続
    $pdo = db_conn();

    // セッションからユーザーIDを取得
    $auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        // SQLクエリを準備
        $stmt = $pdo->prepare("SELECT name FROM m_an_table WHERE auth_id = :auth_id");

        // プレースホルダーに値をバインド
        $stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);

        // SQLクエリを実行
        $status = $stmt->execute();

        // SQL実行時にエラーがある場合はエラーメッセージを表示
        if ($status == false) {
            sql_error($stmt);
        }

        // データを配列に格納
        $names = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $names[] = $row['name'];
        }
    } catch (PDOException $e) {
        // データベース接続エラー処理
        echo 'Database error: ' . $e->getMessage();
    }
    ?>
    <!-- Head[End] -->

    <!-- Main[Start] -->
    <form method="POST" action="tmplt_insert.php">
        <div class="jumbotron">
            <fieldset>
                <legend>製造環境の管理</legend>
                <table>
                    <tr>
                        <th>項目</th>
                        <th>設定
                        </th>
                    </tr>
                    <tr>
                        <td>1.項目名</td>
                        <td><input name="title"></td>
                    </tr>
                    <tr>
                        <td>2.管理者/従業員</td>
                        <td>
                            <!-- <input type="checkbox" name="admin_or_emp"> -->
                            <select name="admin_or_emp">
                                <option value="1">管理者</option>
                                <option value="0">従業員</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>3.出勤/退勤</td>
                        <td>
                            <!-- <input type="checkbox" name="work_in_or_out"> -->
                            <select name="work_in_or_out">
                                <option value="1">出勤時</option>
                                <option value="0">退勤時</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>4. [チェック欄]の有無</td>
                        <td><input type="checkbox" name="check_exist"></td>
                    </tr>
                    <tr>
                        <td>5. [テキスト記入欄]の有無</td>
                        <td><input type="checkbox" name="text_exist"></td>
                    </tr>
                    <tr>
                        <td>6. [温度入力欄]の有無</td>
                        <td><input type="checkbox" name="temp_exist"></td>
                    </tr>
                    <tr>
                        <td>7. [写真投稿欄]の有無</td>
                        <td><input type="checkbox" name="photo_exist"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="auth_id" value="<?= h($auth_id) ?>">
                <input type="submit" value="登録">
            </fieldset>
        </div>
    </form>
    <!-- Main[End] -->

</body>
</html>
