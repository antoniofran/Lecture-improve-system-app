<?php

    if (session_id()==='') session_start();

    /*************************/

    $_SESSION["action_slctd"] = 'login';

    if (!empty($_GET["action"])) {

        $_SESSION["action_slctd"] = $_GET["action"];

    }

    /*************************/

    if ($_SESSION["action_slctd"] === 'info') {

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_info_system.php");
        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_info_author.php");

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_footer.php");

    }

    else {

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/index.php");

    }

?>