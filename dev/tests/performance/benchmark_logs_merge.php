<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = realpath(__DIR__ . '/../../../');
require_once $magentoBaseDir . '/lib/internal/Zend/Console/Getopt.php';

try {
    $shell = new Zend_Console_Getopt(array('xml=s' => 'xml', 'csv=s' => 'csv', 'logs=s' => 'logs'));

    $args = $shell->getOptions();
    if (empty($args)) {
        echo $shell->getUsageMessage();
        exit(1);
    }

    $xmlUrl = $shell->getOption('xml');
    $scvUrl = $shell->getOption('csv');
    $newLogsUrl = $shell->getOption('logs');

    if (!file_exists($xmlUrl)) {
        echo 'xml does not exist';
        exit(1);
    }

    if (!file_exists($scvUrl)) {
        echo 'csv does not exist';
        exit(1);
    }

    $xml = simplexml_load_file($xmlUrl);
    $scv = readCsv($scvUrl);

    foreach ($xml as $key => $value) {
        unset($value->httpSample);
        unset($value->assertionResult);
    }

    foreach ($scv as $key => $value) {
        $httpSample = $xml->addChild('httpSample');

        $httpSample->addAttribute('t', $value[1]);
        $httpSample->addAttribute('lt', $value[1]);
        $httpSample->addAttribute('ts', $value[0]);
        $httpSample->addAttribute('s', 'true');
        $httpSample->addAttribute('lb', $value[2]);
        $httpSample->addAttribute('rc', '200');
        $httpSample->addAttribute('rm', 'OK');
        $httpSample->addAttribute('tn', $value[2]);
    }

    $xml->asXML($newLogsUrl);
} catch (\Zend_Console_Getopt_Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n\n" . $e->getUsageMessage() . "\n");
    exit(1);
} catch (\Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

function readCsv($csvFile)
{
    $fileHandle = fopen($csvFile, 'r');
    $lineOfText = array();
    while (!feof($fileHandle)) {
        $lineOfText[] = fgetcsv($fileHandle, 1024);
    }
    fclose($fileHandle);
    return $lineOfText;
}