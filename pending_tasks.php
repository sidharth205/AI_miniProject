<?php
require 'db_connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['completed_tasks'])) {
    foreach ($_POST['completed_tasks'] as $task_id) {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $stmt->close();
    }
}


$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Tasks</title>
    <style>
        body {
            background: linear-gradient(135deg, #fce4ec, #e0f7fa);
            animation: gradientBG 10s ease infinite;
            background-size: 400% 400%;
            font-family: 'Segoe UI', sans-serif;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            margin-top: 50px;
            border-radius: 12px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
            transition: all 0.4s ease;
        }

        .task-item.completed {
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.4s ease;
        }

        .task-name {
            flex-grow: 1;
            margin-left: 10px;
        }

        button {
            margin-top: 20px;
            padding: 12px 20px;
            border: none;
            background: #f44336;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #d32f2f;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2196f3;
            font-weight: bold;
        }
    </style>

    <script>
        function animateAndRemove() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            checkboxes.forEach(cb => {
                const taskDiv = cb.closest('.task-item');
                taskDiv.classList.add('completed');
                setTimeout(() => taskDiv.remove(), 400);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Pending Tasks (Before Today)</h2>
        <form method="POST" onsubmit="animateAndRemove()">
            <?php
            $result = $conn->query("SELECT * FROM tasks WHERE DATE(created_at) < '$today'");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='task-item'>";
                    echo "<label><input type='checkbox' name='completed_tasks[]' value='{$row['task_id']}' />";
                    echo "<span class='task-name'>{$row['task_name']}</span></label>";
                    echo "</div>";
                }
                echo "<button type='submit'>Mark Completed</button>";
            } else {
                echo "<p>No pending tasks found.</p>";
            }
            ?>
        </form>
        <a href="index.php">Go Back</a>
    </div>
</body>
</html>
