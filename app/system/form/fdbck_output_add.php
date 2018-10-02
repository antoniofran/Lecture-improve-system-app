<?php

if (session_id()==='') session_start();

/*******************/

if (isset($_POST["submit"])) {

    //connect to database
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

    //prepare statement
    $stmt = $connection->prepare("CALL survey_feedback_respond_create (?,?);");
    $stmt->bind_param("si", $output_text, $feedback_id);

    //set parameters and execute
    $output_text = $_POST["outputtext"];
    $feedback_id = intval(str_replace(" ", "", $_POST["feedbackid"]));

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/system/index.php?functype=feedback&courseid=".$_SESSION["temp_course_id"]."&coursename=".$_SESSION["temp_course_name"]);

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Novi odgovor na komentar/pitanje je dodan.";

    header("Location: /app/system/index.php?functype=feedback&courseid=".$_SESSION["temp_course_id"]."&coursename=".$_SESSION["temp_course_name"]);

    exit;

}

else {

    if (isset($row)) {

        if ($_SESSION["curr_sys_type"] === 'teacher') {

            echo '
                <li class="add-fdbck">
                    <form action="/app/system/form/fdbck_output_add.php" method="post">

                        <textarea name="outputtext" rows="1" cols="15"></textarea>

                        <input type="hidden" name="feedbackid" value="'.$row["feedback_id"].'">
                        <input class="navbttn-small" type="submit" name="submit" value="Dodaj >>">

                    </form>
                </li>
            ';

        }

    }

    else {

        $_SESSION["site_error"] = "Pristup traženom dijelu web aplikacije je odbijen!";

        header("Location: /index.php");

        exit;

    }

}

?>