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

$sql_tmplt = "SELECT * FROM H_template_table WHERE auth_id = :auth_id";
$stmt_tmplt = $pdo->prepare($sql_tmplt);
$stmt_tmplt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);
$status_tmplt = $stmt_tmplt->execute();

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
if ($status_tmplt == false) {
    sql_error($stmt_tmplt);
} else {
    $row_tmplt = $stmt_tmplt->fetch();
}
if ($status_mmbr == false) {
    sql_error($stmt_mmbr);
} else {
    $row_mmbr = $stmt_mmbr->fetch();
}

$values = [];
$members = [];
while ($row_mmbr = $stmt_mmbr->fetch(PDO::FETCH_ASSOC)) {
    $members[] = [
        'm_id' => $row_mmbr['id'],
        'm_name' => $row_mmbr['name']
    ];
}

// 全データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
$values_tmplt = $stmt_tmplt->fetchAll(PDO::FETCH_ASSOC);
$json_tmplt = json_encode($values_tmplt, JSON_UNESCAPED_UNICODE);
$values_mmbr = $stmt_mmbr->fetchAll(PDO::FETCH_ASSOC);
$json_mmbr = json_encode($values_mmbr, JSON_UNESCAPED_UNICODE);
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
                <!-- <select name="admin_or_emp" id="id_admin_or_emp">
                    <option value="1">管理者</option>
                    <option value="0">従業員</option>
                </select>
                <select name="work_in_or_out" id="id_work_in_or_out">
                    <option value="1">出勤時</option>
                    <option value="0">退勤時</option>
                </select> -->
                <div id="items_container"></div>

                <select name="recorder">
                    <?php foreach ($members as $member): ?>
                        <option value="<?= h($member['m_id']) ?>"><?= h($member['m_name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="hidden" name="auth_id" value="<?= h($auth_id) ?>">
                <input type="submit" value="完了">
            </fieldset>
        </form>
    </div>
    <!-- Main[End] -->

    <script>
        const jsonString = '<?= $json ?>';
        const jsonString_tmplt = '<?= $json_tmplt ?>';
        const jsonString_mmbr = '<?= $json_mmbr ?>';
        let data = [];
        let data_tmplt = [];
        let data_mmbr = [];

        try {
            data = JSON.parse(jsonString);
            data_tmplt = JSON.parse(jsonString_tmplt);
            data_mmbr = JSON.parse(jsonString_mmbr);
            console.log(`data:`, data);
            console.log(`data_tmplt:`, data_tmplt);
            console.log(`data_mmbr:`, data_mmbr);
        } catch (e) {
            console.error('Error parsing JSON:', e);
        }

        function filterData() {
            // Assuming you have some criteria to filter data, implement the logic here
            const filteredData = data_tmplt; // Update this to filter based on actual criteria

            displayData(filteredData);
        }

        function displayData(filteredData) {
            const container = document.getElementById('items_container');
            container.innerHTML = '';

            filteredData.forEach((row, index) => {
                const itemField = document.createElement('div');
                itemField.classList.add('item_field');
                itemField.style.border = 'solid 0.5px black';

                const titleBox = document.createElement('div');
                titleBox.id = 'title_box';
                const titleP = document.createElement('p');
                titleP.textContent = row.title;

                // Hidden field for title
                const titleInput = document.createElement('input');
                titleInput.type = 'hidden';
                titleInput.name = `title_${index}`;
                titleInput.value = row.id;

                titleBox.appendChild(titleP);
                titleBox.appendChild(titleInput);
                itemField.appendChild(titleBox);

                if (row.check_exist == 1) {
                    const checkBox = document.createElement('div');
                    checkBox.id = 'check_box';

                    const yesButton = document.createElement('input');
                    yesButton.type = 'radio';
                    yesButton.name = `check_item_${index}`;
                    yesButton.value = 'YES';
                    yesButton.id = `check_box_yes_${index}`;

                    const yesLabel = document.createElement('label');
                    yesLabel.htmlFor = `check_box_yes_${index}`;
                    yesLabel.textContent = 'YES';

                    const noButton = document.createElement('input');
                    noButton.type = 'radio';
                    noButton.name = `check_item_${index}`;
                    noButton.value = 'NO';
                    noButton.id = `check_box_no_${index}`;

                    const noLabel = document.createElement('label');
                    noLabel.htmlFor = `check_box_no_${index}`;
                    noLabel.textContent = 'NO';

                    checkBox.appendChild(yesButton);
                    checkBox.appendChild(yesLabel);
                    checkBox.appendChild(noButton);
                    checkBox.appendChild(noLabel);

                    itemField.appendChild(checkBox);
                }

                if (row.text_exist == 1) {
                    const textBox = document.createElement('div');
                    textBox.id = 'text_box';
                    const textInput = document.createElement('input');
                    textInput.type = 'text';
                    textInput.name = `text_${index}`;
                    textBox.appendChild(textInput);
                    itemField.appendChild(textBox);
                }

                if (row.photo_exist == 1) {
                    const photoBox = document.createElement('div');
                    photoBox.id = 'photo_box';
                    const photoInput = document.createElement('input');
                    photoInput.type = 'file';
                    photoInput.name = `photo_${index}`;
                    photoBox.appendChild(photoInput);
                    itemField.appendChild(photoBox);
                }

                if (row.temp_exist == 1) {
                    const tempBox = document.createElement('div');
                    tempBox.id = 'temp_box';
                    const tempInput = document.createElement('input');
                    tempInput.type = 'number';
                    tempInput.name = `temp_${index}`;
                    tempBox.appendChild(tempInput);
                    itemField.appendChild(tempBox);
                }

                container.appendChild(itemField);
            });
        }

        // document.getElementById('id_admin_or_emp').addEventListener('change', filterData);
        // document.getElementById('id_work_in_or_out').addEventListener('change', filterData);

        window.onload = filterData;
    </script>
</body>
</html>
