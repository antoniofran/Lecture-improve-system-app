<?php

if (session_id()==='') session_start();

/*******************/

if (isset($_POST["submit"])) {

    //connect to database
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

    //prepare statement
    $stmt = $connection->prepare("CALL application_user_create (?, ?, 1, NULL);");
    $stmt->bind_param("ss", $user_name, $user_pass_hash);

    //set parameters and execute
    $user_name = $_POST["username"];
    $user_pass_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/master/form/user_initial.php");

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Početni admin korisnički račun je izrađen.";

    header("Location: /index.php");

    exit;

}

else {

    if (!isset($_SESSION["admin_exist"])) {
        $_SESSION["admin_exist"] = 1;
    }

    if ($_SESSION["admin_exist"] === 0) {

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

        echo '
            <section class="content-left flt-left">

                <p class="section-title">Izrađivanje početnog admin računa</p>

                <form action="/app/master/form/app_user_initial.php" method="post">

                    <table class="config-list">
                        <tr>
                            <td>Korisničko ime</td>
                            <td><input type="text" name="username"></td>
                        </tr>
                        <tr>
                            <td>Lozinka</td>
                            <td><input type="password" name="password"></td>
                        </tr>
                    </table>

                    <table class="setup-navigator" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <a href="/index.php">
                                    <input class="navbttn-left" type="button" value="<< Nazad">
                                </a>
                            </td>
                            <td>
                                <input class="navbttn-right" type="submit" name="submit" value="Izradi >>">
                            </td>
                        </tr>
                    </table>

                </form>

            </section>
        ';

        include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_footer.php");

    }

    else {

        $_SESSION["site_error"] = "Inicijalni admin korisnički račun već postoji!";

        header("Location: /index.php");

        exit;

    }

}

?>