<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir configuração do banco de dados
    include './banco/conexao.php';

    // Obter CPF do formulário e remover a máscara
    $cpf = $_POST['cpf'];

    // Remover caracteres especiais do CPF

    // Verificar se o CPF tem 11 dígitos
    if (strlen($cpf) != 14) {
        die("CPF inválido.");
    }

    // Preparar e executar consulta SQL
    $sql = "SELECT * FROM tb_usuarios WHERE cpf = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Erro na preparação da declaração: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar resultado
    if ($result->num_rows > 0) {
        // Redirecionar para a página de atualização de senha
        header("Location: atualizar_senha1.php?cpf=" . urlencode($cpf));
        exit();
    } else {
        echo "Usuário não encontrado.";
    }

    // Fechar conexão e declaração
    $stmt->close();
    $conn->close();
}
?>
