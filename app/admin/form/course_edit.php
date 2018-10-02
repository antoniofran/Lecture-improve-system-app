<?php

if (session_id()==='') session_start();

/*******************/

//connect to database
include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    //prepare statement
    $stmt = $connection->prepare("CALL institution_course_update (?, ?, ?);");
    $stmt->bind_param("ssi", $course_name, $course_desc, $course_id);

    //set parameters
    $course_name = $_POST["coursename"];
    $course_desc = $_POST["coursedesc"];
    $course_id = intval(str_replace(" ", "", $_POST["courseid"]));

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/admin/func/setup_course.php");

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Kolegij je uređen.";

    header("Location: /app/admin/func/setup_course.php");

    exit;

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            if (!empty($_GET["courseid"])) {

                //prepare statement
                $stmt = $connection->prepare("CALL institution_course_select (?);");
                $stmt->bind_param("i", $course_id);

                //set parameters
                $course_id = intval(str_replace(" ", "", $_GET["courseid"]));

                //initialize varables
                $course_name = '';
                $course_desc = '';

                //execute statement
                if(!$stmt->execute()) {

                    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                    $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                    header("Location: /app/admin/func/setup_course.php");

                    exit;

                }

                //getting result
                $result = $stmt->get_result();

                //fetching data
                while ($row = mysqli_fetch_array($result)) {
                    $course_name = $row["name"];
                    $course_desc = $row["description"];
                }

                //close database conn
                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                /*******************/

                include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

                echo '
                    <section class="content-left flt-left">
                        <p class="section-title">Uređivanje kolegija institucije</p>

                        <form action="#" method="post">
                            <table>
                                <tr>
                                    <td>Naslov</td>
                                    <td><input type="text" name="coursename" value="'.$course_name.'"></td>
                                </tr>
                                <tr>
                                    <td>Opis</td>
                                    <td><input type="text" name="coursedesc" value="'.$course_desc.'"></td>
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
                                        <input type="hidden" name="courseid" value="'.$course_id.'">
                                        <input class="navbttn-right" type="submit" name="submit" value="Uredi >>">
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

                $_SESSION["site_error"] = "Niste odabrali kolegij!";

                header("Location: /app/admin/func/setup_course.php");

                exit;

            }

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