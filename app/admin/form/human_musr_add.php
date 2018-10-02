<?php

if (session_id()==='') session_start();

/*******************/

//connect to database
include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

/*******************/

if (isset($_POST["submit"])) {

    //prepare statement
    $stmt = $connection->prepare("CALL application_user_create (?, ?, ?, ?);");
    $stmt->bind_param("ssii", $user_name, $user_pass_hash, $user_role_id, $user_human_id);

    //set parameters
    $user_name = $_POST["username"];
    $user_pass_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $user_role_id = intval(str_replace(" ", "", $_POST["userroleid"]));
    $user_human_id = intval(str_replace(" ", "", $_POST["humanid"]));

    //set variables
    $user_human_name = $_POST["humanname"];

    //execute statement
    if(!$stmt->execute()) {

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

        header("Location: /app/admin/form/human_musr_list.php?humanid=".$user_human_id."&humanname=".$user_human_name);

        exit;

    }

    //confirm message
    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

    $_SESSION["site_notice"] = "Korisnički račun nastavnika/studenta je izrađen.";

    header("Location: /app/admin/form/human_musr_list.php?humanid=".$user_human_id."&humanname=".$user_human_name);

    exit;

}

else {

    if (isset($_SESSION["span_open_exist"])) {

        if ($_SESSION["span_open_exist"] === 0) {

            if (!empty($_GET["humanid"]) and !empty($_GET["humanname"])) {

                //getting list of all user roles
                $result = mysqli_query($connection, "CALL application_user_role_query ();");

                //checking if mysqli_query failed
                if($result === false) {

                    include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                    $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                    header("Location: /app/admin/form/human_musr_list.php?humanid=".$human_id."&humanname=".$human_name);

                    exit;

                }

                //getting required additional date
                $human_id = intval(str_replace(" ", "", $_GET["humanid"]));
                $human_name = $_GET["humanname"];

                /*******************/

                include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

                echo '
                    <section class="content-left flt-left">

                        <p class="section-title">Izrađivanje korisničkog računa nastavnika/studenta "'.$human_name.'"</p>

                        <form action="#" method="post">

                            <table class="config-list">
                                <tr>
                                    <td>Korisničko ime</td>
                                    <td>
                                        <input type="text" name="username">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lozinka</td>
                                    <td>
                                        <input type="password" name="password">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Uloga</td>
                                    <td>
                                        <select name="userroleid">
                ';

                while ($row = mysqli_fetch_array($result)) {

                    echo '
                        <option value="'.$row["role_id"].'">'.$row["name"].'</option>
                    ';

                }

                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                echo'
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <table class="setup-navigator" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <a href="/app/admin/form/human_musr_list.php?humanid='.$human_id.'&humanname='.$human_name.'">
                                            <input class="navbttn-left" type="button" value="<< Nazad">
                                        </a>
                                    </td>
                                    <td>
                                        <input type="hidden" name="humanid" value="'.$human_id.'">
                                        <input type="hidden" name="humanname" value="'.$human_name.'">
                                        <input class="navbttn-right" type="submit" name="submit" value="Izradi >>">
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