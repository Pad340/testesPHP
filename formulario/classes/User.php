<?php

namespace classes;

use PDO;
use PDOException;

require __DIR__ . "/Connect.php";

class User
{
    public function __construct()
    {
    }

    public function boostrap(): User
    {
        $this->name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $filteredEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($filteredEmail !== false) {
            $this->email = $filteredEmail;
        } else {
            echo "<p>E-mail inválido.</p>";
            die();
        }
        $this->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $this->dateBirth = $_POST['dateBirth'];
        $this->number = filter_var($_POST['number']);

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        }

        return $this;
    }

    public function register(): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("INSERT INTO user (name, email, password, date_birth, number, ip)
                      VALUES (:name, :email, :password, :date_birth, :number, :ip)");

            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':date_birth', $this->dateBirth);
            $stmt->bindParam(':number', $this->number);
            $stmt->bindParam(':ip', $this->ip);

            $stmt->execute();

            return true;
        } catch (PDOException $exception) {
            error_log("Erro ao salvar no banco de dados: " . $exception->getMessage());
            return false;
        }
    }

    public function read(string $columns = "*"): ?array
    {
        try {
            $stmt = Connect::getInstance()->prepare("SELECT {$columns} FROM user");
            $stmt->execute();

            // Obtém e retorna o primeiro resultado como um objeto User ou null se não houver resultado
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retorna o $user ou null
            return $user !== false ? $user : null;

        } catch (PDOException $exception) {
            // Logar ou tratar o erro de maneira apropriada
            error_log("Erro na obtenção de dados do banco de dados: " . $exception->getMessage());
            return null;
        }
    }

    public function listarId(string $columns = "*"): ?array
    {
        try {
            $id = $_GET["id"];
            $stmt = Connect::getInstance()->prepare("SELECT {$columns} FROM user WHERE id = {$id}");
            $stmt->execute();

            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $user !== false ? $user : null;

        } catch (PDOException $exception) {
            // Logar ou tratar o erro de maneira apropriada
            error_log("Erro na obtenção de dados do banco de dados: " . $exception->getMessage());
            return null;
        }
    }

    public function listarEmail(string $email): ?array
    {
        try {
            $stmt = Connect::getInstance()->prepare("SELECT * FROM user WHERE email = '{$email}'");
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
            $id = $_GET["id"];
            $stmt = Connect::getInstance()->prepare("UPDATE user SET name = :name,
                email = :email, password = :password, date_birth = :date_birth,  number = :number WHERE id = {$id}");

            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':date_birth', $this->dateBirth);
            $stmt->bindParam(':number', $this->number);

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

    public function login(string $email, string $password)
    {
        if ($user = (new User())->listarEmail($email)) { // verificar se o email está cadastrado
            if ($this->contagemTentativa($user[0]['id']) >= 10) {
                echo "<p>Você atingiu o limite de 10 tentativas. Tente novamente em 2 horas.</p>";
                sleep(60 * 60 * 2);
            }
            if (password_verify($password, $user[0]['password'])) { // verificar se a senha digitada é correta para aquele email
                echo "<p>Login efetuado</p>";
            } else {
                echo "<p>Senha incorreta!</p>";
                $this->tentativaSenha($user[0]['id']);
            }

        } else {
            echo "<p>Email não cadastrado!</p>";
        }
    }

    public function tentativaSenha($idUser): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("INSERT INTO senhainvalida (id_user) VALUES($idUser)");
            $stmt->execute();

            return true;
        } catch (PDOException $exception) {
            var_dump($exception);
            return false;
        }
    }

    public function contagemTentativa($idUser): ?int
    {
        try {
            $stmt = Connect::getInstance()->prepare("SELECT * FROM senhainvalida WHERE id_user = $idUser");
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $exception) {
            var_dump($exception);
            return null;
        }
    }
}