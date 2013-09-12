<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$designDir = Magento_TestFramework_Helper_Bootstrap::getInstance()->getAppInstallDir() . '/media_for_change';
Magento_Io_File::rmdirRecursive($designDir);
