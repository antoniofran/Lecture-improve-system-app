<?php

    if (session_id()==='') session_start();

    /*******************/

    if (isset($_SESSION["span_open_exist"])) {

        //updating systemtype
        if (isset($_SESSION["slctd_span_code"])) {

            if ($_SESSION["slctd_span_code"] === '_') {
                $_SESSION["slctd_span_id"] = $_SESSION["span_id_max"];
                $_SESSION["curr_sys_type"] = $_SESSION["curr_role_name"];
            }
            else {
                $_SESSION["slctd_span_id"] = intval(str_replace(" ", "", $_SESSION["slctd_span_code"]));
                $_SESSION["curr_sys_type"] = 'archive';
            }

        }

        else {

            if ($_SESSION["span_open_exist"] === 1) {
                $_SESSION["slctd_span_id"] = $_SESSION["span_id_max"];
                $_SESSION["curr_sys_type"] = $_SESSION["curr_role_name"];
            }
            else {
                $_SESSION["slctd_span_id"] = $_SESSION["span_id_max"];
                $_SESSION["curr_sys_type"] = 'archive';
            }

        }

        /*******************/

        //database connection
        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

        //prepare statement
        $stmt = $connection->prepare("CALL survey_span_nav_query (?, ?, ?);");
        $stmt->bind_param("iii", $human_id, $span_id, $is_archive);

        //set parameters
        $human_id = $_SESSION["curr_human_id"];
        $span_id = $_SESSION["slctd_span_id"];
        $is_archive = ($_SESSION["curr_sys_type"] === 'archive') ? 1 : 0;

        //execute statement
        if(!$stmt->execute()) {

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

            header("Location: /app/system/index.php");

            exit;

        }

        //getting result
        $result = $stmt->get_result();

        //initiate checks
        $rslt_crs_srv_ids = array();
        $rslt_crs_fdb_ids = array();

        /*******************/

        echo '
            <section class="content-left flt-left">

                <p class="section-title">
        ';

        if ($_SESSION["curr_sys_type"] === 'student') {
            echo 'Navigacija anketnog razdoblja za studenta';
        } else if ($_SESSION["curr_sys_type"] === 'teacher') {
            echo 'Navigacija anketnog razdoblja za nastavnika';
        } else if ($_SESSION["curr_sys_type"] === 'preview') {
            echo 'Navigacija anketnog razdoblja kao pregled';
        } else {
            echo 'Navigacija anketnog razdoblja iz arhive';
        }

        echo '
                </p>

                <table class="config-list">

                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Naziv</td>
                            <td>Opis</td>
                            <td>Radnja</td>
                        </tr>
                    </thead>
        ';

        echo '<tbody>';

        while ($row = mysqli_fetch_array($result)) {

            echo '<tr>';

            echo '<td>'.$row["course_id"].'</td>';
            echo '<td>'.$row["name"].'</td>';
            echo '<td>'.$row["description"].'</td>';

            if ($_SESSION["curr_sys_type"] === 'archive' and $_SESSION["curr_role_name"] === 'student' and empty($row["check_id"])) {

                //student ne može pregledati rezultate ankete koju nije ispunio

                array_push($rslt_crs_fdb_ids, intval(str_replace(" ", "", $row["course_id"])));

                echo '
                    <td>
                        <a href="/app/system/index.php?functype=feedback&courseid='.$row["course_id"].'&coursename='.$row["name"].'">razgovor</a>
                    </td>
                ';

            }

            else if ($_SESSION["curr_sys_type"] === 'student' and !empty($row["check_id"])) {

                //student ne može iznova ispuniti anketu koju je već ispunio

                array_push($rslt_crs_fdb_ids, intval(str_replace(" ", "", $row["course_id"])));

                echo '
                    <td>
                        <a href="/app/system/index.php?functype=feedback&courseid='.$row["course_id"].'&coursename='.$row["name"].'">razgovor</a>
                    </td>
                ';

            }

            else {

                array_push($rslt_crs_srv_ids, intval(str_replace(" ", "", $row["course_id"])));
                array_push($rslt_crs_fdb_ids, intval(str_replace(" ", "", $row["course_id"])));

                echo '
                    <td>
                        <a href="/app/system/index.php?functype=survey&courseid='.$row["course_id"].'&coursename='.$row["name"].'">upitnik</a>
                        <span>&bull;<span>
                        <a href="/app/system/index.php?functype=feedback&courseid='.$row["course_id"].'&coursename='.$row["name"].'">razgovor</a>
                    </td>
                ';

            }

            echo '</tr>';

        }

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        echo '</tbody>';

        echo '
                </table>

            </section>
        ';

    }

    else {

        $_SESSION["site_error"] = "Pristup traženom dijelu web aplikacije je odbijen!";

        header("Location: /index.php");

        exit;

    }

?>