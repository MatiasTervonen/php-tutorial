<?php
session_start();

include __DIR__ . "/../inc/header.php";
include __DIR__ . "/../inc/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    try {
        $sql = "DELETE FROM todo_lists WHERE ID = :id";
        $stm = $conn->prepare($sql);
        $stm->bindParam(":id", $id, PDO::PARAM_INT);
        $stm->execute();

        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION["error"] = "Error deleting todo";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

?>



