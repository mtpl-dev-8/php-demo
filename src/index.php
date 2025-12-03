<?php
// Define the path to our JSON data file
$dataFile = 'tasks.json';

// --- HELPER FUNCTIONS ---

// Function to get all tasks from the JSON file
function getTasks() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        return []; // Return an empty array if the file doesn't exist
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true);
}

// Function to save the tasks array back to the JSON file
function saveTasks($tasks) {
    global $dataFile;
    // Re-index the array to prevent gaps after deleting items
    $tasks = array_values($tasks);
    file_put_contents($dataFile, json_encode($tasks, JSON_PRETTY_PRINT));
}


// --- HANDLE USER ACTIONS ---

// Get the user's action from the form submission or URL
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Add a new task
if ($action === 'add' && !empty($_POST['task_text'])) {
    $tasks = getTasks();
    $newTask = [
        'text' => $_POST['task_text'],
        'completed' => false
    ];
    array_unshift($tasks, $newTask); // Add to the beginning of the list
    saveTasks($tasks);
    header('Location: index.php'); // Redirect to prevent re-submission
    exit();
}

// Toggle the completion status of a task
if ($action === 'toggle' && isset($_GET['id'])) {
    $tasks = getTasks();
    $id = (int)$_GET['id'];
    if (isset($tasks[$id])) {
        $tasks[$id]['completed'] = !$tasks[$id]['completed'];
        saveTasks($tasks);
    }
    header('Location: index.php');
    exit();
}

// Delete a task
if ($action === 'delete' && isset($_GET['id'])) {
    $tasks = getTasks();
    $id = (int)$_GET['id'];
    if (isset($tasks[$id])) {
        unset($tasks[$id]); // Remove the task from the array
        saveTasks($tasks);
    }
    header('Location: index.php');
    exit();
}

// Get the final list of tasks to display
$tasks = getTasks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP To-Do List</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; color: #333; max-width: 600px; margin: 40px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #4a69bd; text-align: center; }
        form { display: flex; margin-bottom: 20px; }
        input[type="text"] { flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; }
        ul { list-style-type: none; padding: 0; }
        li { display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
        li.completed span { text-decoration: line-through; color: #999; }
        li span { flex-grow: 1; }
        li a { text-decoration: none; padding: 5px 10px; border-radius: 4px; margin-left: 5px; color: white; font-size: 0.9em; }
        .toggle-btn { background-color: #007bff; }
        .delete-btn { background-color: #dc3545; }
    </style>
</head>
<body>
    <h1>My To-Do List</h1>

    <!-- Form to Add New Tasks -->
    <form action="index.php" method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="task_text" placeholder="Enter a new task..." required>
        <button type="submit">Add Task</button>
    </form>

    <!-- List of Tasks -->
    <ul>
        <?php if (empty($tasks)): ?>
            <li>No tasks yet. Add one above!</li>
        <?php else: ?>
            <?php foreach ($tasks as $index => $task): ?>
                <li class="<?= $task['completed'] ? 'completed' : '' ?>">
                    <span><?= htmlspecialchars($task['text']) ?></span>
                    <a href="?action=toggle&id=<?= $index ?>" class="toggle-btn">
                        <?= $task['completed'] ? 'Undo' : 'Complete' ?>
                    </a>
                    <a href="?action=delete&id=<?= $index ?>" class="delete-btn">Delete</a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</body>
</html>
