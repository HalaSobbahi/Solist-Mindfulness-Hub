<?php
session_start();

require_once 'session_check.php';


$conn = new mysqli("localhost", "root", "", "solist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get logged-in user ID
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}



// Fetch user data
$user_result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_result->fetch_assoc();


// Handle personal info update
if (isset($_POST['update_profile'])) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');

    // Optional: Add email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Update the user info in database
        if ($conn->query("UPDATE users SET full_name='$full_name', email='$email', phone='$phone' WHERE id=$user_id")) {
            header("Location: profile.php?success=info");
            exit;
        } else {
            $error = "DB Error: " . $conn->error;
        }
    }
}



// Handle password change
if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if (!password_verify($current_pass, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "New password and confirm password do not match.";
    } elseif (strlen($new_pass) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_pass)) {
        $error = "Password must include at least one special character.";
    } else {
        // Everything is valid, update password
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        if ($conn->query("UPDATE users SET password='$hashed_pass' WHERE id=$user_id")) {
            header("Location: profile.php?success=password");
            exit;
        } else {
            $error = "DB Error: " . $conn->error;
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Solist Mindfulness Hub</title>
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<style>
    /* ================= Profile Page ================= */
    .profile-container {
        margin: 50px 30px 50px 240px;
        /* align with sidebar */
        display: flex;
        flex-direction: column;
        gap: 30px;
        padding: 20px;
    }

    .profile-container h2 {
        font-size: 28px;
        color: #363535;
        margin-bottom: 20px;
    }

    .profile-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 20px;
        padding: 30px;
        display: flex;
        flex-direction: column;
        gap: 30px;
        max-width: 700px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .profile-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto;
        border: 3px solid #9FB9CC;
    }

    .profile-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .profile-form h3 {
        font-size: 22px;
        color: #363535;
        margin-bottom: 10px;
    }

    .profile-form label {
        font-size: 16px;
        color: #363535;
    }

    .profile-form input {
        padding: 10px 16px;
        border-radius: 12px;
        border: 1px solid #ccc;
        outline: none;
        font-size: 16px;
        transition: .3s;
        background: rgba(255, 255, 255, 0.05);
        color: #9FB9CC;
    }

    .profile-form input:focus {
        border-color: #9FB9CC;
        box-shadow: 0 0 10px rgba(159, 185, 204, 0.5);
    }

    .save-btn {
        padding: 12px 20px;
        border-radius: 20px;
        border: none;
        background: #9FB9CC;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: .3s;
        margin-top: 10px;
    }

    .save-btn:hover {
        background: #698396;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .success-msg,
    .error-msg {
        padding: 12px 16px;
        border-radius: 12px;
        text-align: center;
        font-size: 15px;
    }

    .success-msg {
        background: #363535;
        color: #fff;
    }

    .error-msg {
        background: #ff2d55;
        color: #fff;
    }

    /* ---------- Tablet ---------- */
    @media (max-width: 992px) {
        .profile-container {
            margin: 20px;
        }

        .profile-card {
            padding: 20px;
        }
    }

    /* ---------- Mobile ---------- */
    @media (max-width: 576px) {
        .profile-container {
            margin: 15px 10px;
            gap: 20px;
        }

        .profile-card {
            padding: 15px;
        }

        .profile-img {
            width: 100px;
            height: 100px;
        }

        .save-btn {
            width: 100%;
            padding: 10px;
        }
    }
</style>

<body>


    <header>
        <div class="menu-box" id="menuBtn">
            <i class="fa fa-bars menu-icon"></i>
        </div>
        <img src="img/logo.png" alt="" class="logo">
        <div class="logout-box">
            <a href="logout.php">
                <i class="fa-solid fa-arrow-right-from-bracket logout-icon"></i>
            </a>
        </div>
    </header>

    <div class="side-menu" id="sideMenu">
        <div class="menu-logo">
            <img src="img/logo.png" alt="Logo">
        </div>
        <a href="user.php"><i class="fa fa-home" style="margin-right: 15px;"></i>Home</a>
        <a href="user.php"><i class="fa fa-shopping-cart" style="margin-right: 15px;"></i>Cart</a>
        <a href="#"><i class="fa fa-list" style="margin-right: 15px;"></i>Orders</a>
        <a href="wishlist.php"><i class="fa fa-heart" style="margin-right: 15px;"></i>Wishlist</a>
        <a href=""><i class="fa fa-credit-card" style="margin-right: 15px;"></i>Payment methods</a>

        <a href="profile.php" class="active"><i class="fa fa-user" style="margin-right: 15px;"></i>Profile</a>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="profile-container">
        <h2>My Profile</h2>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'info'): ?>
            <p class="success-msg">Profile info updated successfully!</p>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'password'): ?>
            <p class="success-msg">Password changed successfully!</p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>


        <form method="POST" class="profile-form" enctype="multipart/form-data">
            <h3>Personal Info</h3>



            <label>Full Name</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+961 71 123 456">

            <button type="submit" name="update_profile" class="save-btn">Save Changes</button>
        </form>


        <form method="POST" class="profile-form">
            <h3>Change Password</h3>
            <label>Current Password</label>
            <input type="password" name="current_pass" required>

            <label>New Password</label>
            <input type="password" name="new_pass" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_pass" required>

            <button type="submit" name="change_password" class="save-btn">Change Password</button>
        </form>
    </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-logo"><img src="img/logo.png" alt="Solist Logo"></div>
            <div class="footer-links">
                     <h5>My Account</h5>
                <a href="cart.php">Cart</a>
                <a href="">Orders</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="#">Payment methods</a>
                <a href="profile.php">Profile</a>
            </div>
            <div class="footer-links">
                <h5>Connect With Us</h5>
                <div class="social-media-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="support-box">
                    <p class="support-title">Get support from Solist</p>
                    <div class="support-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:hello@solistmindfulness.com">hello@solistmindfulness.com</a>
                    </div>
                    <div class="support-item">
                        <i class="fas fa-phone-alt"></i>
                        <a href="tel:+96171447314">+961 71 447 314</a>
                    </div>
                    <p class="support-note">We will gladly assist you in a short time.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-text">© 2026 Solist Mindfulness Hub. All rights reserved.</p>
                <img src="img/visa-master.png" alt="Payment Methods" class="visa-master">
            </div>
            <button id="scrollTopBtn" aria-label="Scroll to top">
                <i class="fas fa-arrow-up"></i>
            </button>
        </div>
    </footer>

    <script>
        const menuBtn = document.getElementById('menuBtn');
        const sideMenu = document.getElementById('sideMenu');
        const overlay = document.getElementById('overlay');
        menuBtn.addEventListener('click', () => {
            sideMenu.classList.add('active');
            overlay.classList.add('active');
        });
        overlay.addEventListener('click', () => {
            sideMenu.classList.remove('active');
            overlay.classList.remove('active');
        });

        const scrollBtn = document.getElementById("scrollTopBtn");
        window.addEventListener("scroll", () => {
            scrollBtn.style.display = window.scrollY > 300 ? "flex" : "none";
        });
        scrollBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    </script>

</body>

</html>