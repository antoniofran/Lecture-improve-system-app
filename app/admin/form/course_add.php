<?php

if (session_id()==='') session_start();

/*******************/

if (isset($_POST["submit"])) {

    //connect to database
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

    //prepare statement
    $stmt = $connection->prepare("CALL institution_course_create (?, ?);");
    $stmt->bind_param("ss", $course_name, $course_desc);

    //set parameters and execute
    $course_name = $_POST["coursename"];
    $course_desc = $_POST["coursedesc"];

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/admin/func/setup_course.php");

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Novi kolegij je dodan.";

    header("Location: /app/admin/func/setup_course.php");

    exit;

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

            echo '
                <section class="content-left flt-left">
                    <p class="section-title">Dodavanje kolegija institucije</p>

                    <form action="#" method="post">

                        <table>
                            <tr>
                                <td>Naslov</td>
                                <td><input type="text" name="coursename"></td>
                            </tr>
                            <tr>
                                <td>Opis</td>
                                <td><input type="text" name="coursedesc"></td>
                            </tr>
                        </table>

                        <table class="setup-navigator" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <a href="/app/admin/func/setup_course.php">
                                        <input class="navbttn-left" type="button" value="<< Nazad">
                                    </a>
                                </td>
                                <td>
                                    <input class="navbttn-right" type="submit" name="submit" value="Dodaj >>">
                                </td>
                            </tr>
                        </table>

                    </form>
                </section>
            ';

            include_once($_SERVER['DOCUMENT_ROOT']."/app/admin/actn/setup_navigate.php");

            include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_footer.php");

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

}

?>