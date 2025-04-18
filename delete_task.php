<?php
require 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$task_id = $data['task_id'];


$query = "DELETE FROM tasks WHERE task_id = $task_id";
$conn->query($query);


$check_dependencies = $conn->query("SELECT dependency_id FROM task_dependencies WHERE task_id = $task_id");

while ($dependency = $check_dependencies->fetch_assoc()) {
    $goal_id = $dependency['dependency_id'];

    
    $remaining_dependencies = $conn->query("SELECT COUNT(*) AS remaining FROM task_dependencies WHERE dependency_id = $goal_id")->fetch_assoc()['remaining'];

   
    if ($remaining_dependencies == 0) {
        $conn->query("DELETE FROM tasks WHERE task_id = $goal_id");
    }
}

echo json_encode(['status' => 'success']);
?>
