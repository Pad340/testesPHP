<?php

use classes\User;

require __DIR__ . "/classes/User.php";

include __DIR__ . "/forms/create.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = (new User())->boostrap()->register();
}

if ($user = (new User())->read()) {
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