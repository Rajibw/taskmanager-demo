<?php
session_start();
include(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, title, description, deadline, status FROM tasks WHERE user_id = ? ORDER BY deadline ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            background:
                radial-gradient(circle at top, rgba(195, 216, 9, 0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(195, 216, 9, 0.10), transparent 25%),
                linear-gradient(135deg, #1a1a1a 0%, #222022 100%);
        }

        .page-wrapper {
            padding: 28px 16px 40px;
        }

        .topbar {
            max-width: 1180px;
            margin: 0 auto 22px;
            padding: 22px 24px;
            border-radius: 24px;
            background: rgba(34, 32, 34, 0.88);
            border: 1px solid rgba(195, 216, 9, 0.16);
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .topbar h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .topbar p {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, 0.68);
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-cool {
            border: none;
            border-radius: 12px;
            padding: 11px 16px;
            font-weight: 700;
            transition: 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-add {
            background: linear-gradient(135deg, #C3D809, #aabf00);
            color: #222022;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            color: #222022;
            box-shadow: 0 10px 22px rgba(195, 216, 9, 0.18);
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .btn-logout:hover {
            background: rgba(255, 80, 80, 0.12);
            color: #ffb5b5;
            transform: translateY(-2px);
        }

        .task-grid {
            max-width: 1180px;
            margin: 0 auto;
        }

        .task-card {
            height: 100%;
            border-radius: 22px;
            background: rgba(34, 32, 34, 0.86);
            border: 1px solid rgba(195, 216, 9, 0.12);
            box-shadow: 0 14px 36px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(14px);
            color: #fff;
            overflow: hidden;
            transition: 0.28s ease;
        }

        .task-card:hover {
            transform: translateY(-6px);
            border-color: rgba(195, 216, 9, 0.28);
            box-shadow: 0 20px 42px rgba(0, 0, 0, 0.38);
        }

        .task-card .card-body {
            padding: 22px;
        }

        .task-title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 10px;
            color: #fff;
        }

        .task-desc {
            color: rgba(255, 255, 255, 0.72);
            font-size: 14px;
            line-height: 1.6;
            min-height: 48px;
        }

        .meta {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 13px;
            color: rgba(255, 255, 255, 0.72);
        }

        .meta strong {
            color: #fff;
        }

        .badge-custom {
            display: inline-block;
            margin-top: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .badge-pending {
            background: rgba(195, 216, 9, 0.12);
            color: #eaff6b;
            border: 1px solid rgba(195, 216, 9, 0.25);
        }

        .badge-completed {
            background: rgba(76, 175, 80, 0.12);
            color: #9be59f;
            border: 1px solid rgba(76, 175, 80, 0.25);
        }

        .badge-overdue {
            background: rgba(255, 80, 80, 0.12);
            color: #ffb5b5;
            border: 1px solid rgba(255, 80, 80, 0.28);
        }

        .task-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .mini-btn {
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.25s ease;
        }

        .mini-btn:hover {
            transform: translateY(-2px);
        }

        .btn-edit {
            background: rgba(255, 193, 7, 0.15);
            color: #ffd95e;
            border: 1px solid rgba(255, 193, 7, 0.22);
        }

        .btn-complete {
            background: rgba(195, 216, 9, 0.15);
            color: #eaff6b;
            border: 1px solid rgba(195, 216, 9, 0.22);
        }

        .btn-delete {
            background: rgba(255, 80, 80, 0.14);
            color: #ffb5b5;
            border: 1px solid rgba(255, 80, 80, 0.22);
        }

        .empty-state {
            max-width: 1180px;
            margin: 30px auto 0;
            padding: 30px;
            text-align: center;
            border-radius: 22px;
            background: rgba(34, 32, 34, 0.78);
            border: 1px dashed rgba(195, 216, 9, 0.18);
            color: rgba(255, 255, 255, 0.75);
        }

        .empty-state h4 {
            color: #fff;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="topbar">
            <div>
                <h2>📋 Your Tasks</h2>
                <p>Manage everything in one clean workspace</p>
            </div>
            <div class="actions">
                <a href="add_task.php" class="btn-cool btn-add">+ Add Task</a>
                <a href="../auth/logout.php" class="btn-cool btn-logout">Logout</a>
            </div>
        </div>

        <div class="task-grid">
            <div class="row g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card task-card">
                                <div class="card-body">
                                    <div class="task-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                    <div class="task-desc"><?php echo htmlspecialchars($row['description']); ?></div>

                                    <div class="meta">
                                        <div><strong>Deadline:</strong> <?php echo htmlspecialchars($row['deadline']); ?></div>
                                        <div><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></div>
                                    </div>

                                    <?php
                                    if ($row['deadline'] < date("Y-m-d") && $row['status'] === 'pending') {
                                        echo '<span class="badge-custom badge-overdue">⚠ Overdue</span>';
                                    } elseif ($row['status'] === 'completed') {
                                        echo '<span class="badge-custom badge-completed">✓ Completed</span>';
                                    } else {
                                        echo '<span class="badge-custom badge-pending">● Pending</span>';
                                    }
                                    ?>

                                    <div class="task-actions">
                                        <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="mini-btn btn-edit">Edit</a>
                                        <a href="complete_task.php?id=<?php echo $row['id']; ?>" class="mini-btn btn-complete">Complete</a>
                                        <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="mini-btn btn-delete">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <h4>No tasks yet</h4>
                            <p>You can start by adding your first task using the button above.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>