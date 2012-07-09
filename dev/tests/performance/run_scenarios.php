<?php
/**
 * JMeter scenarios execution script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$testsBaseDir = __DIR__;
$magentoBaseDir = realpath($testsBaseDir . '/../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
Magento_Autoload::getInstance()->addIncludePath("$testsBaseDir/framework");

$configFile = "$testsBaseDir/config.php";
$configFile = file_exists($configFile) ? $configFile : "$configFile.dist";
$configData = require($configFile);

$jMeterJarFile = getenv('jmeter_jar_file') ?: 'ApacheJMeter.jar';
try {
    $shell = new Magento_Shell(true);
    $config = new Benchmark_Config($configData, $testsBaseDir);
    $urlHost = $config->getApplicationUrlHost();
    $urlPath = $config->getApplicationUrlPath();
    $reportDir = $config->getReportDir();
    $installOptions = $config->getInstallOptions();
    if ($installOptions) {
        $baseUrl = 'http://' . $urlHost . $urlPath;
        $installOptions = array_merge($installOptions, array('url' => $baseUrl, 'secure_base_url' => $baseUrl));
        $app = new Benchmark_Application($magentoBaseDir, $shell);
        echo 'Uninstalling application' . PHP_EOL;
        $app->uninstall();
        echo "Installing application at '$baseUrl'" . PHP_EOL;
        $app->install($installOptions, $config->getFixtureFiles());
        echo PHP_EOL;
    }
    if (file_exists($reportDir) && !Varien_Io_File::rmdirRecursive($reportDir)) {
        throw new Magento_Exception("Cannot cleanup reports directory '$reportDir'.");
    }
    $scenarioTotalCount = count($config->getScenarios());
    $scenarioFailCount = 0;
    $scenarioNum = 1;
    foreach ($config->getScenarios() as $scenarioFile => $scenarioParams) {
        echo "Scenario $scenarioNum of $scenarioTotalCount: '$scenarioFile'" . PHP_EOL;
        $scenarioParams[Benchmark_Scenario::PARAM_HOST] = $urlHost;
        $scenarioParams[Benchmark_Scenario::PARAM_PATH] = $urlPath;
        try {
            $scenario = new Benchmark_Scenario($scenarioFile, $scenarioParams, $reportDir, $jMeterJarFile, $shell);
            echo "1) Warming up scenario to populate related cache (if any)" . PHP_EOL;
            $scenario->runDry(2);
            echo "2) Measuring scenario performance" . PHP_EOL;
            $scenario->run();
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            $scenarioFailCount++;
        }
        echo PHP_EOL;
        $scenarioNum++;
    }
    if ($scenarioFailCount) {
        throw new Magento_Exception("Failed $scenarioFailCount of $scenarioTotalCount scenario(s)");
    }
    echo 'Successful' . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
