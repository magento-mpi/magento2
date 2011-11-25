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
Varien_Io_File::rmdirRecursive($mediaDir);
