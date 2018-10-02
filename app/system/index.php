<?php

    if (session_id()==='') session_start();

    /********************/

    if (isset($_SESSION["curr_role_name"])) {

        if ($_SESSION["curr_role_name"] === 'admin') {

            header("Location: /app/admin/index.php");

            exit;

        }

        /********************/

        //processing spancode
        if (isset($_POST["spancode"])) {

            $_SESSION["slctd_span_code"] = $_POST["spancode"];

            $_SESSION["site_notice"] = "Anketno razdoblje je izabrano!";

        }

        /********************/

        //create variables
        $_SESSION["span_open_exist"] = 0;

        //connect to database
        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

        //run the store proc
        $result = mysqli_query($connection, "CALL survey_span_open_check_exist ();");

        //check for failure
        if($result === false) {

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

            header("Location: /app/system/index.php");

            exit;

        }

        //loop the result set
        while ($row = mysqli_fetch_array($result)) {

            $_SESSION["span_open_exist"] = intval(str_replace(" ", "", $row["span_open_exist"]));
            $_SESSION["span_id_max"] = intval(str_replace(" ", "", $row["span_id_max"]));

        }

        //close database conn
        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        /********************/

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

        include_once($_SERVER['DOCUMENT_ROOT']."/app/system/actn/span_navigate.php");

        if (isset($_GET["functype"]) and isset($_GET["courseid"]) and isset($_GET["coursename"])) {

            $_SESSION["temp_course_id"] = $_GET["courseid"];
            $_SESSION["temp_course_name"] = $_GET["coursename"];

            /************/

            if ($_GET["functype"] === 'survey') {
                include_once($_SERVER['DOCUMENT_ROOT']."/app/system/func/span_survey.php");
            }
            else {
                include_once($_SERVER['DOCUMENT_ROOT']."/app/system/func/span_feedback.php");
            }

        }

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_footer.php");
    }

    else {

        $_SESSION["site_error"] = "Pristup traženom dijelu web aplikacije je odbijen!";

        header("Location: /index.php");

        exit;

    }

?>