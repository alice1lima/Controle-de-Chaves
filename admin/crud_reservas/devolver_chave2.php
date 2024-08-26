<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php");
    exit();
}

$id_usuario_sessao = $_SESSION['id_usuario'];

if (isset($_GET['id_reserva'])) {
    $id_reserva = intval($_GET['id_reserva']);

    // Verifica se a reserva pertence ao usuário logado
    $sql_check = "SELECT id_usuario FROM tb_reservas WHERE id_reserva = $id_reserva";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows == 1) {
        $row = $result_check->fetch_assoc();
        if ($row['id_usuario'] == $id_usuario_sessao) {
            // Atualiza o status da reserva para indicar que a chave foi devolvida
            $sql_update = "UPDATE tb_reservas SET devolver_chave = 1 WHERE id_reserva = $id_reserva";
            if ($conn->query($sql_update)) {
                echo "<script>alert('Chave devolvida com sucesso');</script>";
            } else {
                echo "Erro ao devolver chave: " . $conn->error;
            }
        } else {
            echo "Não autorizado a devolver a chave.";
        }
    } else {
        echo "Reserva não encontrada.";
    }

    header("Location: ../index_admin.php");
    exit();
} else {
    echo "ID da reserva não encontrado na URL.";
    exit();
}


?>
