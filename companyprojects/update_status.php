<?php
include "db.php";
$id = $_POST['id'];
mysqli_query($conn, "UPDATE users SET approved='yes' WHERE id=$id");
header("Location: data.php");
?>
