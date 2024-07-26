<?php
session_start();
$id = $_GET["id"]; // ?id=**を受け取る
include("funcs.php");
sschk();
$pdo = db_conn();

// セッションからユーザーIDを取得
$auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$sql = "SELECT * FROM H_record_table WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

$sql_mmbr = "SELECT * FROM H_member_table WHERE auth_id = :auth_id";
$stmt_mmbr = $pdo->prepare($sql_mmbr);
$stmt_mmbr->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);
$status_mmbr = $stmt_mmbr->execute();

// データ表示
if ($status == false) {
    sql_error($stmt);
} else {
    $row = $stmt->fetch();
}
$rcrd = [
    'id' => $row['id'],
    'admin_or_emp' => $row['admin_or_emp'],
    'work_in_or_out' => $row['work_in_or_out'],
    'title' => $row['title'],
    'recorder' => $row['recorder'],
    'check_item' => $row['check_item'],
    'photo' => $row['photo'],
    'text' => $row['text'],
    'temp' => $row['temp'],
    'indate' => $row['indate'],
    'auth_id' => $row['auth_id'],
];

if ($status_mmbr == false) {
    sql_error($stmt_mmbr);
} else {
    $members = [];
    while ($row_mmbr = $stmt_mmbr->fetch(PDO::FETCH_ASSOC)) {
        $members[] = [
            'm_id' => $row_mmbr['id'],
            'm_name' => $row_mmbr['name']
        ];
    }
}

$sql_tmplt = "SELECT * FROM H_template_table WHERE id = :id";
$stmt_tmplt = $pdo->prepare($sql_tmplt);
$stmt_tmplt->bindValue(':id', $rcrd['title'], PDO::PARAM_INT);
$status_tmplt = $stmt_tmplt->execute();

if ($status_tmplt == false) {
    sql_error($stmt_tmplt);
} else {
    $row_tmplt = $stmt_tmplt->fetch();
}

// JSONエンコード
$json_rcrd = json_encode($rcrd, JSON_UNESCAPED_UNICODE);
$json_tmplt = json_encode($row_tmplt, JSON_UNESCAPED_UNICODE);
$json_mmbr = json_encode($members, JSON_UNESCAPED_UNICODE);
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
        div { padding: 10px; font-size: 16px; }
    </style>
</head>
<body id="main">
    <!-- Head[Start] -->
    <header>
        <?= include("menu.php"); ?>
    </header>
    <!-- Head[End] -->

    <!-- Main[Start] -->
    <div>
        <form method="POST" action="rcrd_update.php" enctype="multipart/form-data">
            <fieldset>
                <div id="items_container"></div>
                <select name="recorder">
                    <?php foreach ($members as $member): ?>
                        <option value="<?= h($member['m_id']) ?>" <?= ($member['m_id'] == $rcrd['recorder']) ? 'selected' : '' ?>><?= h($member['m_name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="hidden" name="auth_id" value="<?= h($auth_id) ?>">
                <input type="submit" value="完了">
            </fieldset>
        </form>
    </div>
    <!-- Main[End] -->

    <script>
        const rcrd = <?= $json_rcrd ?>;
        const template = <?= $json_tmplt ?>;
        const members = <?= $json_mmbr ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const itemsContainer = document.getElementById('items_container');

            // テンプレートタイトルを表示
            const titleItem = document.createElement('div');
            titleItem.innerHTML = `<h2>テンプレートタイトル: ${template.title}</h2>`;
            itemsContainer.appendChild(titleItem);

            if (template.check_exist == 1) {
                const checkItem = document.createElement('div');
                checkItem.innerHTML = `
                    <label>チェック項目：</label>
                    <input type="radio" name="check_item" value="YES" ${rcrd.check_item === 'YES' ? 'checked' : ''}>YES
                    <input type="radio" name="check_item" value="NO" ${rcrd.check_item === 'NO' ? 'checked' : ''}>NO
                `;
                itemsContainer.appendChild(checkItem);
            }

            if (template.photo_exist == 1) {
                const photoItem = document.createElement('div');
                photoItem.innerHTML = `
                    <label>写真：</label>
                    <input type="file" name="photo" accept="image/*">
                    ${rcrd.photo ? `<img src="${rcrd.photo}" alt="Current Photo" style="max-width: 100px;">` : ''}
                `;
                itemsContainer.appendChild(photoItem);
            }

            if (template.text_exist == 1) {
                const textItem = document.createElement('div');
                textItem.innerHTML = `
                    <label>テキスト：</label>
                    <textarea name="text">${rcrd.text}</textarea>
                `;
                itemsContainer.appendChild(textItem);
            }

            if (template.temp_exist == 1) {
                const tempItem = document.createElement('div');
                tempItem.innerHTML = `
                    <label>温度：</label>
                    <input type="number" name="temp" value="${rcrd.temp}">
                `;
                itemsContainer.appendChild(tempItem);
            }
        });
    </script>
</body>
</html>
