<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Filesystem\Directory\Write $rootDirectory */
$rootDirectory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\App\Filesystem')->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
$rootDirectory->copyFile($rootDirectory->getRelativePath(__DIR__ . '/robots.txt'), 'robots.txt');
