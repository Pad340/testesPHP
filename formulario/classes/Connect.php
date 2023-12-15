<?php

namespace classes;

use PDO;
use PDOException;

class Connect
{
    /** @var PDO  */
    private static PDO $instance;

    /** @const array */
    private const OPTIONS = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ];

    /**
     * @return PDO|null
     */
    public static function getInstance(): ?PDO
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost;dbname=formulario",
                    "root",
                    "",
                    self::OPTIONS
                );
            } catch (PDOException $exception) {
                error_log("Erro de conexão: " . $exception->getMessage());
            }
        }
        return self::$instance;
    }
}