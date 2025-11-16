<?php
session_start();
if (isset($_POST["usuario"])) {
    $_SESSION["usuario"] = $_POST["usuario"];
    echo "ok";
} else {
    echo "error";
}
?>