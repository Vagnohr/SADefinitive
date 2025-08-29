<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuario tem permissão de ADM ou Secretária
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Obtendo o Nome do usuario do Usuario Logado
$id_usuario = $_SESSION['usuario'];
$sqlusuario = "SELECT nome FROM usuario WHERE id_usuario = :id_usuario";
$stmtusuario = $pdo->prepare($sqlusuario);
$stmtusuario->bindParam(':id_usuario', $id_usuario);
$stmtusuario->execute();
$usuario = $stmtusuario->fetch(PDO::FETCH_ASSOC);
$nome_usuario = $usuario['nome'];

// Definição das Permissões por usuario
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

// Obtendo as Opções Disponíveis para o usuario Logado
$opcoes_menu = $permissoes[$id_usuario];

// Inicializa a variável
$remedio = null;

// Se o Formulário for enviado, busca o remedio pelo id ou pelo primeiro nome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['busca_remedio'])) {
        $busca = trim($_POST['busca_remedio']);

        // Verifica se a busca é um número(id) ou um Nome
        if (is_numeric($busca)) {
            $sql = "SELECT * FROM remedio WHERE id_remedio = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            // Pega apenas o primeiro nome digitado
            $primeiro_nome = explode(" ", $busca)[0];

            // Busca apenas pelo primeiro nome (comparando com a primeira palavra do campo nome)
            $sql = "SELECT * FROM remedio 
                    WHERE SUBSTRING_INDEX(nome, ' ', 1) LIKE :primeiro_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':primeiro_nome', "%$primeiro_nome%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $remedio = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o remedio não for encontrado, Exibe um Alerta
        if (!$remedio) {
            echo "<script>alert('Usuário não encontrado!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuário</title>
    <link rel="stylesheet" href="Estilo/styles.css">
    <link rel="stylesheet" href="Estilo/style.css">
    <script src="Mascara/scripts.js"></script>
    <script src="Mascara/script.js"></script>
</head>
<body>
    <!-- MENU DROPDOWN -->
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

    <!-- Formulário para Buscar usuários --> 
    <form action="alterar_remedio.php" method="POST">
        <label for="busca_remedio">Digite o ID ou Nome:</label>
        <input type="text" id="busca_remedio" name="busca_remedio" required onkeyup="buscarSugestoes()">
        <div id="sugestoes"></div>
        <button type="submit">Buscar</button>
    </form>
    
    <?php if ($remedio): ?>
        <form action="processa_alteracao_remedio.php" method="POST">
            <input type="hidden" name="id_remedio" value="<?= htmlspecialchars($remedio['id_remedio']) ?>">
            <br>
            <label for="nome">Nome:</label> 
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($remedio['nome']) ?>" required>
            <br>
            <label for="email">Descrição:</label> 
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($remedio['descricao']) ?>" required>
            <br>
            <label for="email">Quantidade:</label> 
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($remedio['qtde']) ?>" required>
            <br>
            <label for="email">Email:</label> 
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($remedio['email']) ?>" required>
            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca_remedio'])): ?>
        <!-- Se buscou alguém, botão volta para mostrar a tabela completa -->
        <a href="alterar_remedio.php">Voltar</a>
    <?php else: ?>
        <!-- Se não buscou nada, volta para a tela principal -->
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>