<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Framework\Filesystem\Directory\Write $rootDirectory */
$rootDirectory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\App\Filesystem'
)->getDirectoryWrite(
    \Magento\Framework\App\Filesystem::ROOT_DIR
);
$rootDirectory->copyFile($rootDirectory->getRelativePath(__DIR__ . '/robots.txt'), 'robots.txt');
