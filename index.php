<?php
session_start();
include('./banco/conexao.php'); // Inclua seu arquivo de conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];

    // Preparar a consulta SQL para verificar se o usuário existe e buscar a senha
    $query = "SELECT id_usuario, nome_usuario, eh_admin, eh_ativo, senha FROM tb_usuarios WHERE cpf = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt === false) {
        die('Erro na preparação da consulta SQL: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $cpf);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_usuario, $nome_usuario, $eh_admin, $eh_ativo, $senha_armazenada);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Verificar se o usuário existe, está ativo e a senha está correta
    if (isset($id_usuario) && $eh_ativo == 1) {
        // Verificar se a senha armazenada é criptografada
        if (password_verify($senha, $senha_armazenada)) {
            // A senha fornecida corresponde à senha hash armazenada
            loginUser($id_usuario, $nome_usuario, $eh_admin);
        } elseif ($senha === $senha_armazenada) {
            // A senha fornecida corresponde à senha em texto simples armazenada
            loginUser($id_usuario, $nome_usuario, $eh_admin);
        } else {
            echo "<script>alert('Senha incorreta!');</script>";
        }
    } else {
        echo "<script>alert('CPF não encontrado ou usuário inativo!');</script>";
    }
}

function loginUser($id_usuario, $nome_usuario, $eh_admin) {
    // Armazenar o id_usuario e nome_usuario na sessão
    $_SESSION['id_usuario'] = $id_usuario;
    $_SESSION['nome_usuario'] = $nome_usuario;

    // Verificar se é o administrador
    if ($eh_admin == 1) {
        header("Location: ./admin/index_admin.php"); // Redireciona para a página de administração
    } else {
        header("Location: ./usuario/index_usuario.php"); // Redireciona para a página de usuário
    }
    exit();
}
?>






<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">

<title>Login</title>
</head>
<body>

<div class="header-container">
    <div class="title-container">
        <h1 class="titulo1"><b>Controle</b></h1>
        <h3 class="subtitulo"><span class="rotated-letter"><b>de</b></span></h3>
        <h1 class="titulo2"><b>Chaves</b></h1>
    </div>
</div>

<div class="form-container">
    <form method="post" action="">
        <h3 class="exemplo"><b>Login</b></h3>
        <h4>Usuário:</h4> 
        <input type="text" name="cpf" required placeholder="Digite seu CPF..." maxlength="14" oninput="mascaraCPF(this)"><br>
        <h4>Senha:</h4>
        <div class="password-container">
            <input type="password" id="senha" name="senha" required placeholder="Digite sua senha..." maxlength="8">
            <i class="bi bi-eye" id="toggleSenha" onclick="togglePassword()"></i>
        </div><br>
        <button type="submit">Login</button>
        <a class="senha" href="cpf.php">Redefinir senha?</a>
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
                        <li><a href="https://www.facebook.com/SenaiRN/"><i class="bi bi-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/senairn/"><i class="bi bi-instagram"></i></a></li>
                        <li><a href="https://twitter.com/senairn"><i class="bi bi-twitter"></i></a></li>
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

<script>
function mascaraCPF(input) {
    let value = input.value;
    value = value.replace(/\D/g, ""); // Remove tudo o que não é dígito
    value = value.replace(/(\d{3})(\d)/, "$1.$2"); // Coloca um ponto entre o terceiro e o quarto dígitos
    value = value.replace(/(\d{3})(\d)/, "$1.$2"); // Coloca um ponto entre o sexto e o sétimo dígitos
    value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2"); // Coloca um hífen entre o nono e o décimo dígitos
    input.value = value;
}

function togglePassword() {
    var passwordField = document.getElementById("senha");
    var toggleIcon = document.getElementById("toggleSenha");
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("bi-eye");
        toggleIcon.classList.add("bi-eye-slash");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("bi-eye-slash");
        toggleIcon.classList.add("bi-eye");
    }
}

</script>

<style>
body {
    margin: 0;
    font-family: 'Chau', 'Philomene', sans-serif;
    background-color: #EFECEC;   
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  
    min-height: 100vh; /* Ajustado para min-height para garantir que o conteúdo ocupe toda a altura da tela */
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

/*estilo login */
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-grow: 1;
    padding-top: 10px;
}

form {
    background: #fff;
    padding: 50px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

input[type="text"], input[type="password"] {
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

.senha {
    display: block;
    margin-top: 10px;
    color: #007BFF;
    text-decoration: none;
}

.senha:hover {
    text-decoration: underline;
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

/* Media Queries */
@media (max-width: 768px) {
    .col-md-6 {
        flex: 1 1 100%;
        text-align: center;
    }

    .footer-links {
        justify-content: center;
        margin-top: 10px;
        margin-left: 0; /* Reset margin-left */
    }

    h4 {
        margin-left: 0; /* Reset margin for mobile */
    }
}

@media (max-width: 400px) {
    .titulo1, .titulo2 {
        font-size: 1.5em;
    }

    .subtitulo {
        font-size: 1.2em;
    }

    input[type="text"], input[type="password"] {
        max-width: 100%;
    }
}

.password-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-container input[type="password"],
.password-container input[type="text"] {
    width: 100%;
    padding-right: 30px;
}

.password-container i {
    position: absolute;
    right: 10px;
    cursor: pointer;
}

</style>

</body>
</html>
