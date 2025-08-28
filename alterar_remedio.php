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

$opcoes_menu = $permissoes[$id_perfil];

// Inicializa a variável
$remedio = null;

// Se o Formulário for enviado, busca o remedio pelo id ou pelo nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['busca_remedio'])) {
        $busca = trim($_POST['busca_remedio']);

        if (is_numeric($busca)) {
            $sql = "SELECT * FROM remedio WHERE id_remedio = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM remedio WHERE nome LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $remedio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$remedio) {
            echo "<script>alert('remedio não encontrado!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar remedios</title>
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
        <h2 style="margin: 0;">Alterar remedios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="alterar_remedio.php" method="POST">
        <label for="busca_remedio">Digite o ID ou Nome do remedio:</label>
        <input type="text" id="busca_remedio" name="busca_remedio" required>
        <button type="submit">Buscar</button>
    </form>
    <?php if ($remedio): ?>
        <form action="processa_alteracao_remedio.php" method="POST">
            <input type="hidden" name="id_remedio" value="<?= htmlspecialchars($remedio['id_remedio']) ?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome_prod" name="nome_prod" value="<?= htmlspecialchars($remedio['nome_prod']) ?>" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"><?= htmlspecialchars($remedio['descricao']) ?></textarea>

            <label for="qtde">Quantidade:</label>
            <input type="number" id="qtde" name="qtde" value="<?= htmlspecialchars($remedio['qtde']) ?>" required>

            <label for="valor_unit">Valor Unitário:</label>
            <input type="number" step="0.01" id="valor_unit" name="valor_unit" value="<?= htmlspecialchars($remedio['valor_unit']) ?>" required>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca_remedio'])): ?>
        <a href="alterar_remedio.php">Voltar</a>
    <?php else: ?>
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>