<?php

use classes\User;

require __DIR__ . "/classes/User.php";

$user = new User();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user->update();
}

if ($user = (new User())->listarId()[0]) {

    echo "<p>Nome: {$user['name']}</p>";
    echo "<p>Email: {$user['email']}</p>";
    echo "<p>Senha: {$user['password']}</p>";
    echo "<p>Telefone: {$user['number']}</p>";
    echo "<p>Data de nascimento: {$user['date_birth']}</p>";
    echo "<p>IP: {$user['ip']}</p>";
    echo "<p>Data de cadastro: {$user['date_register']}</p>";
    echo "<p><a href='deletar.php?&id={$user['id']}'>Deletar</a></p>";

    echo "<p><a href='./'/>Voltar</p>";
}

include __DIR__ . "/forms/update.php";
