<?php
session_start();
include(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = (int) $_GET['id'];

// Fetch existing task for this user
$stmt = $conn->prepare("SELECT id, title, description, deadline FROM tasks WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
$stmt->close();

if (!$task) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $deadline = $_POST['deadline'];

    if (!empty($title) && !empty($deadline)) {
        $update = $conn->prepare("UPDATE tasks SET title = ?, description = ?, deadline = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("sssii", $title, $desc, $deadline, $id, $user_id);

        if ($update->execute()) {
            $update->close();
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to update task.";
        }

        $update->close();
    } else {
        $error = "Please fill in title and deadline.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            background:
                radial-gradient(circle at top, rgba(195, 216, 9, 0.16), transparent 28%),
                linear-gradient(135deg, #1a1a1a 0%, #222022 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .task-box {
            width: 100%;
            max-width: 420px;
            padding: 26px 24px;
            border-radius: 22px;
            background: rgba(34, 32, 34, 0.88);
            border: 1px solid rgba(195, 216, 9, 0.18);
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.42);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 18px;
            color: #fff;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff;
            border-radius: 12px;
            padding: 12px 14px;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border-color: #C3D809;
            box-shadow: 0 0 0 3px rgba(195, 216, 9, 0.10);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        .btn-cool {
            width: 100%;
            margin-top: 8px;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #C3D809, #aabf00);
            color: #222022;
            font-size: 15px;
            font-weight: 800;
            transition: 0.25s ease;
        }

        .btn-cool:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(195, 216, 9, 0.18);
        }

        .back-link {
            display: inline-block;
            margin-top: 14px;
            color: #C3D809;
            text-decoration: none;
            font-weight: 700;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="task-box">
        <h2>Edit Task</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="title" class="form-control mb-3" value="<?php echo htmlspecialchars($task['title']); ?>" placeholder="Title" required>
            <textarea name="description" class="form-control mb-3" placeholder="Description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
            <input type="date" name="deadline" class="form-control mb-3" value="<?php echo htmlspecialchars($task['deadline']); ?>" required>
            <button type="submit" class="btn-cool">Update Task</button>
        </form>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>