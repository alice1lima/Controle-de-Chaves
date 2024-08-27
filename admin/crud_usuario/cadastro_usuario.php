<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

// Obter cargos para preencher o SELECT
$sql_cargos = "SELECT id_cargo, cargo FROM tb_cargos";
$result_cargos = $conn->query($sql_cargos);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se todos os campos obrigatórios estão preenchidos
    if (empty($_POST['cpf']) || empty($_POST['nome_usuario']) || empty($_POST['senha']) || empty($_POST['id_cargo']) || empty($_POST['telefone'])) {
        echo "<script>alert('Favor preencher todos os campos obrigatórios.');</script>";
    } else {
        $cpf = $conn->real_escape_string($_POST['cpf']);
        $nome_usuario = $conn->real_escape_string($_POST['nome_usuario']);
        $senha = $conn->real_escape_string($_POST['senha']);
        $telefone = $conn->real_escape_string($_POST['telefone']);
        $id_cargo = intval($_POST['id_cargo']); // Assegura que o ID é um número inteiro
        $eh_admin = isset($_POST['eh_admin']) ? 1 : 0;
        $eh_ativo = isset($_POST['eh_ativo']) ? 1 : 0;

        // Verificar se o CPF e o telefone têm exatamente 11 dígitos
        if (strlen(preg_replace('/\D/', '', $cpf)) !== 11 || strlen(preg_replace('/\D/', '', $telefone)) !== 11) {
            echo "<script>alert('CPF e telefone devem ter exatamente 11 dígitos.');</script>";
        } else if (!preg_match('/^[a-zA-Z0-9]+$/', $senha)) {
            // Verificar se a senha contém apenas letras e números
            echo "<script>alert('A senha deve conter apenas letras e números, sem espaços ou caracteres especiais.');</script>";
        } else {
            // Verificar se o CPF já está em uso
            $sql_check_cpf = "SELECT * FROM tb_usuarios WHERE cpf = '$cpf'";
            $result_check_cpf = $conn->query($sql_check_cpf);

            if ($result_check_cpf->num_rows > 0) {
                echo "<script>alert('Este CPF já está cadastrado.');</script>";
            } else {
                // Insira os dados do usuário no banco de dados
                $sql_insert = "INSERT INTO tb_usuarios (cpf, nome_usuario, senha, telefone, id_cargo, eh_admin, eh_ativo) VALUES ('$cpf', '$nome_usuario', '$senha','$telefone', '$id_cargo', '$eh_admin', '$eh_ativo')";

                if ($conn->query($sql_insert)) {
                    echo "<script>alert('Cadastro realizado com sucesso');</script>";
                } else {
                    echo "Erro ao cadastrar usuário: " . $conn->error;
                }
            }
        }
    }
}
?>

<?php
session_start(); // Iniciar sessão

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php"); // Redireciona para a página de login
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">
    <title>Cadastro de Usuário</title>

</head>
<body>
<div class="header-container">
       

       <div class="title-container">
           
           <h1 class="titulo1"> Controle</h1>
           <h3 class="subtitulo"><span class="rotated-letter"> <b>de</b></span></h3>
           <h1 class="titulo2">Chaves</h1>
           
       </div>

       <button class="popup-btn" onclick="document.getElementById('popup').style.display='block'"><i class="bi bi-person-circle"></i></button>
   </div>
   
   <div id="popup" class="popup-container">
    <div class="popup-content">
        <span class="close-btn" onclick="document.getElementById('popup').style.display='none'">&times;</span>
            <p>Olá, <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>!</p>  
            <a href="../../logout.php">Sair</a>
    </div>
    </div>
    <div class="nav-links">
    <a href="../index_admin.php" >Inicio</a>
 
    <a href="../crud_usuario/cadastro_usuario.php">Cadastrar Usuarios</a>
    <a href="visualizar_usuario.php">Visualizar Usuário</b></a>


</div>
<div class="form-container">
    <form action="" method="post" onsubmit="return validateForm()">
    <h3 class="exemplo">Cadastro de Usuário</h3>

        <h4>CPF:</h4>
            <input type="text" name="cpf" id="cpf" required oninput="maskCPF(this)" placeholder="Digite o CPF...">
        
        <h4>Nome do Usuário:</h4> 
            <input type="text" name="nome_usuario" required placeholder="Digite o nome e sobrenome...">

        <h4>Telefone:</h4> 
            <input type="text" name="telefone" id="telefone" required oninput="maskTelefone(this)" placeholder="(xx) x xxxx-xxxx">
        
        <h4>Senha:</h4>
            <input type="password" name="senha" required maxlength="16" placeholder="Digite a Senha">
        
        <h4>Cargo:</h4> 
            <select name="id_cargo" required>
                <option value="">Selecione um cargo</option>
                <?php
                if ($result_cargos->num_rows > 0) {
                    while ($linha_cargo = $result_cargos->fetch_assoc()) {
                        echo "<option value='" . $linha_cargo['id_cargo'] . "'>" . $linha_cargo['cargo'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Nenhum cargo encontrado</option>";
                }
                ?>
            </select>
        
        <h4>Administrador:</h4>
          <p><input type="checkbox" name="eh_admin" id="checkbox_admin" value="1"> Administrador</p>
       
        <h4>Ativo:</h4>
          <p>  <input type="checkbox" name="eh_ativo" id="checkbox_ativo" value="1"> Ativo </p>
        
            <button type="submit">Cadastrar</button>
        

    </form>
    </div>

    <footer>
        <div class="container2">
            <div class="row">
                <div class="col-md-6">
                    <img src="../logo-senai.png" alt=" ">
                </div>
                <div class="col-md-6 text-right">
                    <ul class="social-media">
                    <li><a href="https://www.facebook.com/SenaiRN/
"><i class="bi bi-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/senairn/
"><i class="bi bi-instagram"></i></a></li>
                        <li><a href="https://twitter.com/senairn
"><i class="bi bi-twitter"></i></a></li>
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

.logo {
    display: flex;
    justify-content: center;
    align-items: center;
}

.logo img {
    width: 150px;
    height: auto;
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

.titulo1 {
    color: #EC2B57;
    margin: 0 10px;
    font-size: 2em;
    
}

.subtitulo {
    color: #000000;
    margin: 0 10px;
    font-size: 1.5em;
}

.titulo2 {
    color: #245397;
    margin: 0 10px;
    font-size: 2em;
}

.rotated-letter {
    display: inline-block;
    transform: rotate(-90deg);
    transform-origin: center;
}

/* CSS para colar os elementos h1 e h3 */
.titulo1, .subtitulo, .titulo2 {
    margin: 0;
    padding: 0;
}

/* Opcional: Estilizando os títulos para visualização */
.titulo1 {
    font-size: 36px;
    font-weight: bold;
}

.subtitulo {
    font-size: 22px;
    font-weight: normal;
    padding-top: 10px;
}

.titulo2 {
    font-size: 36px;
    font-weight: bold;
}

/* Adiciona um pequeno espaçamento entre o h3 e o segundo h1 se necessário */
.subtitulo {
    margin-bottom: 0.5em; /* Ajuste conforme necessário */
}

.form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding-top: 10px;
        }

        form {
            background: #fff;
            padding: 70px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            align-items: center;
        }

        input[type="text"], input[type="password"] {
            width: 100%; /* Aumenta a largura dos campos de entrada */
            max-width: 400px; /* Define uma largura máxima para os campos de entrada */
            padding: 20px;
            margin: 10px 0;
            border: 1px solid #245397;
            border-radius: 20px;
            
        }
        select {
            width: 100%; /* Aumenta a largura dos campos de entrada */
            max-width: 400px; /* Define uma largura máxima para os campos de entrada */
            padding: 20px;
            margin: 10px 0;
            border: 1px solid #245397;
            border-radius: 20px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            background-color: #EFECEC;
            color: #245397;
            cursor: pointer;
            box-shadow: 0 4px 6px #245397; /* Sombra apenas na parte inferior */
        }

        /* Estilo para o botão */
.popup-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 10px 20px;
    background-color:#EFECEC;
    color: #245397;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    font-size: 20px;
}

/* Estilo para o popup container */
.popup-container {
    display: none; /* inicialmente oculto */
    position: fixed;
    top: 60px; /* um pouco abaixo do topo para não cobrir o botão */
    right: 20px; /* distância da direita */
    width: 300px; /* largura do popup */
    background-color: #EFECEC;
    border: 1px solid #ccc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    padding: 15px;
    border-radius: 5px;
}

/* Estilo para o conteúdo do popup */
.popup-content {
    text-align: left; /* alinhamento do texto à esquerda */
}

/* Estilo para o botão de fechar */
.close-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 18px;
    cursor: pointer;
}

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

 /* Estilo para os links */
 .nav-links {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            gap: 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: black;
            transition: color 0.3s ease;
        }

        .nav-links a.active {
            color: #EC2B57; /* Cor mais forte para o link ativo */
            font-weight: bold; /* Negrito no link ativo */
        }

        .nav-links a:hover {
            color: #FF85A0; /* Cor ao passar o mouse por cima dos links */
        }

        .exemplo {
    color: #245397;
    padding-bottom: 20px;
}

h4{
text-align: left;          
color: #245397;
        }

        @media screen and (max-width: 600px) {
 

  
 .col-md-6 {
     flex: 1 1 100%;
     text-align: center;
 }

 .footer-links {
     justify-content: center;
     margin-top: 10px;
     margin-left: 0; /* Reset margin-left */
 }


}

</style>


<script>
        function maskCPF(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            input.value = value;
        }

        function maskTelefone(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{1})(\d{4})(\d{4})$/, '$1 $2-$3');
            input.value = value;
        }

        function validateForm() {
            const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
            const telefone = document.getElementById('telefone').value.replace(/\D/g, '');

            if (cpf.length !== 11) {
                alert('O CPF deve ter exatamente 11 dígitos.');
                return false;
            }
            if (telefone.length !== 11) {
                alert('O telefone deve ter exatamente 11 dígitos.');
                return false;
            }
            return true;
        }
    </script>

<script>
          // Função para alternar a visibilidade do popup
function togglePopup() {
    var popup = document.getElementById('popup');
    // Alterna o display do popup
    if (popup.style.display === 'block') {
        popup.style.display = 'none';
    } else {
        popup.style.display = 'block';
    }
}

// Adiciona um evento para fechar o popup quando clicar fora dele
window.onclick = function(event) {
    var popup = document.getElementById('popup');
    var btn = document.querySelector('.popup-btn');
    // Verifica se o clique foi fora do popup e do botão
    if (event.target !== popup && event.target !== btn && !popup.contains(event.target)) {
        popup.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", function() {
            const links = document.querySelectorAll(".nav-links a");
            const currentPath = window.location.pathname;

            links.forEach(link => {
                const linkPath = new URL(link.href).pathname;
                if (currentPath === linkPath) {
                    link.classList.add("active");
                } else {
                    link.classList.remove("active");
                }
            });
        });
</script>