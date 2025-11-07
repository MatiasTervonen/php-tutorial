<?php
session_start();

include __DIR__ . "/../inc/header.php";
include __DIR__ . "/../inc/database.php";

$titleError = "";
$taskError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["title"])) {
    $todoId = $_POST["id"];
    $title = $_POST["title"];
    $tasks = $_POST["tasks"] ?? [];

    $valid = true;

    if (empty($title)) {
        $titleError = "Title is required.";
        $valid = false;
    }

    if (empty($tasks)) {
        $taskError = "Task cant be empty";
        $valid = false;
    }

    if ($valid) {
        try {
            $sql = "UPDATE todo_lists SET title = :title WHERE ID = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(["title" => $title, "id" => $todoId]);

            foreach ($tasks as $task) {
                $sql = "UPDATE todo_tasks SET task = :task WHERE ID = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(["task" => $task["task"], "id" => $task["id"]]);
            }

            $_SESSION["success"] = "Todo updated succesfully!";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION["error"] = "error updating todo" . $e->getMessage();
            header("Location: index.php");
            exit();
        }
    } else {
        $todo = [
            "id" => $todoId,
            "title" => $title,
            "tasks" => $tasks,
        ];
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $todoId = $_POST["id"];

    try {
        $sql = "SELECT
 t.ID AS todo_id,
 t.title AS todo_title,
 t.created_at AS todo_created,
 task.ID AS task_id,
 task.task AS task_task 
 FROM todo_lists t 
 LEFT JOIN todo_tasks task 
    ON t.ID = task.list_id 
 WHERE t.ID = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute(["id" => $todoId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            $todo = [
                "id" => $rows[0]["todo_id"],
                "title" => $rows[0]["todo_title"],
                "t.created_at" => $rows[0]["todo_created"],
                "tasks" => [],
            ];

            foreach ($rows as $row) {
                if ($row["task_id"]) {
                    $todo["tasks"][] = [
                        "id" => $row["task_id"],
                        "task" => $row["task_task"],
                    ];
                }
            }
        }
    } catch (PDOException $e) {
        $_SESSION["error"] = "Error loading todo" . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>


   <form action="edit-todo.php" method="POST" class="flex flex-col items-center my-8 bg-slate-700 rounded-2xl border-2 border-slate-500 min-w-3xl mx-auto pb-10 min-h-[70vh] relative">

     <a href="index.php" class="text-lg font-semibold absolute top-5 left-10 bg-blue-600 py-2 px-4 rounded-xl flex gap-1 items-center transition-transform duration-200 transform-gpu hover:scale-105 cursor-pointer">
     <i class="iconoir-nav-arrow-left"></i>  
    <p>Takaisin</p>    
    </a>

        <h1 class="text-3xl my-5 font-semibold underline">Edit Todo</h1>

        <div class="bg-slate-900 rounded-md py-10 max-w-xl w-full text-xl">
            <input type="hidden" name="id" value="<?= htmlspecialchars(
                $todo["id"],
            ) ?>"/>

        <div class="flex flex-col mb-1 w-full max-w-md mx-auto">
         <label for="title">Title...</label>   
         <input type="text" name="title" value="<?= htmlspecialchars(
             $todo["title"],
         ) ?>" class="border-2 rounded-md p-2  bg-slate-800 placeholder:text-lg  hover:border-blue-500 focus:outline-none focus:border-green-300" />
        
             <p id="titleError" class="text-red-500 mt-2 mb-8"><?php echo $titleError; ?></p>

            <h2 class=" text-2xl font-semibold">Tasks</h2>

             <p id="taskError" class="text-red-500 mt-2 mb-8"><?php echo $taskError; ?></p>

         <?php foreach ($todo["tasks"] as $index => $task): ?>
            
            <div class="flex items-center gap-5 mb-10">
             <input type="hidden" name="tasks[<?= $index ?>][id]" value="<?= htmlspecialchars(
    $task["id"],
) ?>"/>    
            <input type="text" name="tasks[<?= $index ?>][task]" value="<?= htmlspecialchars(
    $task["task"],
) ?>" class="border-2 rounded-md p-2 bg-slate-800 placeholder:text-lg  hover:border-blue-500 focus:outline-none focus:border-green-300 w-full"/>

        

            </div>

          <?php endforeach; ?>
          </div>


          
        </div>
               
        <button type="submit" class="w-1/4 text-center bg-blue-600 py-2 px-4 mt-10 rounded-md border-2 border-blue-700 shadow-md font-semibold text-xl hover:scale-105 transition-transform duration-200 cursor-pointer">Save</button>
    
   </form>



<?php include __DIR__ . "/../inc/footer.php"; ?>
