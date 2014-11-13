<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use \Magento\Tools\Composer\Package\Package;

require __DIR__ . '/../../../bootstrap.php';
$generationDir = __DIR__ . '/_packages';

use \Magento\Tools\Composer\Helper\Zipper;
use \Magento\Tools\Composer\Package\Reader;

/**
 * Composer Archiver Tool
 *
 * This tool creates archive (zip) packages for each component in Magento
 */
try {
    $opt = new \Zend_Console_Getopt(
        array(
            'output|o=s' => 'Generation dir. Default value ' . $generationDir,
            'dir|d=s' => 'Working directory. Default value ' . realpath(BP),
        )
    );
    $opt->parse();

    $workingDir = $opt->getOption('d') ?: realpath(BP);
    $workingDir = str_replace('\\', '/', realpath($workingDir));

    if (!$workingDir || !is_dir($workingDir)) {
        throw new Exception($opt->getOption('d') . " must be a Magento code base.");
    }

    $generationDir = $opt->getOption('o') ?: $generationDir;
    $logWriter = new \Zend_Log_Writer_Stream('php://output');

    $logWriter->setFormatter(new \Zend_Log_Formatter_Simple('[%timestamp%] : %message%' . PHP_EOL));
    $logger = new Zend_Log($logWriter);
    $logger->setTimestampFormat("H:i:s");

    $logger->info(sprintf("Your archives output directory: %s. ", $generationDir));
    $logger->info(sprintf("Your Magento Installation Directory: %s ", $workingDir));
    $reader = new Reader($workingDir);

    try {
        if (!file_exists($generationDir)) {
            mkdir($generationDir, 0777, true);
        }
    } catch (\Exception $ex) {
        $logger->error(sprintf("ERROR: Creating Directory %s failed. Message: %s", $generationDir, $ex->getMessage()));
        exit($e->getCode());
    }

    $logger->info(sprintf("Zip Archive Location: %s", $generationDir));

    $noOfZips = 0;

    foreach ($reader->readMagentoPackages() as $package) {
        $noOfZips += archivePackage($package, $generationDir, $logger);
    }

    //archive root package
    $noOfZips += archivePackage($reader->readFromDir(''), $generationDir, $logger, $reader->getExcludePaths());

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
exit(0);

/**
 * Archive a package
 *
 * @param Package $package
 * @param string $generationDir
 * @param Zend_Log $logger
 * @param array $excludes
 * @return int
 * @throws Exception
 */
function archivePackage($package, $generationDir, $logger, $excludes = [])
{
    $version = $package->get('version');
    $fileName = str_replace('/', '_', $package->get('name')) . "-{$version}" . '.zip';
    $sourceDir = str_replace('\\', '/', realpath(dirname($package->getFile())));
    $noOfZips = Zipper::zip($sourceDir, $generationDir . '/' . $fileName, $excludes);
    $logger->info(sprintf("Created zip archive for %-40s [%9s]", $fileName, $version));
    return $noOfZips;
}