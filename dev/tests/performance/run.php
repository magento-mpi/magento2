<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * JMeter execution from command line
 */
$command = "java -jar D:\Install\jakarta-jmeter-2.4\bin\ApacheJMeter.jar -n";

/**
 * JMeter parameters for all instances and scenarios
 */
$globalParams = array(
    'host'  => 'mage-dev.varien.com',
    'loops' => '100'
);

/**
 * Number of possible concurrent users for testing
 */
$userThreads = array(1, 10, 30);

/**
 * Instances and instance parameters definition
 */
$instances = array(
    'prestashop'    => array('path' => '/dev/performance/prestashop/'),
    'oxid'          => array('path' => '/dev/performance/oxid/'),
    'oscommerce_2'  => array('path' => '/dev/performance/oscommerce-two/catalog/'),
    'oscommerce_3'  => array('path' => '/dev/performance/oscommerce/'),
    'magento_2'     => array('path' => '/dev/performance/magento-two/'),
);

/**
 * All test scenarios with samplers group prefix if applicable
 */
$scenarios = array(
    'category_view' => array(),
    'home_page'     => array(),
    'product_view'  => array(),
    'quick_search'  => array(),
    'add_to_cart'   => array('prefix' => 'Cart'),
    'checkout'      => array('prefix' => 'Checkout'),
    'advanced_search' => array(),
);

/**
 * Run tests with all combinations of concurrent users, scenarios and instances
 */
foreach ($userThreads as $thread) {
    foreach ($scenarios as $scenarioCode => $scenarioParams) {
        foreach ($instances as $instanceCode => $instanceParams) {
            $params = $globalParams;
            $params['users']    = $thread;
            $params = array_merge($params, $instanceParams);

            $testFile = dirname(__FILE__) . '/testsuite/' . $scenarioCode . '-' . $instanceCode . '.jmx';
            if (!file_exists($testFile)) {
                echo 'File "' . $testFile . '" doesn\'t exist';
                continue;
            }
            $logFile  = dirname(__FILE__) . '/tmp/' . $thread . '-' . $scenarioCode . '-' . $instanceCode . '.jtl';
            $instanceCmd = $command . ' -t ' . $testFile . ' -l ' . $logFile;
            foreach ($params as $name => $value) {
                $instanceCmd .= ' -J' . $name . '=' . $value;
            }
            system($instanceCmd, $result);
            sleep(30);
        }
    }
}


/**
 * Generate summary data in csv format
 */
$reportFile = fopen('tmp/report.csv', 'w');
fputcsv($reportFile, array_merge(array('Scenario', 'Metric', 'Sampler', 'Instance',), $userThreads));
foreach ($scenarios as $scenarioCode => $scenarioData) {
    /* Aggregated data array per scenario */
    $data  = array();
    $scenarioPrefix = isset($scenarioData['prefix']) ? $scenarioData['prefix'] : false;
    foreach ($instances as $instanceCode => $instanceData) {
        $data[$instanceCode] = array();
        foreach ($userThreads as $thread) {
            /* Prepare http samples array */
            $file  = dirname(__FILE__) . '/tmp/' . $thread . '-' . $scenarioCode . '-' . $instanceCode . '.jtl';
            if (!file_exists($file)) {
                /**
                 * Adding "0" values for not existing results
                 */
                $data[$instanceCode]['full_scenario_total'][$thread] = array(
                    'avg' => 0,
                    'min' => 0,
                    'max' => 0,
                    'rps' => 0,
                );
                echo 'File "' . $file . "\" doesnt exist \n";
                continue;
            }
            $xml  = simplexml_load_file($file);
            if ($xml->xpath('//failure[.=\'true\']')) {
                 echo 'There is an failure assertion in: "' . $file . "\" file.\n";
                continue;
            }
            $nodes = $xml->xpath('httpSample');

            $sampleData = array();
            foreach ($nodes as $node) {
                $attributes = $node->attributes();
                $label = (string) $attributes['lb'];
                if (!isset($sampleData[$label])) {
                    $sampleData[$label] = array();
                }
                /* Collect response time per sample label */
                $sampleData[$label][] = (int)$attributes['t'];
            }

            /* Prepare total and clean scenario data */
            if (count($sampleData)>1) {
                $sampleData['full_scenario_total'] = array();
                foreach ($sampleData as $label => $timers) {
                    $sampleData['full_scenario_total'] = array_merge($sampleData['full_scenario_total'], $timers);
                    if ($scenarioPrefix && strpos($label, $scenarioPrefix) === 0) {
                        if (!isset($sampleData['clean_scenario_total'])) {
                            $sampleData['clean_scenario_total'] = array();
                        }
                        $sampleData['clean_scenario_total'] = array_merge($sampleData['clean_scenario_total'], $timers);
                    }
                }
            }


            /* Calculate test execution time */
            $firstNode = $nodes[0]->attributes();
            $lastNode = $nodes[count($nodes)-1]->attributes();
            $time = (float)$lastNode['ts'] - (float)$firstNode['ts'];

            /**
             * Prepare summary data:
             * - per sampler
             * - per scenario
             * - totals
             */
            $samplesCount = count($sampleData);
            foreach ($sampleData as $label => $timers) {
                if (!isset($data[$instanceCode][$label])) {
                    $data[$instanceCode][$label] = array();
                }
                $data[$instanceCode][$label][$thread] = array();
                $data[$instanceCode][$label][$thread]['avg'] = round(array_sum($sampleData[$label])/count($sampleData[$label]), 2);
                $data[$instanceCode][$label][$thread]['min'] = round(min($sampleData[$label]), 2);
                $data[$instanceCode][$label][$thread]['max'] = round(max($sampleData[$label]), 2);

                /* add sampler data if necessary */
                if ($time && ($samplesCount == 1 || $label == 'full_scenario_total')) {
                    $data[$instanceCode][$label][$thread]['rps'] = round(count($sampleData[$label])/$time*1000, 2);
                }
            }
        };
    }

    foreach ($data as $instanceCode => $instanceData) {
        foreach ($instanceData as $samplerCode => $samplerData) {
            foreach (array('avg', 'min', 'max', 'rps') as $metricCode) {
                $row = array(
                    $scenarioCode,
                    $metricCode,
                    $samplerCode,
                    $instanceCode,
                );
                foreach ($samplerData as $users => $metrics) {
                    if (isset($metrics[$metricCode])) {
                        $row[] = $metrics[$metricCode];
                    }
                }
                if (count($row)>4) {
                    fputcsv($reportFile, $row);
                }
            }
        }
    }
}
fclose($reportFile);
