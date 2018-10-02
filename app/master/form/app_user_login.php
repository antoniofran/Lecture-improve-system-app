<?php

if (session_id()==='') session_start();

/*******************/

include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    //prepare statement
    $stmt = $connection->prepare("CALL application_user_check_login (?);");
    $stmt->bind_param("s", $user_name);

    //set parameters
    $user_name = $_POST["username"];

    //initialize varables
    $user_pass = $_POST["password"];

    $user_human_id = null;
    $user_human_name = '';
    $user_pass_hash = '';
    $user_role_name = '';
    $user_active = 0;

    //execute stmt
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /index.php");

        exit;

    }

    //getting result
    $result = $stmt->get_result();

    //fetching data
    while ($row = mysqli_fetch_array($result)) {
        $user_human_id = intval(str_replace(" ", "", $row["user_human_id"]));
        $user_human_name = $row["user_human_name"];
        $user_pass_hash = $row["user_pass_hash"];
        $user_role_name = $row["user_role_name"];
        $user_active = intval(str_replace(" ", "", $row["user_is_active"]));
    }

    //closing connection
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    //processing login data
    if (!password_verify($user_pass,$user_pass_hash)) {

        $_SESSION["site_error"] = "Nesipravni korisnički podaci!";

        header("Location: /index.php");

        exit;

    }

    else if ($user_active !== 1) {

        $_SESSION["site_error"] = "Korisnički račun je neaktivan!";

        header("Location: /index.php");

        exit;

    }

    else {

        $_SESSION["site_notice"] = "Korisnik je prijavljen!";

        $_SESSION["curr_human_id"] = $user_human_id;
        $_SESSION["curr_human_name"] = $user_human_name;
        $_SESSION["curr_role_name"] = strtolower($user_role_name);

        if ($_SESSION["curr_role_name"] === 'admin') {
            header("Location: /app/admin/index.php");
        }
        else {
            header("Location: /app/system/index.php");
        }

        exit;

    }

}

else {

    //checking if already logged in
    if (isset($_SESSION["curr_role_name"])) {

        if ($_SESSION["curr_role_name"] === 'admin') {
            header("Location: /app/admin/index.php");
        }
        else {
            header("Location: /app/system/index.php");
        }

        exit;

    }

    /*********************/

    //create variables
    $_SESSION["admin_exist"] = 1;

    //run the store proc
    $result = mysqli_query($connection, "CALL application_user_check_initial ();");

    //check for failure
    if($result === false) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /index.php");

        exit;

    }
    
    //loop the result set
    while ($row = mysqli_fetch_array($result)) {
    
        $_SESSION["admin_exist"] = intval(str_replace(" ", "", $row["admin_exist"]));
    
    }

    //close database conn
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    /*********************/

    echo '
        <section class="content-left flt-left">

            <p class="section-title">Prijava postojećeg korisnika</p>
    ';

    if ($_SESSION["admin_exist"] === 0) {

        echo '<a href="/app/master/form/app_user_initial.php">Izradite početni admin korisnički račun ...</a>';

    }

    echo '
            <form action="/app/master/form/app_user_login.php" method="post">

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
                            &nbsp;
                        </td>
                        <td>
                            <input class="navbttn-right" type="submit" name="submit" value="Prijava >>">
                        </td>
                    </tr>
                </table>
            </form>

        </section>
    ';

}

?>