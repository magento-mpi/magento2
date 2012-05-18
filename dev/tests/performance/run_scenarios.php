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

$baseDir = realpath(__DIR__ . '/../../../');

$configFile = __DIR__ . '/config.php';
$configFile = file_exists($configFile) ? $configFile : "$configFile.dist";
$config = require($configFile);
$installOptions = isset($config['install_options']) ? $config['install_options'] : array();
$scenarioFiles = $config['scenario_files'];
$fixtures = $config['fixtures'];
$reportDir = isset($config['report_dir']) ? $config['report_dir'] : __DIR__ . '/report';

/* Build JMeter command */
$jMeterJarFile = getenv('jmeter_jar_file') ?: 'ApacheJMeter.jar';
$jMeterProperties = '';
foreach ($config['scenario_params'] as $key => $value) {
    $jMeterProperties .= " -J$key=$value";
}
$jMeterCmd = 'java -jar ' . escapeshellarg($jMeterJarFile);
exec("$jMeterCmd --version 2>&1", $jMeterOutput, $exitCode);
if ($exitCode) {
    echo implode(PHP_EOL, $jMeterOutput);
    exit($exitCode);
}

/* Install application */
if ($installOptions) {
    $installCmd = sprintf('php -f %s --', escapeshellarg("$baseDir/dev/shell/install.php"));
    passthru("$installCmd --uninstall", $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
    foreach ($installOptions as $optionName => $optionValue) {
        $installCmd .= sprintf(' --%s %s', $optionName, escapeshellarg($optionValue));
    }
    passthru($installCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
}

/* Magento initialization (for reports removal and fixtures) */
require_once __DIR__ . '/../../../app/bootstrap.php';
Mage::app();

/* Clean reports */
Varien_Io_File::rmdirRecursive($reportDir);

/* Apply fixtures */
foreach ($fixtures as $fixture) {
    require_once $fixture;
}

/* Execute scenarios and collect failures */
$failures = array();
foreach ($scenarioFiles as $scenarioFile) {
    $scenarioLogFile = $reportDir . DIRECTORY_SEPARATOR . basename($scenarioFile, '.jmx') . '.jtl';
    $scenarioCmd = sprintf(
        $jMeterCmd . ' -n -t %s -l %s' . $jMeterProperties,
        escapeshellarg($scenarioFile),
        escapeshellarg($scenarioLogFile)
    );
    passthru($scenarioCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
    $scenarioLogXml = simplexml_load_file($scenarioLogFile);
    $failedAssertions = $scenarioLogXml->xpath('//assertionResult[failure[text()="true"] or error[text()="true"]]');
    if ($failedAssertions) {
        $failures[$scenarioFile] = $failedAssertions;
    }
}

/* Handle failures */
if ($failures) {
    foreach ($failures as $scenarioFile => $failedAssertions) {
        echo "Scenario '$scenarioFile' has failed!\n";
        foreach ($failedAssertions as $assertionResult) {
            if (isset($assertionResult->failureMessage)) {
                echo $assertionResult->failureMessage . "\n";
            } else if (isset($assertionResult->errorMessage)) {
                echo $assertionResult->errorMessage . "\n";
            }
        }
    }
    exit(1);
}
