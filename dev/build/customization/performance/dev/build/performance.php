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

$jMeterJarFile = isset($_ENV['jmeter_jar_file']) ? $_ENV['jmeter_jar_file'] : 'ApacheJMeter.jar';
$databaseDumpFile = isset($_ENV['database_dump_file']) ? $_ENV['database_dump_file'] : __DIR__ . '/database_dump.sql';
$scenarios = include(isset($_ENV['scenarios_source_file']) ? $_ENV['scenarios_source_file'] : __DIR__ . '/config.php');
$reportDir = isset($_ENV['report_dir']) ? $_ENV['report_dir'] : "$baseDir/dev/tests/performance/report";

$localXmlFile = "$baseDir/app/etc/local.xml";
$localXml = simplexml_load_file($localXmlFile);

$databaseImportCmd = sprintf(
    'mysql --protocol=TCP --host=%s --user=%s --password=%s %s < %s',
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
    $scenarioLogFile = $reportDir . DIRECTORY_SEPARATOR . basename($scenarioFile, '.jmx') . '.jtl';
    $scenarioCmd = sprintf($jMeterCmd, escapeshellarg($scenarioFile), escapeshellarg($scenarioLogFile));
    passthru($scenarioCmd, $exitCode);
    if ($exitCode) {
        exit($exitCode);
    }
}
