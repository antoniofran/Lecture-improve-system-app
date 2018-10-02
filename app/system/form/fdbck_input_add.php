<?php

if (session_id()==='') session_start();

/*******************/

if (isset($_POST["submit"])) {

    //connect to database
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

    //prepare statement
    $stmt = $connection->prepare("CALL survey_feedback_create (?,?,?,?);");
    $stmt->bind_param("siii", $input_text, $type_id, $course_id, $span_id);

    //set parameters and execute
    $input_text = $_POST["inputtext"];
    $type_id = intval(str_replace(" ", "", $_POST["typeid"]));
    $course_id = intval(str_replace(" ", "", $_POST["courseid"]));
    $span_id = intval(str_replace(" ", "", $_POST["spanid"]));

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/system/index.php?functype=feedback&courseid=".$_SESSION["temp_course_id"]."&coursename=".$_SESSION["temp_course_name"]);

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Novi komentar/pitanje je dodano.";

    header("Location: /app/system/index.php?functype=feedback&courseid=".$_SESSION["temp_course_id"]."&coursename=".$_SESSION["temp_course_name"]);

    exit;

}

else {

    if (isset($row)) {

        echo '
            <li class="add-fdbck">
        ';

        /*************/

        if ($_SESSION["curr_sys_type"] === 'student') {

            echo '
                <form action="/app/system/form/fdbck_input_add.php" method="post">
            ';

        }

        /*************/

        echo '
            <textarea name="inputtext" rows="1" cols="15"></textarea>
        ';

        /*************/

        if ($_SESSION["curr_sys_type"] === 'student') {

            echo '
                <input type="hidden" name="typeid" value="'.$row["type_id"].'">
                <input type="hidden" name="courseid" value="'.$_SESSION["temp_course_id"].'">
                <input type="hidden" name="spanid" value="'.$_SESSION["slctd_span_id"].'">
                <input class="navbttn-small" type="submit" name="submit" value="Dodaj >>">
            ';

        }

        else {

            echo '
                <input class="navbttn-small disabled-ui" type="button" value="Dodaj >>">
            ';

        }

        /*************/

        if ($_SESSION["curr_sys_type"] === 'student') {

            echo '
                </form>
            ';

        }

        /*************/

        echo '
            </li>
        ';

    }

    else {

        $_SESSION["site_error"] = "Pristup traženom dijelu web aplikacije je odbijen!";

        header("Location: /index.php");

        exit;

    }

}

?>