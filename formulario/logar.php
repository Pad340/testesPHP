<?php

use classes\User;

require __DIR__ . "/classes/User.php";

include __DIR__ . "/forms/login.php";

$user = new User();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user->login($_POST['email'], $_POST['password']);
}

echo "<p><a href='./'/>Voltar</p>";