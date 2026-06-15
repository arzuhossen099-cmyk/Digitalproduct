<?php
require_once __DIR__ . '/../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = (int)$_POST['article_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];

    if ($article_id && $name && $comment) {
        $stmt = $pdo->prepare("INSERT INTO comments (article_id, guest_name, guest_email, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$article_id, $name, $email, $comment]);

        // Redirect back
        $stmt = $pdo->prepare("SELECT slug FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        $slug = $stmt->fetchColumn();
        header("Location: /news/index.php?slug=$slug&msg=comment_pending");
        exit;
    }
}
header("Location: /index.php");
?>
