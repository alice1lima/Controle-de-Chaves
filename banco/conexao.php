
<?php
$hostname = 'localhost';
$usuario = 'root';
$senha = '1234';
$database = 'db_salas';

//conectar ao MYSQL
$conn = mysqli_connect($hostname, $usuario, $senha, $database);

// verificar a conexão

if(!$conn){
    die("Erro na conexão: ". mysqli_connect_error());
} else{
    echo " ";
}

//definir o conjunto de caracteres para uft8(opcional)

mysqli_set_charset($conn,"utf8");


?>