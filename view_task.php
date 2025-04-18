<?php
require 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Manager</title>
  <style>
    body {
      background: linear-gradient(135deg, #fce4ec, #e0f7fa);
      animation: gradientBG 15s ease infinite;
      background-size: 400% 400%;
      font-family: 'Segoe UI', sans-serif;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      margin-top: 40px;
      border-radius: 12px;
      box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
    }

    h2 {
      margin-bottom: 15px;
      margin-top: 40px;
      border-bottom: 2px solid #ddd;
      padding-bottom: 5px;
    }

    .goal-group, .task-group {
      margin-bottom: 20px;
    }

    .goal-item, .task-item {
      padding: 10px;
      background-color: #e0f2f1;
      border-radius: 8px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      overflow: hidden;
    }

    .goal-item {
      transition: background-color 1s ease-in-out;
    }

    .task-item {
      background-color: #f0f0f0;
      transition: opacity 0.4s ease-out, transform 0.4s ease-out;
    }

    .task-item.completed {
      opacity: 0;
      transform: scale(0.8);
    }

    .goal-fill {
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background-color: green;
      z-index: 0;
      transition: width 2s ease-out;
    }

    .goal-item span {
      z-index: 1;
    }

    #popup {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    #popup-content {
      background-color: white;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      max-width: 400px;
      font-size: 18px;
      font-weight: bold;
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

    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #2196f3;
      font-weight: bold;
    }
    .empty-message {
  color: #999;
  text-align: center;
  font-style: italic;
  margin-top: 20px;
  font-size: 16px;
}

  </style>
</head>
<body>
  <div class="container">
  <h2>Goals</h2>
<div id="goals-section">
  <?php
  $hasGoals = false;
  $goals = $conn->query("SELECT task_id, task_name FROM tasks WHERE is_goal = 1");
  while ($goal = $goals->fetch_assoc()) {
    $hasGoals = true;
    echo "<div class='goal-item' data-goal-id='{$goal['task_id']}'>
            <span>{$goal['task_name']}</span>
            <input type='checkbox' class='goal-checkbox'>
            <div class='goal-fill'></div>
          </div>";
  }
  if (!$hasGoals) {
    echo "<div class='empty-message' id='no-goals'>No goals yet. Add one, lazy bum 💤</div>";
  }
  ?>
</div>

<h2>Tasks</h2>
<div id="tasks-section">
  <?php
  $hasTasks = false;
  $tasks = $conn->query("SELECT * FROM tasks WHERE is_goal = 0");
  while ($task = $tasks->fetch_assoc()) {
    $hasTasks = true;
    echo "<div class='task-item' data-task-id='{$task['task_id']}'>
            <span>{$task['task_name']}</span>
            <input type='checkbox' class='task-checkbox'>
          </div>";
  }
  if (!$hasTasks) {
    echo "<div class='empty-message' id='no-tasks'>No tasks yet. You're either very efficient or very lazy 🤔</div>";
  }
  ?>
</div>


    <a href="index.php">Go Back</a>
  </div>

  <div id="popup">
    <div id="popup-content"></div>
  </div>

  <script>
    
    document.querySelectorAll('.goal-checkbox').forEach(box => {
      box.addEventListener('change', function () {
        const goalDiv = this.closest('.goal-item');
        const goalId = goalDiv.dataset.goalId;

       
        if (this.checked) {
          const fillElement = goalDiv.querySelector('.goal-fill');
          fillElement.style.width = '100%'; 
          setTimeout(() => {
            showPopup(goalDiv.querySelector('span').innerText); 
          }, 1000);
        }

       
        fetch('delete_task.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ task_id: goalId })
        });

       
        goalDiv.classList.add('completed');
        setTimeout(() => {
          goalDiv.remove();
          
        }, 400);
      });
    });

   
    document.querySelectorAll('.task-checkbox').forEach(box => {
      box.addEventListener('change', function () {
        const taskDiv = this.closest('.task-item');
        const taskId = taskDiv.dataset.taskId;

       
        fetch('delete_task.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ task_id: taskId })
        });

       
        taskDiv.classList.add('completed');
        setTimeout(() => {
          taskDiv.remove();
        }, 400);
      });
    });

   
    function showPopup(taskName) {
      const popup = document.getElementById('popup');
      const content = document.getElementById('popup-content');
      content.innerHTML = `🎉 Congratulations! You completed "${taskName}"! <br><br>You're on fire, keep going!`;
      popup.style.display = 'flex';
    }

   
    document.getElementById('popup').addEventListener('click', function (e) {
      if (e.target.id === 'popup') {
        this.style.display = 'none';
      }
    });
    function checkEmptyStates() {
  const goalsSection = document.getElementById('goals-section');
  const tasksSection = document.getElementById('tasks-section');

  if (!goalsSection.querySelector('.goal-item')) {
    if (!document.getElementById('no-goals')) {
      goalsSection.innerHTML = '<div class="empty-message" id="no-goals">No goals yet. Add one, lazy bum 💤</div>';
    }
  }

  if (!tasksSection.querySelector('.task-item')) {
    if (!document.getElementById('no-tasks')) {
      tasksSection.innerHTML = '<div class="empty-message" id="no-tasks">No tasks yet. You\'re either very efficient or very lazy 🤔</div>';
    }
  }
}

  </script>
</body>
</html>
