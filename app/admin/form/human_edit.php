<?php

if (session_id()==='') session_start();

/*******************/

//connect to database
include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    //prepare statement
    $stmt = $connection->prepare("CALL institution_human_update (?, ?, ?);");
    $stmt->bind_param("ssi", $human_name, $human_desc, $human_id);

    //set parameters
    $human_name = $_POST["humanname"];
    $human_desc = $_POST["humandesc"];
    $human_id = intval(str_replace(" ", "", $_POST["humanid"]));

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/admin/func/setup_human.php");

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Nastavnik/Student je uređen.";

    header("Location: /app/admin/func/setup_human.php");

    exit;

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            if (!empty($_GET["humanid"])) {

                //prepare statement
                $stmt = $connection->prepare("CALL institution_human_select (?);");
                $stmt->bind_param("i", $human_id);

                //set parameters
                $human_id = intval(str_replace(" ", "", $_GET["humanid"]));

                //initialize varables
                $human_name = '';
                $human_desc = '';

                //execute statement
                if(!$stmt->execute()) {

                    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                    $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                    header("Location: /app/admin/func/setup_human.php");

                    exit;

                }

                //getting result
                $result = $stmt->get_result();

                //fetching data
                while ($row = mysqli_fetch_array($result)) {
                    $human_name = $row["name"];
                    $human_desc = $row["description"];
                }

                //close database conn
                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                /*******************/

                include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

                echo '
                    <section class="content-left flt-left">
                        <p class="section-title">Uređivanje nastavnika/studenta institucije</p>

                        <form action="#" method="post">
                            <table>
                                <tr>
                                    <td>Naslov</td>
                                    <td><input type="text" name="humanname" value="'.$human_name.'"></td>
                                </tr>
                                <tr>
                                    <td>Opis</td>
                                    <td><input type="text" name="humandesc" value="'.$human_desc.'"></td>
                                </tr>
                            </table>

                            <table class="setup-navigator" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <a href="/app/admin/func/setup_human.php">
                                            <input class="navbttn-left" type="button" value="<< Nazad">
                                        </a>
                                    </td>
                                    <td>
                                        <input type="hidden" name="humanid" value="'.$human_id.'">
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