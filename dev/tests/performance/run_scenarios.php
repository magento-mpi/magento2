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
$scenarioParams = $config['scenario_params'];
$fixtures = $config['fixtures'];
$reportDir = isset($config['report_dir']) ? $config['report_dir'] : __DIR__ . '/report';

/* Validate scenarios existence */
foreach ($scenarioFiles as $scenarioFile) {
    if (!file_exists($scenarioFile)) {
        echo "Scenario file '$scenarioFile' does not exist." . PHP_EOL;
        exit(1);
    }
}

/* Validate JMeter command presence */
$jMeterJarFile = getenv('jmeter_jar_file') ?: 'ApacheJMeter.jar';
$jMeterExecutable = 'java -jar ' . escapeshellarg($jMeterJarFile);
exec("$jMeterExecutable --version 2>&1", $jMeterOutput, $exitCode);
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

/* Clean reports */
Varien_Io_File::rmdirRecursive($reportDir);

/* Apply fixtures */
Mage::app();
foreach ($fixtures as $fixture) {
    require_once $fixture;
}

/* Execute each scenario couple times to populate cache (if any) before measuring performance */
$scenarioDryRunParams = array_merge($scenarioParams, array('users' => 1, 'loops' => 2));
foreach ($scenarioFiles as $scenarioFile) {
    $scenarioCmd = buildJMeterCmd($jMeterExecutable, $scenarioFile, $scenarioDryRunParams);
    passthru($scenarioCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
}

/* Execute scenarios and collect failures */
$failures = array();
foreach ($scenarioFiles as $scenarioFile) {
    $scenarioLogFile = $reportDir . DIRECTORY_SEPARATOR . basename($scenarioFile, '.jmx') . '.jtl';
    $scenarioCmd = buildJMeterCmd($jMeterExecutable, $scenarioFile, $scenarioParams, $scenarioLogFile);
    passthru($scenarioCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
    $scenarioLogXml = simplexml_load_file($scenarioLogFile);
    $failedAssertions = $scenarioLogXml->xpath('//assertionResult[failure[text()="true"] or error[text()="true"]]');
    if ($failedAssertions) {
        foreach ($failedAssertions as $assertionResult) {
            if (isset($assertionResult->failureMessage)) {
                $failures[$scenarioFile][] = (string)$assertionResult->failureMessage;
            }
            if (isset($assertionResult->errorMessage)) {
                $failures[$scenarioFile][] = (string)$assertionResult->errorMessage;
            }
        }
    }
}

/* Handle failures */
if ($failures) {
    foreach ($failures as $scenarioFile => $failureMessages) {
        echo "Scenario '$scenarioFile' has failed!" . PHP_EOL;
        echo implode(PHP_EOL, $failureMessages);
    }
    exit(1);
}


/**
 * Build JMeter command
 *
 * @param string $jMeterExecutable
 * @param string $testPlanFile
 * @param array $localProperties
 * @param string|null $sampleLogFile
 * @return string
 */
function buildJMeterCmd($jMeterExecutable, $testPlanFile, array $localProperties = array(), $sampleLogFile = null) {
    $result = $jMeterExecutable . ' -n -t ' . escapeshellarg($testPlanFile);
    if ($sampleLogFile) {
        $result .= ' -l ' . escapeshellarg($sampleLogFile);
    }
    foreach ($localProperties as $key => $value) {
        $result .= " -J$key=$value";
    }
    return $result;
}
