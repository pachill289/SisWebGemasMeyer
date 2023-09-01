<?php
    setcookie("usuario", "", time() - 86400, "/");
    header('Location:index.php');
?>