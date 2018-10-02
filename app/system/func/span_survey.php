<?php

if (session_id()==='') session_start();

/*******************/

if (isset($rslt_crs_srv_ids)) {

    if (!empty($rslt_crs_srv_ids)) {

        //connect to database
        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

        /*********************/

        if (isset($_POST["submit"])) {

            $grd_one_arr = array();
            $grd_two_arr = array();
            $grd_three_arr = array();
            $grd_four_arr = array();
            $grd_five_arr = array();

            /*********************/

            foreach( $_POST as $stuff => $val ) {

                if ($stuff !== "submit") {

                    $result_id_sent = intval(substr($stuff, 9)); //intval prevents "sql injection"

                    $grade_sent = intval(str_replace(" ", "", $val)); //intval prevents "sql injection"

                    $grade_used = intval( floor( (floatval($grade_sent) + 50.0) / 20.0 ) + 1 );

                    if ($grade_used === 1) array_push($grd_one_arr, $result_id_sent);
                    else if ($grade_used === 2) array_push($grd_two_arr, $result_id_sent);
                    else if ($grade_used === 3) array_push($grd_three_arr, $result_id_sent);
                    else if ($grade_used === 4) array_push($grd_four_arr, $result_id_sent);
                    else if ($grade_used === 5) array_push($grd_five_arr, $result_id_sent);

                }

            }

            /*********************/

            $is_success = 1;

            mysqli_autocommit($connection, FALSE);

            if (!empty($grd_one_arr)) {

                $query_str = 'UPDATE survey_result SET grade_sum = grade_sum + 1, grade_count = grade_count + 1  WHERE result_id IN ('.implode (',',$grd_one_arr).');';

                if ($connection->query($query_str) === FALSE) {
                    $is_success = 0;
                }

            }

            if (!empty($grd_two_arr)) {

                $query_str = 'UPDATE survey_result SET grade_sum = grade_sum + 2, grade_count = grade_count + 1  WHERE result_id IN ('.implode (',',$grd_two_arr).');';

                if ($connection->query($query_str) === FALSE) {
                    $is_success = 0;
                }

            }

            if (!empty($grd_three_arr)) {

                $query_str = 'UPDATE survey_result SET grade_sum = grade_sum + 3, grade_count = grade_count + 1  WHERE result_id IN ('.implode (',',$grd_three_arr).');';

                if ($connection->query($query_str) === FALSE) {
                    $is_success = 0;
                }

            }

            if (!empty($grd_four_arr)) {

                $query_str = 'UPDATE survey_result SET grade_sum = grade_sum + 4, grade_count = grade_count + 1  WHERE result_id IN ('.implode (',',$grd_four_arr).');';

                if ($connection->query($query_str) === FALSE) {
                    $is_success = 0;
                }

            }

            if (!empty($grd_five_arr)) {

                $query_str = 'UPDATE survey_result SET grade_sum = grade_sum + 5, grade_count = grade_count + 1  WHERE result_id IN ('.implode (',',$grd_five_arr).');';

                if ($connection->query($query_str) === FALSE) {
                    $is_success = 0;
                }

            }

            mysqli_commit($connection);

            /*********************/

            //prepare statement
            $stmt = $connection->prepare("INSERT INTO survey_result_check(course_id,human_id,span_id) VALUES(?,?,?);");
            $stmt->bind_param("iii", $course_id, $human_id, $span_id);

            //set parameters
            $course_id = $_SESSION["temp_course_id"];
            $human_id = $_SESSION["curr_human_id"];
            $span_id = $_SESSION["slctd_span_id"];

            //execute statement
            if(!$stmt->execute()) {
                $is_success = 0;
            }

            //commit statement
            mysqli_commit($connection);

            /*********************/

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            /*********************/

            if ($is_success === 0) {

                $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                header("Location: /app/system/index.php");

                exit;

            }

            /*********************/

            $_SESSION["site_notice"] = "Unesene ocjene su pohranjene.";

            header("Location: /app/system/index.php");

            exit;

        }

        else {

            //prepare statement
            $stmt = $connection->prepare("CALL survey_result_query (?, ?);");
            $stmt->bind_param("ii", $course_id, $span_id);

            //set parameters
            $course_id = $_SESSION["temp_course_id"];
            $span_id = $_SESSION["slctd_span_id"];

            //execute statement
            if(!$stmt->execute()) {

                include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

                $_SESSION["site_error"] = "Rad s bazom podataka nije prošao uredno!";

                header("Location: /app/system/index.php");

                exit;

            }

            //getting result
            $result = $stmt->get_result();

            /*****************/

            echo '
                <section class="content-right flt-right">

                    <p class="section-title">
            ';

            if ($_SESSION["curr_sys_type"] === 'student') {
                echo 'Ispuna anketnih pitanja za kolegij "'.$_SESSION["temp_course_name"].'"';
            } else if ($_SESSION["curr_sys_type"] === 'teacher') {
                echo 'Pregled trenutnih ocjena za kolegij "'.$_SESSION["temp_course_name"].'"';
            } else if ($_SESSION["curr_sys_type"] === 'preview') {
                echo 'Pregled anketnih pitanja za kolegij "'.$_SESSION["temp_course_name"].'"';
            } else {
                echo 'Pregled arhiviranih ocjena za kolegij "'.$_SESSION["temp_course_name"].'"';
            }

            echo '
                    </p>
            ';

            /*******************/

            if ($_SESSION["curr_sys_type"] === 'student') {

                echo '
                    <form action="#" method="post">
                ';

            }

            /*******************/

            $elem_type_code = '~';

            /*******************/

            while ($row = mysqli_fetch_array($result)) {

                if ($elem_type_code !== $row["type_id"]) {

                    if ($elem_type_code !== '~') {
                        echo '
                                </tbody>

                            </table>
                        ';
                    }

                    /***********/

                    if ($_SESSION["curr_sys_type"] === 'student' or $_SESSION["curr_sys_type"] === 'preview') {

                        echo '
                            <p class="section-title-sub">'.$row["input_title"].'</p>
                        ';

                    }
                    else {

                        echo '
                            <p class="section-title-sub">'.$row["output_title"].'</p>
                        ';

                    }

                    /***********/

                    echo '
                        <table class="survey-table" cellspacing="0" cellpadding="2" border="1">

                            <tbody>
                    ';

                    /***********/

                    $elem_type_code = $row["type_id"];

                }

                /***************/

                echo '<tr>';

                if ($_SESSION["curr_sys_type"] === 'student' or $_SESSION["curr_sys_type"] === 'preview') {

                    echo '<td>'.$row["input_text"].'</td>';

                    echo '
                        <td>

                            <table>
                                <tr>
                                    <td><img class="grade-select" src="/img/emoji/angry.png"></td>
                                    <td><input class="grade-select" name="rsltgrade'.$row["result_id"].'" type="range" min="-49" max="49" step="1" value="0"></td>
                                    <td><img class="grade-select" src="/img/emoji/happy.png"></td>
                                </tr>
                            </table>

                        </td>
                    ';

                }

                else {

                    $curr_grade_sum = floatval(str_replace(" ", "", $row["grade_sum"]));

                    $curr_grade_count = floatval(str_replace(" ", "", $row["grade_count"]));

                    if ( $curr_grade_sum > 0.0 and $curr_grade_count > 0.0 ) {
                        $curr_grade_avg = ($curr_grade_sum / $curr_grade_count / 5.0) * 100.0;
                    }
                    else {
                        $curr_grade_avg = 0.0;
                    }

                    /***********/

                    echo '<td>'.$row["output_text"].'</td>';

                    echo '<td>Ocjenjeno s "'.round($curr_grade_avg,2).'%".</td>';

                }

                echo '</tr>';

            }

            /*******************/

            echo '
                    </tbody>

                </table>
            ';

            /*******************/

            include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

            /*******************/

            if ($_SESSION["curr_sys_type"] === 'student' or $_SESSION["curr_sys_type"] === 'preview') {

                echo '
                    <table class="setup-navigator" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                ';

                if ($_SESSION["curr_sys_type"] === 'student') {

                    echo '
                        <input class="navbttn-right" type="submit" name="submit" value="Pošalji >>">
                    ';

                }

                else {

                    echo '
                        <input class="navbttn-right disabled-ui" type="button" value="Pošalji >>">
                    ';

                }

                echo '
                            </td>
                        </tr>
                    </table>
                ';

            }

            /*******************/

            if ($_SESSION["curr_sys_type"] === 'student') {

                echo '
                    </form>
                ';

            }

            /*******************/

            echo '
                </section>
            ';

        }

    }

    else {

        $_SESSION["site_error"] = "Nema kolegija kojem možete ispuniti anketu!";

        header("Location: /app/system/index.php");

        exit;

    }

}

else {

    $_SESSION["site_error"] = "Pristup traženom dijelu web aplikacije je odbijen!";

    header("Location: /index.php");

    exit;

}

?>