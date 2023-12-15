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

    (new User())->update($_GET['id'], $name, $email, $password, $dateBirth, $number);
}

if ($user = (new User())->listar()[0]) {
    echo "<p>Dados salvos com sucesso!</p><br>";

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
