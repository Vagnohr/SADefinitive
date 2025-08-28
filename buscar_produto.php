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

// Definição das Permissões
$permissoes = [
    1=>["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
        "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],

    2=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php", "alterar_fornecedor.php"]],

    3=>["Cadastrar"=>["cadastro_fornecedor.php", "cadastro_produto.php"],
        "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir"=>["excluir_produto.php"]],

    4=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php"]],
];
$opcoes_menu = $permissoes[$id_perfil];

// Inicializa variavel
$produtos = [];

// Se buscou algo
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM produto WHERE id_produto = :busca ORDER BY nome_prod ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM produto 
                WHERE nome_prod LIKE :busca_nome 
                ORDER BY nome_prod ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM produto ORDER BY nome_prod ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busca de Produtos</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <!-- Menu -->
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
        <h2 style="margin: 0;">Buscar Produtos:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="buscar_produto.php" method="POST">
        <label for="busca">Digite o ID ou Nome:</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>
    <?php if (!empty($produtos)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Valor Unitário</th>
                <th>Ações</th>
            </tr>
        <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id_produto']) ?></td>
                <td><?= htmlspecialchars($p['nome_prod']) ?></td>
                <td><?= htmlspecialchars($p['descricao']) ?></td>
                <td><?= htmlspecialchars($p['qtde']) ?></td>
                <td><?= htmlspecialchars($p['valor_unit']) ?></td>
                <td>
                    <a href="alterar_produto.php?id=<?= $p['id_produto'] ?>">Alterar Produto</a>
                    <a href="excluir_produto.php?id=<?= $p['id_produto'] ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir Produto</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum Produto Encontrado.</p>
    <?php endif; ?>
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])): ?>
        <a href="buscar_produto.php">Voltar</a>
    <?php else: ?>
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>