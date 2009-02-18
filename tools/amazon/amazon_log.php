<?php

/**
 * Amazon log from database
 * print last 10 records from amazon_api_debug table
 *
 */
    ini_set('display_errors', 1);

    $mageFilename = '../../app/Mage.php';

    if (!file_exists($mageFilename)) {
        echo $mageFilename." was not found";
        exit;
    }

    require_once $mageFilename;
    Mage::app('admin');


    #$_query = "SELECT * FROM amazonpayments_api_debug ORDER BY debug_id DESC LIMIT 10";
    #$res = mysql_query($_query);

    $loadDebug = Mage::getModel('amazonpayments/api_debug')->getCollection();
        #->setPage(1, 10);
        #->addLimit(10);

    #while ($records = mysql_fetch_assoc($res)) {
    $logArray = array();
    foreach ($loadDebug->getData() as $_debug) {
        $logArray[] = $_debug;
    }
    $logArray = array_reverse($logArray);

    $i = 1;
    echo "Last 10 records in the Amazon log table:<hr />\n";
    foreach ($logArray as $_debug) {
        if ($i++ > 10) break;

        echo '<table border="0" cellpadding="0" cellspacing="0">'."\n";
        foreach ($_debug as $k => $v) {
            echo "\n<tr><td>\n{$k}\n</td>\n";

            if ($k == 'response_body') {
                if (!strpos($v, 'calculations-response')) {
                    echo "<td><textarea cols=\"120\" rows=\"10\" readonly>{$v}</textarea>\n</td></tr>\n\n";
                } else {
                    echo "<td><textarea cols=\"120\" rows=\"10\" readonly>{$v}\n\nUrlencoded:\n"
                        .urldecode($v)
                        ."</textarea>\n</td></tr>\n\n";
                }
            } elseif ($k == 'request_body') {
                echo "<td><textarea cols=\"120\" rows=\"10\" readonly>{$v}</textarea>\n</td></tr>\n\n";
            } else {
                echo "<td>\n{$v}\n</td></tr>\n\n";
            }
        }
        echo "</table>\n";
        echo "<hr \>\n\n\n";
    }

?>