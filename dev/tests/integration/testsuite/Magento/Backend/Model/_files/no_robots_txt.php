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
$rootDirectory =  \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Filesystem')->getDirectoryWrite(\Magento\Filesystem::ROOT);
if ($rootDirectory->isExist('robots.txt')) {
    $rootDirectory->delete('robots.txt');
}
