<?php
session_start();
require_once 'conexao.php';
require_once 'funcoes_email.php'; // Arquivo com as funções que geram a senha e simulam email

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuario WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Gera uma senha temporária e aleatória
        $senha_temporaria = gerarSenhaTemporaria();
        $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

        // Atualiza a senha do usuario no banco
        $sql = "UPDATE usuario SET senha = :senha, senha_temporaria = TRUE WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Simula o envio do email (Grava em txt)
        simularEnvioEmail($email, $senha_temporaria);
        echo "<script>alert('Uma Senha temporária foi gerada e eviada (Simulação). Verifique o arquivo emails_simulados.txt');window.location.href='index.php';</script>";    
    } else {
        echo "<script>alert('E-mail não encontrado');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar a Senha</title>
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <h2>Recuperar Senha:</h2>
    <form action = "recuperar_senha.php" method="POST">
        <label for="email">Digite o seu E-mail Cadastrado:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit">Enviar a Senha Temporária</button>

        <a href="index.php">Voltar para o Login</a>
    </form>
</body>
</html>