<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Tasks</title>
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #fce4ec);
            animation: gradientBG 15s ease infinite;
            background-size: 400% 400%;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            color: #333;
        }

        select,
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            background: #f4f4f4;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        a {
            text-decoration: none;
            color: #007bff;
            margin-top: 20px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            display: block;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Find Tasks for a Goal</h2>
        <form method="POST">
            <label for="goal_task">Select Goal Task:</label>
            <select name="goal_task" id="goal_task">
                <option value="">--Select a Goal Task--</option>
                <?php
                $result = $conn->query("SELECT task_id, task_name FROM tasks WHERE is_goal = 1");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['task_id']}'>{$row['task_name']}</option>";
                }
                ?>
            </select>
            <input type="submit" name="search" value="Find Tasks">
        </form>

        <?php
        if (isset($_POST['search'])) {
            $goal_task = $_POST['goal_task'];
            
           
            $query = $conn->query("SELECT dependency_id FROM task_dependencies WHERE task_id = '$goal_task'");
            
            $tasks = [];
            while ($row = $query->fetch_assoc()) {
                $tasks[] = $row['dependency_id'];
            }

            if (!empty($tasks)) {
                echo "<h3>For achieving this goal, you need to complete:</h3><ul>";
                foreach ($tasks as $task) {
                    $task_result = $conn->query("SELECT task_name FROM tasks WHERE task_id = '$task'");
                    $task_row = $task_result->fetch_assoc();
                    echo "<li>{$task_row['task_name']}</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No dependent tasks found for this goal.</p>";
            }
        }
        ?>
        <a href='index.php'>Go Back</a>
    </div>

</body>

</html>
