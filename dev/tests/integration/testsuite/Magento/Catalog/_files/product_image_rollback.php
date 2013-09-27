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

/** @var $config \Magento\Catalog\Model\Product\Media\Config */
$config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Product\Media\Config');
Magento_Io_File::rmdirRecursive($config->getBaseMediaPath());
Magento_Io_File::rmdirRecursive($config->getBaseTmpMediaPath());
