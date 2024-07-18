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
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header"><a class="navbar-brand" href="env_select.php">環境データ一覧</a></div>
            </div>
        </nav>
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
    <form method="POST" action="env_insert.php">
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
                        <td><input type="checkbox" name="env_q1"></td>
                    </tr>
                    <tr>
                        <td>2. 器具の衛生</td>
                        <td><input type="checkbox" name="env_q2"></td>
                    </tr>
                    <tr>
                        <td>3. 食品や器具の取扱い</td>
                        <td><input type="checkbox" name="env_q3"></td>
                    </tr>
                    <tr>
                        <td>4. 廃棄物の取扱い</td>
                        <td><input type="checkbox" name="env_q4"></td>
                    </tr>
                    <tr>
                        <td>5-1. 健康管理（従業員）</td>
                        <td><input type="checkbox" name="env_q5_1"></td>
                    </tr>
                    <tr>
                        <td>5-2. 服装（従業員）</td>
                        <td><input type="checkbox" name="env_q5_2"></td>
                    </tr>
                    <tr>
                        <td>5-3. 手洗い（従業員）</td>
                        <td><input type="checkbox" name="env_q5_3"></td>
                    </tr>
                    <tr>
                        <td>6. 使用水</td>
                        <td><input type="checkbox" name="env_q6"></td>
                    </tr>
                    <tr>
                        <td>7. 害虫・昆虫対策</td>
                        <td><input type="checkbox" name="env_q7"></td>
                    </tr>
                    <tr>
                        <td>8. 情報管理の提供</td>
                        <td><input type="checkbox" name="env_q8"></td>
                    </tr>
                    <tr>
                        <td>特記事項：</td>
                        <td><textarea name="env_text" rows="4" cols="40"></textarea></td>
                    </tr>
                    <tr>
                        <td>サイン</td>
                        <td>
                            <select name="env_name">
                                <?php foreach ($names as $name): ?>
                                    <option value="<?= h($name) ?>"><?= h($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="auth_id" value="<?= h($auth_id) ?>">
                <input type="submit" value="送信">
            </fieldset>
        </div>
    </form>
    <!-- Main[End] -->

</body>
</html>
