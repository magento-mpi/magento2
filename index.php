<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

$bootstrapFile = 'app/bootstrap.php';

if (!file_exists($bootstrapFile)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo "{$bootstrapFile} was not found";
    }
    exit;
}

if (file_exists('maintenance.flag')) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

require_once $bootstrapFile;

#Magento_Profiler::enable();
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Html());
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Firebug());
#Magento_Profiler::registerOutput(new Magento_Profiler_Output_Csvfile(__DIR__ . '/var/log/profiler.csv'));

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';
/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

Mage::run($mageRunCode, $mageRunType);
