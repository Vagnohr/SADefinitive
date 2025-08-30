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
$sqlPerfil = "SELECT nome FROM usuario WHERE id_usuario = :id_usuario";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_usuario', $id_perfil);
$stmtPerfil->execute();

// Definição das Permissões por Perfil
$permissoes = [
    1=>["Cadastrar"=>["cadastro_usuario.php", "cadastro_secretaria.php", "cadastro_funcionario.php", "cadastro_fornecedor.php", "cadastro_remedio.php"],
        "Buscar"=>["buscar_usuario.php", "buscar_secretaria.php", "buscar_funcionario.php", "buscar_fornecedor.php", "buscar_remedio.php"],
        "Alterar"=>["alterar_usuario.php", "alterar_secretaria.php", "alterar_funcionario.php", "alterar_fornecedor.php", "alterar_remedio.php"],
        "Excluir"=>["excluir_usuario.php", "excluir_secretaria.php", "excluir_funcionario.php", "excluir_fornecedor.php", "excluir_remedio.php"]],
    2=>["Cadastrar"=>["cadastro_remedio.php"],
        "Buscar"=>["buscar_remedio.php", "buscar_funcionario.php", "buscar_fornecedor.php"],
        "Alterar"=>["alterar_funcionario", "alterar_fornecedor.php"]],
    3=>["Cadastrar"=>["cadastro_remedio.php"],
        "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_remedio.php"],
        "Alterar"=>["alterar_fornecedor.php", "alterar_remedio.php"],
        "Excluir"=>["excluir_remedio.php"]],
    4=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_remedio.php"],
        "Alterar"=>["alterar_cliente.php"]],
];

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Verifica se o usuario tem permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "Acesso Negado";
    exit();
}

// Processa o formulário
if($_SERVER['REQUEST_METHOD'] =="POST"){
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $id_perfil_form = $_POST['id_perfil'];

    $errors = [];

     // Verificação: nome do usuario não pode conter números ou caracteres especiais
     if (!preg_match("/^[A-Za-zÀ-ÿ\s]+$/", $nome)) {
        $errors[] = "O nome do Usuário não pode conter números ou caracteres especiais!";
    }

    // Validação do email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Digite um email válido!";
    }

    // Verifica se o email já existe no banco
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Este email já está cadastrado!";
    }

    // Se houver erros, mostra alerta
    if (count($errors) > 0) {
        echo "<script>alert('" . implode("\\n", $errors) . "');history.back();</script>";
        exit;
    }

    // Cadastro do usuário
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) VALUES (:nome, :email, :senha, :id_perfil)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senhaHash);
    $stmt->bindParam(':id_perfil', $id_perfil_form);

    if($stmt->execute()){
        echo "<script>alert('Usuário cadastrado com sucesso!');window.location.href='cadastro_usuario.php';</script>";
    }else{
        echo "<script>alert('Erro ao cadastrar usuário!');history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuario</title>
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
        <h2 style="margin: 0;">Cadastrar Usuario:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="cadastro_usuario.php" method="POST" id="formCadastro">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <label for="id_perfil">Perfil:</label>
        <select id="id_perfil" name="id_perfil">
            <option value="1">Administrador</option>
            <option value="2">Secretaria</option>
            <option value="3">Funcionário</option>
            <option value="4">Fornecedor</option>
        </select>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>

    <a href="principal.php">Voltar Para o Menu</a>

    <!-- JavaScript externo -->
    <script src="Mascara/script.js"></script>
</body>
</html>