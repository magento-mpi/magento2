<?php
/**
 * Script to retrieve number of orders before checkout scenario execution
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../../../app/bootstrap.php';
Mage::app('', 'store');
$collection = new Mage_Sales_Model_Resource_Order_Collection();
echo "Num orders: ", $collection->getSize(), PHP_EOL;
