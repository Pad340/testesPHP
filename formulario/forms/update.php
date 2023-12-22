<form name="post" method="post">
    <div>
        <label for="name">Nome:
            <input type="text" name="name" value="<?= $user["name"] ?>"/>
        </label>
        <br><br>
        <label for="email">E-mail:
            <input type="email" name="email" value="<?= $user["email"] ?>"/>
        </label>
        <br><br>
        <label for="password">Senha:
            <input type="password" name="password" value="<?= $user["password"] ?>"/>
        </label>
        <br><br>
        <label for="dateBirth">Data de nascimento:
            <input type="date" name="dateBirth" value="<?= $user["date_birth"] ?>"/>
        </label>
        <br><br>
        <label for="number">Número de telefone:
            <input type="tel" name="number" value="<?= $user["number"] ?>"/>
        </label>
        <br><br>
    </div>
    <input type="submit" name="enviar" value="Atualizar"/>
</form>