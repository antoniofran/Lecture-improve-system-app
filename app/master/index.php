<?php

    include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_header.php");

    /* initially-get["info"]-if_elseif: "app_info_author" and "app_info_system" */

    /* afterward-session["key"]-if_elseif: "app_insttn_list" or "app_user_login" */

    include_once($_SERVER['DOCUMENT_ROOT']."/app/master/form/app_user_login.php");

    include_once($_SERVER['DOCUMENT_ROOT']."/app/master/func/app_footer.php");

?>