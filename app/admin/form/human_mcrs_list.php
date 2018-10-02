<?php

if (session_id()==='') session_start();

/*******************/

//connect to database
include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    $act_mcrs_arr = array();
    $iact_mcrs_arr = array();

    /*********************/

    foreach( $_POST as $stuff => $val ) {
        if ($val === 'active') {
            array_push($act_mcrs_arr, intval(substr($stuff, 12)));
        }
        else if ($val === 'inactive') {
            array_push($iact_mcrs_arr, intval(substr($stuff, 12)));
        }
    }

    /*********************/

    $is_success = 1;

    if (!empty($act_mcrs_arr)) {

        $act_mcrs_qry = 'UPDATE institution_mapper SET active = 1 WHERE human_id = '.$_POST["humanid"].' AND course_id IN ('.implode (',',$act_mcrs_arr).');';

        if ($connection->query($act_mcrs_qry) === FALSE) {
            $is_success = 0;
        }

    }

    if (!empty($iact_mcrs_arr)) {

        $iact_mcrs_qry = 'UPDATE institution_mapper SET active = 0 WHERE human_id = '.$_POST["humanid"].' AND course_id IN ('.implode (',',$iact_mcrs_arr).');';

        if ($connection->query($iact_mcrs_qry) === FALSE) {
            $is_success = 0;
        }

    }

    /*********************/

    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    /*********************/

    if ($is_success === 1) {

        $_SESSION["site_notice"] = "Kolegiji nastavnika/studenta su ažurirani.";

        header("Location: /app/admin/func/setup_human.php");

        exit;

    } else {

        $_SESSION["site_error"] = "Kolegiji studenta/natavnika nisu ažurirani.";

        header("Location: /app/admin/form/human_mcrs_list.php?humanid=".$_POST["humanid"]."&humanname=".$_POST["humanname"]);

        exit;

    }

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            if (!empty($_GET["humanid"]) and !empty($_GET["humanname"])) {

                //prepare statement
                $stmt = $connection->prepare("CALL institution_mapper_query (?);");
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

                /*******************/

                include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

                echo '
                    <section class="content-left flt-left">

                        <p class="section-title">Popis kolegija pridruženih nastavniku/studentu "'.$human_name.'"</p>

                        <a href="/app/admin/form/human_mcrs_add.php?humanid='.$human_id.'&humanname='.$human_name.'">Pridruži novi kolegij nastavniku/studentu "'.$human_name.'" ...</a>

                        <form action="#" method="post">
                            <table class="config-list">
                                <thead>
                                    <tr>
                                        <td>ID</td>
                                        <td>Ime</td>
                                        <td>Opis</td>
                                        <td>Status</td>
                                    </tr>
                                </thead>
                ';

                echo '<tbody>';

                while ($row = mysqli_fetch_array($result)) {

                    echo '<tr>';

                    echo '<td>'.$row["course_id"].'</td>';
                    echo '<td>'.$row["name"].'</td>';
                    echo '<td>'.$row["description"].'</td>';

                    if ( intval(str_replace(" ", "", $row["active"])) === 1 ) {
                        echo'
                            <td>
                                <select name="coursestatus'.$row["course_id"].'">
                                    <option value="active" selected>aktivno</option>
                                    <option value="inactive">neaktivno</option>
                                </select>
                            </td>
                        ';
                    }

                    else {
                        echo'
                            <td>
                                <select name="coursestatus'.$row["course_id"].'">
                                    <option value="active">aktivno</option>
                                    <option value="inactive" selected>neaktivno</option>
                                </select>
                            </td>
                        ';
                    }

                    echo '</tr>';

                }

                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                echo '</tbody>';

                echo '
                            </table>

                            <table class="setup-navigator" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <a href="/app/admin/func/setup_human.php">
                                            <input class="navbttn-left" type="button" value="<< Nazad">
                                        </a>
                                    </td>
                                    <td>
                                        <input type="hidden" name="humanid" value="' .$human_id.'">
                                        <input type="hidden" name="humanname" value="'.$human_name.'">
                                        <input class="navbttn-right" type="submit" name="submit" value="Primjeni >>">
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