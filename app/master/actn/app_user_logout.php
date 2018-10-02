<?php

    if (session_id()==='') session_start();

    unset($_SESSION["curr_human_id"]);
    unset($_SESSION["curr_human_name"]);
    unset($_SESSION["curr_role_name"]);

    session_destroy();

    /****************/

    if (session_id()==='') session_start();

    $_SESSION["site_notice"] = "Korisnik je odjavljen!";

    header("Location: /index.php");

    exit;

?>