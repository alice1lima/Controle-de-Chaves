<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir configuração do banco de dados
    include './banco/conexao.php';

    // Obter CPF e nova senha do formulário
    $cpf = $_POST['cpf'];
    $senha1 = $_POST['senha1'];
    $senha2 = $_POST['senha2'];

    // Verificar se as senhas são iguais
    if ($senha1 == $senha2) {
        // Hash da nova senha
        $nova_senha_hash = password_hash($senha1, PASSWORD_DEFAULT);

        // Atualizar a senha no banco de dados
        $sql = "UPDATE tb_usuarios SET senha = ? WHERE cpf = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Erro na preparação da declaração: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("ss", $nova_senha_hash, $cpf);
        if ($stmt->execute()) {
            echo "Senha atualizada com sucesso.";
            header("Location: index.php");
            exit(); // Importante sair do script após redirecionar
        } else {
            echo "Erro ao atualizar a senha no banco de dados: " . htmlspecialchars($stmt->error);
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "As senhas não coincidem. Por favor, insira senhas iguais nos dois campos.";
        header("Location: cpf.php");
    }

    // Fechar a conexão
    $conn->close();
} else {
    // Obter CPF da query string
    if (isset($_GET['cpf'])) {
        $cpf = $_GET['cpf'];
    } else {
        die("CPF não fornecido.");
    }
}
?>





<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">
    <title>Atualizar Senha</title>
</head>
<body>

<div class="header-container">
        <div class="title-container">
            <h1 class="titulo1">Controle</h1>
            <h3 class="subtitulo"><span class="rotated-letter">de</span></h3>
            <h1 class="titulo2">Chaves</h1>
        </div>
    </div>

    <h5>CPF encontrado!</h5>


<div class="form-container">
    <form action="" method="post">
        <h3 class="exemplo"> <b>Criar Senha</b></h3>
        <input type="hidden" name="cpf" value="<?php echo htmlspecialchars($cpf); ?>">
        <h4>Nova Senha:</h4>   
        <input type="password" id="senha1" name="senha1" required maxlength="8" placeholder="Senha de 8 digitos...">
        <h4>Confirmar Senha: </h4> 
        <input type="password" id="senha2" name="senha2" required maxlength="8" placeholder="Digite novamente...">
        <button type="submit" onclick="verificarSenhas()">Atualizar Senha</button>
        <a href="index.php">Cancelar</a>
    </form>
    </div>

    <footer>
        <div class="container2">
            <div class="row">
                <div class="col-md-6">
                    <img src="img/logo-senai.png" alt=" ">
                </div>
                <div class="col-md-6 text-right">
                    <ul class="social-media">
                        <li><a href="#"><i class="bi bi-facebook"></i></a></li>
                        <li><a href="#"><i class="bi bi-instagram"></i></a></li>
                        <li><a href="#"><i class="bi bi-twitter"></i></a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="copyright">
                        <p>&copy; 2024 SENAI - Todos os direitos reservados</p>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <p class="footer-links">
                        <a href="#">Termos de uso</a> | <a href="#">Política de privacidade</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
<!----------------------------------------CSS de criar senha----------------------------------------------->

<style>
     body {
        margin: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    background-color: #EFECEC;  
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh; 
        }
        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            background-color: #FFFFFF;
        }
 
        .title-container {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #FFFFFF;
            padding: 20px;
            width: 100%;
            text-align: center; /* Centraliza o texto dentro dos elementos */
            font-family: "Chau Philomene One", sans-serif;
            font-weight: 400;
            font-style: normal;
        }
        .titulo1{
            color: #EC2B57;
            margin: 0;
            font-size: 2em;
        }
         .titulo2 {
            color: #245397;
            margin: 0;
            font-size: 2em;
        }

        .subtitulo {
            color: #000000;
            margin: 0;
            font-size: 1.5em;
        }

        .rotated-letter {
            display: inline-block;
            transform: rotate(-90deg);
            transform-origin: center;
        }
        /* Final da logo */

        /*estilo login */
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }

        form {
            background: #fff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        input[type="password"] {
            width: 100%; /* Aumenta a largura dos campos de entrada */
            max-width: 400px; /* Define uma largura máxima para os campos de entrada */
            padding: 20px;
            margin: 10px 0;
            border: 1px solid #245397;
            border-radius: 20px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        button, .styled-link {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            background-color: #EFECEC;
            color: #245397;
            cursor: pointer;
            box-shadow: 0 4px 6px #245397; /* Sombra apenas na parte inferior */
            text-decoration: none; /* Remove a decoração do link */
            margin-right: 10px;
        }

        button:hover, .styled-link:hover {
            background-color: #dcdcdc;
        }

       

        h4{
          margin-left: -150px;
          color: #245397;
        }

        a {
            display: block;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .chau-philomene-one-regular {
  font-family: "Chau Philomene One", sans-serif;
  font-weight: 400;
  font-style: normal;
}

.chau-philomene-one-regular-italic {
  font-family: "Chau Philomene One", sans-serif;
  font-weight: 400;
  font-style: italic;
}

.exemplo {
    color: #245397;
    padding-bottom: 20px;
}

/*css rodapé*/
footer {
    background-color: #245397;
    color: white;
    padding: 10px 0;
    width: 100%;
    margin-top: 30px;
}

.container2 {
    width: 90%;
    margin: 0 auto;
}

.row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.col-md-6 {
    flex-basis: 50%;
}

img {
    width: 150px;
    height: auto;
}

.social-media {
    list-style: none;
    margin: 0;
    padding: 0;
    text-align: right;
}

.social-media li {
    display: inline-block;
    margin-left: 10px;
}

.social-media a {
    color: #ffffff;
    font-size: 20px;
}

.copyright {
    font-size: 14px;
}

hr {
    margin: 10px 0;
    border: 1px solid #e0e0e0;
}

.footer-links {
    display: inline-block;
    margin-left: 320px;
}

.footer-links a {
    color: white;
    margin-left: 10px;
}

.footer-links a:hover {
    color: black;
}

h4{
    margin-left: -150px;
    color: #245397;
}

@media (max-width: 768px) {
            .col-md-6 {
                flex: 1 1 100%;
                text-align: center;
            }
            .footer-links {
                justify-content: center;
                margin-top: 10px;
                margin-left: 0;
            }
            h4 {
                margin-left: 0;
            }
        }
        @media (max-width: 400px) {
            .titulo1, .titulo2 {
                font-size: 1.5em;
            }
            .subtitulo {
                font-size: 1.2em;
            }
            input[type="password"] {
                max-width: 100%;
            }
        }

        h5{
            text-align: center;
        }
  
</style>


<!-----------------------------------Script da senha ----------------------------------->

<script>
function verificarSenhas() {
    var senha1 = document.getElementById("senha1").value;
    var senha2 = document.getElementById("senha2").value;

    if (senha1 == senha2) {
       
    } else {
        alert("As senhas são diferentes. Por favor, insira senhas iguais nos dois campos.");
    }
}
</script>



