<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuario tem permissão de ADM ou Almoxarife
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 3) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
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
$permissoes = [
    1=>["Cadastrar"=>["cadastro_usuario.php","cadastro_perfil.php","cadastro_cliente.php","cadastro_fornecedor.php","cadastro_produto.php","cadastro_funcionario.php"],
        "Buscar"=>["buscar_usuario.php","buscar_perfil.php","buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php","buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php","alterar_perfil.php","alterar_cliente.php","alterar_fornecedor.php","alterar_produto.php","alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php","excluir_perfil.php","excluir_cliente.php","excluir_fornecedor.php","excluir_produto.php","excluir_funcionario.php"]],
    2=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php","alterar_fornecedor.php"]],
    3=>["Cadastrar"=>["cadastro_fornecedor.php","cadastro_produto.php"],
        "Buscar"=>["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"=>["alterar_fornecedor.php","alterar_produto.php"],
        "Excluir"=>["excluir_produto.php"]],
    4=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_produto.php"],
        "Alterar"=>["alterar_cliente.php"]],
];

$opcoes_menu = $permissoes[$id_perfil];

// Inicializa a variável
$produto = null;

// Se o Formulário for enviado, busca o produto pelo id ou pelo nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['busca_produto'])) {
        $busca = trim($_POST['busca_produto']);

        if (is_numeric($busca)) {
            $sql = "SELECT * FROM produto WHERE id_produto = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM produto WHERE nome_prod LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            echo "<script>alert('Produto não encontrado!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Produtos</title>
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
        <h2 style="margin: 0;">Alterar Produtos:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="alterar_produto.php" method="POST">
        <label for="busca_produto">Digite o ID ou Nome do Produto:</label>
        <input type="text" id="busca_produto" name="busca_produto" required>
        <button type="submit">Buscar</button>
    </form>
    <?php if ($produto): ?>
        <form action="processa_alteracao_produto.php" method="POST">
            <input type="hidden" name="id_produto" value="<?= htmlspecialchars($produto['id_produto']) ?>">

            <label for="nome_prod">Nome:</label>
            <input type="text" id="nome_prod" name="nome_prod" value="<?= htmlspecialchars($produto['nome_prod']) ?>" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"><?= htmlspecialchars($produto['descricao']) ?></textarea>

            <label for="qtde">Quantidade:</label>
            <input type="number" id="qtde" name="qtde" value="<?= htmlspecialchars($produto['qtde']) ?>" required>

            <label for="valor_unit">Valor Unitário:</label>
            <input type="number" step="0.01" id="valor_unit" name="valor_unit" value="<?= htmlspecialchars($produto['valor_unit']) ?>" required>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca_produto'])): ?>
        <a href="alterar_produto.php">Voltar</a>
    <?php else: ?>
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>