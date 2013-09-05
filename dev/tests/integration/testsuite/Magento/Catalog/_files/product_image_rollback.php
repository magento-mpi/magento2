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
$config = Mage::getSingleton('Magento_Catalog_Model_Product_Media_Config');
\Magento\Io\File::rmdirRecursive($config->getBaseMediaPath());
\Magento\Io\File::rmdirRecursive($config->getBaseTmpMediaPath());
