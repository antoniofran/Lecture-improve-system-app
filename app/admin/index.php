<?php

    if (session_id()==='') session_start();

    /*******************/

    if (isset($_SESSION["curr_role_name"])) {

        if ($_SESSION["curr_role_name"] !== 'admin') {

            header("Location: /app/system/index.php");

            exit;

        }

        /*******************/

        //create variables
        $_SESSION["span_open_exist"] = 1;

        //connect to database
        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

        //run the store proc
        $result = mysqli_query($connection, "CALL survey_span_open_check_exist ();");

        //check for failure
        if($result === false) {

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

            header("Location: /app/admin/index.php");

            exit;

        }

        //loop the result set
        while ($row = mysqli_fetch_array($result)) {

            $_SESSION["span_open_exist"] = intval(str_replace(" ", "", $row["span_open_exist"]));

        }

        //close database conn
        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        /*******************/

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

        if ($_SESSION["span_open_exist"] === 0) {

            echo '
                <section class="content-left flt-left">

                    <p class="section-title">Početak podešavanja anketnog razdoblja</p>

                    <span>Dobrodošli u admin korisničko sučelje, pristnite gumb "Podesi&nbsp;>>" ako imate namjeru podesiti novo anketno razdoblje.</span><br>

                    <table class="setup-navigator" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                <a href="/app/admin/func/setup_course.php">
                                    <input class="navbttn-right" type="button" value="Podesi >>">
                                </a>
                            </td>
                        </tr>
                    </table>

                </section>
            ';

        }

        else {

            echo '
                <section class="content-left flt-left">

                    <p class="section-title">Početak podešavanja anketnog razdoblja</p>

                    <span>Dobrodošli u admin korisničko sučelje, pristnite gumb "<<&nbsp;Zaustavi" ako imate namjeru zaustaviti trenutno anketno razdoblje.</span><br>

                    <table class="setup-navigator" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <a href="/app/admin/actn/span_close.php" onclick="return confirm(\'Zaustavljate anketno razdoblje u tijeku, time onemogućujete daljnju ispunu upitnika uz razgovor za anketno razdoblje u tijeku. Želite li nastaviti?\')">
                                    <input class="navbttn-left" type="button" value="<< Zaustavi">
                                </a>
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                    </table>

                </section>
            ';

        }

        include_once($_SERVER['DOCUMENT_ROOT']."/app/admin/actn/setup_navigate.php");

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_footer.php");

    }

    else {

        $_SESSION["site_error"] = "Pristup traženom dijelu web aplikacije je odbijen!";

        header("Location: /index.php");

        exit;

    }

?>