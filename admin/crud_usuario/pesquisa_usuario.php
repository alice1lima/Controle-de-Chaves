<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
// Verifica se foi enviado um termo de pesquisa
if(isset($_GET['termo_pesquisa'])) {
    // Conexão com o banco de dados (supondo que você já tenha uma conexão)
    include($_SERVER['DOCUMENT_ROOT'] . '/controle_chave/banco/conexao.php');

    // Limpa e prepara o termo de pesquisa
    $termo_pesquisa = mysqli_real_escape_string($conn, $_GET['termo_pesquisa']);

    // Query para selecionar os resultados correspondentes ao termo de pesquisa
    $sql = "  SELECT  cpf, nome_usuario, eh_admin, eh_ativo
        FROM tb_usuarios
        WHERE cpf LIKE '%$termo_pesquisa%' 
           OR nome_usuario LIKE '%$termo_pesquisa%'";

  

    // Executa a consulta
    $resultado = mysqli_query($conn, $sql);

    if (!$resultado) {
        echo "Erro na consulta SQL: " . mysqli_error($conn);
        exit;
    }
    
    // Restante do código para processar os resultados da consulta...
    

    // Verifica se há resultados
    if(mysqli_num_rows($resultado) > 0) {
        // Exibe os resultados
        while($linha = mysqli_fetch_assoc($resultado)) {
            // Aqui você exibe os resultados conforme necessário

         // Dentro do loop while para exibir os resultados
echo "<div class='resultado-pesquisa'>";
echo "<h2>Resultado da Pesquisa</h2>";
echo "<p><span class='campo cpf'>CPF:</span> " . $linha['cpf'] . "</p>";
echo "<p><span class='campo nome>Nome:</span> " . $linha['nome_usuario'] . "</p>";
echo "<p><span class='campo admin'>admin:</span> " . $linha['eh_admin'] . "</p>";
echo "<p><span class='campo ativo'>ativo:</span> " . $linha['eh_ativo'] . "</p>";

echo "</div>";

        }
    } else {
        echo "Nenhum resultado encontrado.";
    }

    // Fecha a conexão
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>
    /* Estilos para o resultado da pesquisa */


</style>

</body>
</html>
