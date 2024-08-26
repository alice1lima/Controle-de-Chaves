<?php
session_start(); // Iniciar sessão

// Configurações de exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php"); // Redireciona para a página de login
    exit();
}

include ($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

// Definir o número de itens por página
$itens_por_pagina = 6;

// Obter o número da página atual
$pagina_atual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

// Calcular o offset para a consulta SQL
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Obter o número total de itens
$sql_total = "SELECT COUNT(*) as total FROM tb_salas";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_itens = $row_total['total'];

// Calcular o número total de páginas
$total_paginas = ceil($total_itens / $itens_por_pagina);

// Obter os itens para a página atual
$sql = "SELECT id_sala, sala, bloco FROM tb_salas LIMIT $offset, $itens_por_pagina";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chau+Philomene+One:ital@0;1&display=swap" rel="stylesheet">
    <title>Visualizar Salas</title>
</head>

<body>
    <div class="header-container">
        <div class="title-container">
            <h1 class="titulo1"> Controle</h1>
            <h3 class="subtitulo"><span class="rotated-letter"> <b>de</b></span></h3>
            <h1 class="titulo2">Chaves</h1>
        </div>
        <button class="popup-btn" onclick="document.getElementById('popup').style.display='block'"><i
                class="bi bi-person-circle"></i></button>
    </div>

    <div id="popup" class="popup-container">
        <div class="popup-content">
            <span class="close-btn" onclick="document.getElementById('popup').style.display='none'">&times;</span>
            <p>Olá, <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>!</p>
            <a href="../../logout.php">Sair</a>
        </div>
    </div>
    <div class="nav-links">
        <a href="../index_admin.php">Inicio</a>
        <a href="../crud_salas/cadastrar_sala.php"> Cadastrar salas</a>
        <a href="visualizar_sala.php">Visualizar Salas</a>
    </div>

    <nav class="nav-links">
        <input type="text" id="searchInput" placeholder="Digite para pesquisar..." onkeyup="filterTable()">
    </nav>

    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1' id='userTable'>
            <tr>
                <th style='background-color: white;'>Sala</th>
                <th style='background-color: white;'>Bloco</th>
                <th style='background-color: white;'>Ações</th>
            </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . $row["sala"] . "</td>
                <td>" . $row["bloco"] . "</td>
                <td>
                    <a href='atualizar_sala.php?id_sala=" . $row["id_sala"] . "'><i class='bi bi-pencil-square' style='font-size: 25px;'></i></a> 
                </td>
            </tr>";
        }
        echo "</table>";

        // Adicionar navegação de paginação centralizada
        echo "<nav aria-label='Page navigation example'>
            <ul class='pagination justify-content-center'>";
        for ($i = 1; $i <= $total_paginas; $i++) {
            $active = $i == $pagina_atual ? 'active' : '';
            echo "<li class='page-item $active'><a class='page-link' href='visualizar_sala.php?pagina=$i'>$i</a></li>";
        }
        echo "  </ul>
          </nav>";
    } else {
        echo "Nenhuma sala encontrada.";
    }

    $conn->close();
    ?>

    <footer>
        <div class="container2">
            <div class="row">
                <div class="col-md-6">
                    <img src="../logo-senai.png" alt=" ">
                </div>
                <div class="col-md-6 text-right">
                    <ul class="social-media">
                        <li><a href="#"><i class="bi bi-facebook'></i></a></li>
                    <li><a href=" #"><i class='bi bi-instagram'></i></a></li>
                        <li><a href="#"><i class='bi bi-twitter'></i></a></li>
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
        text-align: center;
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

    .titulo1,
    .subtitulo,
    .titulo2 {
        margin: 0;
        padding: 0;
    }

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

    .subtitulo {
        margin-bottom: 0.5em;
    }


    table {
        width: 60%;
        margin-left: auto;
        margin-right: auto;
        border-collapse: collapse;
        border-bottom: 1px solid #EC2B57;
    }

    table,
    th,
    td {
        border-bottom: 1px solid #EC2B57;
        border-left: none;
        border-right: none;
        background-color: #ffffff;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
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
        margin-top: auto;
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
        color: #EC2B57;
        font-weight: bold;
    }

    .nav-links a:hover {
        color: #FF85A0;
    }

    .exemplo {
        color: #245397;
        padding-bottom: 20px;
    }

    h4 {
        margin-left: -150px;
        color: #245397;
    }

    button {
        padding: 10px 20px;
        border: none;
        border-radius: 20px;
        background-color: #EFECEC;
        color: #245397;
        cursor: pointer;
        box-shadow: 0 4px 6px #245397;
    }

    @media screen and (max-width: 600px) {



        .col-md-6 {
            flex: 1 1 100%;
            text-align: center;
        }

        .footer-links {
            justify-content: center;
            margin-top: 10px;
            margin-left: 0;
            /* Reset margin-left */
        }


    }

    @media screen and (max-width: 768px) {


        table,
        th,
        td {
            font-size: 0.7em;
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
    window.onclick = function (event) {
        var popup = document.getElementById('popup');
        var btn = document.querySelector('.popup-btn');
        // Verifica se o clique foi fora do popup e do botão
        if (event.target !== popup && event.target !== btn && !popup.contains(event.target)) {
            popup.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
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


<script>
    /*Filtro de pesquisa */
    function filterTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("userTable");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            tr[i].style.display = "none";
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    }
                }
            }
        }
    }
</script>