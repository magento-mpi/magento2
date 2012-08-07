<?php
$rootDir = realpath(__DIR__ . '/../../../..');

require_once $rootDir . '/lib/Magento/Autoload.php';
$paths[] = $rootDir . '/lib';
$paths[] = $rootDir . '/dev';
Magento_Autoload::getInstance()->addIncludePath($paths);
$defaultReportFile = __DIR__ . '/report.log';

try {
    $options = new Zend_Console_Getopt(array(
        'file=s' => "File containing json encoded acl identifier map (old => new)",
        'mode|m' => "Application mode. If set to 'preview' - no database update happens, only report is generated",
        'format|f-w' => "Report output type. Defaults to console. If filename is specified - report is written to file",
        'dbtype=w' => "Database server vendor",
        'dbhost=w' => "Database server host",
        'dbuser=w' => "Database server user",
        'dbpassword=w' => "Database server password",
        'dbname=w' => "Database name",
        'dbtable=w' => "Table containing resource ids",
    ));

    $fileReader = new Tools_Migration_Acl_Db_FileReader();

    $map = $fileReader->extractData($options->getOption('file'));

    $dbAdapterFactory = new Tools_Migration_Acl_Db_Adapter_Factory();

    $dbAdapter = $dbAdapterFactory->getAdapter(
        $options->getOption('dbtype'),
        $dbConfig = array(
            'host' => $options->getOption('dbhost'),
            'username' => $options->getOption('dbuser'),
            'password' => $options->getOption('dbpassword'),
            'dbname' => $options->getOption('dbname'),
        )
    );

    $loggerFactory = new Tools_Migration_Acl_Db_Logger_Factory();
    $logger = $loggerFactory->getLogger(
        $options->getOption('format'), $options->getOption('dbtable', $defaultReportFile)
    );

    $writer = new Tools_Migration_Acl_Db_Writer($dbAdapter, $options->getOption('dbtable'));
    $reader = new Tools_Migration_Acl_Db_Reader($dbAdapter, $options->getOption('dbtable'));

    $updater = new Tools_Migration_Acl_Db_Updater($reader, $writer, $logger, $options->getOption('mode'));
    $updater->migrate($map);

    $logger->report();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
} catch (InvalidArgumentException $exp) {
    echo $exp->getMessage();
} catch (Exception $exp) {
    echo $exp->getMessage();
}
