<?php

if (session_id()==='') session_start();

/*******************/

//connect to database
include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    $act_musr_arr = array();
    $iact_musr_arr = array();

    /*********************/

    foreach( $_POST as $stuff => $val ) {
        if ($val === 'active') {
            array_push($act_musr_arr, intval(substr($stuff, 10)));
        }
        else if ($val === 'inactive') {
            array_push($iact_musr_arr, intval(substr($stuff, 10)));
        }
    }

    /*********************/

    $is_success = 1;

    if (!empty($act_musr_arr)) {

        $act_musr_qry = 'UPDATE application_user SET active = 1 WHERE human_id = '.$_POST["humanid"].' AND user_id IN ('.implode (',',$act_musr_arr).');';

        if ($connection->query($act_musr_qry) === FALSE) {
            $is_success = 0;
        }

    }

    if (!empty($iact_musr_arr)) {

        $iact_musr_qry = 'UPDATE application_user SET active = 0 WHERE human_id = '.$_POST["humanid"].' AND user_id IN ('.implode (',',$iact_musr_arr).');';

        if ($connection->query($iact_musr_qry) === FALSE) {
            $is_success = 0;
        }

    }

    /*********************/

    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    /*********************/

    if ($is_success === 1) {

        $_SESSION["site_notice"] = "Računi nastavnika/studenta su ažurirani.";

        header("Location: /app/admin/func/setup_human.php");

        exit;

    } else {

        $_SESSION["site_error"] = "Računi nastavnika/studenta nisu ažurirani.";

        header("Location: /app/admin/form/human_musr_list.php?humanid=".$_POST["humanid"]."&humanname=".$_POST["humanname"]);

        exit;

    }

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            if (!empty($_GET["humanid"]) and !empty($_GET["humanname"])) {

                //prepare statement
                $stmt = $connection->prepare("CALL application_user_query (?);");
                $stmt->bind_param("i", $human_id);

                //set parameters
                $human_id = intval(str_replace(" ", "", $_GET["humanid"]));

                //store varables
                $human_name = $_GET["humanname"];

                //execute statement
                if(!$stmt->execute()) {

                    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                    $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                    header("Location: /app/admin/form/human_musr_list.php?humanid=".$human_id."&humanname=".$human_name);

                    exit;

                }

                //getting result
                $result = $stmt->get_result();

                /*******************/

                include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

                echo '
                    <section class="content-left flt-left">

                        <p class="section-title">Popis korisničkih računa nastavnika/studenta "'.$human_name.'"</p>

                        <a href="/app/admin/form/human_musr_add.php?humanid='.$human_id.'&humanname='.$human_name.'"">Izradi novi račun nastavnika/studenta "'.$human_name.'" ...</a>

                        <form action="#" method="post">
                            <table class="config-list">
                                <thead>
                                    <tr>
                                        <td>ID</td>
                                        <td>Naziv</td>
                                        <td>Uloga</td>
                                        <td>Status</td>
                                    </tr>
                                </thead>
                ';

                echo '<tbody>';

                while ($row = mysqli_fetch_array($result)) {

                    echo '<tr>';

                    echo '<td>'.$row["user_id"].'</td>';
                    echo '<td>'.$row["name"].'</td>';
                    echo '<td>'.strtolower($row["role_name"]).'</td>';

                    if ( intval(str_replace(" ", "", $row["active"])) === 1 ) {
                        echo'
                            <td>
                                <select name="userstatus'.$row["user_id"].'">
                                    <option value="active" selected>aktivan</option>
                                    <option value="inactive">neaktivan</option>
                                </select>
                            </td>
                        ';
                    }

                    else {
                        echo'
                            <td>
                                <select name="userstatus'.$row["user_id"].'">
                                    <option value="active">aktivan</option>
                                    <option value="inactive" selected>neaktivan</option>
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