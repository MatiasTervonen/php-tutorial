<?php
session_start(); 

include __DIR__ . "/../inc/header.php";
include __DIR__ . "/../inc/database.php";

$title = "";
$tasks = [];

$titleError = "";
$taskError = "";

if (!empty($_POST)) {
    $title = $_POST["title"];
    $tasks = $_POST["tasks"] ?? [];

    $valid = true;

    if (empty($title)) {
        $titleError = "Title is required.";
        $valid = false;
    }

    if (empty($tasks)) {
        $taskError = "At least one task is required.";
        $valid = false;
    }

    if ($valid) {
        $sql = "INSERT INTO todo_lists (title) VALUES (:title)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":title", $title);
        $stmt->execute();

        $todoListId = $conn->lastInsertId();

        $sql =
            "INSERT INTO todo_tasks (list_id, task) VALUES (:list_id, :task)";
        $stmt = $conn->prepare($sql);
        foreach ($tasks as $task) {
            $stmt->bindParam(":list_id", $todoListId);
            $stmt->bindParam(":task", $task);
            $stmt->execute();
        }

        header("Location: index.php");
        exit();
    }
}
?>


    <form action="create-todo.php" method="POST"  class="flex flex-col items-center my-8 bg-slate-700 rounded-2xl border-2 border-slate-500 min-w-3xl mx-auto pb-10 min-h-[70vh] justify-between relative">

    <a href="index.php" class="text-lg font-semibold absolute top-5 left-10 bg-blue-600 py-2 px-4 rounded-xl flex gap-1 items-center transition-transform duration-200 transform-gpu hover:scale-105 cursor-pointer">
     <i class="iconoir-nav-arrow-left"></i>  
    <p>Takaisin</p>    
    </a>

    <div class="flex flex-col w-1/2 relative">
    
       
        <h1 class="text-3xl my-5 font-semibold underline text-center">Create Todo</h1>
 
        
        <input id="title" class="border-2 rounded-md p-2  bg-slate-900 placeholder:text-lg mt-5 hover:border-blue-500 focus:outline-none focus:border-green-300" type="text" placeholder="Title..." name="title" value="<?php echo htmlspecialchars(
            $title,
        ); ?>">
        <p id="titleError" class="text-red-500 my-2"><?php echo $titleError; ?></p>

         <input id="task" class="border-2 rounded-md p-2  bg-slate-900 placeholder:text-lg mt-2 hover:border-blue-500 focus:outline-none focus:border-green-300" type="text" placeholder="Task..." name="task">

        <p id="taskError" class="text-red-500 my-2"><?php echo $taskError; ?></p>

         <button id="addTaskButton" class="self-center bg-blue-600 py-2 px-4  my-5 rounded-md border-2 border-blue-700 shadow-md font-semibold w-2/3  hover:bg-blue-800 hover:scale-105 transition-transform duration-200 cursor-pointer">Add task</button>

         <div id="hiddenTasks"></div>

  


    <div class="bg-slate-900  rounded-md mt-5 w-full">
        <h2 id="displayTitle" class="text-xl font-semibold underline my-5 text-center">
            <?php echo $title ? $title : "Preview"; ?>
        </h2>
         <ul id="taskList" class="my-5"></ul>
    </div>

    </div>


    <button type="submit" class="bg-blue-600 py-2 px-4 mt-10 rounded-md border-2 border-blue-700 shadow-md font-semibold w-1/3 text-center hover:bg-blue-800 hover:scale-105 transition-transform duration-200 cursor-pointer">Save</button>

   

    </form>

        
  


<script>
const titleInput = document.getElementById('title');
const displayTitle = document.getElementById('displayTitle');
const taskInput = document.getElementById('task');
const displayTask = document.getElementById('displayTask');
const taskList = document.getElementById('taskList');
const hiddenTasks = document.getElementById('hiddenTasks');
const addTaskButton = document.getElementById('addTaskButton');
const titleError = document.getElementById('titleError');
const taskError = document.getElementById('taskError');
const form = document.querySelector('form');

let tasks = [];


form.addEventListener('submit', function(event) {
    if (titleInput.value.trim() === '') {
        event.preventDefault();
        titleError.textContent = 'Title is required.';
    }
    if (tasks.length === 0) {
        event.preventDefault();
        taskError.textContent = 'At least one task is required.';
    }
});

titleInput.addEventListener("input", function() {
    displayTitle.textContent = titleInput.value.trim() || "Preview";
})

taskInput.addEventListener("input", function() {
    displayTask.textContent = taskInput.value;
});


addTaskButton.addEventListener("click", (event) => {
    event.preventDefault(); 

    const value = taskInput.value.trim();
    if (!value) return;
    tasks.push(value);
    taskInput.value = "";
    renderTasks();
});


const deleteTask = (index) => {
    tasks.splice(index, 1);
    renderTasks();
}


function renderTasks() {
    taskList.innerHTML = tasks.map((t, i) => 
    `<li class="flex justify-between border-b-2 border-slate-600 py-2 px-4">${t}
    <button class="text-red-500 cursor-pointer" onclick="deleteTask(${i})">delete</button>
    </li>`
).join("");

hiddenTasks.innerHTML = tasks.map((t, i) => {
    return `<input type="hidden" name="tasks[]" value="${t}">`
}).join("");

}

function clearErrors() {
    titleError.textContent = "";
    taskError.textContent = "";
}
titleInput.addEventListener("input", clearErrors);
taskInput.addEventListener("input", clearErrors);

</script>

<?php include __DIR__ . "/../inc/footer.php"; ?>
