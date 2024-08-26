<?php
session_start(); // Iniciar sessão

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

// Verificar se o usuário está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php"); // Redireciona para a página de login
    exit();
}

$id_usuario_sessao = $_SESSION['id_usuario']; // Obtém o ID do usuário da sessão

// Verifica se o ID da reserva foi passado na URL
if (isset($_GET["id_reserva"])) {
    $id_reserva = intval($_GET["id_reserva"]); // Assegura que o ID é um número inteiro

    // Exclui a reserva do banco de dados se pertence ao usuário logado
    $sql = "DELETE FROM tb_reservas WHERE id_reserva = $id_reserva AND id_usuario = $id_usuario_sessao";
    $resultado = mysqli_query($conn, $sql);

    if ($resultado && mysqli_affected_rows($conn) > 0) {
        echo "Reserva excluída com sucesso!";
    } else {
        echo "Erro ao excluir a reserva ou você não tem permissão para excluí-la.";
    }
} else {
    echo "ID da reserva não encontrado na URL.";
}
header("Location: visualizar_reservas.php");
exit();

?>
