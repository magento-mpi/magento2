<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\Filesystem $filesystem */
$filesystem = $objectManager->create('Magento\Framework\App\Filesystem');

/** @var $tmpDirectory \Magento\Framework\Filesystem\Directory\WriteInterface */
$tmpDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::TMP_DIR);
$tmpDirectory->create($tmpDirectory->getAbsolutePath());

$targetTmpFilePath = $tmpDirectory->getAbsolutePath('magento_small_image.jpg');
if (file_exists($targetTmpFilePath)) {
    unlink($targetTmpFilePath);
}