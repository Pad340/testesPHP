<?php
session_start();

session_destroy();

header("Location: /testesPHP/formulario/logar.php");