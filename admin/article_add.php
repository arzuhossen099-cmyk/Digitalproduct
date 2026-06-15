<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)$_POST['category_id'];
    $admin_id = $_SESSION['admin_id'];
    $title_en = $_POST['title_en'] ?? '';
    $title_bn = $_POST['title_bn'] ?? '';
    $slug = create_slug($title_en);
    $content_en = $_POST['content_en'] ?? '';
    $content_bn = $_POST['content_bn'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $is_trending = isset($_POST['is_trending']) ? 1 : 0;

    $featured_image = '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        $featured_image = time() . '.' . $ext;
        move_uploaded_file($_FILES['featured_image']['tmp_name'], '../uploads/' . $featured_image);
    }

    if ($title_en && $content_en) {
        $stmt = $pdo->prepare("INSERT INTO articles (category_id, admin_id, title_en, title_bn, slug, content_en, content_bn, featured_image, is_featured, is_breaking, is_trending, status, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$category_id, $admin_id, $title_en, $title_bn, $slug, $content_en, $content_bn, $featured_image, $is_featured, $is_breaking, $is_trending, $status]);
        $message = 'Article added successfully!';
        redirect('articles.php');
    } else {
        $error = 'Title and content are required.';
    }
}

$categories = $pdo->query("SELECT id, name_en FROM categories")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Article</h1>
</div>

<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>

<div class="card shadow">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title (English)</label>
                        <input type="text" name="title_en" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title (Bangla)</label>
                        <input type="text" name="title_bn" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content (English)</label>
                        <textarea name="content_en" id="content_en" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content (Bangla)</label>
                        <textarea name="content_bn" id="content_bn" class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo h($cat['name_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" name="featured_image" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured">
                        <label class="form-check-label" for="is_featured">Is Featured</label>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_breaking" class="form-check-input" id="is_breaking">
                        <label class="form-check-label" for="is_breaking">Is Breaking</label>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_trending" class="form-check-input" id="is_trending">
                        <label class="form-check-label" for="is_trending">Is Trending</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Publish Article</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    CKEDITOR.replace('content_en');
    CKEDITOR.replace('content_bn');
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
