<?php
session_start();
require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem_erro'] = "ID de receita inválido.";
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Busca a receita
$stmt = $pdo->prepare("SELECT * FROM receitas WHERE id = ?");
$stmt->execute([$id]);
$receita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$receita) {
    $_SESSION['mensagem_erro'] = "Receita não encontrada.";
    header("Location: index.php");
    exit;
}

// Deleta a imagem, se existir
if (!empty($receita['imagem'])) {
    $caminho_imagem = "../assets/img/uploads/" . $receita['imagem'];
    if (file_exists($caminho_imagem)) {
        unlink($caminho_imagem);
    }
}

// Deleta a receita do banco
try {
    $stmt = $pdo->prepare("DELETE FROM receitas WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['mensagem_sucesso'] = "Receita excluída com sucesso.";
    header("Location: index.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['mensagem_erro'] = "Erro ao excluir receita: " . $e->getMessage();
    header("Location: index.php");
    exit;
}
