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
$config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Catalog\Model\Product\Media\Config');
$filesystem =  \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Filesystem');
$filesystem->delete($config->getBaseMediaPath());
$filesystem->delete($config->getBaseTmpMediaPath());
