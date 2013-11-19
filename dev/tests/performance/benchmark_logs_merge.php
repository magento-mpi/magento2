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
    'xml-s' => 'xml',
    'csv-s' => 'csv',
    'logs-s' => 'logs'
));

$args = $shell->getOptions();
if (empty($args)) {
    echo $shell->getUsageMessage();
    exit(1);
}

$xml_url = $shell->getOption('xml');
$scv_url = $shell->getOption('csv');
$new_logs_url = $shell->getOption('logs');

if (!file_exists($xml_url)) {
    echo 'xml not exist';
    exit(1);
}

if (!file_exists($scv_url)) {
    echo 'csv not exist';
    exit(1);
}

$xml = simplexml_load_file($xml_url);
$scv = readCSV($scv_url);
$result = array();

foreach($scv as $line) {
    if ($line[2]!='') {
        if (!isset($result[$line[2]])) {
            $result[$line[2]]['t'] = $line[1];
            $result[$line[2]]['ts'] = $line[0];
        } else {
            if ($result[$line[2]]['t']<$line[1]) {
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

    $assertionResult = $httpSample->addChild('assertionResult');
    $assertionResult->addChild('name', 'false');
    $assertionResult->addChild('failure', 'false');
    $assertionResult->addChild('error', 'false');
}

$xml->asXML($new_logs_url);

function readCSV($csvFile){
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle) ) {
        $line_of_text[] = fgetcsv($file_handle, 1024);
    }
    fclose($file_handle);
    return $line_of_text;
}