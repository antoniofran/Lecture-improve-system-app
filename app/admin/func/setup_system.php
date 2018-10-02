<?php

if (session_id()==='') session_start();

/*****************/

if (isset($_SESSION["span_open_exist"])) {

    if ($_SESSION["span_open_exist"] === 0) {

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

        echo '
            <section class="content-left flt-left">

                <p class="section-title">Završetak podešavanja anketnog razdoblja</p>

                <span>Novo anketno razdoblje može započeti, pristnite gumb "Započni&nbsp;>>" ako imate namjeru započeti novo anketno razdoblje.</span><br>

                <table class="setup-navigator" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <a href="/app/admin/func/setup_human.php">
                                <input class="navbttn-left" type="button" value="<< Nazad">
                            </a>
                        </td>
                        <td>
                            <a href="/app/admin/actn/span_open.php" onclick="return confirm(\'Započinjete anketno razdoblje, time onemogućujete daljnju izmjenu kolegija i nastavnika/studenata dok je anketno razdoblje u tijeku. Želite li nastaviti?\')">
                                <input class="navbttn-right" type="button" value="Započni >>">
                            </a>
                        </td>
                    </tr>
                </table>

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

?>