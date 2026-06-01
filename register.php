<?php
include('../config/db.php');

$message = "";
$messageType = "";
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Registered successfully!";
            $messageType = "success";
            $name = "";
            $email = "";
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-container {
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

        .register-container h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 6px;
            color: #fff;
        }

        .register-container p {
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
            border: 1px solid transparent;
        }

        .success {
            background: rgba(195, 216, 9, 0.10);
            border-color: rgba(195, 216, 9, 0.28);
            color: #eaff6b;
        }

        .error {
            background: rgba(255, 80, 80, 0.10);
            border-color: rgba(255, 80, 80, 0.26);
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

        .login-box {
            text-align: center;
            padding: 12px 10px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 13px;
            color: rgba(255, 255, 255, 0.78);
        }

        .login-box a {
            color: #C3D809;
            font-weight: 700;
            text-decoration: none;
        }

        .login-box a:hover {
            text-decoration: underline;
        }

        @media (max-width: 420px) {
            .register-container {
                max-width: 100%;
                padding: 22px 18px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <p>Create your account in a few seconds</p>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>

            <button type="submit" class="submit-btn">Create Account</button>
        </form>

        <div class="divider">or</div>

        <div class="login-box">
            Already registered? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>