<?php
session_start();

include __DIR__ . "/../inc/header.php";
include __DIR__ . "/../inc/database.php";

if (isset($_GET["id"])) {
    $todoId = $_GET["id"];

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
    } catch (PDOexception $e) {
        $_SESSION["error"] = "Error loading todo" . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>


   <div class="flex flex-col items-center my-8 bg-slate-700 rounded-2xl border-2 border-slate-500 min-w-3xl mx-auto pb-10 min-h-[70vh] relative">

    <a href="index.php" class="text-lg font-semibold absolute top-5 left-10 bg-blue-600 py-2 px-4 rounded-xl flex gap-1 items-center transition-transform duration-200 transform-gpu hover:scale-105 cursor-pointer">
      <i class="iconoir-nav-arrow-left"></i>  
      <p>Takaisin</p>    
    </a>

        <h1 class="text-3xl my-5 font-semibold underline">Todo list</h1>

        <div class="bg-slate-900 rounded-md py-10 max-w-xl w-full text-xl">
       

        <div class="flex flex-col mb-1 w-full max-w-md mx-auto">

            <h1 class="text-2xl text-center mb-10 underline"><?= $todo[
                "title"
            ] ?></h1>        

         <?php foreach ($todo["tasks"] as $index => $task): ?>
            
            <div class="flex items-center justify-center gap-5 mb-10">

            <h2 class="text-xl" ><?= $task["task"] ?></h2>

            </div>

          <?php endforeach; ?>
          </div>   
        </div>
   </div>




<?php include __DIR__ . "/../inc/footer.php"; ?>
