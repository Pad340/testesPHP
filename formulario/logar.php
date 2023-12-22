<?php

use classes\User;

require __DIR__ . "/classes/User.php";

include __DIR__ . "/forms/login.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    (new User())->login($_POST['email'], $_POST['password']);
}
echo "<p><a href='./'/>Voltar</p>";