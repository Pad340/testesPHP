<?php

namespace classes;

use PDO;
use PDOException;

require __DIR__ . "/Session.php";
require __DIR__ . "/Connect.php";

class User
{
    public function __construct()
    {
    }

    public function boostrap(): User
    {
        if ($this->validaNome(filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS))) {
            $this->name = $_POST['name'];
        } else {
            die();
        }
        $filteredEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($filteredEmail !== false) {
            if (!$this->listarEmail($filteredEmail)) {
                $this->email = $filteredEmail;
            } else {
                echo "<p>Este email já está cadastrado.</p>";
                die();
            }
        } else {
            echo "<p>Email inválido.</p>";
            die();
        }
        if ($this->validaSenha($_POST['password'])) {
            $this->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        } else {
            die();
        }
        $this->dateBirth = filter_var($_POST['dateBirth']);
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

            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':password', $_POST['password']);
            $stmt->bindParam(':date_birth', $_POST['dateBirth']);
            $stmt->bindParam(':number', $_POST['number']);

            $stmt->execute();

            return true;
        } catch (PDOException $exception) {
            error_log("Erro ao salvar no banco de dados: " . $exception->getMessage());
            return false;
        }
    }

    public function deleteUser(): bool
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

    public function login(string $email, string $password): void
    {
        if ($user = (new User())->listarEmail($email)) { // verificar se o email está cadastrado
            if (password_verify($password, $user[0]['password']) && $this->contagemTentativa($user[0]['id']) <= 9) { // verificar se a senha digitada é correta para aquele email
                echo "<p>Login efetuado</p>";
                (new Session())->set("userLogin", ["userId" => $user[0]['id'], "userName" => $user[0]['name']]);
                var_dump($_SESSION);
            } else if ($this->contagemTentativa($user[0]['id']) > 9) {
                echo "<p>Você atingiu o limite de 10 tentativas. Tente novamente em 2 horas.</p>";
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

    public function contagemTentativa(string $idUser): ?int
    {
        try {
            $stmt = Connect::getInstance()->prepare("SELECT * FROM senhainvalida WHERE id_user = {$idUser} AND horario >= NOW() - INTERVAL 26 HOUR");
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $exception) {
            var_dump($exception);
            return null;
        }
    }

    public function validaSenha($password): bool
    {
        // tamanho
        if (mb_strlen($password) >= 8 && mb_strlen($password) <= 16) {
            // tem numero, letra M e m, e catactere?
            if (!(is_numeric(filter_var($password, FILTER_SANITIZE_NUMBER_INT)) // true se num
                && preg_match('/[A-Z]/', $password) // true se M
                && preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/', $password) // true se caracter
                && preg_match('/[a-z]/', $password))) { // true se m
                echo "<p>A senha deve conter pelo menos uma letra maiuscula, uma minuscula, um número e um caractere especial.</p>";
                return false;
            }
        } else {
            echo "<p>A senha deve conter de 8 à 16 caracteres.</p>";
            return false;
        }
        return true;
    }

    public function validaNome($name): bool
    {
        if (is_numeric(filter_var($name, FILTER_SANITIZE_NUMBER_INT))
            || preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/', $name)) {
            echo "<p>O nome não deve conter números, nem catacteres especiais.</p>";
            return false;
        }
        return true;
    }
}