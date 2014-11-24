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
    $shell = new Zend_Console_Getopt(array(
        'm|mainline=s' => 'Response time report for mainline',
        'b|branch=s' => 'Response time report for branch',
        'o|output=s' => 'Target output file'
    ));

    $args = $shell->getOptions();
    if (empty($args)) {
        echo $shell->getUsageMessage();
        exit(1);
    }


    $mainlineResults = readResponseTimeReport($shell->getOption('mainline'));
    $branchResults = readResponseTimeReport($shell->getOption('branch'));

    $result = new SimpleXMLElement('<testResults version="1.2" />');
    foreach (array_keys($mainlineResults) as $sampleName) {
        $success = isset($mainlineResults[$sampleName]['success'])
            && $mainlineResults[$sampleName]['success']
            && isset($branchResults[$sampleName])
            && isset($branchResults[$sampleName]['success'])
            && $branchResults[$sampleName]['success'];

        $deviation = $success
            ? getDeviation($mainlineResults[$sampleName]['times'], $branchResults[$sampleName]['times'])
            : 100;

        $sample = $result->addChild('httpSample');
        $sample->addAttribute('s', $success ? 'true' : 'false');
        $sample->addAttribute('t', round($deviation, 4));
        $sample->addAttribute('lb', $sampleName . ' degradation');
    }

    $dom = new DOMDocument("1.0");
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($result->asXML());
    file_put_contents($shell->getOption('output'), $dom->saveXML());

} catch (\Zend_Console_Getopt_Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n\n" . $e->getUsageMessage() . "\n");
    exit(1);
} catch (\Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

function readResponseTimeReport($filename) {
    $result = [];
    $f = fopen($filename, 'r');
    while (!feof($f) && is_array($line = fgetcsv($f))) {
        $responseTime = $line[1];
        $title = $line[2];
        $success = $line[7];
        if (!isset($result[$title])) {
            $result[$title] = ['times' => [], 'success' => true];
        }

        $result[$title]['times'][] = $responseTime;
        $result[$title]['success'] &= ($success == 'true');
    }
    return $result;
}

function getMeanValue(array $times) {
    return array_sum($times) / count($times);
}

function getDeviation(array $mainlineResults, array $branchResults) {
    return 100 * (getMeanValue($branchResults) / getMeanValue($mainlineResults) - 1);
}