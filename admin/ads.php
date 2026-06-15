<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/header.php';

$message = '';

if (isset($_POST['add_ad'])) {
    $position = $_POST['position'];
    $type = $_POST['type'];
    $link_url = $_POST['link_url'];
    $ad_code = $_POST['ad_code'];

    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_url = 'ad_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image_url);
    }

    $stmt = $pdo->prepare("INSERT INTO advertisements (position, type, image_url, link_url, ad_code) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$position, $type, $image_url, $link_url, $ad_code]);
    $message = 'Advertisement added!';
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM advertisements WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Ad deleted!';
}

$ads = $pdo->query("SELECT * FROM advertisements ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Advertisements</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">Add New Advertisement</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-select" required>
                            <option value="header">Header</option>
                            <option value="sidebar">Sidebar</option>
                            <option value="footer">Footer</option>
                            <option value="article_middle">Article Middle</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" id="ad_type" required>
                            <option value="image">Image</option>
                            <option value="code">Google AdSense / Code</option>
                        </select>
                    </div>
                    <div id="image_field" class="mb-3">
                        <label class="form-label">Ad Image</label>
                        <input type="file" name="image" class="form-control">
                        <label class="form-label mt-2">Link URL</label>
                        <input type="url" name="link_url" class="form-control">
                    </div>
                    <div id="code_field" class="mb-3 d-none">
                        <label class="form-label">Ad Code</label>
                        <textarea name="ad_code" class="form-control" rows="5"></textarea>
                    </div>
                    <button type="submit" name="add_ad" class="btn btn-primary">Add Advertisement</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">Ad Inventory</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Type</th>
                            <th>Preview / Code Snippet</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ads as $ad): ?>
                        <tr>
                            <td><?php echo h(ucfirst($ad['position'])); ?></td>
                            <td><?php echo h(ucfirst($ad['type'])); ?></td>
                            <td>
                                <?php if ($ad['type'] == 'image'): ?>
                                    <img src="../uploads/<?php echo h($ad['image_url']); ?>" width="100">
                                <?php else: ?>
                                    <code><?php echo h(substr($ad['ad_code'], 0, 50)); ?>...</code>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-success"><?php echo h($ad['status']); ?></span></td>
                            <td>
                                <a href="?delete=<?php echo $ad['id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('ad_type').addEventListener('change', function() {
        if (this.value === 'image') {
            document.getElementById('image_field').classList.remove('d-none');
            document.getElementById('code_field').classList.add('d-none');
        } else {
            document.getElementById('image_field').classList.add('d-none');
            document.getElementById('code_field').classList.remove('d-none');
        }
    });
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
