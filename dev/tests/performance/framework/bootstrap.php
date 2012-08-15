<?php
/**
 * Performance framework bootstrap script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$testsBaseDir = realpath(__DIR__ . '/..');
$magentoBaseDir = realpath($testsBaseDir . '/../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
Magento_Autoload::getInstance()->addIncludePath("$testsBaseDir/framework");

$configFile = "$testsBaseDir/config.php";
$configFile = file_exists($configFile) ? $configFile : "$configFile.dist";
$configData = require($configFile);
$config = new Magento_Config($configData, $testsBaseDir);

$installOptions = $config->getInstallOptions();
if ($installOptions) {
    // Populate install options with global options
    $baseUrl = 'http://' . $config->getApplicationUrlHost() . $config->getApplicationUrlPath();
    $installOptions = array_merge($installOptions, array('url' => $baseUrl, 'secure_base_url' => $baseUrl));
    $adminOptions = $config->getAdminOptions();
    foreach ($adminOptions as $key => $val) {
        $installOptions['admin_' . $key] = $val;
    }

    // Install application
    $installer = new Magento_Installer($magentoBaseDir . '/dev/shell/install.php', new Magento_Shell(true));
    echo 'Uninstalling application' . PHP_EOL;
    $installer->uninstall();
    echo "Installing application at '$baseUrl'" . PHP_EOL;
    $installer->install($installOptions, $config->getFixtureFiles());
    echo PHP_EOL;
}

$reportDir = $config->getReportDir();
if (file_exists($reportDir) && !Varien_Io_File::rmdirRecursive($reportDir)) {
    throw new Magento_Exception("Cannot cleanup reports directory '$reportDir'.");
}

return $config;
