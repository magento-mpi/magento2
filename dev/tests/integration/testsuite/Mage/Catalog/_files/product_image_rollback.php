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

/** @var $config Mage_Catalog_Model_Product_Media_Config */
$config = Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config');
Varien_Io_File::rmdirRecursive($config->getBaseMediaPath());
Varien_Io_File::rmdirRecursive($config->getBaseTmpMediaPath());
