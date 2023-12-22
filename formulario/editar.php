<?php

use classes\Session;
use classes\User;

require __DIR__ . "/classes/User.php";

if (!(new Session())->has("userLogin")) {
    echo "<p>Antes de tudo, faça login.</p>";
    echo "<p><a href='logar.php'>Login</a></p>";
} else {
    if ($user = (new User())->listarId()[0]) {
        echo "<p>Nome: {$user['name']}</p>";
        echo "<p>Email: {$user['email']}</p>";
        echo "<p>Senha: {$user['password']}</p>";
        echo "<p>Telefone: {$user['number']}</p>";
        echo "<p>Data de nascimento: {$user['date_birth']}</p>";
        echo "<p>IP: {$user['ip']}</p>";
        echo "<p>Data de cadastro: {$user['date_register']}</p>";
        echo "<p><a href='deletar.php?&id={$user['id']}'>Deletar</a></p>";
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        (new User())->update();
    }

    include __DIR__ . "/forms/update.php";
}

echo "<p><a href='./'/>Voltar</p>";