<?php 
require 'db_connect.php';


$successMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = $_POST['task_name'];
    $is_goal = isset($_POST['is_goal']) ? 1 : 0;
    $has_dependencies = isset($_POST['dependencies']) && !empty($_POST['dependencies']);

   
    $stmt = $conn->prepare("INSERT INTO tasks (task_name, is_goal) VALUES (?, ?)");
    $stmt->bind_param("si", $task_name, $is_goal);
    $stmt->execute();
    $task_id = $stmt->insert_id;
    $stmt->close();

    
    if ($has_dependencies) {
        foreach ($_POST['dependencies'] as $dep_name) {
            $dep_id = null;

           
            $dep_stmt = $conn->prepare("SELECT task_id FROM tasks WHERE task_name = ?");
            $dep_stmt->bind_param("s", $dep_name);
            $dep_stmt->execute();
            $dep_stmt->bind_result($dep_id);
            $dep_stmt->fetch();
            $dep_stmt->close();

            if (!$dep_id) {
                $dep_goal = 0; 
                $dep_insert_stmt = $conn->prepare("INSERT INTO tasks (task_name, is_goal) VALUES (?, ?)");
                $dep_insert_stmt->bind_param("si", $dep_name, $dep_goal);
                $dep_insert_stmt->execute();
                $dep_id = $dep_insert_stmt->insert_id;
                $dep_insert_stmt->close();
            }

           
            $check_dep_stmt = $conn->prepare("SELECT 1 FROM task_dependencies WHERE task_id = ? AND dependency_id = ?");
            $check_dep_stmt->bind_param("ii", $task_id, $dep_id);
            $check_dep_stmt->execute();
            $check_dep_stmt->store_result();
            $exists = $check_dep_stmt->num_rows > 0;
            $check_dep_stmt->close();

            if (!$exists) {
                $dep_rel_stmt = $conn->prepare("INSERT INTO task_dependencies (task_id, dependency_id) VALUES (?, ?)");
                $dep_rel_stmt->bind_param("ii", $task_id, $dep_id);
                $dep_rel_stmt->execute();
                $dep_rel_stmt->close();
            }
        }
    }

   
    $successMessage = "Task Added Successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Task Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f2f2f2, #d3d3f3);
            animation: gradient 15s ease infinite;
            background-size: 400% 400%;
        }

        @keyframes gradient {
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
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"],
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
        }

        input[type="checkbox"],
        input[type="radio"] {
            margin-right: 10px;
        }

        button {
            padding: 12px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #4f46e5;
        }

        .dependency-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .nav-links {
            margin-top: 30px;
            text-align: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #4f46e5;
            font-weight: 600;
            margin: 0 10px;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #312e81;
        }

        .success-message {
            color: green;
            margin-top: 10px;
            font-weight: bold;
        }
        .trash-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.trash-btn:hover {
    transform: scale(1.2);
}

    </style>
   <script>
function toggleDependencies(hasDeps) {
    document.getElementById("dependenciesContainer").style.display = hasDeps ? "block" : "none";
}

function addDependencyField(value = "") {
    const container = document.getElementById("dependencyFields");
    const inputGroup = document.createElement("div");
    inputGroup.className = "dependency-input";
    inputGroup.innerHTML = `
        <input type="text" name="dependencies[]" placeholder="Enter dependency task" value="${value}" required />
        <button type="button" onclick="removeDependencyField(this)" class="trash-btn" title="Remove">
            🗑️
        </button>
    `;
    container.appendChild(inputGroup);
}

function removeDependencyField(button) {
    const inputGroup = button.parentNode;
    inputGroup.remove();
}

function checkGoal() {
    const isGoal = document.getElementById("is_goal").checked;
    const dependenciesContainer = document.getElementById("dependenciesContainer");

    if (isGoal) {
        dependenciesContainer.style.display = "block";
        if (!document.querySelector("#dependencyFields .dependency-input")) {
            addDependencyField();
        }
    } else {
        dependenciesContainer.style.display = "none";
        document.getElementById("dependencyFields").innerHTML = '';
    }
}

function validateForm(event) {
    const isGoal = document.getElementById("is_goal").checked;
    const dependencyFields = document.querySelectorAll('#dependencyFields input[type="text"]');

    if (isGoal) {
        let atLeastOneFilled = false;
        dependencyFields.forEach(field => {
            if (field.value.trim() !== "") {
                atLeastOneFilled = true;
            }
        });

        if (!atLeastOneFilled) {
            alert("A goal must have at least one dependency!");
            event.preventDefault();
        }
    }
}

window.onload = checkGoal;
</script>



</head>

<body>
    <div class="container">
        <h2>Add New Task</h2>
        <form action="index.php" method="POST" onsubmit="validateForm(event)">
            <label>Task Name:</label>
            <input type="text" name="task_name" required />

            <label>
                <input type="checkbox" name="is_goal" value="1" id="is_goal" onclick="checkGoal()" />
                Is this task a Goal?
            </label>

            <div id="dependenciesContainer" style="display:none;">
                <label>Dependencies:</label>
                <div id="dependencyFields"></div>
                <button type="button" onclick="addDependencyField()">Add Another Dependency</button>
            </div>

            <button type="submit">Add Task</button>
        </form>

        <?php if ($successMessage): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <div class="nav-links">
            <a href="view_task.php">View All Tasks</a>
            |
            <a href="query_tasks.php">Search by Goal</a>
            |
            <a href="pending_tasks.php">Pending Tasks</a>
        </div>
    </div>
</body>

</html>
