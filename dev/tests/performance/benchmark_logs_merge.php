<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = realpath(__DIR__ . '/../../../');
require_once $magentoBaseDir. '/lib/Zend/Console/Getopt.php';

$shell = new Zend_Console_Getopt(array(
    'xml=s'  => 'xml',
    'csv=s'  => 'csv',
    'logs=s' => 'logs'
));

$args = $shell->getOptions();
if (empty($args)) {
    echo $shell->getUsageMessage();
    exit(1);
}

$xmlUrl = $shell->getOption('xml');
$scvUrl = $shell->getOption('csv');
$newLogsUrl = $shell->getOption('logs');

if (!file_exists($xmlUrl)) {
    echo 'xml not exist';
    exit(1);
}

if (!file_exists($scvUrl)) {
    echo 'csv not exist';
    exit(1);
}

$xml = simplexml_load_file($xmlUrl);
$scv = readCsv($scvUrl);
$result = array();

foreach($xml as $key => $value) {
    unset($value->httpSample);
    unset($value->assertionResult);
}

foreach($scv as $line) {
    if ($line[2] != '') {
        if (!isset($result[$line[2]])) {
            $result[$line[2]]['t'] = $line[1];
            $result[$line[2]]['ts'] = $line[0];
        } else {
            if ($result[$line[2]]['t'] < $line[1]) {
                $result[$line[2]]['t'] = $line[1];
                $result[$line[2]]['ts'] = $line[0];
            }
        }
    }
}

foreach($result as $key => $value) {
    $httpSample = $xml->addChild('httpSample');

    $httpSample->addAttribute('t',$value['t']);
    $httpSample->addAttribute('lt',$value['t']);
    $httpSample->addAttribute('ts',$value['ts']);
    $httpSample->addAttribute('s','true');
    $httpSample->addAttribute('lb',$key);
    $httpSample->addAttribute('rc','200');
    $httpSample->addAttribute('rm','OK');
    $httpSample->addAttribute('tn',$key);
}

$xml->asXML($newLogsUrl);

function readCsv($csvFile){
    $fileHandle = fopen($csvFile, 'r');
    $lineOfText = array();
    while (!feof($fileHandle) ) {
        $lineOfText[] = fgetcsv($fileHandle, 1024);
    }
    fclose($fileHandle);
    return $lineOfText;
}