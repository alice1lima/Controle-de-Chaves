<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./index.php");
    exit();
}

$id_usuario_sessao = $_SESSION['id_usuario'];
$tem_chave = false;
$tem_chave2 = false;
$cargo_usuario = '';
$nome_usuario = '';

// Verifique o cargo e nome do usuário
$sql_cargo_nome = "SELECT u.nome_usuario, c.cargo FROM tb_usuarios u JOIN tb_cargos c ON u.id_cargo = c.id_cargo WHERE u.id_usuario = $id_usuario_sessao";
$result_cargo_nome = $conn->query($sql_cargo_nome);

if ($result_cargo_nome->num_rows > 0) {
    $row_cargo_nome = $result_cargo_nome->fetch_assoc();
    $cargo_usuario = $row_cargo_nome['cargo'];
    $nome_usuario = $row_cargo_nome['nome_usuario'];

    if ($cargo_usuario == 'ASG') {
        // Usuário ASG pode pegar qualquer chave
        $sql_check_chave = "SELECT id_reserva FROM tb_reservas WHERE devolver_chave = 0";
        $result_check_chave = $conn->query($sql_check_chave);
        $tem_chave = $result_check_chave->num_rows > 0;

        $sql_check_chave2 = "SELECT id_ocupacao FROM tb_ocupacoes WHERE devolver_chave = 0";
        $result_check_chave2 = $conn->query($sql_check_chave2);
        $tem_chave2 = $result_check_chave2->num_rows > 0;
    } else {
        // Lógica para usuários normais
        $sql_check_chave = "SELECT id_reserva FROM tb_reservas WHERE id_usuario = $id_usuario_sessao AND devolver_chave = 0";
        $result_check_chave = $conn->query($sql_check_chave);
        $tem_chave = $result_check_chave->num_rows > 0;

        $sql_check_chave2 = "SELECT id_ocupacao FROM tb_ocupacoes WHERE id_usuario = $id_usuario_sessao AND devolver_chave = 0";
        $result_check_chave2 = $conn->query($sql_check_chave2);
        $tem_chave2 = $result_check_chave2->num_rows > 0;
    }
} else {
    echo "Cargo e nome do usuário não encontrados.";
    exit();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Chaves</title>
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">
    <script src="javascript/cadastro.js"></script>
</head>
<body>
    <div class="header-container">
        <div class="title-container">
            <h1 class="titulo1">Controle</h1>
            <h3 class="subtitulo"><span class="rotated-letter"><b>de</b></span></h3>
            <h1 class="titulo2">Chaves</h1>
        </div>
        <button class="popup-btn" onclick="togglePopup()"><i class="bi bi-person-circle" ></i></button>
    </div>

    <div id="popup" class="popup-container">
        <div class="popup-content">
            <span class="close-btn" onclick="togglePopup()">&times;</span>
            <p>Olá, <?php echo htmlspecialchars($nome_usuario); ?>!</p>
            <a href="../logout.php">Sair</a>
        </div>
    </div>

    <div class="container">
        <div class="button-row">
            <a href="crud_ocupacoes/cadastrar_ocupacao.php"><button class="button"> Pegar Chave</button></a>

            <div class="button-row">
    <?php if ($cargo_usuario != 'ASG'): ?>
        <a href="crud_reserva/cadastrar_reservas.php"><button class="button">Fazer Reserva</button></a>
    <?php endif; ?>
</div>

        </div>

        <div class="button-row">
        <button id="openModalBtn2" class="button">Salas em uso</button>
            <button id="openModalBtn" class="button">Visualizar Reservas</button>
        </div>
        
        <?php if ($tem_chave): ?>
            <div class="button-row">
                <a href="crud_reserva/devolver_chave2.php?id_reserva=<?php echo $result_check_chave->fetch_assoc()['id_reserva']; ?>"><button class="button">Devolver Chave</button></a>
            </div>
        <?php endif; ?>

        <?php if ($tem_chave2): ?>
            <div class="button-row">
                <a href="crud_ocupacoes/devolver_chave.php?id_ocupacao=<?php echo $result_check_chave2->fetch_assoc()['id_ocupacao']; ?>"><button class="button">Devolver Chave</button></a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Reservas -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('myModal')">&times;</span>
        <h2>Reservas da Semana</h2>
        <div id="dataAtual"></div>
        <nav class="centered-nav">
            <input type="text" id="searchInput" placeholder="Digite para pesquisar..." onkeyup="filterTable('userTable', 'searchInput')">
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

        $sql = "SELECT r.id_reserva, r.entrada_previsao, r.saida_previsao, r.eh_aprovada,r.devolver_chave, u.nome_usuario, u.telefone, s.sala, s.bloco 
                FROM tb_reservas r
                JOIN tb_usuarios u ON r.id_usuario = u.id_usuario
                JOIN tb_salas s ON r.id_sala = s.id_sala 
                WHERE (r.entrada_previsao BETWEEN '$start_of_week' AND '$end_of_week'
                OR r.saida_previsao BETWEEN '$start_of_week' AND '$end_of_week')";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table  id='userTable'>
                    <tr>
                        <th style='background-color: white;'> Usuários</th>
                        <th style='background-color: white;'> Telefone</th>
                        <th style='background-color: white;'>Sala</th>
                        <th style='background-color: white;'>Local</th>
                        <th style='background-color: white;'>Data e Hora - Entrada</th>
                        <th style='background-color: white;'>Data e Hora - Saída </th>
                        <th style='background-color: white;'>Chave Devolvida</th>

                    </tr>";
            while ($row = $result->fetch_assoc()) {
                $entrada_previsao = date("d/m/Y H:i:s", strtotime($row["entrada_previsao"]));
                $saida_previsao = date("d/m/Y H:i:s", strtotime($row["saida_previsao"]));

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
            echo "<tr><td colspan='5'>Nenhuma reserva encontrada.</td></tr>";
        }
        $conn->close();
        ?>
    </div>
</div>



       <!-- Modal Salas em Uso -->
       <div id="myModal2" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('myModal2')">&times;</span>
            <h2>Salas em Uso</h2>
            <nav class="centered-nav">
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
                <th style='background-color: white;'>Data e Hora - Entrada</th>
                <th style='background-color: white;'>Data e Hora - Saída</th>
                <th style='background-color: white;'>Chave Devolvida</th>

            </tr>";
    while ($row = $result->fetch_assoc()) {
        // Formatar as datas usando a classe DateTime
        $dh_entrada = new DateTime($row["dh_entrada"]);
        $dh_saida = new DateTime($row["dh_saida"]);

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
    echo "<tr><td colspan='6'>Nenhuma ocupação encontrada.</td></tr>";
}

$conn->close();
?>
        </div>
    </div>


  
    <footer>
        <div class="container2">
            <div class="row">
                <div class="col-md-6">
                    <img src="logo-senai.png" alt=" ">
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





 <script>
       // Script para abrir e fechar o modal principal
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("openModalBtn");
        var modal2 = document.getElementById("myModal2");
        var btn2 = document.getElementById("openModalBtn2");

        // Quando o usuário clica no botão, abre o modal
        btn.onclick = function() {
            modal.style.display = "block";
        }
        btn2.onclick = function() {
            modal2.style.display = "block";
        }
        
        // Função para fechar os modais
        function closeModal(modalId) {
            var modal = document.getElementById(modalId);
            modal.style.display = "none";
        }

        // Quando o usuário clica fora do modal, fecha o modal
        window.onclick = function(event) {
            var popup = document.getElementById('popup');
        var btn = document.querySelector('.popup-btn');
        if (event.target !== popup && event.target !== btn && !popup.contains(event.target)) {
            popup.style.display = 'none';
        }
        if (event.target == modal) {
                modal.style.display = "none";
        }
        if (event.target == modal2) {
                modal2.style.display = "none";
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

        // Fecha o modal principal
        function closeModal() {
            modal.style.display = "none"; 
            modal2.style.display = "none";
        };


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
</body>
</html>

<style>
    td{
       color: #245397;
    }
    th{
    color: #EC2B57;

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
        margin: 10px 0;
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

h2{
    text-align: center;
    color: #245397;
}

.centered-nav {
    display: flex;
    justify-content: center;
    margin-bottom: 20px ;

}

#searchInput2, #searchInput {
    width: 300px; /* Aumente o tamanho do input conforme necessário */
    padding: 10px;
    font-size: 16px;
    border: 1px solid black;
    border-radius: 4px;

}



</style>

<!--style do pagina completo -->
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

</style>