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

$mediaDir = Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config')->getBaseMediaPath();
mkdir($mediaDir . '/m/a', 0777, true);
copy(__DIR__ . '/magento_image.jpg', $mediaDir . '/m/a/magento_image.jpg');
