<?php

// Initialize session

if (session_id()==='') session_start();

/*****************/

// Redirect to homepage

    /* if ["not logged in"] then {rediract to master homepage} */

    /* if ["currUri" <> master] and ["currUri <> currRole" meaning "/app/admin <> roleStudent"] then {rediract to currRole homepage} */

/*****************/

echo '
    <!DOCTYPE html>

    <html>

        <head>

            <meta charset="UTF-8">

            <title>Sustav poboljšavanja nastave</title>

            <link rel="stylesheet" href="/css/normalize.css">
            <link rel="stylesheet" href="/css/stylesheet.css">
            <link rel="shortcut icon" href="/img/favicon/favicon.ico">

            '.(
                (
                    isset($_SESSION["site_notice"])
                    or isset($_SESSION["site_error"])
                ) ?
                    '
                        <style>
                            .content-left, .content-right {
                                min-height: calc(100vh - 11.35em) !important;
                            }
                        </style>
                    '
                :
                    '')
            .'

        </head>

        <body>

            <article class="container">

                <section class="header clearfix">

                    <div class="logo flt-left">

                        <a href="/index.php">Sustav poboljšavanja nastave</a>

                    </div>
';

include_once($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_segm_welcome.php");

echo '
                </section>
';

include_once($_SERVER['DOCUMENT_ROOT']."/app/master/actn/app_segm_message.php");

echo '
                <div class="content-container clearfix">
';

?>