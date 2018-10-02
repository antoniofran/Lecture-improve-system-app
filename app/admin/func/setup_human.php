<?php

if (session_id()==='') session_start();

/*******************/

//connect to database
include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    $act_hmn_arr = array();
    $iact_hmn_arr = array();

    /*********************/

    foreach( $_POST as $stuff => $val ) {
        if ($val === 'active') {
            array_push($act_hmn_arr, intval(substr($stuff, 11))); //intval prevents "sql injection"
        }
        else if ($val === 'inactive') {
            array_push($iact_hmn_arr, intval(substr($stuff, 11))); //intval prevents "sql injection"
        }
    }

    /*********************/

    $is_success = 1;

    mysqli_autocommit($connection, FALSE);

    if (!empty($act_hmn_arr)) {

        $act_hmn_qry = 'UPDATE institution_human SET active = 1 WHERE human_id IN ('.implode (',',$act_hmn_arr).');';

        if ($connection->query($act_hmn_qry) === FALSE) {
            $is_success = 0;
        }

    }

    if (!empty($iact_hmn_arr)) {

        $iact_hmn_qry = 'UPDATE institution_human SET active = 0 WHERE human_id IN ('.implode (',',$iact_hmn_arr).');';

        if ($connection->query($iact_hmn_qry) === FALSE) {
            $is_success = 0;
        }

    }

    mysqli_commit($connection);

    /*********************/

    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    /*********************/

    if ($is_success === 0) {

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/admin/func/setup_human.php");

        exit;

    }

    /*********************/

    $_SESSION["site_notice"] = "Nastavnici/Studenti su ažurirani.";

    header("Location: /app/admin/func/setup_system.php");

    exit;

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            $result = mysqli_query($connection, "CALL institution_human_query ();");

            if($result === false) {

                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                header("Location: /app/admin/func/setup_course.php");

                exit;

            }

            /****************/

            include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

            echo '
                <section class="content-left flt-left">

                    <p class="section-title">Podešavanje nastavnika/studenata institucije</p>

                    <a href="/app/admin/form/human_add.php">Dodaj novog nastavnika/studenta ...</a>

                    <form action="#" method="post">
                        <table class="config-list">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>Ime</td>
                                    <td>Opis</td>
                                    <td>Status</td>
                                    <td>Uredi</td>
                                </tr>
                            </thead>
            ';

            echo '<tbody>';

            while ($row = mysqli_fetch_array($result)) {

                echo '<tr>';

                echo '<td>'.$row["human_id"].'</td>';
                echo '<td>'.$row["name"].'</td>';
                echo '<td>'.$row["description"].'</td>';

                if ( intval(str_replace(" ", "", $row["active"])) === 1 ) {
                    echo'
                        <td>
                            <select name="humanstatus'.$row["human_id"].'">
                                <option value="active" selected>aktivan</option>
                                <option value="inactive">neaktivan</option>
                            </select>
                        </td>
                    ';
                }

                else {
                    echo'
                        <td>
                            <select name="humanstatus'.$row["human_id"].'">
                                <option value="active">aktivan</option>
                                <option value="inactive" selected>neaktivan</option>
                            </select>
                        </td>
                    ';
                }

                echo '
                    <td>
                        <a href="/app/admin/form/human_edit.php?humanid='.$row["human_id"].'">podaci</a>
                        <span>&bull;<span>
                        <a href="/app/admin/form/human_musr_list.php?humanid='.$row["human_id"].'&humanname='.$row["name"].'">računi</a>
                        <span>&bull;<span>
                        <a href="/app/admin/form/human_mcrs_list.php?humanid='.$row["human_id"].'&humanname='.$row["name"].'">kolegiji</a>
                    </td>
                ';

                echo '</tr>';

            }

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            echo '</tbody>';

            echo '
                        </table>

                        <table class="setup-navigator" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <a href="/app/admin/func/setup_course.php">
                                        <input class="navbttn-left" type="button" value="<< Nazad">
                                    </a>
                                </td>
                                <td>
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