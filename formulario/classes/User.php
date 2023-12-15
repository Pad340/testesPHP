<?php

namespace classes;
use PDO;
use PDOException;

require __DIR__ . "/Connect.php";

class User
{
    /** @var User */
    private $user;

    public function __construct()
    {

    }

    public function boostrap(): User
    {
        $this->user->name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filteredEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($filteredEmail !== false) {
            $this->user->email = $filteredEmail;
        } else {
            echo "<p>E-mail inválido.</p>";
            die();
        }
        $this->user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $this->user->dateBirth = $_POST['dateBirth'];
        $this->user->number = filter_var($_POST['number']);

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->user->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->user->ip = $_SERVER['REMOTE_ADDR'];
        }

        return $this;
    }

    public function register(): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("INSERT INTO user (name, email, password, date_birth, number, ip)
                      VALUES (:name, :email, :password, :date_birth, :number, :ip)");

            $stmt->bindParam(':name', $this->user->name);
            $stmt->bindParam(':email', $this->user->email);
            $stmt->bindParam(':password', $this->user->password);
            $stmt->bindParam(':date_birth', $this->user->dateBirth);
            $stmt->bindParam(':number', $this->user->number);
            $stmt->bindParam(':ip', $this->user->ip);

            $stmt->execute();

            return true;
        } catch (PDOException $exception) {
            error_log("Erro ao salvar no banco de dados: " . $exception->getMessage());
            return false;
        }
    }

    public function read(): ?array
    {
        try {
            $stmt = Connect::getInstance()->prepare("SELECT * FROM user");
            $stmt->execute();

            // Obtém e retorna o primeiro resultado como um objeto User ou null se não houver resultado
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retorna o objeto User ou null
            return $user !== false ? $user : null;

        } catch (PDOException $exception) {
            // Logar ou tratar o erro de maneira apropriada
            error_log("Erro na obtenção de dados do banco de dados: " . $exception->getMessage());
            return null;
        }
    }

    public function listar(): ?array
    {
        try {
            $id = $_GET["id"];
            $stmt = Connect::getInstance()->prepare("SELECT * FROM user WHERE id = {$id}");
            $stmt->execute();

            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $user !== false ? $user : null;

        } catch (PDOException $exception) {
            // Logar ou tratar o erro de maneira apropriada
            error_log("Erro na obtenção de dados do banco de dados: " . $exception->getMessage());
            return null;
        }
    }

    public function update(): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("UPDATE user SET name = :name,
                email = :email, password = :password, date_birth = :date_birth,  number = :number WHERE id = {$id}");

            $stmt->bindParam(':name', $this->user->name);
            $stmt->bindParam(':email', $this->user->email);
            $stmt->bindParam(':password', $this->user->password);
            $stmt->bindParam(':date_birth', $this->user->dateBirth);
            $stmt->bindParam(':number', $this->user->number);

            $stmt->execute();

            return true;
        } catch (PDOException $exception) {
            error_log("Erro ao salvar no banco de dados: " . $exception->getMessage());
            return false;
        }
    }

    public function delete(): bool
    {
        try {
            $id = $_GET["id"];
            $stmt = Connect::getInstance()->prepare("DELETE FROM user WHERE id = {$id}");

            $stmt->execute();

            return true;
        } catch (PDOException $exception) {
            error_log("Erro ao salvar no banco de dados: " . $exception->getMessage());
            return false;
        }
    }
}