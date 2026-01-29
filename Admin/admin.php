<?php
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$user_count = 0;
$result = $conn->query("SELECT COUNT(*) as total_users FROM users WHERE role='user'");
if ($result) {
    $row = $result->fetch_assoc();
    $user_count = $row['total_users'];
}
?>
<?php
$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Determine which page to load inside <main>
$page = $_GET['page'] ?? 'dashboard';

// Allowed pages to avoid security issues
$allowed_pages = ['dashboard', 'products', 'categories', 'orders', 'users', 'payments'];
if (!in_array($page, $allowed_pages)) $page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Solist</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="../Admin/admin.css">
<link rel="stylesheet" href="css/user.css">
<link rel="icon" href="../img/icon.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- Header -->
<header class="admin-header">

    <h2 class="admin-title">Solist Admin Panel</h2>
    <div class="admin-user">
        <i class="fa fa-user-shield"></i> Admin
        <a href="../login.php"><i class="fa fa-right-from-bracket"></i></a>
    </div>
</header>

<!-- Side Menu -->
<aside class="admin-side" id="sideMenu">
    <div class="side-logo">
        <img src="../img/icon.png" alt="Solist">
        <h3>Admin</h3>
    </div>

    <nav class="admin-nav">
<a href="admin.php?page=dashboard" class="<?php echo $page=='dashboard'?'active':''; ?>"><i class="fa fa-chart-line"></i> Dashboard</a>
<a href="admin.php?page=products" class="<?php echo $page=='products'?'active':''; ?>"><i class="fa fa-box"></i> Products</a>
<a href="admin.php?page=categories" class="<?php echo $page=='categories'?'active':''; ?>"><i class="fa fa-layer-group"></i> Categories</a>
<a href="admin.php?page=orders" class="<?php echo $page=='orders'?'active':''; ?>"><i class="fa fa-shopping-bag"></i> Orders</a>
<a href="admin.php?page=users" class="<?php echo $page=='users'?'active':''; ?>"><i class="fa fa-users"></i> Users</a>
<a href="admin.php?page=payments" class="<?php echo $page=='payments'?'active':''; ?>"><i class="fa fa-credit-card"></i> Payments</a>

    </nav>
</aside>

<!-- Main Content -->







<!-- Main Content -->
<main class="admin-main">
<?php
switch ($page) {
    case 'dashboard':
        // Total users count for dashboard
        $result = $conn->query("SELECT COUNT(*) as total_users FROM users WHERE role='user'");
        $row = $result->fetch_assoc();
        $user_count = $row['total_users'];
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Orders</h4>
                <p>120</p>
            </div>
            <div class="stat-card">
                <h4>Total Users</h4>
                <p><?php echo $user_count; ?></p>
            </div>
            <div class="stat-card">
                <h4>Total Products</h4>
                <p>42</p>
            </div>
            <div class="stat-card">
                <h4>Revenue</h4>
                <p>$12,450</p>
            </div>
        </div>

        <section class="admin-section">
            <h3>Latest Orders</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Hala So</td>
                        <td>$120</td>
                        <td><span class="status paid">Paid</span></td>
                        <td>2026-01-29</td>
                    </tr>
                </tbody>
            </table>
        </section>
        <?php
        break;

    case 'products':
        ?>
        <h2>Products Page</h2>
        <?php
// Fetch products

        $prod_result = $conn->query("
            SELECT i.id, i.name, i.price, i.stock, i.is_in_stock, c.name AS category
            FROM items i
            LEFT JOIN categories c ON i.category_id=c.id
            ORDER BY i.id DESC
        ");
        ?>
        <section class="admin-section">
            <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
                <button id="addProductBtn" class="btn btn-primary">Add Product</button>
            </div>
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>In Stock</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php
                if($prod_result && $prod_result->num_rows>0){
                    $i=1;
                    while($p = $prod_result->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td><?php echo htmlspecialchars($p['name']);?></td>
                            <td><?php echo htmlspecialchars($p['category']);?></td>
                            <td><?php echo htmlspecialchars($p['price']);?></td>
                            <td><?php echo htmlspecialchars($p['stock']);?></td>
                            <td>
                                <input type="checkbox" class="toggleStock" data-id="<?php echo $p['id'];?>" <?php echo $p['is_in_stock']?'checked':'';?>>
                            </td>
                            <td>
                                <button class="btn btn-danger deleteBtn" data-id="<?php echo $p['id'];?>">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="7" style="text-align:center;">No products found</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </section>

        <!-- Add Product Overlay -->
        <div id="productOverlay" class="overlay" style="display:none;">
            <div class="overlay-content">
                <h3>Add Product</h3>
                <form id="productForm" enctype="multipart/form-data">
                    <input type="text" name="name" placeholder="Name" required>
                    <select name="category_id" required>
                        <?php
                        $cats = $conn->query("SELECT id,name FROM categories ORDER BY name");
                        while($c = $cats->fetch_assoc()){
                            echo "<option value='{$c['id']}'>{$c['name']}</option>";
                        }
                        ?>
                    </select>
                    <input type="number" name="price" placeholder="Price" step="0.01" required>
                    <input type="number" name="stock" placeholder="Stock" required>
                    <label>In Stock: <input type="checkbox" name="is_in_stock" checked></label>
                    <textarea name="description" placeholder="Description"></textarea>
                    <input type="file" name="image" required>
                    <div style="text-align:right;">
                        <button type="submit" class="btn btn-add">Add</button>
                        <button type="button" id="closeProductOverlay" class="btn btn-close">Cancel</button>
                    </div>
                    <p id="prodMessage" class="success-msg">Product added!</p>
                </form>
            </div>
        </div>

        <script>
        // Overlay
        const addProdBtn = document.getElementById('addProductBtn');
        const prodOverlay = document.getElementById('productOverlay');
        const closeProdBtn = document.getElementById('closeProductOverlay');
        addProdBtn.onclick = ()=>prodOverlay.style.display='flex';
        closeProdBtn.onclick = ()=>prodOverlay.style.display='none';

        // Add product via AJAX
        const prodForm = document.getElementById('productForm');
        const prodMessage = document.getElementById('prodMessage');
        prodForm.onsubmit = function(e){
            e.preventDefault();
            const formData = new FormData(prodForm);
            fetch('add_product.php',{method:'POST',body:formData})
            .then(res=>res.text()).then(data=>{
                prodMessage.style.display='block';
                prodForm.reset();
                setTimeout(()=>{prodOverlay.style.display='none'; prodMessage.style.display='none'; location.reload();},1500);
            });
        }

        // Delete product
        document.querySelectorAll('.deleteBtn').forEach(btn=>{
            btn.onclick = function(){
                if(confirm("Delete this product?")){
                    const id=this.dataset.id;
                    fetch('delete_product.php',{
                        method:'POST',
                        headers:{'Content-Type':'application/x-www-form-urlencoded'},
                        body:'id='+encodeURIComponent(id)
                    }).then(res=>res.text()).then(data=>{
                        if(data.trim()=='deleted'){alert('Deleted'); location.reload();}
                        else{alert('Error deleting');}
                    });
                }
            }
        });

        // Toggle stock
        document.querySelectorAll('.toggleStock').forEach(cb=>{
            cb.onchange = function(){
                const id=this.dataset.id, val=this.checked?1:0;
                fetch('toggle_stock.php',{
                    method:'POST',
                    headers:{'Content-Type':'application/x-www-form-urlencoded'},
                    body:'id='+encodeURIComponent(id)+'&val='+val
                });
            }
        });
        </script>

        <?php
        break;
    break;


  case 'users':
    // Fetch all users (excluding admins if you want)
    $user_result = $conn->query("SELECT id, full_name, email, phone, role, status, created_at FROM users WHERE role='user' ORDER BY created_at DESC");
    ?>
    <section class="admin-section">
        <h3>Users List</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($user_result && $user_result->num_rows > 0) {
                    $i = 1;
                    while ($user = $user_result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <span class="status <?php echo $user['status']=='active'?'paid':'unpaid'; ?>">
                                    <?php echo htmlspecialchars($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No users found</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </section>
    <?php
    break;


    case 'orders':
        ?>
        <h2>Orders Page</h2>
        <!-- Fetch and display orders from DB here -->
        <?php
        break;

  case 'categories':
    // Fetch all categories
    $cat_result = $conn->query("SELECT id, name, slug FROM categories ORDER BY id DESC");
    ?>
    <section class="admin-section">
        <h3>Categories</h3>

        <!-- Top Right Controls -->
        <div style="display:flex; justify-content:flex-end; margin-bottom:15px;">
            <button id="addCategoryBtn" class="btn btn-primary">Add Category</button>
        </div>

        <!-- Categories Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($cat_result && $cat_result->num_rows > 0) {
                    $i = 1;
                    while ($cat = $cat_result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                            <td>
                                <button class="btn btn-danger deleteBtn" data-id="<?php echo $cat['id']; ?>">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">No categories found</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </section>

   <!-- Add Category Overlay -->
<div id="categoryOverlay" class="overlay" style="display:none;">
    <div class="overlay-content">
        <h3>Add New Category</h3>
        <form id="categoryForm">
            <label for="name">Category Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="slug">Slug:</label>
            <input type="text" id="slug" name="slug" required>
            <div class="form-buttons">
                <button type="submit" class="btn add-btn">Add Category</button>
                <button type="button" id="closeOverlay" class="btn close-btn">Cancel</button>
            </div>
        </form>
        <p id="catMessage" class="success-msg">Category is added!</p>
    </div>
</div>

    <!-- JS for overlay, add, and delete -->
    <script>
    const addBtn = document.getElementById('addCategoryBtn');
    const overlay = document.getElementById('categoryOverlay');
    const closeBtn = document.getElementById('closeOverlay');
    const form = document.getElementById('categoryForm');
    const message = document.getElementById('catMessage');

    // Show overlay
    addBtn.onclick = () => overlay.style.display = 'block';
    closeBtn.onclick = () => overlay.style.display = 'none';

    // Add category via AJAX
    form.onsubmit = function(e){
        e.preventDefault();
        const formData = new FormData(form);

        fetch('add_category.php', {
            method: 'POST',
            body: formData
        }).then(res => res.text())
        .then(data => {
            message.style.display = 'block';
            form.reset();
            setTimeout(() => {
                overlay.style.display = 'none';
                message.style.display = 'none';
                location.reload(); // reload page to show new category
            }, 1500);
        });
    }

  // Delete category
const deleteBtns = document.querySelectorAll('.deleteBtn');
deleteBtns.forEach(btn => {
    btn.onclick = function(){
        if(confirm("Are you sure you want to delete this category?")){
            const id = this.dataset.id;

            fetch('delete_category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === 'deleted'){
                    alert('Category deleted successfully');
                    location.reload();
                } else {
                    alert('Error deleting category');
                }
            });
        }
    }
});

    </script>

    <!-- CSS Styles -->
    <style>
    .overlay {
        position: fixed;
        top:0; left:0;
        width:100%; height:100%;
        background: rgba(0,0,0,0.6);
        display:flex;
        align-items:center;
        justify-content:center;
        z-index:1000;
    }
    .overlay-content {
        background:#fff;
        padding:20px;
        border-radius:10px;
        width:300px;
        text-align:center;
    }
    .overlay-content input { width: 100%; margin-bottom:10px; padding:5px; }

    /* Buttons */
    .btn { padding:7px 15px; margin:5px; cursor:pointer; border:none; border-radius:5px; }
    .btn-primary { background-color:#4CAF50; color:white; }
    .btn-secondary { background-color:#ccc; color:black; }
    .btn-danger { background-color:#f44336; color:white; }
    
    </style>
    <?php
    break;


    case 'payments':
        ?>
        <h2>Payments Page</h2>
        <!-- Fetch and display payments from DB here -->
        <?php
        break;

 
}
?>
</main>



<script>
const menuBtn = document.getElementById('menuBtn');
const sideMenu = document.getElementById('sideMenu');
menuBtn.onclick = () => sideMenu.classList.toggle('open');
</script>

</body>
</html>

