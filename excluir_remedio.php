<?php
session_start();
require_once 'conexao.php';

// Verifica se usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtendo o Nome do Perfil do Usuario Logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// Definição das Permissões por Perfil

$opcoes_menu = $permissoes[$id_perfil];

// Verifica permissão
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 3) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Lista todos os remedios
$sql = "SELECT * FROM remedio ORDER BY nome_prod ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$remedios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se receber id para excluir
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM remedio WHERE id_remedio = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('remedio excluído com sucesso!');window.location.href='excluir_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir remedio.');window.location.href='excluir_remedio.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Excluir remedio</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <!-- MENU -->
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li><a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_"," ",basename($arquivo,".php"))) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div style="position: relative; text-align: center; margin: 20px 0;">
        <h2 style="margin: 0;">Excluir remedios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 0%; transform: translateY(-70%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
    <?php if (!empty($remedios)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Valor Unitário</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($remedios as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id_remedio']) ?></td>
                    <td><?= htmlspecialchars($p['nome_prod']) ?></td>
                    <td><?= htmlspecialchars($p['descricao']) ?></td>
                    <td><?= htmlspecialchars($p['qtde']) ?></td>
                    <td><?= htmlspecialchars($p['valor_unit']) ?></td>
                    <td>
                        <a href="excluir_remedio.php?id=<?= $p['id_remedio'] ?>" onclick="return confirm('Tem certeza que deseja excluir este remedio?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum remedio encontrado.</p>
    <?php endif; ?>
    <a href="principal.php">Voltar para o Menu</a>
</body>
</html>