<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="tmplt_select.php">チェック項目一覧</a>

            <?php if ($_SESSION["kanri_flg"] == "1"): ?>
                <a class="navbar-brand" href="tmplt_index.php">チェック項目登録</a>
                <a class="navbar-brand" href="member_index.php">従業員登録</a>
            <?php endif; ?>

            <a class="navbar-brand" href="member_select.php">従業員一覧</a>
            <a class="navbar-brand" href="rcrd_select.php">チェック一覧</a>
            <a class="navbar-brand" href="rcrd_index.php">[チェック]</a>
            <a class="navbar-brand" href="logout.php">ログアウト</a>


            <?php if ($_SESSION["kanri_flg"] == "1"): ?>
                <a class="navbar-brand" href="shr_index.php">一般ユーザー登録</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
