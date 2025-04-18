# AI_miniProject
# 🧠 Planning Companion

**Planning Companion** is a PHP & MySQL-based task management web application designed to help users efficiently plan, organize, and complete their tasks. It leverages concepts from **Forward and Backward Chaining** in Artificial Intelligence to manage task dependencies and goals. This project was developed as a mini project for academic purposes.

---

## 🚀 Features

- ✅ Add tasks and optionally mark them as goals
- 🔗 Define dependencies for each goal (mandatory for goals)
- 📊 Visualize goals, dependencies, and task completion status
- 🎯 View progress of each goal with dynamic percentage completion
- ✨ Congratulatory animation when goals are fully completed
- 🔍 Search for goal-based dependency trees (backward chaining)
- 📆 Filter pending tasks (forward chaining style)
- 🗑 Smooth animations for deletion and UI interactivity

---

## 🗂 Project Structure

### `index.php`

- Entry point of the application.
- Allows users to add new tasks.
- If marked as a goal, dependencies can (and must) be added.
- Dynamic form with ability to add/remove dependencies.
- Displays success message and navigation links.

### `view_task.php`

- Displays all tasks in two sections:
  - **Goals**: Lists all goals with number of dependencies and completion percentage.
  - **Tasks**: Lists all tasks (goal or not) with delete options.
- Visual feedback:
  - Green fill animation when a goal reaches 100% completion.
  - Smooth fade-out for deleted tasks.
- Motivational popup on goal completion.

### `query_tasks.php`

- Accepts a goal name and performs **backward chaining** to display all its dependencies.
- Helps trace the sub-tasks required to complete a specific goal.

### `pending_tasks.php`

- Implements **forward chaining** by identifying tasks that are still pending (i.e., not completed).
- Useful for tracking what’s left to do regardless of goal structure.

### `delete_task.php`

- Backend logic for removing tasks from the database.
- Handles both independent and dependent tasks.
- Ensures that deletion animations sync with frontend.

### `db_connect.php`

- Establishes the MySQL database connection using `mysqli`.
- Shared by all files for consistent access to the task database.

---

## 🧠 AI Concepts Used

### Forward Chaining

Used in `pending_tasks.php` to list actionable tasks based on current task completion states.

### Backward Chaining

Implemented in `query_tasks.php` to traverse from a goal to its dependent sub-tasks recursively.

---

## ⚙️ Technologies Used

- **Frontend**: HTML, CSS, JavaScript (Vanilla)
- **Backend**: PHP
- **Database**: MySQL (via XAMPP or similar stack)
- **Styling**: Google Fonts, Custom Animations, Gradient UI

---

## 💾 Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/planning-companion.git
   Set up your MySQL database:

2. Create a database named your_db_name

Import the following tables:


CREATE TABLE tasks (
  task_id INT AUTO_INCREMENT PRIMARY KEY,
  task_name VARCHAR(255) NOT NULL,
  is_goal TINYINT(1) NOT NULL,
  is_completed TINYINT(1) DEFAULT 0
);

CREATE TABLE task_dependencies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT,
  dependency_id INT,
  FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE,
  FOREIGN KEY (dependency_id) REFERENCES tasks(task_id) ON DELETE CASCADE
);
Update db_connect.php with your database credentials.

3.Run the app locally using XAMPP or your preferred PHP environment.

📌 Notes
Dependencies are only allowed for goals.

Tasks marked as goals must have at least one dependency.

The UI is responsive and designed for a minimal and clean experience.

📸 Screenshots
![image](https://github.com/user-attachments/assets/b52903cf-66d3-4f40-a964-3e4c0eeaec64)
![image](https://github.com/user-attachments/assets/1d864eb1-3a82-48c3-b123-a3191357bff5)
![image](https://github.com/user-attachments/assets/598938f9-ecff-4372-80d3-817751c06634)
![image](https://github.com/user-attachments/assets/91830e3b-0536-4ded-a816-c16cd5ee00d6)


📚 License
This project is built for educational purposes. Feel free to fork, modify, or use it as a base for your own task manager.

🧑‍💻 Author
Sidharth — GitHub

