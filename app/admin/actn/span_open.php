<?php

    if (session_id()==='') session_start();

    /*******************/

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

            /*******************/

            $result = mysqli_query($connection, "CALL survey_span_open ();");

            if($result === false) {

                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                header("Location: /app/admin/func/setup_system.php");

                exit;

            }

            /*******************/

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            /*******************/

            unset($_SESSION["span_open_exist"]);

            $_SESSION["site_notice"] = "Anketno razdoblje je započeto!";

            header("Location: /app/admin/index.php");

            exit;

        }

        else {

            $_SESSION["site_error"] = "Anketno razdoblje je u tijeku!";

            header("Location: /app/admin/index.php");

            exit;

        }

    }

    else {

        $_SESSION["site_error"] = "Pristup traženom dijelu web aplikacije je odbijen!";

        header("Location: /index.php");

        exit;

    }

?>