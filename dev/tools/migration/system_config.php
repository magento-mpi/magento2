<?php

$type = 'w';
$mode = 'w';

$fileManager = new Tools_Migration_System_FileManager($mode);

$loggerFactory = new Tools_Migration_System_Configuration_Logger_Factory();
$logger = $loggerFactory->getLogger($type);

$generator = new Tools_Migration_System_Configuration_Generator($fileManager, $logger);

$mapper = new Tools_Migration_System_Configuration_Mapper($logger);

$parser = new Tools_Migration_System_Configuration_Parser();
$reader = new Tools_Migration_System_Configuration_Reader($fileManager, $parser, $mapper);

foreach ($reader->getConfiguration() as $file => $config) {
    $generator->createConfiguration($file, $config);
    $fileManager->remove($file);
}
