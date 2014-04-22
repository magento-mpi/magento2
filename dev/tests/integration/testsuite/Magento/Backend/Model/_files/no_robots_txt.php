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

/** @var \Magento\Framework\Filesystem\Directory\Write $rootDirectory */
$rootDirectory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\App\Filesystem'
)->getDirectoryWrite(
    \Magento\Framework\App\Filesystem::ROOT_DIR
);
if ($rootDirectory->isExist('robots.txt')) {
    $rootDirectory->delete('robots.txt');
}
