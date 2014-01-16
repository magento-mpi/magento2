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

/** @var \Magento\Filesystem\Directory\WriteInterface $mediaDirectory */
$mediaDirectory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Filesystem')
    ->getDirectoryWrite(\Magento\Filesystem::MEDIA);

$mediaDirectory->delete($config->getBaseMediaPath());
$mediaDirectory->delete($config->getBaseTmpMediaPath());
