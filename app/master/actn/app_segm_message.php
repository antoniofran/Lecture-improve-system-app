<?php

    if (session_id()==='') session_start();

    /*****************/

    if (isset($_SESSION["site_notice"])) {

        if (isset($_SESSION["site_error"])) unset($_SESSION["site_error"]);

        /*************/

        echo '<div id="site-notice">'.$_SESSION["site_notice"].'</div>';

        unset($_SESSION["site_notice"]);

    }

    else if (isset($_SESSION["site_error"])) {

        echo '<div id="site-error">'.$_SESSION["site_error"].'</div>';

        unset($_SESSION["site_error"]);

    }

?>