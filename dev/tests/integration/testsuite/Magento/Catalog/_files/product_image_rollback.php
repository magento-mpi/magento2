<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use Magento\Framework\App\Filesystem\DirectoryList;

/** @var $config \Magento\Catalog\Model\Product\Media\Config */
$config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Catalog\Model\Product\Media\Config'
);

/** @var \Magento\Framework\Filesystem\Directory\WriteInterface $mediaDirectory */
$mediaDirectory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\Filesystem'
)->getDirectoryWrite(
    DirectoryList::MEDIA
);

$mediaDirectory->delete($config->getBaseMediaPath());
$mediaDirectory->delete($config->getBaseTmpMediaPath());
