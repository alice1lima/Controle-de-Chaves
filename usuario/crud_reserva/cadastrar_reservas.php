<?php
session_start(); // Iniciar sessão

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_sala = $_POST['id_sala'];
    // $id_usuario = $_SESSION['id_usuario']; // Remover essa linha para permitir que qualquer usuário cadastre
    $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null; // Alteração sugerida
    $entrada_previsao = $_POST['entrada_previsao'];
    $saida_previsao = $_POST['saida_previsao'];

    // Verificar se a entrada é anterior à saída
    if ($entrada_previsao >= $saida_previsao) {
        echo "<script>alert('A data de entrada deve ser anterior à data de saída.');</script>";
    } else {
        // Verificar se há conflitos com outras reservas ou ocupações
        $sql_verificar_conflito = "
            SELECT 'reserva' as tipo, entrada_previsao as inicio, saida_previsao as fim FROM tb_reservas 
            WHERE id_sala = '$id_sala'
            AND eh_aprovada = 1
            AND (
                (entrada_previsao <= '$entrada_previsao' AND saida_previsao > '$entrada_previsao') OR 
                (entrada_previsao < '$saida_previsao' AND saida_previsao >= '$saida_previsao') OR
                ('$entrada_previsao' <= entrada_previsao AND '$saida_previsao' > entrada_previsao)
            )
            UNION
            SELECT 'ocupacao' as tipo, dh_entrada as inicio, dh_saida as fim FROM tb_ocupacoes 
            WHERE id_sala = '$id_sala'
            AND (
                (dh_entrada <= '$entrada_previsao' AND dh_saida > '$entrada_previsao') OR 
                (dh_entrada < '$saida_previsao' AND dh_saida >= '$saida_previsao') OR
                ('$entrada_previsao' <= dh_entrada AND '$saida_previsao' > dh_entrada)
            )
        ";
        $result_verificar_conflito = $conn->query($sql_verificar_conflito);

        if ($result_verificar_conflito->num_rows > 0) {
            echo "<script>alert('Já existe uma reserva ou ocupação para esta sala no período selecionado.');</script>";
        } else {
            // Inserir a nova reserva no banco de dados
            $sql_insert = "INSERT INTO tb_reservas (id_sala, id_usuario, entrada_previsao, saida_previsao) 
                           VALUES ('$id_sala', '$id_usuario', '$entrada_previsao', '$saida_previsao')";

            if ($conn->query($sql_insert)) {
                echo "<script>alert('Reserva cadastrada com sucesso');</script>";
            } else {
                echo "Erro ao cadastrar reserva: " . $conn->error;
            }
        }
    }
}

// Recuperar dados das salas
$sql_salas = "SELECT id_sala, sala, bloco FROM tb_salas";
$result_salas = $conn->query($sql_salas);
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
    <title>Cadastrar Reserva</title>
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
    <a href="../index_usuario.php" >Inicio</a>
    <a href="../crud_reserva/cadastrar_reservas.php">Cadastrar Reservas</a>
    <a href="visualizar_reservas.php">Visualizar Reservas</b></a>


</div>

<div class="form-container">

    <form action="" method="post">


        <!-- Campo oculto para id_usuario -->
        <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>">

        <h4>Sala: </h4>
            <select name="id_sala" required>
                <option value="">Selecione uma sala</option>
                <?php
                if ($result_salas->num_rows > 0) {
                    while ($row = $result_salas->fetch_assoc()) {
                        echo "<option value='" . $row['id_sala'] . "'>" . $row['sala'] . " - " . $row['bloco'] . "</option>";
                    }
                }
                ?>
            </select>
        
        <h4>Entrada Previsão: </h4>
            <input type="datetime-local" name="entrada_previsao" required>
        
        <h4>Saída Previsão:  </h4>
            <input type="datetime-local" name="saida_previsao" required>
       
      
        <p><input type="submit" value="Cadastrar" class="button"></p>

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
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        input[type="datetime-local"] {
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
        .button-row {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.button {
    display: inline-block;
    padding: 10px 30px;
    margin: 10px;
    text-decoration: none;
    color: #245397;
    background-color: white;
    border: none;
    border-radius: 25px;
    box-shadow: 0 4px 6px #245397;
    cursor: pointer;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    font-size: 1.2em;
}

.button:hover {
    background-color: #dcdcdc;
    box-shadow: 0 6px 8px #245397;
}

.button:active {
    background-color: #cfcfcf;
    box-shadow: 0 3px 4px #245397;
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

          // Função para alternar a visibilidade do popup
          function togglePopup() {
            var popup = document.getElementById('popup');
            var overlay = document.getElementById('popupOverlay');
            if (popup.style.display === 'block') {
                popup.style.display = 'none';
                overlay.style.display = 'none';
            } else {
                popup.style.display = 'block';
                overlay.style.display = 'block';
            }
        }

        // Adiciona um event listener ao ícone dentro do botão
        document.querySelector('.popup-btn i').addEventListener('click', function(event) {
            event.stopPropagation(); // Evita que o clique no ícone também clique no botão
            togglePopup();
        });
</script>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php"); // Redireciona para a página de login
    exit();
}

// Verifique se a variável de sessão 'nome_usuario' está definida
$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Usuário não identificado';


?>
