<?php

if (session_id()==='') session_start();

/*******************/

echo '<div class="archive flt-left">';

if (isset($_SESSION["curr_role_name"])) {

    if ($_SESSION["curr_role_name"] === 'admin') {

        if ($_SESSION["span_open_exist"] === 0) {

            echo '
                <a href="/app/admin/actn/span_open.php" onclick="return confirm(\'Započinjete anketno razdoblje, time onemogućujete daljnju izmjenu kolegija i nastavnika/studenata dok je anketno razdoblje u tijeku. Želite li nastaviti?\')">
                    Započni ankento razdoblje ...
                </a>
            ';

        }

        else {

            echo '
                <a href="/app/admin/actn/span_close.php" onclick="return confirm(\'Zaustavljate anketno razdoblje u tijeku, time onemogućujete daljnju ispunu upitnika uz razgovor za anketno razdoblje u tijeku. Želite li nastaviti?\')">
                    Zaustavi anketno razdoblje ...
                </a>
            ';

        }

    }

    else {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

        /**************/

        $result = mysqli_query($connection, "CALL survey_span_query ();");

        if($result === false) {

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

            header("Location: /app/system/index.php");

            exit;

        }

        /**************/

        echo '
            <form action="/app/system/index.php" method="post">

                <select id="span-select" name="spancode" onchange="this.form.submit()">
        ';

        $curr_row_num = 0;

        while ($row = mysqli_fetch_array($result)) {

            $this_span_code = '_';

            /**************/

            if (!empty($row["date_finished"])) {
                $this_span_code = $row["span_id"];
            }

            /**************/

            if (isset($_SESSION["slctd_span_code"])) {

                if ($_SESSION["slctd_span_code"] === $this_span_code) {
                    echo '
                        <option value="'.$this_span_code.'" selected>'.$row["date_started"].' – '.$row["date_finished"].'</option>
                    ';
                }
                else {
                    echo '
                        <option value="'.$this_span_code.'">'.$row["date_started"].' – '.$row["date_finished"].'</option>
                    ';
                }

            }

            else {

                if ($curr_row_num === 0) {
                    echo '
                        <option value="'.$this_span_code.'" selected>'.$row["date_started"].' – '.$row["date_finished"].'</option>
                    ';
                }
                else {
                    echo '
                        <option value="'.$this_span_code.'">'.$row["date_started"].' – '.$row["date_finished"].'</option>
                    ';
                }

            }

            /**************/

            $curr_row_num += 1;

        }

        echo '
                </select>

            </form>
        ';

        /**************/

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    }

}

else {

    if ($_SESSION["action_slctd"] === 'info') {

        echo '<a href="/index.php">Povratak u web aplikaciju sustava ...</a>';

    }

    else {

        echo '<a href="/index.php?action=info">Informacije o autoru i sustavu ...</a>';

    }

}

echo '</div>';

?>