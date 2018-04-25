<?php

require_once("conf/conf.main.php");

function logMessage($message, $isError=False) {
    if ($isError) {
        // Log to default error log
        error_log($message);
    }

    // Append information
    $date = date('d/m/Y H:i:s');
    $prefix = "[$date][".$_SESSION['username']."] --- "; 

    file_put_contents(LOG_FILEPATH, $prefix.$message."\n", FILE_APPEND);
}