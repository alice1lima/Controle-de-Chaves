<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./admin/index.php");
    exit();
}

$id_usuario_sessao = $_SESSION['id_usuario'];

if (isset($_GET['id_ocupacao'])) {
    $id_ocupacao = intval($_GET['id_ocupacao']);

    // Verifica se a ocupação pertence ao usuário logado
    $sql_check = "SELECT id_usuario FROM tb_ocupacoes WHERE id_ocupacao = $id_ocupacao";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows == 1) {
        $row = $result_check->fetch_assoc();
        if ($row['id_usuario'] == $id_usuario_sessao) {
            $sql_update = "UPDATE tb_ocupacoes 
                           SET dh_saida = CURRENT_TIMESTAMP, devolver_chave = 1 
                           WHERE id_ocupacao = $id_ocupacao";

            if ($conn->query($sql_update)) {
                echo "<script>alert('Chave devolvida com sucesso');</script>";
                header("Location: ../index_usuario.php");
            } else {
                echo "Erro ao devolver chave: " . $conn->error;
            }
        } else {
            echo "Não autorizado a devolver a chave.";
        }
    } else {
        echo "Ocupação não encontrada.";
    }

    exit();
} else {
    echo "ID da ocupação não encontrado na URL.";
    exit();
 


//$conn->close();


}
?>
