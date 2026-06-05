<?php
require_once __DIR__ . '/config/db.php';

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";

$pages = ['', 'search.php', 'plans.php', 'blog.php'];
foreach ($pages as $page) {
    echo '<url>';
    echo '<loc>' . $base_url . $page . '</loc>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

// Blog Posts
$stmt = $pdo->query("SELECT slug FROM blog_posts WHERE status = 'published'");
while ($post = $stmt->fetch()) {
    echo '<url>';
    echo '<loc>' . $base_url . 'blog_post.php?slug=' . $post['slug'] . '</loc>';
    echo '<changefreq>weekly</changefreq>';
    echo '<priority>0.6</priority>';
    echo '</url>';
}

echo '</urlset>';
?>
