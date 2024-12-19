<?php
$tasksFile = 'tasks.json';

// Load tasks
$tasks = file_exists($tasksFile) ? json_decode(file_get_contents($tasksFile), true) : [];

// Handle add task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add' && isset($_POST['new_task'])) {
        $newTask = trim(htmlspecialchars($_POST['new_task']));
        if (!empty($newTask)) {
            $tasks[] = ['text' => $newTask, 'done' => false];
            file_put_contents($tasksFile, json_encode($tasks));
        }
    } elseif ($action === 'delete' && isset($_POST['index'])) {
        $index = intval($_POST['index']);
        if (isset($tasks[$index])) {
            array_splice($tasks, $index, 1);
            file_put_contents($tasksFile, json_encode($tasks));
        }
    } elseif ($action === 'toggle' && isset($_POST['index'])) {
        $index = intval($_POST['index']);
        if (isset($tasks[$index])) {
            $tasks[$index]['done'] = !$tasks[$index]['done'];
            file_put_contents($tasksFile, json_encode($tasks));
        }
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TO DO APP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
        body { max-width: 600px; margin: 2rem auto; }
        .task.done { text-decoration: line-through; }
        ul,li {
            list-style: none;
        }

        form.task-data button {
            padding: 0;
        }

        li.task {
            display: flex;
            justify-content: space-between;
        }
        li.task.done p {
            text-decoration: line-through;
            font-weight: 400;
        }

        form.task-action button {
            background: white;
            color: black;
        }

        form.task-action button:hover {
            color: white;
            background: #9B4DCA;
        }

        li.task p {
            font-weight: 400;
            text-align: left;
            margin: 0;
        }
        form.task-data {
            width: calc(100% - 120px);
            display: block;
            display: flex;
            flex-wrap: wrap;
        }

        form.task-action {
            width: 120px;
            /*background: rebeccapurple;*/
        }

        form.task-data p {
            display: block;
            width: 100%;
        }

        form.task-data button {
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>To-Do App</h1>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="new_task" placeholder="Add a new task..." required>
        <button type="submit">Add Task</button>
    </form>
    <ul class="task-list">
        <?php 
        $c = 1;
        foreach ($tasks as $index => $task): ?>
            <li class="task <?= $task['done'] ? 'done' : '' ?> column-75">
                <form method="POST" style="display: inline;" class="task-data">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" style="background: none; border: none; cursor: pointer; color: black;">
                        <p><?= $c.' . '.htmlspecialchars($task['text']) ?></p>
                    </button>
                </form>
                <form method="POST" style="display: inline;" class="task-action">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" style="margin-left: 10px;">Delete</button>
                </form>
            </li>
        <?php $c++; endforeach; ?>
    </ul>
</body>
</html>
