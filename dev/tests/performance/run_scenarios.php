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

// Run scenarios
try {
    /** @var $bootstrap Magento_Bootstrap */
    $bootstrap = require_once __DIR__ . '/framework/bootstrap.php';

    // Run queue
    $queue = new Magento_Scenario_Queue($bootstrap->getConfig());
    $queue->run();

    if ($queue->getNumFailedScenarios()) {
        throw new Magento_Exception(
            "Failed {$queue->getNumFailedScenarios()} of {$queue->getNumScenarios()} scenario(s)"
        );
    }

    echo 'Successful', PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
}
