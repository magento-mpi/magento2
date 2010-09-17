<?php 

require_once('Classes.php');
$options = getopt('o:a:r:');

$_GET['act'] = $options['a'];
$_GET['r'] = $options['r'];

$controller = new Performance_Report_Action();

ob_start();
$controller->run();
$log = ob_get_clean();

if (!file_put_contents($options['o'], $log)) {
    $stderr = fopen('php://stderr', 'w');
    fwrite($stderr, "Problem saving html report.");
    fclose($stderr);
    exit(1);
}

exit(0);

