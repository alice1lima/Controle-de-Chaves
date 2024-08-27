<?php
session_start(); // Iniciar sessão

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./index.php"); // Redireciona para a página de login
    exit();
}

// Verifique se a variável de sessão 'nome_usuario' está definida
$nome_usuario = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Usuário não identificado';


include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');
$id_usuario_sessao = $_SESSION['id_usuario'];



$sql_check_chave = "SELECT id_reserva FROM tb_reservas WHERE id_usuario = $id_usuario_sessao AND devolver_chave = 0";
$result_check_chave = $conn->query($sql_check_chave);
$tem_chave = $result_check_chave->num_rows > 0;
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Chaves</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">
    
    <script url="javascript/cadastro.js"></script>

</head>
<body>
    <div class="header-container">
       

        <div class="title-container">
            
            <h1 class="titulo1"> Controle</h1>
            <h3 class="subtitulo"><span class="rotated-letter"> <b>de</b></span></h3>
            <h1 class="titulo2">Chaves</h1>
            
        </div>
      
        <button class="popup-btn" onclick="togglePopup()"><i class="bi bi-person-circle"></i></button>
    </div>



    <div id="popup" class="popup-container">
        <div class="popup-content">
            <span class="close-btn" onclick="togglePopup()">&times;</span>
            <p>Olá, <?php echo htmlspecialchars($nome_usuario); ?>!</p>
            <a href="../logout.php">Sair</a>
        </div>
    </div>
    </div>

    

    <div class="container">
    <div class="button-row">
        <a href="crud_usuario/cadastro_usuario.php" class="button">Cadastrar usuario</a>
        <a href="crud_salas/cadastrar_sala.php" class="button">Cadastrar salas</a>
        <a href="crud_cargo/cadastrar_cargo.php" class="button">Cadastrar cargo</a>
    </div>
    <div class="button-row">
        <a href="crud_reservas/cadastrar_reservas.php" class="button">Cadastrar reservas</a>
        <button id="openModalBtn" class="button">Visualizar Reservas</button>
        <button id="openModalBtn2" class="button">Salas em uso</button>

    </div>

    <?php if ($tem_chave): ?>
            <div class="button-row">
                <a href="crud_reservas/devolver_chave2.php?id_reserva=<?php echo $result_check_chave->fetch_assoc()['id_reserva']; ?>"><button class="button">Devolver Chave</button></a>
            </div>
        <?php endif; ?>
</div>


  <!-- Modal Reservas -->
  <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('myModal')">&times;</span>
        <h2>Reservas da Semana</h2>
        <nav>
            <input class="pesquisa" type="text" id="searchInput" placeholder="Digite para pesquisar..." onkeyup="filterTable('userTable', 'searchInput')">
        </nav>
        
        <?php
include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');


if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php");
    exit();
}

// Obtendo a data de início e fim da semana atual
$start_of_week = date('Y-m-d H:i:s', strtotime('monday this week'));
$end_of_week = date('Y-m-d H:i:s', strtotime('sunday this week 23:59:59'));

$sql = "SELECT r.id_reserva, r.entrada_previsao, r.saida_previsao, r.eh_aprovada, r.devolver_chave, u.nome_usuario, u.telefone, s.sala, s.bloco 
        FROM tb_reservas r
        JOIN tb_usuarios u ON r.id_usuario = u.id_usuario
        JOIN tb_salas s ON r.id_sala = s.id_sala 
        WHERE (r.entrada_previsao BETWEEN '$start_of_week' AND '$end_of_week'
        OR r.saida_previsao BETWEEN '$start_of_week' AND '$end_of_week')";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table  id='userTable'>
            <tr>
                <th style='background-color: white;'>Usuários</th>
                <th style='background-color: white;'>Telefone</th>
                <th style='background-color: white;'>Sala</th>
                <th style='background-color: white;'>Local</th>
                <th style='background-color: white;'>Entrada Previsão</th>
                <th style='background-color: white;'>Saída Previsão</th>
                <th style='background-color: white;'>Chave Devolvida</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $entrada_previsao = date("d/m/Y H:i:s", strtotime($row["entrada_previsao"]));
        $saida_previsao = date("d/m/Y H:i:s", strtotime($row["saida_previsao"]));

        // Verificar se a chave foi devolvida
        $chave_devolvida = $row["devolver_chave"] ? 'Sim' : 'Não';

        echo "<tr>
                <td>" . $row["nome_usuario"] . "</td>
                <td>" . $row["telefone"] . "</td>
                <td>" . $row["sala"] . "</td>
                <td>" . $row["bloco"] . "</td>
                <td>" . $entrada_previsao . "</td>
                <td>" . $saida_previsao . "</td>
                <td>" . $chave_devolvida . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<tr><td colspan='7'>Nenhuma reserva encontrada.</td></tr>";
}
$conn->close();
?>

    </div>
</div>



<!-- Modal Salas em Uso -->
<div id="myModal2" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('myModal2')">&times;</span>
            <h2>Ocupações da Semana</h2>
            <nav>
            <input type="text" id="searchInput2" placeholder="Digite para pesquisar..." onkeyup="filterTable('userTable2', 'searchInput2')">
        </nav>
           
        <?php
include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php");
    exit();
}

// Obtendo a data de início e fim da semana atual
$start_of_week = date('Y-m-d H:i:s', strtotime('monday this week'));
$end_of_week = date('Y-m-d H:i:s', strtotime('sunday this week 23:59:59'));

$sql = "SELECT 
            o.id_ocupacao, 
            o.dh_entrada, 
            o.dh_saida, 
            u.nome_usuario, 
            u.telefone, 
            s.sala, 
            s.bloco,
            o.devolver_chave
        FROM tb_ocupacoes o
        JOIN tb_usuarios u ON o.id_usuario = u.id_usuario
        JOIN tb_salas s ON o.id_sala = s.id_sala
        WHERE (o.dh_entrada BETWEEN '$start_of_week' AND '$end_of_week'
        OR o.dh_saida BETWEEN '$start_of_week' AND '$end_of_week')
        ORDER BY o.dh_entrada ASC"; // Ordenando por data de entrada ascendente

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table  id='userTable2'>
            <tr>
                <th style='background-color: white;'>Usuários</th>
                <th style='background-color: white;'>Telefone</th>
                <th style='background-color: white;'>Sala</th>
                <th style='background-color: white;'>Local</th>
                <th style='background-color: white;'>Data Entrada</th>
                <th style='background-color: white;'>Data Saída</th>
                <th style='background-color: white;'>Chave Devolvida</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        // Formatar as datas usando a classe DateTime
        $dh_entrada = new DateTime($row["dh_entrada"]);
        $dh_saida = new DateTime($row["dh_saida"]);

        // Verificar se a chave foi devolvida
        $chave_devolvida = $row["devolver_chave"] ? 'Sim' : 'Não';

        echo "<tr>
                <td>" . $row["nome_usuario"] . "</td>
                <td>" . $row["telefone"] . "</td>
                <td>" . $row["sala"] . "</td>
                <td>" . $row["bloco"] . "</td>
                <td>" . $dh_entrada->format('d/m/Y H:i:s') . "</td>
                <td>" . $dh_saida->format('d/m/Y H:i:s') . "</td>
                <td>" . $chave_devolvida . "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<tr><td colspan='7'>Nenhuma ocupação encontrada.</td></tr>";
}

$conn->close();
?>

        </div>
    </div>
<footer>
        <div class="container2">
            <div class="row">
                <div class="col-md-6">
                    <img src="./logo-senai.png" alt=" ">
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



.container {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.button-row {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.button {
    display: inline-block;
    padding: 30px 60px;
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

#openModalBtn {
    cursor: pointer;
}

.popup-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 10px 20px;
    background-color: #EFECEC;
    color: #245397;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    font-size: 20px;
}

.popup-container {
    display: none;
    position: fixed;
    top: 60px;
    right: 20px;
    width: 300px;
    background-color: #EFECEC;
    border: 1px solid #ccc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    padding: 15px;
    border-radius: 5px;
}

.popup-content {
    text-align: left;
}

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
          margin-left: -150px;
          color: #245397;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #EC2B57 ;
        }

        table, th, td {
            border-bottom: 1px solid #EC2B57 ;
            border-left: none;
            border-right: none;
            background-color: #ffffff;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

         /* Estilos responsivos */
@media screen and (max-width: 600px) {
    .header-container {
        flex-direction: column;
        align-items: center;
    }

    .button-row {
        flex-direction: column;
        align-items: center;
    }

    .button {
        margin: 3px 0;
        width: 100%;
        text-align: center;
    }
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



@media screen and (max-width: 768px) {
    .modal-content {
        width: 90%;
    }

    table, th, td {
        font-size: 14px;
        padding: 6px;
    }
}

@media screen and (max-width: 480px) {
    .modal-content {
        margin: 10% auto;
        padding: 15px;
    }

    th, td {
        font-size: 12px;
        padding: 4px;
    }

    .close {
        font-size: 24px;
    }
}
     
    </style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('modal')) {
        const modalId = urlParams.get('modal');
        document.getElementById(modalId).style.display = 'block';
    }
});

var modal = document.getElementById("myModal");
var btn = document.getElementById("openModalBtn");
var modal2 = document.getElementById("myModal2");
var btn2 = document.getElementById("openModalBtn2");

// Quando o usuário clica no botão, abre o modal
btn.onclick = function() {
    openModal('myModal');
}
btn2.onclick = function() {
    openModal('myModal2');
}

// Função para abrir o modal e atualizar a URL
function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
    const newUrl = new URL(window.location);
    newUrl.searchParams.set('modal', modalId);
    window.history.pushState(null, '', newUrl.toString());
}

// Função para fechar o modal e atualizar a URL
function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
    const newUrl = new URL(window.location);
    newUrl.searchParams.delete('modal');
    window.history.pushState(null, '', newUrl.toString());
}

// Quando o usuário clica fora do modal, fecha o modal
window.onclick = function(event) {
    var popup = document.getElementById('popup');
    var btn = document.querySelector('.popup-btn');
    if (event.target !== popup && event.target !== btn && !popup.contains(event.target)) {
        popup.style.display = 'none';
    }
    if (event.target == modal) {
        closeModal('myModal');
    }
    if (event.target == modal2) {
        closeModal('myModal2');
    }
}

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

/*Filtro de pesquisa */
function filterTable(tableId, inputId) {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById(inputId);
    filter = input.value.toUpperCase();
    table = document.getElementById(tableId);
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0]; // Ajuste o índice conforme necessário
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}
    </script>

<!--Modal-->
<style>
nav{
    display: flex;
            justify-content: center;
            align-items: center;
            height: 60px; /* altura do nav */
}
     
   
       
        /* Estilos para o modal */
        .modal {
            display: none; /* Ocultar o modal por padrão */
            position: fixed; /* Ficar fixo na tela */
            z-index: 1; /* Ficar acima de outros elementos */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Permitir rolagem se necessário */
            background-color: rgb(0,0,0); /* Fallback para cores mais antigas */
            background-color: rgba(0,0,0,0.4); /* Cor de fundo com opacidade */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% a partir do topo e centralizado */
            padding: 20px;
            width: 80%; /* Largura do modal */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #EC2B57 ;

        }

        table, th, td {
            border-bottom: 1px solid #EC2B57 ;
            border-left: none;
            border-right: none;
            background-color: #ffffff;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }
        td{
            color: #245397;
        }

        th {
            background-color: #f2f2f2;
            color: #EC2B57;
        }

        .search-bar {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        h2{
            text-align: center;
            color: #245397;
        }
   
       
</style>