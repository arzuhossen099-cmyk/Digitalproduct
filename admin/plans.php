<?php
require_once 'header.php';

if (isset($_POST['add_plan'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $desc = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO plans (name, price, duration_days, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $duration, $desc]);
    echo "<div class='alert alert-success'>Plan added!</div>";
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM plans WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: plans.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM plans ORDER BY price ASC");
$plans = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Add New Plan</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (BDT)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (Days)</label>
                        <input type="number" name="duration" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_plan" class="btn btn-primary w-100">Add Plan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Existing Plans</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Days</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td>৳<?php echo number_format($p['price'], 2); ?></td>
                                <td><?php echo $p['duration_days']; ?></td>
                                <td>
                                    <a href="plans.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this plan?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
