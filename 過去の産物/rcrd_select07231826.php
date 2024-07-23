<?php
// 0. SESSION開始！！
session_start();

// 1. 関数群の読み込み
include("funcs.php");

// LOGINチェック
sschk();

// 2. データ登録SQL作成
$auth_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// データ取得SQL作成
$pdo = db_conn();
$sql = "SELECT * FROM H_record_table WHERE auth_id = :auth_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':auth_id', $auth_id, PDO::PARAM_INT);
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
$values = [];
if ($status === false) {
    sql_error($stmt);
} else {
    $values = $stmt->fetchAll(PDO::FETCH_ASSOC); // 全データ取得
}

$titles = [];
$templates = [];//select要素用
if ($status_tmplt === false) {
    sql_error($stmt_tmplt);
} else {
    while ($row = $stmt_tmplt->fetch(PDO::FETCH_ASSOC)) {
        $titles[$row['id']] = $row['title'];
        $templates[] = [//select要素用
          't_id' => $row['id'],
          't_title' => $row['title']
      ];
    }
}

$names = [];
$members = [];//select要素用
if ($status_mmbr === false) {
    sql_error($stmt_mmbr);
} else {
    while ($row = $stmt_mmbr->fetch(PDO::FETCH_ASSOC)) {
        $names[$row['id']] = $row['name'];
        $members[] = [//select要素用
          'm_id' => $row['id'],
          'm_name' => $row['name']
      ];
    }
}



// var_dump($names);
// レコードとテンプレートデータをマージ
foreach ($values as &$value) {
    if (isset($titles[$value['title']])) {
        $value['template_title'] = $titles[$value['title']];
    } else {
        $value['template_title'] = 'タイトル不明（チェック項目が削除されました）';
    }

    if (isset($names[$value['recorder']])) {
        $value['template_name'] = $names[$value['recorder']];
    } else {
        $value['template_name'] = '記録者不明（名簿から削除されています）';
    }
    // 画像データをBase64エンコードする
    if ($value['photo'] !== null) {
        $value['photo'] = base64_encode($value['photo']);
    } else {
        $value['photo'] = null;
    }
}

// var_dump($values);
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'JSONエンコードエラー: ' . json_last_error_msg();
    exit;
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
    <div class="container jumbotron">絞り込み
        <select name="admin_or_emp" id="id_admin_or_emp">
            <option value="">管理者/従業員：全て</option>
            <option value="1">管理者</option>
            <option value="0">従業員</option>
        </select>
        <select name="work_in_or_out" id="id_work_in_or_out">
            <option value="">出勤時/退勤時：全て</option>
            <option value="1">出勤時</option>
            <option value="0">退勤時</option>
        </select>
        <select name="recorder" id="id_sel_recorder">
          <option value="">記録者：全て</option>
          <?php foreach ($members as $member): ?>
            <option value="<?= h($member['m_id']) ?>"><?= h($member['m_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <select name="title" id="id_sel_title">
          <option value="">チェック項目：全て</option>
          <?php foreach ($templates as $template): ?>
            <option value="<?= h($template['t_id']) ?>"><?= h($template['t_title']) ?></option>
          <?php endforeach; ?>
        </select>

        <div style="background: gray;">メモ
            <ul style="padding: 0; list-style-type: none;">
                <li style="display: inline-block; margin-right: 20px;">記録者絞り込み：現状selectのみ作成</li>
                <li style="display: inline-block; margin-right: 20px;">日付絞り込み</li>
                <li style="display: inline-block; margin-right: 20px;">項目絞り込み：現状selectのみ作成</li>
            </ul>
        </div>

        <table id="record_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>項目名</th>
                    <th>管理者/従業員</th>
                    <th>出勤/退勤</th>
                    <th>[チェック欄]</th>
                    <th>[テキスト記入欄]</th>
                    <th>[温度入力欄]</th>
                    <th>[写真投稿欄]</th>
                    <th>記録者</th>
                    <?php if($_SESSION["kanri_flg"] == "1"){ ?>
                    <th>削除</th>
                    <th>編集</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody id="table_body">
            </tbody>
        </table>
    </div>
</div>
<!-- Main[End] -->

<script>
// PHPからのJSONデータを取得
const jsonString = '<?= isset($json) ? $json : '' ?>';
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
    const selRecorder = document.getElementById('id_sel_recorder').value;
    const selTitle = document.getElementById('id_sel_title').value;

    const filteredData = data.filter(row => {
        return (adminOrEmp === "" || row.admin_or_emp == adminOrEmp) &&
              (workInOrOut === "" || row.work_in_or_out == workInOrOut) &&
              (selRecorder === "" || row.recorder == selRecorder) &&
              (selTitle === "" || row.title == selTitle);
    });

    displayData(filteredData);
}

function displayData(filteredData) {
    const tableBody = document.getElementById('table_body');
    tableBody.innerHTML = '';

    filteredData.forEach(v => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${v.id}</td>
            <td>${v.template_title}</td>
            <td>${v.admin_or_emp == 1 ? "管理者" : "従業員"}</td>
            <td>${v.work_in_or_out == 1 ? "出勤時" : "退勤時"}</td>
            <td>${v.check_item}</td>
            <td>${v.text}</td>
            <td>${v.temp}</td>
            <td>${v.photo ? '<img class="photo" src="data:image/jpeg;base64,' + v.photo + '" alt="Photo">' : '無'}</td>
            <td>${v.template_name}</td>
            <?php if($_SESSION["kanri_flg"] == "1"){ ?>
            <td><a href="tmplt_delete.php?id=${v.id}">削除</a></td>
            <td><a href="tmplt_detail.php?id=${v.id}">編集</a></td>
            <?php } ?>
        `;
        tableBody.appendChild(tr);
    });
}

// フィルタリングイベントの設定
document.getElementById('id_admin_or_emp').addEventListener('change', filterData);
document.getElementById('id_work_in_or_out').addEventListener('change', filterData);
document.getElementById('id_sel_recorder').addEventListener('change', filterData);
document.getElementById('id_sel_title').addEventListener('change', filterData);

// ページ読み込み時にフィルタリングを実行
window.onload = filterData;
</script>
</body>
</html>
