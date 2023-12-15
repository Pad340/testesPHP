<?php

use classes\User;

require __DIR__ . "/classes/User.php";

if ($user = (new User())->delete()) {
    echo "<p>Dados deletados com sucesso!</p><br>";
}

echo "<p><a href='./'/>Voltar</p>";
