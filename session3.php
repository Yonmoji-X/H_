<?php
session_start();
echo session_id(); //自分に割り振られたKEY
echo $_SESSION["name"];
echo $_SESSION["age"];

