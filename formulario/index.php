<?php

use classes\User;

require __DIR__ . "/classes/User.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $filteredEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($filteredEmail !== false) {
        $email = $filteredEmail;
    } else {
        echo "<p>E-mail inválido.</p>";
        die();
    }
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dateBirth = $_POST['dateBirth'];
    $number = filter_var($_POST['number']);

    (new User())->register(
        $name,
        $email,
        $password,
        $dateBirth,
        $number
    );

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

}

include __DIR__ . "/forms/create.php";
