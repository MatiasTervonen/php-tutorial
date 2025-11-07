<?php
session_start();

include __DIR__ . "/../inc/header.php";
include __DIR__ . "/../inc/database.php";

$sql = "SELECT * FROM todo_lists";
$stmt = $conn->prepare($sql);
$stmt->execute();

$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


   <div class="flex flex-col items-center my-8 bg-slate-700 rounded-2xl border-2 border-slate-500 max-w-3xl w-full mx-auto pb-10 min-h-[70vh] px-6">
        <h1 class="text-3xl my-5 font-semibold underline">Todo Lists</h1>
         <?php if (isset($_SESSION["error"])) {
             echo "<p class='text-red-500 mb-2'>" . $_SESSION["error"] . "</p>";
             unset($_SESSION["error"]);
         } ?>
         <?php if (isset($_SESSION["success"])) {
             echo "<p class='text-green-500 mb-2'>" .
                 $_SESSION["success"] .
                 "</p>";
             unset($_SESSION["success"]);
         } ?>
        <div class="bg-slate-900  rounded-md mt-5 max-w-xl w-full">
         <?php if (empty($todos)): ?>
            <p class="text-xl mt-10 text-center my-20">No todo lists found. Create one!</p>   
          <?php else: ?>        
            <ul class="flex flex-col items-center justify-center mt-5">
               <?php foreach ($todos as $todo): ?>  
                  
            
                  <a href="expanded-todo.php?id=<?= urlencode(
                      $todo["ID"],
                  ) ?>" class="text-xl mb-5 bg-slate-800 py-2 w-3/4 rounded-full flex justify-between items-center px-4 hover:scale-105 hover:bg-slate-700">  
                   <?= htmlspecialchars($todo["title"]) ?>
                   <div>
                  <div class="flex gap-3 items-center">

               <form action="edit-todo.php" method="POST">
                  <button class="text-amber-500 hover:cursor-pointer"><i class="iconoir-edit-pencil"></i></button>
                  <input type="hidden" name="id" value="<?= htmlspecialchars(
                      $todo["ID"],
                  ) ?>"/>
               </form>

                  <form action="delete-todo.php" method="POST" onsubmit="return confirm('Do you really want to delete this todo?');">
                   <input type="hidden" name="id" value="<?= htmlspecialchars(
                       $todo["ID"],
                   ) ?>"/>  
                  <button class="text-red-500 hover:cursor-pointer"><i class="iconoir-xmark-circle"></i></button>
                  </form>                 
                  </div>
                   </div>
                  </a>
                  
                <?php endforeach; ?>
               </ul>   

                <?php endif; ?>
            </div>
               

        <a class=" bg-blue-600 py-2 px-4 mt-10 rounded-md border-2 border-blue-700 shadow-md font-semibold text-xl hover:scale-105 transition-transform duration-200" href="create-todo.php">Create New Todo</a>
   </div>

<?php include __DIR__ . "/../inc/footer.php"; ?>
