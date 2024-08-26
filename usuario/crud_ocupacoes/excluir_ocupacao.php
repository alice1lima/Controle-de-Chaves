<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

// Verifica se o ID da reserva foi passado na URL
if (isset($_GET["id_ocupacao"])) {
    $id_ocupacao = intval($_GET["id_ocupacao"]); // Assegura que o ID é um número inteiro

    // Consulta SQL para selecionar os dados da reserva com o ID fornecido
    $sql = "SELECT * FROM tb_ocupacoes WHERE id_ocupacao = $id_ocupacao";
    $resultado = mysqli_query($conn, $sql);

    // Verifica se a reserva foi encontrada
    if (mysqli_num_rows($resultado) == 1) {
        // Deleta a reserva do banco de dados
        $deleta = mysqli_query($conn, "DELETE FROM tb_ocupacoes WHERE id_ocupacao = $id_ocupacao");

        if ($deleta) {
            echo "Reserva deletada com sucesso!";
            header("Location: visualizar_ocupacao.php");
            exit();
        } else {
            echo "Erro ao deletar a ocupação: " . mysqli_error($conn);
        }
    } else {
        echo "ocupação não encontrada.";
    }
} else {
    echo "ID da reserva não encontrado na URL.";
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