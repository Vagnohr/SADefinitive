<?php
session_start();
require_once 'conexao.php';

if(!isset($_SESSION['perfil'])) {
    header("Location: index.php");
    exit();
}

// Obtendo o Nome do Perfil do Usuario Logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome FROM usuario WHERE id_usuario = :id_usuario";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();

// Definição das Permissões por Perfil
$permissoes = [
    1=>["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_remedio.php", "cadastro_funcionario.php"],
        "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_remedio.php", "buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_remedio.php", "alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_remedio.php", "excluir_funcionario.php"]],

    2=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_remedio.php"],
        "Alterar"=>["alterar_cliente.php", "alterar_fornecedor.php"]],

    3=>["Cadastrar"=>["cadastro_fornecedor.php", "cadastro_remedio.php"],
        "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_remedio.php"],
        "Alterar"=>["alterar_fornecedor.php", "alterar_remedio.php"],
        "Excluir"=>["excluir_remedio.php"]],

    4=>["Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_remedio.php"],
        "Alterar"=>["alterar_cliente.php"]],
];

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>
    <link rel="stylesheet" href="Estilo/styles.css">
    <script src="Mascara/scripts.js"></script>
</head>
<body>
    <header>
        <div class="saudacao">
            <h2>Bem vindo, <?php echo $_SESSION["usuario"]; ?>! Perfil: <?php echo $nome_perfil; ?></h2>
        </div>
        <div class="logout">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

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
</body>
</html>