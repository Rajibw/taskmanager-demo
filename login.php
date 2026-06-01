<?php
session_start();
include(__DIR__ . '/../config/db.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ../tasks/dashboard.php");
        exit();
    } else {
        $message = "Invalid email or password";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at top, rgba(195, 216, 9, 0.16), transparent 30%),
                linear-gradient(135deg, #1a1a1a 0%, #222022 100%);
            padding: 18px;
            color: #fff;
        }

        .login-container {
            width: 100%;
            max-width: 360px;
            padding: 26px 24px;
            border-radius: 22px;
            background: rgba(34, 32, 34, 0.88);
            border: 1px solid rgba(195, 216, 9, 0.18);
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.42);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .login-container h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 6px;
            color: #fff;
        }

        .login-container p {
            text-align: center;
            color: rgba(255, 255, 255, 0.68);
            margin-bottom: 18px;
            font-size: 14px;
        }

        .alert {
            padding: 11px 13px;
            border-radius: 12px;
            margin-bottom: 14px;
            font-size: 13px;
            border: 1px solid rgba(255, 80, 80, 0.26);
            background: rgba(255, 80, 80, 0.10);
            color: #ffb5b5;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            color: #f3f3f3;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            outline: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            font-size: 14px;
            transition: 0.25s ease;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        .form-group input:focus {
            border-color: #C3D809;
            box-shadow: 0 0 0 3px rgba(195, 216, 9, 0.10);
        }

        .submit-btn {
            width: 100%;
            margin-top: 6px;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #C3D809, #aabf00);
            color: #222022;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(195, 216, 9, 0.18);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 16px 0 14px;
            color: rgba(255, 255, 255, 0.45);
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.10);
        }

        .register-box {
            text-align: center;
            padding: 12px 10px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 13px;
            color: rgba(255, 255, 255, 0.78);
        }

        .register-box a {
            color: #C3D809;
            font-weight: 700;
            text-decoration: none;
        }

        .register-box a:hover {
            text-decoration: underline;
        }

        @media (max-width: 420px) {
            .login-container {
                max-width: 100%;
                padding: 22px 18px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <p>Welcome back, please sign in</p>

        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>

        <div class="divider">or</div>

        <div class="register-box">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>