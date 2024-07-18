<?php
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

// 値をバインド
$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);

$status = $stmt->execute();

// 3. データ表示
$values = "";
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
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>チェック項目作成</title>
<link href="./css/all.css" rel="stylesheet">
<style>
div{padding: 10px;font-size:16px;}
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
  <form method="POST" action="rcrd_insert.php">
    <fieldset>
      <select name="admin_or_emp" id="id_admin_or_emp">
        <option value="1">管理者</option>
        <option value="0">従業員</option>
      </select>
      <select name="work_in_or_out" id="id_work_in_or_out">
        <option value="1">出勤時</option>
        <option value="0">退勤時</option>
      </select>
      <div id="items_container"></div>
      <input type="submit" value="送信">
    </fieldset>
  </form>
</div>
<!-- Main[End] -->

<script>
  const jsonString = '<?= $json ?>';
  let data = [];

  try {
    data = JSON.parse(jsonString);
    console.log(data);
  } catch (e) {
    console.error('Error parsing JSON:', e);
  }

  function filterData() {
    const adminOrEmp = document.getElementById('id_admin_or_emp').value;
    const workInOrOut = document.getElementById('id_work_in_or_out').value;

    const filteredData = data.filter(row =>
      row.admin_or_emp == adminOrEmp && row.work_in_or_out == workInOrOut
    );

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
      titleP.name = `title_${index}`;
      titleP.textContent = row.title;
      titleBox.appendChild(titleP);
      itemField.appendChild(titleBox);

      if (row.check_exist == 1) {
        const checkBox = document.createElement('div');
        checkBox.id = 'check_box';
        const yesButton = document.createElement('input');
        yesButton.type = 'radio';
        yesButton.name = `check_item_${index}`;
        yesButton.value = 'YES';
        yesButton.id = `check_box_yes_${index}`;
        const noButton = document.createElement('input');
        noButton.type = 'radio';
        noButton.name = `check_item_${index}`;
        noButton.value = 'NO';
        noButton.id = `check_box_no_${index}`;
        checkBox.appendChild(yesButton);
        checkBox.appendChild(noButton);
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

  document.getElementById('id_admin_or_emp').addEventListener('change', filterData);
  document.getElementById('id_work_in_or_out').addEventListener('change', filterData);

  window.onload = filterData;
</script>
</body>
</html>
