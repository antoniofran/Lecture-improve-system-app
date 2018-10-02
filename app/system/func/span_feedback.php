<?php

if (session_id()==='') session_start();

/*******************/

if (isset($rslt_crs_fdb_ids)) {

    if (!empty($rslt_crs_fdb_ids)) {

        //connect to database
        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_open.php");

        /*********************/

        //prepare statement
        $stmt = $connection->prepare("CALL survey_feedback_query (?, ?);");
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
            echo 'Upućivanje komentara/pitanja u pogledu kolegija "'.$_SESSION["temp_course_name"].'"';
        } else if ($_SESSION["curr_sys_type"] === 'teacher') {
            echo 'Odgovaranje na komentare/pitanja u pogledu kolegija "'.$_SESSION["temp_course_name"].'"';
        } else if ($_SESSION["curr_sys_type"] === 'preview') {
            echo 'Pregled sučelja za komentare/pitanja u pogledu kolegija "'.$_SESSION["temp_course_name"].'"';
        } else {
            echo 'Pregled arhiviranih komentara/pitanja u pogledu kolegija "'.$_SESSION["temp_course_name"].'"';
        }

        echo '
                </p>
        ';

        /*******************/

        $fdbck_type_code = '~';

        $num_fdbck_used = 0;
        $num_rspnd_used = 0;

        $is_fdbck_open = false;

        /*******************/

        $message_empty_prefix = ($_SESSION["curr_sys_type"] === 'archive') ? 'U arhivi' : 'Trenutno';

        /*******************/

        while ($row = mysqli_fetch_array($result)) {

            $count_fdbck = intval(str_replace(" ", "", $row["count_fdbck"]));
            $count_rspnd = intval(str_replace(" ", "", $row["count_rspnd"]));

            /***************/

            if ($fdbck_type_code !== $row["type_id"]) {

                if ($fdbck_type_code !== '~') {

                    echo '
                        </ul>
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
                    <ul>
                ';

                /***********/

                $fdbck_type_code = $row["type_id"];

                $num_fdbck_used = 0;
                $num_rspnd_used = 0;

                $is_fdbck_open = false;

            }

            /***************/

            if ($num_fdbck_used < $count_fdbck and !$is_fdbck_open) {

                echo '
                    <li>
                ';

                /***********/

                echo '
                    <span>'.$row["input_text"].'</span>
                ';

                /***********/

                echo '
                    <ul>
                ';

                /***********/

                $is_fdbck_open = true;

            }

            /***************/

            if ($is_fdbck_open) {

                if ($num_rspnd_used < $count_rspnd) {

                    echo '
                        <li>
                            <span>'.$row["output_text"].'</span>
                        </li>
                    ';

                    /***********/

                    $num_rspnd_used += 1;

                }

                /***************/

                if ($num_rspnd_used === $count_rspnd) {

                    if ($_SESSION["curr_sys_type"] === 'teacher') {

                        include($_SERVER['DOCUMENT_ROOT']."/app/system/form/fdbck_output_add.php");

                    }

                    else if ($count_rspnd === 0){

                        echo '
                            <li>
                                <span><em>'.$message_empty_prefix.' nema odgovora!</em></span>
                            </li>
                        ';

                    }

                    /***********/

                    echo '
                        </ul>
                    ';

                    /***********/

                    $num_fdbck_used += 1;
                    $num_rspnd_used = 0;

                    $is_fdbck_open = false;

                }

            }

            /***************/

            if ($num_fdbck_used === $count_fdbck) {

                if ($_SESSION["curr_sys_type"] === 'student' or $_SESSION["curr_sys_type"] === 'preview') {

                    include($_SERVER['DOCUMENT_ROOT']."/app/system/form/fdbck_input_add.php");

                }

                else if ($count_fdbck === 0) {

                    $temp_type_id = intval(str_replace(" ", "", $row["type_id"]));

                    if ($temp_type_id === 1) {

                        echo '
                            <li>
                                <span><em>'.$message_empty_prefix.' nema komentara!</em></span>
                            </li>
                        ';

                    }

                    else {

                        echo '
                            <li>
                                <span><em>'.$message_empty_prefix.' nema pitanja!</em></span>
                            </li>
                        ';

                    }

                }

            }

        }

        /*******************/

        echo '
            </ul>
        ';

        /*******************/

        include($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_data_close.php");

        /*******************/

        echo '
            </section>
        ';

    }

    else {

        $_SESSION["site_error"] = "Nema kolegija kojeg možete pregledati!";

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