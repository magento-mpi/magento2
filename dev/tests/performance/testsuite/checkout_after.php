<?php
/**
 * Script to retrieve number of orders after checkout scenario execution, and to compare it to the expected value.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Parse parameters
$params = getopt('', array('beforeOutput:', 'scenarioExecutions:'));
preg_match("/Num orders: (\\d+)/", $params['beforeOutput'], $matches);
$numOrdersBefore = $matches[1];
$expectedOrdersCreated = $params['scenarioExecutions'];

// Retrieve current number of orders and calculate number of orders created
require_once __DIR__ . '/../../../../app/bootstrap.php';
Mage::app('', 'store');
$collection = new Mage_Sales_Model_Resource_Order_Collection();
$numOrdersNow = $collection->getSize();
$actualOrdersCreated = $numOrdersNow - $numOrdersBefore;

// Compare number of new orders to the expected value
if ($expectedOrdersCreated != $actualOrdersCreated) {
    echo "Failure: expected {$expectedOrdersCreated} new orders, while actually created {$actualOrdersCreated}";
    exit(1);
}

echo "Verification successful, {$actualOrdersCreated} of {$expectedOrdersCreated} orders created";
