<form name="post" action="./" method="post">
    <p style="margin-bottom: 10px"><a href="./" title="Atualizar">Atualizar</a></p>
    <div>
        <label for="name">Nome:
            <input type="text" name="name" placeholder="Nome:" required/>
        </label>
        <br><br>
        <label for="email">E-mail:
            <input type="email" name="email" placeholder="E-mail:" required/>
        </label>
        <br><br>
        <label for="password">Senha:
            <input type="password" name="password" placeholder="Senha:" required/>
        </label>
        <br><br>
        <label for="dateBirth">Data de nascimento:
            <input type="date" name="dateBirth" placeholder="Data de nascimento:" required/>
        </label>
        <br><br>
        <label for="number">Número de telefone:
            <input type="tel" name="number" placeholder="Número de telefone:" required/>
        </label>
    </div>
    <input type="submit" name="enviar" value="Cadastrar"/>
</form>