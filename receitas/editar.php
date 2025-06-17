<?php
session_start();

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CookJoynn/';

require_once '../config/db.php';
require_once '../includes/header.php';

$erros = [];
$sucesso = false;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-4'><div class='alert alert-danger'>ID de receita inválido.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$id = intval($_GET['id']);

// Busca os dados atuais da receita
try {
    $stmt = $pdo->prepare("SELECT * FROM receitas WHERE id = ?");
    $stmt->execute([$id]);
    $receita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receita) {
        echo "<div class='container py-4'><div class='alert alert-danger'>Receita não encontrada.</div></div>";
        require_once '../includes/footer.php';
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Erro ao carregar receita: " . $e->getMessage() . "</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// Processa edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $tempo_preparo = intval($_POST['tempo_preparo'] ?? 0);
    $ingredientes = trim($_POST['ingredientes'] ?? '');
    $modo_preparo = trim($_POST['modo_preparo'] ?? '');
    $imagem_nome = $receita['imagem'];

    if (!$titulo) $erros[] = "Título é obrigatório.";
    if (!$categoria) $erros[] = "Categoria é obrigatória.";
    if ($tempo_preparo <= 0) $erros[] = "Informe um tempo válido.";
    if (!$ingredientes) $erros[] = "Informe os ingredientes.";
    if (!$modo_preparo) $erros[] = "Informe o modo de preparo.";

    // Upload de nova imagem
    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_nome = uniqid('receita_', true) . '_' . basename($_FILES['imagem']['name']);
        $caminho_destino = "../assets/img/uploads/" . $imagem_nome;

        if (!move_uploaded_file($imagem_tmp, $caminho_destino)) {
            $erros[] = "Erro ao salvar a nova imagem.";
        }
    }

    if (empty($erros)) {
        $sql = "UPDATE receitas SET titulo = ?, categoria = ?, tempo_preparo = ?, ingredientes = ?, modo_preparo = ?, imagem = ? WHERE id = ?";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $titulo,
                $categoria,
                $tempo_preparo,
                $ingredientes,
                $modo_preparo,
                $imagem_nome,
                $id
            ]);
            $sucesso = true;
            $receita = array_merge($receita, [
                'titulo' => $titulo,
                'categoria' => $categoria,
                'tempo_preparo' => $tempo_preparo,
                'ingredientes' => $ingredientes,
                'modo_preparo' => $modo_preparo,
                'imagem' => $imagem_nome
            ]);
        } catch (PDOException $e) {
            $erros[] = "Erro ao atualizar receita: " . $e->getMessage();
        }
    }
}
?>

<main class="container py-4">
    <h1 class="mb-4">Editar Receita</h1>

    <?php if ($sucesso): ?>
        <div class="alert alert-success">Receita atualizada com sucesso!</div>
    <?php endif; ?>

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
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($receita['titulo']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select">
                <option value="">-- Selecione --</option>
                <?php
                $categorias = ['almoço', 'jantar', 'sobremesa', 'lanche', 'café-da-manhã'];
                foreach ($categorias as $cat): ?>
                    <option value="<?= $cat ?>" <?= ($cat === $receita['categoria']) ? 'selected' : '' ?>>
                        <?= ucfirst($cat) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tempo de Preparo (min)</label>
            <input type="number" name="tempo_preparo" class="form-control" value="<?= htmlspecialchars($receita['tempo_preparo']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ingredientes</label>
            <textarea name="ingredientes" class="form-control" rows="4"><?= htmlspecialchars($receita['ingredientes']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Modo de Preparo</label>
            <textarea name="modo_preparo" class="form-control" rows="4"><?= htmlspecialchars($receita['modo_preparo']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem</label><br>
            <?php if (!empty($receita['imagem'])): ?>
                <img src="../assets/img/uploads/<?= htmlspecialchars($receita['imagem']) ?>" alt="Imagem atual" style="max-height: 200px;" class="mb-2 d-block">
            <?php endif; ?>
            <input type="file" name="imagem" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</main>

<?php require_once '../includes/footer.php'; ?>
