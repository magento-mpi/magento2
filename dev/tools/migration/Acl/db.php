<?php
$shortOpts = 'ph';
$options = getopt($shortOpts);

try {
    $fileReader = new Tools_Migration_Acl_Db_FileReader();
    $map = $fileReader->extractData($options['file']);

    $dbAdapterFactory = new Tools_Migration_Acl_Db_Adapter_Factory();
    $dbAdapter = $dbAdapterFactory->getAdapter($options['type'], $options['db_config']);

    $loggerFactory = new Tools_Migration_Acl_Db_Logger_Factory();
    $logger = $loggerFactory->getLogger($options['format']);

    $writer = new Tools_Migration_Acl_Db_Writer($dbAdapter);
    $reader = new Tools_Migration_Acl_Db_Reader($dbAdapter);

    $updater = new Tools_Migration_Acl_Db_Updater($reader, $writer, $logger, $options['mode']);
    $updater->migrate($map);

    $logger->report();
} catch (InvalidArgumentException $exp) {
    echo $exp->getMessage();
} catch (Exception $exp) {
    echo $exp->getMessage();
}
