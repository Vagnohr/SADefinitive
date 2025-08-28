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

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nome_prod = trim($_POST['nome_prod']);
    $descricao = trim($_POST['descricao']);
    $qtde = $_POST['qtde'];
    $valor_unit = $_POST['valor_unit'];

    $errors = [];

    if (empty($nome_prod)) {
        $errors[] = "O nome do produto é obrigatório!";
    }
    
    // Verificação: nome do produto não pode conter números ou caracteres especiais
    if (!preg_match("/^[A-Za-zÀ-ÿ\s]+$/", $nome_prod)) {
        $errors[] = "O nome do produto não pode conter números ou caracteres especiais!";
    }

    if (!is_numeric($qtde) || $qtde < 0) {
        $errors[] = "Quantidade inválida!";
    }

    if (!is_numeric($valor_unit) || $valor_unit < 0) {
        $errors[] = "Valor unitário inválido!";
    }

    if (count($errors) > 0) {
        echo "<script>alert('" . implode("\\n", $errors) . "');history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO produto (nome_prod, descricao, qtde, valor_unit) VALUES (:nome_prod, :descricao, :qtde, :valor_unit)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_prod', $nome_prod);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':qtde', $qtde);
    $stmt->bindParam(':valor_unit', $valor_unit);

    if ($stmt->execute()) {
        echo "<script>alert('Produto cadastrado com sucesso!');window.location.href='cadastro_produto.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar produto!');history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <!-- Menu Dropdown -->
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
        <h2 style="margin: 0;">Cadastro de Produtos:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="cadastro_produto.php" method="POST" id="formCadastro">
        <label for="nome_prod">Nome do Produto:</label>
        <input type="text" id="nome_prod" name="nome_prod" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao"></textarea>

        <label for="qtde">Quantidade:</label>
        <input type="number" id="qtde" name="qtde" min="0" required>

        <label for="valor_unit">Valor Unitário:</label>
        <input type="number" step="0.01" id="valor_unit" name="valor_unit" min="0" required>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>
    <a href="principal.php">Voltar Para o Menu</a>
</body>
</html>