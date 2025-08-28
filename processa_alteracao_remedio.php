<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuario tem permissão de ADM ou Almoxarife
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 3) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_remedio = $_POST['id_remedio'];
    $nome_prod = $_POST['nome_prod'];
    $descricao = $_POST['descricao'];
    $qtde = $_POST['qtde'];
    $valor_unit = $_POST['valor_unit'];

    $sql = "UPDATE remedio SET nome_prod=:nome_prod, descricao=:descricao, qtde=:qtde, valor_unit=:valor_unit 
            WHERE id_remedio=:id_remedio";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_prod', $nome_prod);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':qtde', $qtde);
    $stmt->bindParam(':valor_unit', $valor_unit);
    $stmt->bindParam(':id_remedio', $id_remedio, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('remedio atualizado com sucesso!');window.location.href='buscar_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar remedio.');window.location.href='alterar_remedio.php?id=$id_remedio';</script>";
    }
}
?>