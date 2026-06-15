<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';
$error = '';

// Add Category
if (isset($_POST['add_category'])) {
    $name_en = $_POST['name_en'] ?? '';
    $name_bn = $_POST['name_bn'] ?? '';
    $slug = create_slug($name_en);
    $description = $_POST['description'] ?? '';
    $icon = $_POST['icon'] ?? '';

    if ($name_en && $name_bn) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name_en, name_bn, slug, description, icon) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name_en, $name_bn, $slug, $description, $icon]);
            $message = 'Category added successfully!';
        } catch (PDOException $e) {
            $error = 'Slug already exists or database error.';
        }
    } else {
        $error = 'All fields are required.';
    }
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Category deleted successfully!';
}

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Categories</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo h($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo h($error); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">Add New Category</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name (English)</label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name (Bangla)</label>
                        <input type="text" name="name_bn" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (FontAwesome class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="fas fa-futbol">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">Category List</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name (EN)</th>
                            <th>Name (BN)</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><i class="<?php echo h($cat['icon']); ?>"></i></td>
                            <td><?php echo h($cat['name_en']); ?></td>
                            <td><?php echo h($cat['name_bn']); ?></td>
                            <td><?php echo h($cat['slug']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
