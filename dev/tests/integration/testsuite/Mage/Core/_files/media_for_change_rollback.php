<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$designDir = Magento_Test_Bootstrap::getInstance()->getInstallDir() . '/media_for_change';
Varien_Io_File::rmdirRecursive($designDir);
