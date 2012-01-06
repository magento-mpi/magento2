<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test cases for pricesSegmentationDataProvider
 */

$testCases = array(
    array('db', 'user'),
    array('memcache', 'memcache'),
    array('memcached', 'memcached'),
    array('eaccelerator', 'eaccelerator'),
    array('', ''),
    array('dummy', ''),
);

return $testCases;
