<?php
session_start();

session_unset();

session_destroy();

header("Location: /../testesPHP/formulario/logar.php");

die();