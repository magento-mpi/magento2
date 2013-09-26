<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $config Magento_Catalog_Model_Product_Media_Config */
$config = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Catalog_Model_Product_Media_Config');
Magento_Io_File::rmdirRecursive($config->getBaseMediaPath());
Magento_Io_File::rmdirRecursive($config->getBaseTmpMediaPath());
