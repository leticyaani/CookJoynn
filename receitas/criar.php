<?php
session_start(); 

// Redireciona se o usuário não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

require_once '../config/db.php';
require_once '../includes/header.php';

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $tempo_preparo = intval($_POST['tempo_preparo'] ?? 0);
    $ingredientes = trim($_POST['ingredientes'] ?? '');
    $modo_preparo = trim($_POST['modo_preparo'] ?? '');
    $imagem_nome = null;
    $usuario_id = $_SESSION['usuario_id'];

    // Validação
    if (!$titulo) $erros[] = "Título é obrigatório.";
    if (!$categoria) $erros[] = "Categoria é obrigatória.";
    if ($tempo_preparo <= 0) $erros[] = "Informe um tempo válido.";
    if (!$ingredientes) $erros[] = "Informe os ingredientes.";
    if (!$modo_preparo) $erros[] = "Informe o modo de preparo.";

    // Upload da imagem
    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_nome = uniqid('receita_', true) . '_' . basename($_FILES['imagem']['name']);
        $caminho_destino = "../assets/img/uploads/" . $imagem_nome;

        if (!move_uploaded_file($imagem_tmp, $caminho_destino)) {
            $erros[] = "Erro ao salvar a imagem.";
        }
    }

    if (empty($erros)) {
        $sql = "INSERT INTO receitas (titulo, categoria, tempo_preparo, ingredientes, modo_preparo, imagem, usuario_id, criado_em) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $titulo,
                $categoria,
                $tempo_preparo,
                $ingredientes,
                $modo_preparo,
                $imagem_nome,
                $usuario_id
            ]);
            $sucesso = true;
        } catch (PDOException $e) {
            $erros[] = "Erro ao salvar receita: " . $e->getMessage();
        }
    }
}
?>

<main class="container py-4">
    <div class="criar-receita-container">
        <h1 class="mb-4">Criar Nova Receita</h1>

        <?php if ($sucesso): ?>
            <div class="alert alert-success">Receita criada com sucesso!</div>
            <a href="index.php" class="btn btn-primary">Voltar à lista</a>
        <?php else: ?>

            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                            <li><?= htmlspecialchars($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Título da Receita</label>
                    <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria" class="form-select">
                        <option value="">-- Selecione --</option>
                        <?php
                        $categorias = ['almoço', 'jantar', 'sobremesa', 'lanche', 'café-da-manhã'];
                        foreach ($categorias as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($cat === ($_POST['categoria'] ?? '')) ? 'selected' : '' ?>>
                                <?= ucfirst($cat) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tempo de Preparo (minutos)</label>
                    <input type="number" name="tempo_preparo" class="form-control" value="<?= htmlspecialchars($_POST['tempo_preparo'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ingredientes</label>
                    <textarea name="ingredientes" class="form-control" rows="4"><?= htmlspecialchars($_POST['ingredientes'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Modo de Preparo</label>
                    <textarea name="modo_preparo" class="form-control" rows="4"><?= htmlspecialchars($_POST['modo_preparo'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagem (opcional)</label>
                    <input type="file" name="imagem" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">Salvar Receita</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
