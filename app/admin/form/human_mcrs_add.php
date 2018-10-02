<?php

if (session_id()==='') session_start();

/*******************/

//connect to database
include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    //prepare statement
    $stmt = $connection->prepare("CALL institution_mapper_create (?, ?);");
    $stmt->bind_param("ii", $human_id, $course_id);

    //set parameters
    $human_id = intval(str_replace(" ", "", $_POST["humanid"]));
    $course_id = intval(str_replace(" ", "", $_POST["courseid"]));

    //set variables
    $human_name = $_POST["humanname"];

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/admin/form/human_mcrs_list.php?humanid=".$_POST["humanid"]."&humanname=".$_POST["humanname"]);

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Postojeći kolegij je pridružen nastavniku/studentu.";

    header("Location: /app/admin/form/human_mcrs_list.php?humanid=".$_POST["humanid"]."&humanname=".$_POST["humanname"]);

    exit;

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            if (!empty($_GET["humanid"]) and !empty($_GET["humanname"])) {

                //prepare statement
                $stmt = $connection->prepare("CALL institution_mapper_select (?);");
                $stmt->bind_param("i", $human_id);

                //set parameters
                $human_id = intval(str_replace(" ", "", $_GET["humanid"]));

                //store varables
                $human_name = $_GET["humanname"];

                //execute statement
                if(!$stmt->execute()) {

                    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                    $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                    header("Location: /app/admin/form/human_mcrs_list.php?humanid=".$human_id."&humanname=".$human_name);

                    exit;

                }

                //getting result
                $result = $stmt->get_result();

                //check num of rows
                if ($result->num_rows === 0) {

                    $_SESSION["site_error"] = "Nema kolegija za pridružiti ovom nastavniku/studentu!";

                    header("Location: /app/admin/form/human_mcrs_list.php?humanid=".$human_id."&humanname=".$human_name);

                    exit;

                }

                /********************/

                include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

                echo '
                    <section class="content-left flt-left">

                        <p class="section-title">Pridruživanje kolegija nastavniku/studentu "'.$human_name.'"</p>

                        <form action="#" method="post">
                            <table>
                                <tr>
                                    <td>Kolegij</td>
                                    <td>
                                        <select name="courseid">
                ';

                while ($row = mysqli_fetch_array($result)) {
                    echo '<option value="'.$row["course_id"].'">'.$row["course_id"].'|'.$row["name"].'|'.$row["description"].'</option>';
                }

                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                echo'
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <table class="setup-navigator" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <a href="/app/admin/form/human_mcrs_list.php?humanid='.$human_id.'&humanname='.$human_name.'">
                                            <input class="navbttn-left" type="button" value="<< Nazad">
                                        </a>
                                    </td>
                                    <td>
                                        <input type="hidden" name="humanid" value="'.$human_id.'">
                                        <input type="hidden" name="humanname" value="'.$human_name.'">
                                        <input class="navbttn-right" type="submit" name="submit" value="Pridruži >>">
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

                $_SESSION["site_error"] = "Niste odabrali nastavnika/studenta!";

                header("Location: /app/admin/func/setup_human.php");

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