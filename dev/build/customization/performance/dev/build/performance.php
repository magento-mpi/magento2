<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$baseDir = realpath(__DIR__ . '/../../');

$databaseDumpFile = "$baseDir/dev/build/database_dump.sql";

$jMeterJarFile = isset($_ENV['jmeter_jar']) ? $_ENV['jmeter_jar'] : 'ApacheJMeter.jar';

$scenarios = array(
    "$baseDir/dev/tests/performance/testsuite/add_to_cart-magento_2.jmx",
    "$baseDir/dev/tests/performance/testsuite/advanced_search-magento_2.jmx",
    "$baseDir/dev/tests/performance/testsuite/category_view-magento_2.jmx",
    "$baseDir/dev/tests/performance/testsuite/checkout-magento_2.jmx",
    "$baseDir/dev/tests/performance/testsuite/home_page-magento_2.jmx",
    "$baseDir/dev/tests/performance/testsuite/product_view-magento_2.jmx",
    "$baseDir/dev/tests/performance/testsuite/quick_search-magento_2.jmx",
);

$localXmlFile = "$baseDir/app/etc/local.xml";
$localXml = simplexml_load_file($localXmlFile);

$databaseImportCmd = sprintf(
    'mysqldump --protocol=TCP --host=%s --user=%s --password=%s %s < %s',
    escapeshellarg((string)$localXml->global->resources->default_setup->connection->host),
    escapeshellarg((string)$localXml->global->resources->default_setup->connection->username),
    escapeshellarg((string)$localXml->global->resources->default_setup->connection->password),
    escapeshellarg((string)$localXml->global->resources->default_setup->connection->dbname),
    escapeshellarg($databaseDumpFile)
);
passthru($databaseImportCmd, $exitCode);
if ($exitCode) {
    exit($exitCode);
}

$jMeterPropertyPrefix = 'jmeter_prop_';
$jMeterProperties = '';
foreach ($_ENV as $key => $value) {
    if (strpos($key, $jMeterPropertyPrefix) !== 0) {
        continue;
    }
    $key = substr($key, strlen($jMeterPropertyPrefix));
    $jMeterProperties .= " -J$key=$value";
}

$jMeterCmd = 'java -jar ' . escapeshellarg($jMeterJarFile) . ' -n -t %s -l %s' . $jMeterProperties;
foreach ($scenarios as $scenarioFile) {
    $scenarioLogFile = "$baseDir/dev/tests/performance/report/" . basename($scenarioFile, '.jmx') . ".jtl";
    $scenarioCmd = sprintf($jMeterCmd, escapeshellarg($scenarioFile), escapeshellarg($scenarioLogFile));
    passthru($scenarioCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
}
