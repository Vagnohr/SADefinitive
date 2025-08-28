<?php
session_start();
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido, e define variaveis de sessão
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['id_perfil'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        
        // Verifica se a senha e Temporária
        if ($usuario['senha_temporaria']) {
            // Redireciona para a troca da senha Temporária
            header("Location: alterar_senha.php");
            exit();
        }else{
            // Redireciona para a página Principal
            header("Location: principal.php");
            exit();
        }
    }else{
        // Login invalido
        echo "<script>alert('E-mail ou Senha incorretos!');window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <h2>Login:</h2>
    <form action="index.php" method="POST">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
    </form>

    <p><a href="recuperar_senha.php">Esqueci minha Senha</a></p>
</body>
</html>