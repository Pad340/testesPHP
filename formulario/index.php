<?php

use classes\Session;
use classes\User;

require __DIR__ . "/classes/User.php";

if (!(new Session())->has("userLogin")) {
    echo "<h1>Antes de tudo, faça login.</h1>";
    echo "<h2><a href='logar.php'>Login</a></h2>";
} else {
    echo "<h1>Olá {$_SESSION['userLogin']->userName}</h1>";
    include __DIR__ . "/forms/create.php";
    echo "<p><a href='forms/destroy.php'>Sair</a></p>";

    if ($user = (new User())->read()) {
        echo "<h1>Últimos registros:</h1>";
        foreach ($user as $users) {
            echo "<p><a href='editar.php?&id={$users['id']}'>Editar</a></p>";
            echo "<p><a href='deletar.php?&id={$users['id']}'>Deletar</a></p>";
            echo "<p>Nome: {$users['name']}</p>";
            echo "<p>Email: {$users['email']}</p>";
            echo "<p>Senha: {$users['password']}</p>";
            echo "<p>Telefone: {$users['number']}</p>";
            echo "<p>Data de nascimento: {$users['date_birth']}</p>";
            echo "<p>IP: {$users['ip']}</p>";
            echo "<p>Data de cadastro: {$users['date_register']}</p><br>";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = (new User())->boostrap()->register();
}