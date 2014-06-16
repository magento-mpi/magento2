<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


require __DIR__ . '/../../../bootstrap.php';
$generationDir = __DIR__ . '/_packages';

use \Magento\Tools\Composer\Helper\Zipper;
use \Magento\Tools\Composer\Helper\Helper;

/**
 * Composer Archiver Tool
 *
 * This tool creates archive (zip) packages for each component in Magento, as well as the skeleton package.
 */
try {
    $opt = new \Zend_Console_Getopt(
        array(
            'verbose|v' => 'Detailed console logs',
            'output|o=s' => 'Generation dir. Default value ' . $generationDir,
        )
    );
    $opt->parse();

    $generationDir = $opt->getOption('o') ?: $generationDir;
    $logWriter = new \Zend_Log_Writer_Stream('php://output');

    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat("H:i:s");
    $filter = $opt->getOption('v') ?
            new \Zend_Log_Filter_Priority(Zend_Log::DEBUG) :
            new \Zend_Log_Filter_Priority(Zend_Log::INFO);
    $logger->addFilter($filter);

    $logger->info(sprintf("Your archives output directory: %s. ", $generationDir));
    $logger->info(sprintf("Your Magento Installation Directory: %s ", BP));

    try {
        if (!file_exists($generationDir)) {
            mkdir($generationDir, 0777, true);
        }
    } catch (\Exception $ex) {
        $logger->error(sprintf("ERROR: Creating Directory %s failed. Message: %s", $generationDir, $ex->getMessage()));
        exit($e->getCode());
    }

    $logger->debug(sprintf("Zip Archive Location: %s", $generationDir));

    $noOfZips = 0;

    //Creating zipped folders for all components
    $components = Helper::getComponentsList();
    $excludes = array();
    $name = '';
    foreach ($components as $component) {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($component, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $foundComposerJson = false;
        $prevDepth = 0;
        foreach ($files as $file) {
            if ((!$foundComposerJson) && ($files->getDepth() === 0) && ($files->getDepth() < $prevDepth)) {
                throw new \Exception('Did not find the composer.json file', '1');
            }

            $prevDepth = $files->getDepth();
            if ($files->getDepth() >= 2 || $files->isDir()) {
                continue;
            }

            if ($files->getDepth() === 0) {
                $foundComposerJson = false;
            }

            $file = str_replace('\\', '/', realpath($file));

            if (is_file($file) === true) {
                if (strpos($file, '/composer.json')) {
                    $foundComposerJson = true;
                    $json = json_decode(file_get_contents($file));
                    $name = Helper::vendorPackageToName($json->name);
                    if ($name == '') throw new \Exception('Not a valid vendorPackage name', '1')    ;
                    $noOfZips += Zipper::Zip(
                        dirname($file),
                        $generationDir . '/' . $name . "-". $json->version . '.zip',
                        $excludes
                    );
                    $logger->info(
                        sprintf(
                            "Created zip archive for %-40s [%9s]",
                            $name,
                            $json->version
                        )
                    );
                }
            }
        }
    }

    //Creating zipped folders for skeletons
    $excludes = array_merge(
        $components,
        array(
            str_replace('\\', '/', realpath(BP)) . '/.git',
            str_replace('\\', '/', realpath(BP)) . '/.idea',
            str_replace('\\', '/', realpath(BP)) . '/dev/tools/Magento/Tools/Composer'
        )
    );
    $name = '';
    if (file_exists(str_replace('\\', '/', realpath(BP)) . '/composer.json')) {
        $json = json_decode(file_get_contents(str_replace('\\', '/', realpath(BP)) . '/composer.json'));
        $name = Helper::vendorPackageToName($json->name);
        if ($name == '') throw new \Exception('Not a valid vendorPackage name', '1');
        $noOfZips += Zipper::Zip(
            str_replace('\\', '/', realpath(BP)),
            $generationDir . '/' . $name . '-'. $json->version . '.zip',
            $excludes
        );
        $logger->info(sprintf("Created zip archive for %-40s [%9s]", $name, $json->version));
    } else {
        throw new \Exception('Did not find the composer.json file in '. str_replace('\\', '/', realpath(BP)), '1');
    }

    $logger->info(
        sprintf(
            "SUCCESS: Zipped ". $noOfZips." packages. You should be able to find it at %s. \n",
            $generationDir
        )
    );

} catch (\Zend_Console_Getopt_Exception $e) {
    exit($e->getUsageMessage());
} catch (\Exception $e) {
    exit($e->getMessage());
}
