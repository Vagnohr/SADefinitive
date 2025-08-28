<?php
session_start();
require_once 'conexao.php';

// Verifica se remedio está logado
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

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Inicializa a variavel para evitar Erros
$remedios = [];

// Se o Formulário for Enviado, Busca o remedio pelo id ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM remedio WHERE id_remedio = :busca ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        // Busca apenas pelo PRIMEIRO nome
        $sql = "SELECT * FROM remedio 
                WHERE SUBSTRING_INDEX(nome, ' ', 1) LIKE :busca_nome 
                ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM remedio ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$remedios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar remedios</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_"," ",basename($arquivo,".php"))) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div style="position: relative; text-align: center; margin: 20px 0;">
        <h2 style="margin: 0;">Buscar Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="buscar_remedio.php" method="POST">
        <label for="busca">Digite o ID ou o Nome:</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>   

    <?php if (!empty($remedios)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Valor Unitario</th>
                <th>Ações</th>
            </tr>
        <?php foreach ($remedios as $remedio): ?>
            <tr>
                <td><?= htmlspecialchars($remedio['id_remedio]']) ?></td>
                <td><?= htmlspecialchars($remedio['nome_prod']) ?></td>
                <td><?= htmlspecialchars($remedio['descricao']) ?></td>
                <td><?= htmlspecialchars($remedio['qtde']) ?></td>
                <td><?= htmlspecialchars($remedio['valor_unit']) ?></td>
                <td>
                    <a href="alterar_remedio.php?id=<?= htmlspecialchars($remedio['id_remedio']) ?>">Alterar remédio</a>
                    <a href="excluir_remedio.php?id=<?= htmlspecialchars($remedio['id_remedio']) ?>" onclick="return confirm('Tem Certeza que deseja Excluir esse remédio?')">Excluir reméio</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum remédio Encontrado.</p>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])): ?>
        <!-- Se buscou alguém, botão volta para mostrar a tabela completa -->
        <a href="buscar_remedio.php">Voltar</a>
    <?php else: ?>
        <!-- Se não buscou nada, volta para a tela principal -->
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>