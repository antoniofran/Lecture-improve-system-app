<?php

    if (session_id()==='') session_start();

    /*****************/

    echo '<div class="welcome flt-right">';

    if (isset($_SESSION["curr_human_name"])) {

        echo '
            <span>Dobrodošli, '.$_SESSION["curr_human_name"].'!</span>
            <a href="/app/master/actn/app_user_logout.php">Odjavi me!</a>
        ';
    }

    else {

        echo '
            <span>Dobrodošli!</span>
        ';

    }

    echo '</div>';

?>